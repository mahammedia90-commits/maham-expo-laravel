<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ServiceController extends Controller
{
    /**
     * عرض قائمة الخدمات المسجلة
     */
   
    public function index(): JsonResponse
    {
        $services = Service::orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $services->map(function ($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'display_name' => $service->display_name,
                    'description' => $service->description,
                    'status' => $service->status,
                    'last_used_at' => $service->last_used_at?->toISOString(),
                    'created_at' => $service->created_at->toISOString(),
                ];
            }),
        ]);
    }

    /**
     * تسجيل خدمة جديدة
     */
   
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:services,name',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'allowed_ips' => 'nullable|array',
            'allowed_ips.*' => 'string',
            'allowed_permissions' => 'nullable|array',
            'allowed_permissions.*' => 'string',
            'webhook_url' => 'nullable|url',
            'roles' => 'nullable|array',
            'roles.*' => 'string|exists:roles,name',
        ]);

        $roles = $validated['roles'] ?? [];
        unset($validated['roles']);

        $service = Service::create($validated);

        // إضافة الأدوار إذا تم تحديدها
        if (!empty($roles)) {
            $service->assignRoles($roles);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الخدمة بنجاح',
            'data' => [
                'id' => $service->id,
                'name' => $service->name,
                'token' => $service->token,
                'secret' => $service->secret,
                'roles' => $service->roles->map(fn($role) => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'display_name' => $role->display_name,
                ]),
            ],
            'warning' => 'احفظ التوكن والسر الآن! لن يظهروا مرة أخرى.',
        ], 201);
    }

    /**
     * عرض خدمة محددة
     */

    public function show(Service $service): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $service->id,
                'name' => $service->name,
                'display_name' => $service->display_name,
                'description' => $service->description,
                'allowed_ips' => $service->allowed_ips,
                'allowed_permissions' => $service->allowed_permissions,
                'webhook_url' => $service->webhook_url,
                'status' => $service->status,
                'last_used_at' => $service->last_used_at?->toISOString(),
                'logs_count' => $service->logs()->count(),
                'roles' => $service->roles->map(fn($role) => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'display_name' => $role->display_name,
                ]),
            ]
        ]);
    }

    /**
     * تحديث خدمة
     */
   
    public function update(Request $request, Service $service): JsonResponse
    {
        $validated = $request->validate([
            'display_name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'allowed_ips' => 'nullable|array',
            'allowed_permissions' => 'nullable|array',
            'webhook_url' => 'nullable|url',
            'status' => 'in:active,inactive,suspended',
            'roles' => 'nullable|array',
            'roles.*' => 'string|exists:roles,name',
        ]);

        $roles = null;
        if (array_key_exists('roles', $validated)) {
            $roles = $validated['roles'] ?? [];
            unset($validated['roles']);
        }

        $service->update($validated);

        // تحديث الأدوار إذا تم تمريرها
        if ($roles !== null) {
            $service->syncRoles($roles);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الخدمة بنجاح',
            'data' => [
                'roles' => $service->fresh()->roles->map(fn($role) => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'display_name' => $role->display_name,
                ]),
            ],
        ]);
    }

    /**
     * إعادة توليد التوكن
     */
  
    public function regenerateToken(Service $service): JsonResponse
    {
        $newToken = $service->regenerateToken();

        return response()->json([
            'success' => true,
            'message' => 'تم تجديد التوكن بنجاح',
            'data' => [
                'token' => $newToken,
            ],
            'warning' => 'احفظ التوكن الآن! لن يظهر مرة أخرى.',
        ]);
    }

    /**
     * حذف خدمة
     */

    public function destroy(Service $service): JsonResponse
    {
        $service->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الخدمة بنجاح',
        ]);
    }

    /* ========================================
     * Service Roles Management
     * ======================================== */

    /**
     * عرض أدوار الخدمة
     */
    public function roles(Service $service): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $service->roles->map(fn($role) => [
                'id' => $role->id,
                'name' => $role->name,
                'display_name' => $role->display_name,
            ]),
        ]);
    }

    /**
     * إضافة أدوار للخدمة
     */
    public function assignRoles(Request $request, Service $service): JsonResponse
    {
        $validated = $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'string|exists:roles,name',
        ]);

        $service->assignRoles($validated['roles']);

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة الأدوار بنجاح',
            'data' => $service->fresh()->roles->map(fn($role) => [
                'id' => $role->id,
                'name' => $role->name,
                'display_name' => $role->display_name,
            ]),
        ]);
    }

    /**
     * إزالة أدوار من الخدمة
     */
    public function removeRoles(Request $request, Service $service): JsonResponse
    {
        $validated = $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'string|exists:roles,name',
        ]);

        $service->removeRoles($validated['roles']);

        return response()->json([
            'success' => true,
            'message' => 'تم إزالة الأدوار بنجاح',
            'data' => $service->fresh()->roles->map(fn($role) => [
                'id' => $role->id,
                'name' => $role->name,
                'display_name' => $role->display_name,
            ]),
        ]);
    }

    /**
     * مزامنة أدوار الخدمة (استبدال الكل)
     */
    public function syncRoles(Request $request, Service $service): JsonResponse
    {
        $validated = $request->validate([
            'roles' => 'present|array',
            'roles.*' => 'string|exists:roles,name',
        ]);

        $service->syncRoles($validated['roles']);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الأدوار بنجاح',
            'data' => $service->fresh()->roles->map(fn($role) => [
                'id' => $role->id,
                'name' => $role->name,
                'display_name' => $role->display_name,
            ]),
        ]);
    }

    /* ========================================
     * Service-to-Service Endpoints
     * هذه الـ endpoints للاستخدام من الخدمات الأخرى
     * ======================================== */

    /**
     * التحقق من التوكن (للخدمات)
     */
  
    public function verifyUserToken(Request $request): JsonResponse
    {
        $serviceToken = $request->header('X-Service-Token');

        // التحقق من config token أولاً (للتطوير)
        $configToken = config('auth-service.service_token');
        $isConfigToken = $serviceToken && $serviceToken === $configToken;
        $service = null;

        if (!$isConfigToken) {
            $service = Service::validateToken($serviceToken);

            if (!$service) {
                return response()->json([
                    'success' => false,
                    'message' => 'خدمة غير موثقة',
                ], 401);
            }

            // التحقق من IP
            if (!$service->isIpAllowed($request->ip())) {
                $service->logUsage('verify_token', [
                    'ip_address' => $request->ip(),
                    'success' => false,
                    'error' => 'IP not allowed',
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'IP غير مسموح',
                ], 403);
            }
        }

        $userToken = $request->input('token');

        try {
            $payload = auth()->setToken($userToken)->getPayload();
            $user = User::find($payload->get('sub'));

            if (!$user || !$user->isActive()) {
                throw new \Exception('User not found or inactive');
            }

            // Log only if using database service
            if ($service) {
                $service->logUsage('verify_token', [
                    'user_id' => $user->id,
                    'ip_address' => $request->ip(),
                    'success' => true,
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'valid' => true,
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'roles' => $user->roles->pluck('name'),
                    ],
                    'expires_at' => $payload->get('exp'),
                ]
            ]);
        } catch (\Exception $e) {
            // Log only if using database service
            if ($service) {
                $service->logUsage('verify_token', [
                    'ip_address' => $request->ip(),
                    'success' => false,
                    'error' => $e->getMessage(),
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'توكن غير صالح',
            ], 401);
        }
    }

    /**
     * فحص صلاحية مستخدم (للخدمات)
     */
   
    public function checkUserPermission(Request $request): JsonResponse
    {
        $serviceToken = $request->header('X-Service-Token');

        // التحقق من config token أولاً
        $configToken = config('auth-service.service_token');
        $isConfigToken = $serviceToken && $serviceToken === $configToken;
        $service = null;

        if (!$isConfigToken) {
            $service = Service::validateToken($serviceToken);

            if (!$service) {
                return response()->json([
                    'success' => false,
                    'message' => 'خدمة غير موثقة',
                ], 401);
            }
        }

        $request->validate([
            'user_id' => 'required|uuid',
            'permission' => 'required|string',
        ]);

        // التحقق من أن الخدمة مسموح لها بفحص هذه الصلاحية (فقط للخدمات المسجلة)
        if ($service && !$service->canCheckPermission($request->permission)) {
            return response()->json([
                'success' => false,
                'message' => 'الخدمة غير مصرح لها بفحص هذه الصلاحية',
            ], 403);
        }

        $user = User::find($request->user_id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'المستخدم غير موجود',
            ], 404);
        }

        $hasPermission = $user->hasPermissionTo($request->permission);

        if ($service) {
            $service->logUsage('check_permission', [
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'request' => [
                    'permission' => $request->permission,
                ],
                'response' => [
                    'has_permission' => $hasPermission,
                ],
                'success' => true,
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'has_permission' => $hasPermission,
                'user_id' => $user->id,
                'permission' => $request->permission,
            ]
        ]);
    }

    /**
     * الحصول على بيانات مستخدم (للخدمات)
     */
    
    public function getUserInfo(Request $request): JsonResponse
    {
        $serviceToken = $request->header('X-Service-Token');

        // التحقق من config token أولاً
        $configToken = config('auth-service.service_token');
        $isConfigToken = $serviceToken && $serviceToken === $configToken;
        $service = null;

        if (!$isConfigToken) {
            $service = Service::validateToken($serviceToken);

            if (!$service) {
                return response()->json([
                    'success' => false,
                    'message' => 'خدمة غير موثقة',
                ], 401);
            }
        }

        $request->validate([
            'user_id' => 'required|uuid',
        ]);

        $user = User::with('roles')->find($request->user_id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'المستخدم غير موجود',
            ], 404);
        }

        if ($service) {
            $service->logUsage('get_user', [
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'success' => true,
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'status' => $user->status,
                'roles' => $user->roles->pluck('name'),
                'permissions' => $user->getAllPermissions(),
            ]
        ]);
    }

    /**
     * Helper: التحقق من طلب الخدمة
     */
    protected function validateServiceRequest(Request $request): ?Service
    {
        $serviceToken = $request->header('X-Service-Token');

        if (!$serviceToken) {
            return null;
        }

        $service = Service::validateToken($serviceToken);

        if (!$service) {
            return null;
        }

        if (!$service->isIpAllowed($request->ip())) {
            return null;
        }

        return $service;
    }
}
