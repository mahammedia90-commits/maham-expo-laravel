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
use Tymon\JWTAuth\Facades\JWTAuth;
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
     * This route is outside auth:api middleware so expired tokens
     * (within refresh_ttl window) can still be refreshed.
     */
   
    public function refresh(Request $request): JsonResponse
    {
        $token = $request->bearerToken();
        
        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'التوكن مطلوب',
                'error_code' => 'token_required',
            ], 401);
        }

        try {
            $newToken = JWTAuth::setToken($token)->refresh();

            return response()->json([
                'success' => true,
                'data' => [
                    'token' => $newToken,
                    'token_type' => 'bearer',
                    'expires_in' => config('jwt.ttl') * 60,
                ]
            ]);
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json([
                'success' => false,
                'message' => 'انتهت صلاحية التوكن ولا يمكن تجديده',
                'error_code' => 'token_expired',
            ], 401);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'التوكن غير صالح',
                'error_code' => 'token_invalid',
            ], 401);
        }
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

    /**
     * إرسال رمز التحقق من الهاتف عبر SMS أو WhatsApp
     */
    public function sendPhoneOtp(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required|string|max:20',
            'channel' => 'nullable|string|in:sms,whatsapp',
        ]);

        $result = $this->authService->sendPhoneOtp(
            auth()->user(),
            $request->phone,
            $request->input('channel', 'sms')
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
            'data' => [
                'channel' => $result['channel'] ?? 'sms',
            ],
        ]);
    }

    /**
     * التحقق من رمز الهاتف
     */
    public function verifyPhoneOtp(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required|string|max:20',
            'code' => 'required|string|min:4|max:8',
        ]);

        $result = $this->authService->verifyPhoneOtp(
            auth()->user(),
            $request->phone,
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

    /* ========================================
     * OTP Login / Register (Public)
     * ======================================== */

    /**
     * إرسال رمز التحقق لتسجيل الدخول عبر الجوال (بدون توثيق)
     */
    public function sendLoginOtp(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required|string|max:20',
            'user_type' => 'nullable|string|exists:roles,name',
            'channel' => 'nullable|string|in:sms,whatsapp',
        ]);

        $result = $this->authService->sendLoginOtp(
            $request->phone,
            $request->user_type,
            $request->input('channel', 'sms')
        );

        if (!$result['success']) {
            $statusCode = match ($result['error_code'] ?? null) {
                'user_type_mismatch' => 403,
                'account_inactive' => 403,
                default => 400,
            };

            return response()->json([
                'success' => false,
                'message' => $result['message'],
                'error_code' => $result['error_code'] ?? null,
            ], $statusCode);
        }

        $response = [
            'success' => true,
            'message' => $result['message'],
            'data' => [
                'is_new_user' => $result['is_new_user'] ?? false,
                'user_type' => $result['user_type'] ?? null,
            ],
        ];

        // In test mode, return OTP for easy testing
        if (isset($result['otp'])) {
            $response['data']['otp'] = $result['otp'];
        }

        return response()->json($response);
    }

    /**
     * التحقق من رمز الدخول عبر الجوال (بدون توثيق)
     */
    public function verifyLoginOtp(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required|string|max:20',
            'code' => 'required|string|min:4|max:8',
            'user_type' => 'nullable|string|exists:roles,name',
        ]);

        $result = $this->authService->verifyLoginOtp(
            $request->phone,
            $request->code,
            $request->user_type
        );

        if (!$result['success']) {
            $statusCode = match ($result['error_code'] ?? null) {
                'user_type_mismatch' => 403,
                default => 400,
            };

            return response()->json([
                'success' => false,
                'message' => $result['message'],
                'error_code' => $result['error_code'] ?? null,
            ], $statusCode);
        }

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'data' => $result['data'] ?? null,
            'is_new_user' => $result['is_new_user'] ?? false,
        ]);
    }

    /**
     * إكمال التسجيل بعد التحقق من OTP (للمستخدمين الجدد)
     */
    public function completeOtpRegistration(Request $request): JsonResponse
    {
        $request->validate([
            'registration_token' => 'required|string',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email',
            'business_name' => 'nullable|string|max:255',
            'business_type' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:255',
        ]);

        $result = $this->authService->completeOtpRegistration(
            $request->registration_token,
            $request->only(['name', 'email', 'business_name', 'business_type', 'region'])
        );

        if (!$result['success']) {
            $statusCode = match ($result['error_code'] ?? null) {
                'invalid_registration_token' => 400,
                'phone_already_registered' => 409,
                default => 400,
            };

            return response()->json([
                'success' => false,
                'message' => $result['message'],
                'error_code' => $result['error_code'] ?? null,
            ], $statusCode);
        }

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'data' => $result['data'],
        ], 201);
    }
}
