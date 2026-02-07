<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Services\AuditService;
use App\Support\ApiErrorCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class RoleController extends Controller
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * عرض قائمة الأدوار
     */
    
    public function index(Request $request): JsonResponse
    {
        $query = Role::with('permissions');

        // فلترة حسب النوع
        if ($request->input('system_only')) {
            $query->system();
        } elseif ($request->input('custom_only')) {
            $query->custom();
        }

        // الترتيب
        $query->orderBy('level', 'desc')->orderBy('name');

        $roles = $query->get();

        return response()->json([
            'success' => true,
            'data' => $roles->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'display_name' => $role->display_name,
                    'description' => $role->description,
                    'is_system' => $role->is_system,
                    'level' => $role->level,
                    'permissions' => $role->permissions->pluck('name'),
                    'users_count' => $role->users()->count(),
                    'created_at' => $role->created_at->toISOString(),
                ];
            }),
        ]);
    }

    /**
     * إنشاء دور جديد
     */
   
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'level' => 'integer|min:0|max:100',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'description' => $validated['description'] ?? null,
            'level' => $validated['level'] ?? 0,
            'is_system' => false,
        ]);

        if (!empty($validated['permissions'])) {
            $role->givePermissionTo($validated['permissions']);
        }

        $this->auditService->log('role_created', auth()->user(), [
            'new_values' => [
                'role' => $role->name,
                'permissions' => $validated['permissions'] ?? [],
            ],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء الدور بنجاح',
            'data' => [
                'id' => $role->id,
                'name' => $role->name,
                'display_name' => $role->display_name,
                'permissions' => $role->permissions->pluck('name'),
            ]
        ], 201);
    }

    /**
     * عرض دور محدد
     */
   
    public function show(Role $role): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $role->id,
                'name' => $role->name,
                'display_name' => $role->display_name,
                'description' => $role->description,
                'is_system' => $role->is_system,
                'level' => $role->level,
                'permissions' => $role->permissions->map(function ($permission) {
                    return [
                        'name' => $permission->name,
                        'display_name' => $permission->display_name,
                        'group' => $permission->group,
                    ];
                }),
                'users_count' => $role->users()->count(),
                'created_at' => $role->created_at->toISOString(),
                'updated_at' => $role->updated_at->toISOString(),
            ]
        ]);
    }

    /**
     * تحديث دور
     */
   
    public function update(Request $request, Role $role): JsonResponse
    {
        // منع تعديل أدوار النظام
        if ($role->is_system && !auth()->user()->hasRole('super-admin')) {
            return response()->json([
                'success' => false,
                'code' => ApiErrorCode::ROLE_SYSTEM_MODIFICATION_FORBIDDEN,
                'message' => 'لا يمكن تعديل أدوار النظام',
            ], 403);
        }

        $validated = $request->validate([
            'display_name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'level' => 'integer|min:0|max:100',
        ]);

        $oldValues = $role->only(['display_name', 'description', 'level']);
        $role->update($validated);

        $this->auditService->log('role_updated', auth()->user(), [
            'old_values' => $oldValues,
            'new_values' => $validated,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الدور بنجاح',
            'data' => [
                'id' => $role->id,
                'name' => $role->name,
                'display_name' => $role->display_name,
            ]
        ]);
    }

    /**
     * حذف دور
     */
  
    public function destroy(Role $role): JsonResponse
    {
        // منع حذف أدوار النظام
        if ($role->is_system) {
            return response()->json([
                'success' => false,
                'code' => ApiErrorCode::ROLE_SYSTEM_DELETION_FORBIDDEN,
                'message' => 'لا يمكن حذف أدوار النظام',
            ], 403);
        }

        // التحقق من عدم وجود مستخدمين
        if ($role->users()->count() > 0) {
            return response()->json([
                'success' => false,
                'code' => ApiErrorCode::ROLE_HAS_USERS,
                'message' => 'لا يمكن حذف دور مرتبط بمستخدمين',
            ], 422);
        }

        $this->auditService->log('role_deleted', auth()->user(), [
            'old_values' => ['role' => $role->name],
        ]);

        $role->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الدور بنجاح',
        ]);
    }

    /**
     * تحديث صلاحيات الدور
     */
    
    public function syncPermissions(Request $request, Role $role): JsonResponse
    {
        // منع تعديل صلاحيات super-admin
        if ($role->name === 'super-admin') {
            return response()->json([
                'success' => false,
                'code' => ApiErrorCode::ROLE_SUPER_ADMIN_PROTECTED,
                'message' => 'لا يمكن تعديل صلاحيات دور المدير العام',
            ], 403);
        }

        $validated = $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $oldPermissions = $role->permissions->pluck('name')->toArray();
        $role->syncPermissions($validated['permissions']);

        $this->auditService->log('role_permissions_updated', auth()->user(), [
            'role' => $role->name,
            'old_values' => ['permissions' => $oldPermissions],
            'new_values' => ['permissions' => $validated['permissions']],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث صلاحيات الدور بنجاح',
            'data' => [
                'role' => $role->name,
                'permissions' => $role->fresh()->permissions->pluck('name'),
            ]
        ]);
    }

    /**
     * إضافة صلاحيات للدور
     */
   
    public function addPermissions(Request $request, Role $role): JsonResponse
    {
        $validated = $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role->givePermissionTo($validated['permissions']);

        return response()->json([
            'success' => true,
            'message' => 'تمت إضافة الصلاحيات بنجاح',
            'data' => [
                'role' => $role->name,
                'permissions' => $role->fresh()->permissions->pluck('name'),
            ]
        ]);
    }

    /**
     * إزالة صلاحيات من الدور
     */
   
    public function removePermissions(Request $request, Role $role): JsonResponse
    {
        $validated = $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role->revokePermissionTo($validated['permissions']);

        return response()->json([
            'success' => true,
            'message' => 'تمت إزالة الصلاحيات بنجاح',
            'data' => [
                'role' => $role->name,
                'permissions' => $role->fresh()->permissions->pluck('name'),
            ]
        ]);
    }
}
