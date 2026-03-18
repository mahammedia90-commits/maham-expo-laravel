<?php

namespace App\Services;

use App\Models\User;
use App\Mail\PasswordResetMail;
use App\Mail\EmailVerificationMail;
use App\Models\RefreshToken;
use App\Models\Role;
use App\Services\OtpProviderManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Tymon\JWTAuth\Facades\JWTAuth;


class AuthService
{
    public function __construct(
        protected AuditService $auditService,
        protected OtpProviderManager $otpProvider
    ) {}

    /**
     * تسجيل مستخدم جديد
     */
    public function register(array $data): User
    {
        $password = $data['password'] ?? $data['phone'];

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'password' => Hash::make($password),
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
    public function login(array $credentials, string $ip = null): array
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

        // توليد التوكن
        $token = $this->generateToken($user);

        // تحديث آخر تسجيل دخول
        $user->updateLastLogin($ip);

        // تسجيل الحدث
        $this->auditService->log('login', $user, [
            'ip' => $ip,
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

    /**
     * Send phone OTP via SMS or WhatsApp
     */
    public function sendPhoneOtp(User $user, string $phone, string $channel = 'sms'): array
    {
        // Normalize phone to E.164 format
        $phone = $this->normalizePhone($phone);

        $result = $this->otpProvider->sendOtp($phone, $channel);

        if ($result['success']) {
            $this->auditService->log('phone_otp_sent', $user, [
                'ip' => request()->ip(),
                'channel' => $channel,
                'provider' => $this->otpProvider->getActiveProviderName(),
            ]);
        }

        return $result;
    }

    /**
     * Verify phone OTP code
     */
    public function verifyPhoneOtp(User $user, string $phone, string $code): array
    {
        $phone = $this->normalizePhone($phone);

        $result = $this->otpProvider->verifyOtp($phone, $code);

        if ($result['success'] && $result['valid']) {
            // Mark phone as verified
            $user->update([
                'phone' => $phone,
                'phone_verified_at' => now(),
            ]);

            $this->auditService->log('phone_verified', $user, [
                'ip' => request()->ip(),
            ]);

            return [
                'success' => true,
                'message' => 'تم التحقق من رقم الهاتف بنجاح',
            ];
        }

        return $result;
    }

    /**
     * Normalize phone number to E.164 format
     */
    protected function normalizePhone(string $phone): string
    {
        // Remove spaces and dashes
        $phone = preg_replace('/[\s\-]/', '', $phone);

        // If starts with 05 (Saudi local), convert to +966
        if (preg_match('/^05\d{8}$/', $phone)) {
            $phone = '+966' . substr($phone, 1);
        }

        // If starts with 966 without +, add +
        if (preg_match('/^966\d{9}$/', $phone)) {
            $phone = '+' . $phone;
        }

        // If doesn't start with +, add +
        if (!str_starts_with($phone, '+')) {
            $phone = '+' . $phone;
        }

        return $phone;
    }

    /**
     * Send OTP for login/register (public — no auth required)
     * user_type اختياري — إذا المستخدم موجود يُكتشف تلقائياً
     */
    public function sendLoginOtp(string $phone, ?string $userType = null, string $channel = 'sms'): array
    {
        $phone = $this->normalizePhone($phone);

        // Check if user exists with this phone
        $user = User::where('phone', $phone)->first();

        if ($user) {
            // المستخدم موجود — اكتشف نوعه تلقائياً إذا ما تم تحديده
            if (!$userType) {
                $userType = $user->roles->first()?->name ?? 'user';
            }

            if (!$user->hasRole($userType)) {
                return [
                    'success' => false,
                    'message' => 'رقم الجوال غير مسجل بهذا النوع من الحسابات',
                    'error_code' => 'user_type_mismatch',
                ];
            }

            if (!$user->isActive()) {
                return [
                    'success' => false,
                    'message' => $this->getStatusMessage($user->status),
                    'error_code' => 'account_inactive',
                ];
            }
        } else {
            // مستخدم جديد — يجب تحديد النوع
            if (!$userType) {
                $userType = 'user'; // افتراضي
            }
        }

        // Check if in test mode
        $isTestMode = config('otp.test_mode', config('twilio.test_mode', false));

        if ($isTestMode) {
            Cache::put("login_otp_{$phone}", ['type' => $userType, 'mode' => 'test'], now()->addMinutes(10));

            return [
                'success' => true,
                'message' => 'تم إرسال رمز التحقق (وضع الاختبار)',
                'is_new_user' => !$user,
                'user_type' => $userType,
                'test_mode' => true,
            ];
        }

        // Production: send via active OTP provider
        $result = $this->otpProvider->sendOtp($phone, $channel);

        if ($result['success']) {
            // حفظ نوع المستخدم مع الـ OTP request
            Cache::put("login_otp_type_{$phone}", $userType, now()->addMinutes(10));
            $result['is_new_user'] = !$user;
            $result['user_type'] = $userType;
        }

        return $result;
    }

    /**
     * Verify OTP for login/register (public — no auth required)
     * user_type اختياري — يُكتشف تلقائياً إذا المستخدم موجود
     */
    public function verifyLoginOtp(string $phone, string $code, ?string $userType = null): array
    {
        $phone = $this->normalizePhone($phone);

        // Check test mode
        $isTestMode = config('otp.test_mode', config('twilio.test_mode', false));

        if ($isTestMode) {
            $cached = Cache::get("login_otp_{$phone}");
            if (!$cached) {
                return [
                    'success' => false,
                    'message' => 'لم يتم طلب رمز تحقق لهذا الرقم',
                    'valid' => false,
                ];
            }
            // استرجاع نوع المستخدم من الـ cache إذا ما تم تحديده
            if (!$userType && is_array($cached)) {
                $userType = $cached['type'] ?? null;
            }
            Cache::forget("login_otp_{$phone}");
        } else {
            // Production: verify via active OTP provider
            $result = $this->otpProvider->verifyOtp($phone, $code);
            if (!$result['success'] || !($result['valid'] ?? false)) {
                return $result;
            }
            // استرجاع نوع المستخدم المحفوظ أثناء الإرسال
            if (!$userType) {
                $userType = Cache::get("login_otp_type_{$phone}");
            }
            Cache::forget("login_otp_type_{$phone}");
        }

        // OTP is valid — check if user exists
        $user = User::where('phone', $phone)->first();

        if ($user) {
            // اكتشاف نوع المستخدم تلقائياً إذا ما تم تحديده
            if (!$userType) {
                $userType = $user->roles->first()?->name ?? 'user';
            }

            // Verify user type matches
            if (!$user->hasRole($userType)) {
                return [
                    'success' => false,
                    'message' => 'رقم الجوال غير مسجل بهذا النوع من الحسابات',
                    'error_code' => 'user_type_mismatch',
                    'valid' => false,
                ];
            }

            // Existing user — generate token and login
            $token = $this->generateToken($user);
            $user->updateLastLogin(request()->ip());
            $user->update(['phone_verified_at' => now()]);

            $this->auditService->log('login_otp', $user, [
                'ip' => request()->ip(),
                'provider' => $this->otpProvider->getActiveProviderName(),
            ]);

            return [
                'success' => true,
                'message' => 'تم تسجيل الدخول بنجاح',
                'valid' => true,
                'is_new_user' => false,
                'data' => [
                    'user' => $user->fresh()->fullInfo,
                    'token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => config('jwt.ttl') * 60,
                ],
            ];
        }

        // New user — generate temporary registration token
        if (!$userType) {
            $userType = 'user';
        }

        $registrationToken = Str::random(64);
        Cache::put("otp_registration_{$registrationToken}", [
            'phone' => $phone,
            'user_type' => $userType,
            'verified_at' => now()->toISOString(),
        ], now()->addMinutes(30));

        return [
            'success' => true,
            'message' => 'تم التحقق — أكمل بياناتك للتسجيل',
            'valid' => true,
            'is_new_user' => true,
            'data' => [
                'registration_token' => $registrationToken,
            ],
        ];
    }

    /**
     * Complete registration after OTP verification (for new users)
     */
    public function completeOtpRegistration(string $registrationToken, array $data): array
    {
        // Validate registration token
        $cached = Cache::get("otp_registration_{$registrationToken}");

        if (!$cached) {
            return [
                'success' => false,
                'message' => 'رمز التسجيل غير صالح أو منتهي الصلاحية',
                'error_code' => 'invalid_registration_token',
            ];
        }

        $phone = $cached['phone'];
        // user_type من الطلب (أولوية) أو من الكاش
        $userType = $data['user_type'] ?? $cached['user_type'] ?? 'user';

        // Check if phone was already registered (race condition)
        if (User::where('phone', $phone)->exists()) {
            Cache::forget("otp_registration_{$registrationToken}");
            return [
                'success' => false,
                'message' => 'رقم الجوال مسجل مسبقاً',
                'error_code' => 'phone_already_registered',
            ];
        }

        // Create user (email optional, password defaults to phone number)
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $phone,
            'password' => Hash::make($phone), // كلمة المرور = رقم الجوال
            'phone_verified_at' => now(),
            'status' => 'active',
            'metadata' => [
                'business_name' => $data['business_name'] ?? null,
                'business_type' => $data['business_type'] ?? null,
                'region' => $data['region'] ?? null,
            ],
        ]);

        // Assign role
        $user->assignRole([$userType]);

        // Generate token
        $token = $this->generateToken($user);
        $user->updateLastLogin(request()->ip());

        // Cleanup
        Cache::forget("otp_registration_{$registrationToken}");

        $this->auditService->log('register_otp', $user, [
            'ip' => request()->ip(),
            'user_type' => $userType,
        ]);

        return [
            'success' => true,
            'message' => 'تم التسجيل بنجاح',
            'data' => [
                'user' => $user->fresh()->fullInfo,
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60,
            ],
        ];
    }
}
