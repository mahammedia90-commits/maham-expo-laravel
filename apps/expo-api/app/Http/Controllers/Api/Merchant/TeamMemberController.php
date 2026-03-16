<?php

namespace App\Http\Controllers\Api\Merchant;

use App\Http\Controllers\Controller;
use App\Http\Resources\TeamMemberResource;
use App\Models\MemberType;
use App\Models\TeamMember;
use App\Support\ApiResponse;
use App\Support\SafeOrderBy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeamMemberController extends Controller
{
    use SafeOrderBy;

    /**
     * قائمة أعضاء فريق التاجر
     */
    public function index(Request $request): JsonResponse
    {
        $query = TeamMember::with('memberType')
            ->forOwner($request->auth_user_id, 'merchant');

        // Filter by member type
        if ($request->filled('member_type_id')) {
            $query->ofType($request->input('member_type_id'));
        }

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Search
        if ($search = $this->sanitizeSearch($request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Order
        $this->applySafeOrder($query, $request, ['name', 'created_at', 'is_active'], 'created_at', 'desc');

        $members = $query->paginate($request->input('per_page', 15));

        return ApiResponse::paginated($members);
    }

    /**
     * إضافة عضو فريق جديد
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'member_type_id' => 'required|uuid',
            'name' => 'required|string|max:150',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'id_number' => 'nullable|string|max:20',
            'is_active' => 'sometimes|boolean',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Validate member type exists, is active, and belongs to merchant scope
        $memberType = MemberType::where('id', $validated['member_type_id'])
            ->active()
            ->where(function ($q) {
                $q->where('scope', 'merchant')
                  ->orWhere('scope', 'both');
            })
            ->first();

        if (!$memberType) {
            return ApiResponse::error(
                'نوع العضو غير صالح أو غير متاح للتجار',
                'INVALID_MEMBER_TYPE',
                422
            );
        }

        $validated['owner_id'] = $request->auth_user_id;
        $validated['owner_type'] = 'merchant';

        $member = TeamMember::create($validated);
        $member->load('memberType');

        return ApiResponse::created(
            new TeamMemberResource($member),
            'تم إضافة عضو الفريق بنجاح'
        );
    }

    /**
     * عرض عضو فريق
     */
    public function show(Request $request, TeamMember $teamMember): JsonResponse
    {
        // Ensure the member belongs to this merchant
        if ($teamMember->owner_id !== $request->auth_user_id || $teamMember->owner_type !== 'merchant') {
            return ApiResponse::notFound('عضو الفريق غير موجود');
        }

        $teamMember->load('memberType');

        return ApiResponse::success(new TeamMemberResource($teamMember));
    }

    /**
     * تحديث عضو فريق
     */
    public function update(Request $request, TeamMember $teamMember): JsonResponse
    {
        // Ensure the member belongs to this merchant
        if ($teamMember->owner_id !== $request->auth_user_id || $teamMember->owner_type !== 'merchant') {
            return ApiResponse::notFound('عضو الفريق غير موجود');
        }

        $validated = $request->validate([
            'member_type_id' => 'sometimes|uuid',
            'name' => 'sometimes|string|max:150',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'id_number' => 'nullable|string|max:20',
            'is_active' => 'sometimes|boolean',
            'notes' => 'nullable|string|max:1000',
        ]);

        // If changing member type, validate it
        if (isset($validated['member_type_id'])) {
            $memberType = MemberType::where('id', $validated['member_type_id'])
                ->active()
                ->where(function ($q) {
                    $q->where('scope', 'merchant')
                      ->orWhere('scope', 'both');
                })
                ->first();

            if (!$memberType) {
                return ApiResponse::error(
                    'نوع العضو غير صالح أو غير متاح للتجار',
                    'INVALID_MEMBER_TYPE',
                    422
                );
            }
        }

        $teamMember->update($validated);
        $teamMember->load('memberType');

        return ApiResponse::success(
            new TeamMemberResource($teamMember),
            'تم تحديث عضو الفريق بنجاح'
        );
    }

    /**
     * حذف عضو فريق
     */
    public function destroy(Request $request, TeamMember $teamMember): JsonResponse
    {
        // Ensure the member belongs to this merchant
        if ($teamMember->owner_id !== $request->auth_user_id || $teamMember->owner_type !== 'merchant') {
            return ApiResponse::notFound('عضو الفريق غير موجود');
        }

        $teamMember->delete();

        return ApiResponse::success(null, 'تم حذف عضو الفريق بنجاح');
    }

    /**
     * قائمة أنواع الأعضاء المتاحة للتجار
     */
    public function memberTypes(): JsonResponse
    {
        $types = MemberType::active()
            ->forScope('merchant')
            ->ordered()
            ->get();

        return ApiResponse::success($types);
    }
}
