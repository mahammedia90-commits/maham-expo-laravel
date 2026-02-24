<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sponsor;
use App\Support\ApiErrorCode;
use App\Support\ApiResponse;
use App\Support\SafeOrderBy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SponsorController extends Controller
{
    use SafeOrderBy;

    /**
     * List all sponsors with filters
     */
    public function index(Request $request): JsonResponse
    {
        $query = Sponsor::with(['event']);

        // Filters
        if ($eventId = $request->input('event_id')) {
            $query->forEvent($eventId);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($search = $this->sanitizeSearch($request->input('search'))) {
            $query->search($search);
        }

        // Safe sorting
        $this->applySafeOrder($query, $request, [
            'name', 'company_name', 'status', 'created_at',
        ], 'created_at', 'desc');

        $perPage = min($request->input('per_page', 15), 50);
        $sponsors = $query->paginate($perPage);

        return ApiResponse::paginated($sponsors);
    }

    /**
     * Create a new sponsor
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'event_id' => ['required', 'uuid', 'exists:events,id'],
            'user_id' => ['nullable', 'string', 'max:36'],
            'name' => ['required', 'string', 'max:255'],
            'name_ar' => ['required', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'company_name_ar' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'description_ar' => ['nullable', 'string', 'max:2000'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:20'],
            'website' => ['nullable', 'url', 'max:255'],
            'status' => ['sometimes', Rule::enum(\App\Enums\SponsorStatus::class)],
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store(
                config('expo-api.uploads.paths.sponsors', 'uploads/sponsors'),
                'public'
            );
        }

        $validated['created_by'] = $request->input('auth_user_id');
        $validated['created_from'] = $request->header('X-Platform', 'web');

        $sponsor = Sponsor::create($validated);
        $sponsor->load('event');

        return ApiResponse::created($sponsor, __('messages.sponsor.created'));
    }

    /**
     * Show sponsor details
     */
    public function show(Sponsor $sponsor): JsonResponse
    {
        $sponsor->load([
            'event',
            'contracts' => fn($q) => $q->latest()->limit(10),
            'contracts.sponsorPackage',
            'assets' => fn($q) => $q->ordered(),
        ]);

        return ApiResponse::success($sponsor);
    }

    /**
     * Update a sponsor
     */
    public function update(Request $request, Sponsor $sponsor): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => ['nullable', 'string', 'max:36'],
            'name' => ['sometimes', 'string', 'max:255'],
            'name_ar' => ['sometimes', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'company_name_ar' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'description_ar' => ['nullable', 'string', 'max:2000'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:20'],
            'website' => ['nullable', 'url', 'max:255'],
            'status' => ['sometimes', Rule::enum(\App\Enums\SponsorStatus::class)],
        ]);

        // Handle logo upload (replace old one)
        if ($request->hasFile('logo')) {
            if ($sponsor->logo) {
                Storage::disk('public')->delete($sponsor->logo);
            }
            $validated['logo'] = $request->file('logo')->store(
                config('expo-api.uploads.paths.sponsors', 'uploads/sponsors'),
                'public'
            );
        }

        $sponsor->update($validated);
        $sponsor->load('event');

        return ApiResponse::success($sponsor, __('messages.sponsor.updated'));
    }

    /**
     * Delete a sponsor
     */
    public function destroy(Sponsor $sponsor): JsonResponse
    {
        // Check for active contracts
        $hasActiveContracts = $sponsor->contracts()
            ->whereIn('status', ['pending', 'active'])
            ->exists();

        if ($hasActiveContracts) {
            return ApiResponse::error(
                __('messages.sponsor.has_active_contracts'),
                ApiErrorCode::VALIDATION_FAILED,
                422
            );
        }

        // Cleanup logo
        if ($sponsor->logo) {
            Storage::disk('public')->delete($sponsor->logo);
        }

        $sponsor->delete();

        return ApiResponse::success(null, __('messages.sponsor.deleted'));
    }

    /**
     * Approve a sponsor
     */
    public function approve(Sponsor $sponsor): JsonResponse
    {
        $sponsor->approve();
        return ApiResponse::success($sponsor, __('messages.sponsor.approved'));
    }

    /**
     * Activate a sponsor
     */
    public function activate(Sponsor $sponsor): JsonResponse
    {
        $sponsor->activate();
        return ApiResponse::success($sponsor, __('messages.sponsor.activated'));
    }

    /**
     * Suspend a sponsor
     */
    public function suspend(Sponsor $sponsor): JsonResponse
    {
        $sponsor->suspend();
        return ApiResponse::success($sponsor, __('messages.sponsor.suspended'));
    }
}
