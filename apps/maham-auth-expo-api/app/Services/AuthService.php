<?php

namespace App\Services;

use App\Models\User;
use App\Models\Service;
use App\Mail\PasswordResetMail; 
use App\Mail\EmailVerificationMail; 
use App\Models\RefreshToken; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;


class AuthService
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * تسجيل مستخدم جديد
     */
    public function register(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'] ?? null,
            'status' => 'active',
        ]);

        // إسناد دور المستخدم الافتراضي
        $user->assignRole($data['roles'] ?? ['user']);

        return $user;
    }

    /**
     * تسجيل الدخول
     */
    public function login(array $credentials, string $ip = null, ?Service $service = null): array
    {
        $identifier = $credentials['identifier'];
        $identifierType = $credentials['identifier_type'] ?? 'email';

        // البحث عن المستخدم بالإيميل أو رقم الجوال
        $user = User::where($identifierType, $identifier)->first();

        // التحقق من وجود المستخدم
        if (!$user) {
            return [
                'success' => false,
                'message' => 'بيانات الدخول غير صحيحة',
            ];
        }

        // التحقق من حالة المستخدم
        if (!$user->isActive()) {
            $this->auditService->log('login_blocked', $user, [
                'ip' => $ip,
                'reason' => 'Account not active',
            ]);

            return [
                'success' => false,
                'message' => $this->getStatusMessage($user->status),
            ];
        }

        // التحقق من كلمة المرور
        if (!Hash::check($credentials['password'], $user->password)) {
            $this->auditService->log('login_failed', $user, [
                'ip' => $ip,
                'reason' => 'Invalid password',
            ]);

            return [
                'success' => false,
                'message' => 'بيانات الدخول غير صحيحة',
            ];
        }

        // التحقق من أن المستخدم لديه صلاحية استخدام الخدمة
        if ($service && !$service->canUserAccess($user)) {
            $this->auditService->log('login_blocked', $user, [
                'ip' => $ip,
                'reason' => 'User role not allowed for this service',
                'service' => $service->name,
            ]);

            return [
                'success' => false,
                'message' => 'ليس لديك صلاحية للدخول إلى هذه الخدمة',
            ];
        }

        // توليد التوكن
        $token = $this->generateToken($user);

        // تحديث آخر تسجيل دخول
        $user->updateLastLogin($ip);

        // تسجيل الحدث
        $this->auditService->log('login', $user, [
            'ip' => $ip,
            'service' => $service?->name,
        ]);

        return [
            'success' => true,
            'data' => [
                'user' => $user->fullInfo,
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60,
            ],
        ];
    }

    /**
     * تسجيل الخروج
     */
    public function logout(): void
    {
        JWTAuth::invalidate(JWTAuth::getToken());
    }

    /**
     * توليد JWT Token
     */
    public function generateToken(User $user): string
    {
        return JWTAuth::fromUser($user);
    }

    /**
     * تجديد التوكن
     */
    public function refreshToken(): string
    {
        return JWTAuth::refresh(JWTAuth::getToken());
    }

    /**
     * التحقق من صلاحية التوكن
     */
    public function verifyToken(string $token): array
    {
        try {
            $payload = JWTAuth::setToken($token)->getPayload();
            $user = User::find($payload->get('sub'));

            if (!$user) {
                return [
                    'valid' => false,
                    'message' => 'المستخدم غير موجود',
                ];
            }

            if (!$user->isActive()) {
                return [
                    'valid' => false,
                    'message' => 'الحساب غير نشط',
                ];
            }

            return [
                'valid' => true,
                'user' => $user->fullInfo,
                'expires_at' => date('Y-m-d H:i:s', $payload->get('exp')),
            ];
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return [
                'valid' => false,
                'message' => 'انتهت صلاحية التوكن',
            ];
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return [
                'valid' => false,
                'message' => 'التوكن غير صالح',
            ];
        } catch (\Exception $e) {
            return [
                'valid' => false,
                'message' => 'خطأ في التحقق من التوكن',
            ];
        }
    }

    /**
     * رسالة حالة المستخدم
     */
    protected function getStatusMessage(string $status): string
    {
        return match ($status) {
            'inactive' => 'الحساب غير مفعل',
            'suspended' => 'الحساب موقوف',
            'pending' => 'الحساب في انتظار التفعيل',
            default => 'الحساب غير متاح',
        };
    }

    /**
     * إرسال رابط إعادة تعيين كلمة المرور
     */
    public function sendPasswordResetLink(string $email): array
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return [
                'success' => false,
                'message' => __('messages.auth.user_not_found'),
            ];
        }
 
        // توليد توكن إعادة التعيين
        $token = Str::random(64);

        // حذف التوكنات القديمة
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        // حفظ التوكن الجديد
        DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => Hash::make($token), 
            'created_at' => now(),
        ]);

        // تسجيل الحدث
        $this->auditService->log('password_reset_requested', $user, [
            'ip' => request()->ip(),
        ]); 


          $resetLink = config('app.url') .
        "/reset-password?token={$token}&email={$email}";
 
        // هنا يمكن إرسال البريد الإلكتروني
        Mail::to($email)->send(new PasswordResetMail($resetLink));

        return [ 
            'success' => true,
            'message' => __('messages.auth.reset_link_sent'),
            // في بيئة التطوير فقط نرجع التوكن لسهولة الاختبار، في الإنتاج لا نرجع أي معلومات حساسة
            'token' => config('app.debug') ? $resetLink : null,
        ]; 
    }

    /**
     * إعادة تعيين كلمة المرور
     */
    public function resetPassword(string $email, string $token, string $password): array
    {
        $record = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$record) {
            return [
                'success' => false,
                'message' => __('messages.auth.invalid_reset_token'),
            ];
        }

        // التحقق من التوكن
        if (!Hash::check($token, $record->token)) {
            return [
                'success' => false,
                'message' => __('messages.auth.invalid_reset_token'),
            ];
        }

        // التحقق من صلاحية التوكن (ساعة واحدة)
        if (now()->diffInMinutes($record->created_at) > 60) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            return [
                'success' => false,
                'message' => __('messages.auth.reset_token_expired'),
            ];
        }

        // تحديث كلمة المرور
        $user = User::where('email', $email)->first();
        $user->update([
            'password' => Hash::make($password),
        ]);

        // حذف التوكن
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        // تسجيل الحدث
        $this->auditService->log('password_reset', $user, [
            'ip' => request()->ip(),
        ]);

        return [
            'success' => true,
            'message' => __('messages.auth.password_reset_success'),
        ];
    }

    /**
     * تغيير كلمة المرور
     */
    public function changePassword(User $user, string $currentPassword, string $newPassword): array
    {
        // التحقق من كلمة المرور الحالية
        if (!Hash::check($currentPassword, $user->password)) {
            return [
                'success' => false,
                'message' => __('messages.auth.current_password_incorrect'),
            ];
        }

        // التحقق من أن كلمة المرور الجديدة مختلفة
        if (Hash::check($newPassword, $user->password)) {
            return [
                'success' => false,
                'message' => __('messages.auth.password_same_as_old'),
            ];
        }

        // تحديث كلمة المرور
        $user->update([
            'password' => Hash::make($newPassword),
        ]);

        // تسجيل الحدث
        $this->auditService->log('password_changed', $user, [
            'ip' => request()->ip(),
        ]);

        return [
            'success' => true,
            'message' => __('messages.auth.password_changed'),
        ];
    }

    /**
     * تحديث الملف الشخصي
     */
    public function updateProfile(User $user, array $data): array
    {
        $oldEmail = $user->email;

        $user->update($data);

        // إذا تم تغيير البريد الإلكتروني
        if (isset($data['email']) && $data['email'] !== $oldEmail) {
            $user->update(['email_verified_at' => null]);
            // هنا يمكن إرسال بريد التحقق
            // $user->sendEmailVerificationNotification();
        }

        // تسجيل الحدث
        $this->auditService->log('profile_updated', $user, [
            'ip' => request()->ip(),
            'changes' => array_keys($data),
        ]);

        return [
            'success' => true,
            'message' => __('messages.auth.profile_updated'),
            'user' => $user->fresh()->fullInfo,
        ];
    }

    /**
     * إرسال بريد التحقق
     */
    public function sendEmailVerification(User $user): array
    {
        if ($user->hasVerifiedEmail()) {
            return [
                'success' => false,
                'message' => __('messages.auth.email_already_verified'),
            ];
        }

        // توليد كود التحقق
        $code = random_int(100000, 999999); 

        // حفظ الكود في الكاش (صالح لمدة 15 دقيقة)
        cache()->put("email_verification_{$user->id}", $code, now()->addMinutes(15));

        // هنا يمكن إرسال البريد
        Mail::to($user->email)->send(new EmailVerificationMail($code));

        return [ 
            'success' => true,
            'message' => __('messages.auth.verification_code_sent'),
            'code' => config('app.debug') ? $code : null, // في بيئة التطوير فقط نرجع الكود لسهولة الاختبار، في الإنتاج لا نرجع أي معلومات حساسة
        ];
    }

    /**
     * التحقق من البريد الإلكتروني
     */
    public function verifyEmail(User $user, string $code): array
    {
        $cachedCode = cache()->get("email_verification_{$user->id}");

        if (!$cachedCode || $cachedCode != $code) {
            return [
                'success' => false,
                'message' => __('messages.auth.invalid_verification_code'),
            ];
        }

        $user->markEmailAsVerified();
        cache()->forget("email_verification_{$user->id}");

        $this->auditService->log('email_verified', $user, [
            'ip' => request()->ip(),
        ]);

        return [
            'success' => true,
            'message' => __('messages.auth.email_verified'),
        ];
    }
}
