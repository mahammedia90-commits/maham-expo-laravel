<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class PermissionController extends Controller
{
    /**
     * عرض قائمة الصلاحيات
     */
   
    public function index(Request $request): JsonResponse
    {
        $query = Permission::query();

        // فلترة حسب المجموعة
        if ($group = $request->input('group')) {
            $query->byGroup($group);
        }

        // فلترة حسب النوع
        if ($request->input('system_only')) {
            $query->system();
        } elseif ($request->input('custom_only')) {
            $query->custom();
        }

        $permissions = $query->orderBy('group')->orderBy('name')->get();

        // تجميع الصلاحيات
        $grouped = $permissions->groupBy('group');

        return response()->json([
            'success' => true,
            'data' => [
                'permissions' => $permissions->map(function ($permission) {
                    return [
                        'id' => $permission->id,
                        'name' => $permission->name,
                        'display_name' => $permission->display_name,
                        'description' => $permission->description,
                        'group' => $permission->group,
                        'is_system' => $permission->is_system,
                    ];
                }),
                'grouped' => $grouped->map(function ($items) {
                    return $items->map(function ($permission) {
                        return [
                            'id' => $permission->id,
                            'name' => $permission->name,
                            'display_name' => $permission->display_name,
                        ];
                    });
                }),
                'groups' => $grouped->keys(),
            ]
        ]);
    }

    /**
     * إنشاء صلاحية جديدة
     */
   
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'group' => 'nullable|string|max:100',
        ]);

        $permission = Permission::create([
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'description' => $validated['description'] ?? null,
            'group' => $validated['group'] ?? Permission::extractGroup($validated['name']),
            'is_system' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء الصلاحية بنجاح',
            'data' => [
                'id' => $permission->id,
                'name' => $permission->name,
                'display_name' => $permission->display_name,
                'group' => $permission->group,
            ]
        ], 201);
    }

    /**
     * إنشاء صلاحيات لمورد (CRUD)
     */
   
    public function createForResource(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'resource' => 'required|string|max:100',
            'actions' => 'array',
            'actions.*' => 'string|max:50',
        ]);

        $actions = $validated['actions'] ?? ['view', 'create', 'update', 'delete'];
        $permissions = Permission::createForResource($validated['resource'], $actions);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء الصلاحيات بنجاح',
            'data' => collect($permissions)->map(function ($permission) {
                return [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'display_name' => $permission->display_name,
                ];
            }),
        ], 201);
    }

    /**
     * عرض صلاحية محددة
     */
   
    public function show(Permission $permission): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $permission->id,
                'name' => $permission->name,
                'display_name' => $permission->display_name,
                'description' => $permission->description,
                'group' => $permission->group,
                'is_system' => $permission->is_system,
                'roles' => $permission->roles->pluck('name'),
                'created_at' => $permission->created_at->toISOString(),
            ]
        ]);
    }

    /**
     * تحديث صلاحية
     */
    
    public function update(Request $request, Permission $permission): JsonResponse
    {
        // منع تعديل صلاحيات النظام
        if ($permission->is_system) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن تعديل صلاحيات النظام',
            ], 403);
        }

        $validated = $request->validate([
            'display_name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'group' => 'nullable|string|max:100',
        ]);

        $permission->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الصلاحية بنجاح',
            'data' => [
                'id' => $permission->id,
                'name' => $permission->name,
                'display_name' => $permission->display_name,
            ]
        ]);
    }

    /**
     * حذف صلاحية
     */
    
    public function destroy(Permission $permission): JsonResponse
    {
        // منع حذف صلاحيات النظام
        if ($permission->is_system) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن حذف صلاحيات النظام',
            ], 403);
        }

        $permission->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الصلاحية بنجاح',
        ]);
    }
}
