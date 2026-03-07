<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use OpenApi\Attributes as OA;

class UserController extends Controller
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * عرض قائمة المستخدمين
     */
   
    public function index(Request $request): JsonResponse
    {
        $query = User::with('roles');

        // البحث
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // فلترة بالحالة
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // فلترة بالدور
        if ($role = $request->input('role')) {
            $query->role($role);
        }

        // الترتيب
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // الصفحات
        $perPage = min($request->input('per_page', 15), 100);
        $users = $query->paginate($perPage);

        return response()->json([ 
            'success' => true,
            'data' => UserResource::collection($users),
            'pagination' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ]
        ]);
    }

    /**
     * إنشاء مستخدم جديد
     */
  
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', Password::defaults()],
            'phone' => 'nullable|string|max:20',
            'status' => 'in:active,inactive,pending',
            'roles' => 'array',
            'roles.*' => 'exists:roles,name',
            'metadata' => 'nullable|array',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'status' => $validated['status'] ?? 'active',
            'metadata' => $validated['metadata'] ?? null,
        ]);

        if (!empty($validated['roles'])) {
            $user->assignRole($validated['roles']);
        }

        $this->auditService->log('user_created', auth()->user(), [
            'target_user_id' => $user->id,
            'new_values' => $user->only(['name', 'email', 'status']),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء المستخدم بنجاح',
            'data' => new UserResource($user->load('roles')),
        ], 201);
    }

    /**
     * عرض مستخدم محدد
     */
   
    public function show(User $user): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new UserResource($user->load('roles')),
        ]);
    }

    /**
     * تحديث مستخدم
     */
   
    public function update(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => ['sometimes', Password::defaults()],
            'phone' => 'nullable|string|max:20',
            'status' => 'in:active,inactive,suspended,pending',
            'metadata' => 'nullable|array',
        ]);

        $oldValues = $user->only(['name', 'email', 'status']);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        $this->auditService->log('user_updated', auth()->user(), [
            'target_user_id' => $user->id,
            'old_values' => $oldValues,
            'new_values' => $user->only(['name', 'email', 'status']),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث المستخدم بنجاح',
            'data' => new UserResource($user->load('roles')),
        ]);
    }

    /**
     * حذف مستخدم
     */
    
    public function destroy(User $user): JsonResponse
    {
        // منع حذف المستخدم الحالي
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكنك حذف حسابك',
            ], 403);
        }

        $this->auditService->log('user_deleted', auth()->user(), [
            'target_user_id' => $user->id,
            'old_values' => $user->only(['name', 'email']),
        ]);

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف المستخدم بنجاح',
        ]);
    }

    /**
     * إسناد أدوار للمستخدم
     */
   
    public function assignRoles(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name',
        ]);

        $oldRoles = $user->roles->pluck('name')->toArray();
        $user->syncRoles($validated['roles']);

        $this->auditService->log('roles_assigned', auth()->user(), [
            'target_user_id' => $user->id,
            'old_values' => ['roles' => $oldRoles],
            'new_values' => ['roles' => $validated['roles']],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث أدوار المستخدم بنجاح',
            'data' => [
                'user_id' => $user->id,
                'roles' => $user->fresh()->roles->pluck('name'),
            ]
        ]);
    }

    /**
     * إسناد صلاحيات مباشرة للمستخدم
     */

    public function assignPermissions(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $oldPermissions = $user->directPermissions->pluck('name')->toArray();

        // مسح الصلاحيات القديمة وإضافة الجديدة
        $user->directPermissions()->detach();
        $user->givePermissionTo($validated['permissions']);

        $this->auditService->log('permissions_assigned', auth()->user(), [
            'target_user_id' => $user->id,
            'old_values' => ['permissions' => $oldPermissions],
            'new_values' => ['permissions' => $validated['permissions']],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث صلاحيات المستخدم بنجاح',
            'data' => [
                'user_id' => $user->id,
                'direct_permissions' => $validated['permissions'],
                'all_permissions' => $user->getAllPermissions(),
            ]
        ]);
    }

    /**
     * الحصول على صلاحيات المستخدم
     */
    
    public function permissions(User $user): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'user_id' => $user->id,
                'roles' => $user->roles->pluck('name'),
                'direct_permissions' => $user->directPermissions->pluck('name'),
                'all_permissions' => $user->getAllPermissions(),
                'permissions_grouped' => $user->getPermissionsGrouped(),
            ]
        ]);
    }
}
