<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\User;
use App\Services\AuthService;
use App\Services\AuditService;
use App\Support\ApiErrorCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService,
        protected AuditService $auditService
    ) {}

    /**
     * تسجيل مستخدم جديد
     */

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->authService->register($request->validated());
        $token = $this->authService->generateToken($user);

        $this->auditService->log('register', $user, [
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم التسجيل بنجاح',
            'data' => [
                'user' => $user->fullInfo,
                'token' => $token,
            ]
        ], 201);
    }

    /**
     * تسجيل الدخول
     */

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = [
            'identifier' => $request->input('identifier'),
            'identifier_type' => $request->getIdentifierType(),
            'password' => $request->input('password'),
        ];

        $result = $this->authService->login($credentials, $request->ip());

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'code' => ApiErrorCode::INVALID_LOGIN_CREDENTIALS,
                'message' => $result['message'],
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الدخول بنجاح',
            'data' => $result['data'],
        ]);
    }

    /**
     * تسجيل الخروج
     */
   
    public function logout(Request $request): JsonResponse
    {
        $user = auth()->user();

        $this->authService->logout();

        $this->auditService->log('logout', $user, [
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الخروج بنجاح',
        ]);
    }

    /**
     * تجديد التوكن
     */
   
    public function refresh(): JsonResponse
    {
        $token = $this->authService->refreshToken();

        return response()->json([
            'success' => true,
            'data' => [
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60,
            ]
        ]);
    }

    /**
     * بيانات المستخدم الحالي
     */
  
    public function me(): JsonResponse
    {
        return response()->json([
        'success' => true,
        'data' => auth()->user()->fullInfo,
    ]);
    }

    /**
     * التحقق من التوكن (للخدمات الأخرى)
     */
   
    public function verifyToken(Request $request): JsonResponse
    {
        $token = $request->input('token') ?? $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'التوكن مطلوب',
            ], 400);
        }

        $result = $this->authService->verifyToken($token);

        if (!$result['valid']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 401);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'valid' => true,
                'user' => $result['user'],
                'expires_at' => $result['expires_at'],
            ]
        ]);
    }

    /**
     * التحقق من صلاحية معينة
     */
  
    public function checkPermission(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|uuid|exists:users,id',
            'permission' => 'required|string',
        ]);

        $user = User::find($request->user_id);
        $hasPermission = $user->hasPermissionTo($request->permission);

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
     * التحقق من عدة صلاحيات
     */
    
    public function checkPermissions(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|uuid|exists:users,id',
            'permissions' => 'required|array',
            'permissions.*' => 'string',
            'require_all' => 'boolean',
        ]);

        $user = User::find($request->user_id);
        $requireAll = $request->input('require_all', false);

        $result = [];
        foreach ($request->permissions as $permission) {
            $result[$permission] = $user->hasPermissionTo($permission);
        }

        $hasAccess = $requireAll
            ? !in_array(false, $result, true)
            : in_array(true, $result, true);

        return response()->json([
            'success' => true,
            'data' => [
                'has_access' => $hasAccess,
                'user_id' => $user->id,
                'permissions' => $result,
                'require_all' => $requireAll,
            ]
        ]);
    }

    /**
     * طلب إعادة تعيين كلمة المرور 
     */
    
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $result = $this->authService->sendPasswordResetLink($request->email);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 404);
        }

        $response = [
            'success' => true,
            'message' => $result['message'],
        ];
 
        // في بيئة التطوير نرجع التوكن للاختبار 
        if (isset($result['token'])) {
            $response['data'] = ['token' => $result['token']];
        }

        return response()->json($response);
    }

    /**
     * إعادة تعيين كلمة المرور
     */
    
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $result = $this->authService->resetPassword(
            $request->email,
            $request->token,
            $request->password
        );

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => $result['message'],
        ]);
    }

    /**
     * تغيير كلمة المرور
     */
    
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $result = $this->authService->changePassword(
            auth()->user(),
            $request->current_password,
            $request->password
        );

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => $result['message'],
        ]);
    }

    /**
     * تحديث الملف الشخصي
     */
    
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $result = $this->authService->updateProfile(
            auth()->user(),
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'data' => ['user' => $result['user']],
        ]);
    }

    /**
     * إرسال كود التحقق من البريد
     */
  
    public function sendEmailVerification(): JsonResponse
    {
        $result = $this->authService->sendEmailVerification(auth()->user());

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 400);
        }

        $response = [
            'success' => true,
            'message' => $result['message'],
        ]; 

        // في بيئة التطوير نرجع الكود للاختبار
        if (isset($result['code'])) {
            $response['data'] = ['code' => $result['code']];
        }

        return response()->json($response);
    }

    /**
     * التحقق من البريد الإلكتروني
     */
   
    public function verifyEmail(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $result = $this->authService->verifyEmail(
            auth()->user(),
            $request->code
        );

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => $result['message'],
        ]);
    }
}
