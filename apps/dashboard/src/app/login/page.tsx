'use client';

import { useState, useEffect, useRef, FormEvent } from 'react';
import { useRouter } from 'next/navigation';
import { useAuthStore, User } from '@/stores/auth';
import { authApi, ApiResponse } from '@/lib/api';
import {
  Phone,
  Loader2,
  Sparkles,
  ArrowRight,
  ArrowLeft,
  ShieldCheck,
  UserPlus,
  CheckCircle2,
  ChevronDown,
} from 'lucide-react';

/* ─────────────────── translations ─────────────────── */
const t = {
  ar: {
    platformName: 'Maham Expo',
    platformTagline: 'مرحباً بك في مهام إكسبو',
    loginSubtitle: 'سجّل برقم جوالك للبدء',
    phoneLabel: 'رقم الجوال',
    phonePlaceholder: '05xxxxxxxx',
    sendOtp: 'إرسال رمز التحقق',
    otpLabel: 'رمز التحقق',
    otpPlaceholder: '1234',
    verifyOtp: 'تحقق',
    resendOtp: 'إعادة إرسال',
    resendIn: 'إعادة الإرسال بعد',
    seconds: 'ث',
    step3Title: 'خطوة أخيرة — عرّفنا بنشاطك',
    step3Subtitle: '4 حقول فقط وستكون جاهزاً',
    nameLabel: 'اسم التاجر',
    namePlaceholder: 'مثال: أحمد محمد',
    businessNameLabel: 'اسم المؤسسة أو الشركة',
    businessNamePlaceholder: 'مثال: مؤسسة النجاح التجارية',
    businessTypeLabel: 'نوع النشاط',
    businessTypePlaceholder: 'اختر نوع النشاط',
    regionLabel: 'المنطقة',
    regionPlaceholder: 'اختر المنطقة',
    completeRegistration: 'بدء الاستخدام',
    stepPhone: 'رقم الجوال',
    stepOtp: 'رمز التحقق',
    stepProfile: 'بياناتك',
    copyright: '\u00A9 2025 معرض مهام. جميع الحقوق محفوظة.',
  },
  en: {
    platformName: 'Maham Expo',
    platformTagline: 'Welcome to Maham Expo',
    loginSubtitle: 'Sign in with your phone number to start',
    phoneLabel: 'Phone Number',
    phonePlaceholder: '05xxxxxxxx',
    sendOtp: 'Send OTP',
    otpLabel: 'Verification Code',
    otpPlaceholder: '1234',
    verifyOtp: 'Verify',
    resendOtp: 'Resend',
    resendIn: 'Resend in',
    seconds: 's',
    step3Title: 'Last step — tell us about your business',
    step3Subtitle: 'Just 4 fields and you are ready',
    nameLabel: 'Your Name',
    namePlaceholder: 'e.g. Ahmed Mohammed',
    businessNameLabel: 'Business / Company Name',
    businessNamePlaceholder: 'e.g. Success Trading Est.',
    businessTypeLabel: 'Business Type',
    businessTypePlaceholder: 'Select business type',
    regionLabel: 'Region',
    regionPlaceholder: 'Select region',
    completeRegistration: 'Get Started',
    stepPhone: 'Phone',
    stepOtp: 'OTP',
    stepProfile: 'Profile',
    copyright: '\u00A9 2025 Maham Expo. All rights reserved.',
  },
};

const businessTypes = [
  { ar: 'تجارة عامة', en: 'General Trading' },
  { ar: 'مواد غذائية', en: 'Food & Beverages' },
  { ar: 'ملابس وأزياء', en: 'Clothing & Fashion' },
  { ar: 'إلكترونيات', en: 'Electronics' },
  { ar: 'عطور ومستحضرات', en: 'Perfumes & Cosmetics' },
  { ar: 'حرف يدوية', en: 'Handicrafts' },
  { ar: 'خدمات', en: 'Services' },
  { ar: 'أخرى', en: 'Other' },
];

const regions = [
  { ar: 'الرياض', en: 'Riyadh' },
  { ar: 'مكة المكرمة', en: 'Makkah' },
  { ar: 'المدينة المنورة', en: 'Madinah' },
  { ar: 'المنطقة الشرقية', en: 'Eastern Province' },
  { ar: 'القصيم', en: 'Qassim' },
  { ar: 'عسير', en: 'Asir' },
  { ar: 'تبوك', en: 'Tabuk' },
  { ar: 'حائل', en: 'Hail' },
  { ar: 'الحدود الشمالية', en: 'Northern Borders' },
  { ar: 'جازان', en: 'Jazan' },
  { ar: 'نجران', en: 'Najran' },
  { ar: 'الباحة', en: 'Al Baha' },
  { ar: 'الجوف', en: 'Al Jawf' },
];

/* ─────────────────── component ─────────────────── */
export default function LoginPage() {
  const router = useRouter();
  const { setAuth, isAuthenticated } = useAuthStore();

  const [locale, setLocale] = useState('ar');
  const [mounted, setMounted] = useState(false);

  // Step management: 1=phone, 2=otp, 3=registration
  const [step, setStep] = useState(1);

  // Step 1
  const [phone, setPhone] = useState('');

  // Step 2
  const [otp, setOtp] = useState(['', '', '', '']);
  const [isNewUser, setIsNewUser] = useState(false);
  const [registrationToken, setRegistrationToken] = useState('');
  const [countdown, setCountdown] = useState(0);
  const otpRefs = useRef<(HTMLInputElement | null)[]>([]);

  // Step 3
  const [name, setName] = useState('');
  const [businessName, setBusinessName] = useState('');
  const [businessType, setBusinessType] = useState('');
  const [region, setRegion] = useState('');

  // Shared
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');

  const tr = t[locale as keyof typeof t] || t.ar;
  const isRtl = locale === 'ar';
  const ArrowIcon = isRtl ? ArrowLeft : ArrowRight;

  useEffect(() => {
    setMounted(true);
    const savedLocale = localStorage.getItem('locale') || 'ar';
    setLocale(savedLocale);
    document.documentElement.dir = savedLocale === 'ar' ? 'rtl' : 'ltr';
    document.documentElement.lang = savedLocale;
  }, []);

  useEffect(() => {
    if (isAuthenticated) router.replace('/dashboard');
  }, [isAuthenticated, router]);

  // Countdown timer for resend
  useEffect(() => {
    if (countdown <= 0) return;
    const timer = setTimeout(() => setCountdown(countdown - 1), 1000);
    return () => clearTimeout(timer);
  }, [countdown]);

  /* ──── Step 1: Send OTP ──── */
  const handleSendOtp = async (e: FormEvent) => {
    e.preventDefault();
    setError('');
    setLoading(true);
    try {
      const res = await authApi.post<ApiResponse<{ is_new_user: boolean; otp?: string }>>('/auth/otp/send', {
        phone,
        user_type: 'merchant',
        channel: 'sms',
      });
      setIsNewUser(res.data.data?.is_new_user ?? false);
      setStep(2);
      setCountdown(60);
      setOtp(['', '', '', '']);
      setTimeout(() => otpRefs.current[0]?.focus(), 100);
    } catch (err: unknown) {
      const e = err as { response?: { data?: { message?: string } } };
      setError(e.response?.data?.message || 'حدث خطأ');
    } finally {
      setLoading(false);
    }
  };

  /* ──── Step 2: Verify OTP ──── */
  const handleVerifyOtp = async (code?: string) => {
    const otpCode = code || otp.join('');
    if (otpCode.length < 4) return;
    setError('');
    setLoading(true);
    try {
      const res = await authApi.post<
        ApiResponse<{ user?: User; token?: string; registration_token?: string }> & { is_new_user?: boolean }
      >('/auth/otp/verify', {
        phone,
        code: otpCode,
        user_type: 'merchant',
      });

      if (res.data.is_new_user) {
        setRegistrationToken(res.data.data?.registration_token || '');
        setIsNewUser(true);
        setStep(3);
      } else {
        const { user, token } = res.data.data as { user: User; token: string };
        setAuth(user, token);
        router.push('/dashboard');
      }
    } catch (err: unknown) {
      const e = err as { response?: { data?: { message?: string } } };
      setError(e.response?.data?.message || 'رمز التحقق غير صحيح');
      setOtp(['', '', '', '']);
      otpRefs.current[0]?.focus();
    } finally {
      setLoading(false);
    }
  };

  /* ──── Step 3: Complete Registration ──── */
  const handleCompleteRegistration = async (e: FormEvent) => {
    e.preventDefault();
    setError('');
    setLoading(true);
    try {
      const res = await authApi.post<ApiResponse<{ user: User; token: string }>>('/auth/otp/complete-registration', {
        registration_token: registrationToken,
        name,
        business_name: businessName,
        business_type: businessType,
        region,
      });
      const { user, token } = res.data.data;
      setAuth(user, token);
      router.push('/dashboard');
    } catch (err: unknown) {
      const e = err as { response?: { data?: { message?: string } } };
      setError(e.response?.data?.message || 'حدث خطأ');
    } finally {
      setLoading(false);
    }
  };

  /* ──── Resend OTP ──── */
  const handleResendOtp = async () => {
    if (countdown > 0) return;
    setError('');
    setLoading(true);
    try {
      await authApi.post('/auth/otp/send', { phone, user_type: 'merchant', channel: 'sms' });
      setCountdown(60);
      setOtp(['', '', '', '']);
      otpRefs.current[0]?.focus();
    } catch (err: unknown) {
      const e = err as { response?: { data?: { message?: string } } };
      setError(e.response?.data?.message || 'حدث خطأ');
    } finally {
      setLoading(false);
    }
  };

  /* ──── OTP Input Handlers ──── */
  const handleOtpChange = (index: number, value: string) => {
    if (!/^\d*$/.test(value)) return;
    const newOtp = [...otp];
    newOtp[index] = value.slice(-1);
    setOtp(newOtp);

    if (value && index < 3) {
      otpRefs.current[index + 1]?.focus();
    }

    // Auto-submit when all 4 digits entered
    if (value && index === 3) {
      const fullCode = newOtp.join('');
      if (fullCode.length === 4) {
        handleVerifyOtp(fullCode);
      }
    }
  };

  const handleOtpKeyDown = (index: number, e: React.KeyboardEvent) => {
    if (e.key === 'Backspace' && !otp[index] && index > 0) {
      otpRefs.current[index - 1]?.focus();
    }
  };

  const handleOtpPaste = (e: React.ClipboardEvent) => {
    e.preventDefault();
    const pasted = e.clipboardData.getData('text').replace(/\D/g, '').slice(0, 4);
    if (pasted.length === 4) {
      const newOtp = pasted.split('');
      setOtp(newOtp);
      otpRefs.current[3]?.focus();
      handleVerifyOtp(pasted);
    }
  };

  /* ──── Shared styles ──── */
  const inputClass =
    'w-full py-3.5 rounded-2xl border border-white/30 dark:border-white/10 bg-white/50 dark:bg-white/5 backdrop-blur-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500/40 transition-all text-sm';

  const selectClass =
    'w-full py-3.5 px-4 rounded-2xl border border-white/30 dark:border-white/10 bg-white/50 dark:bg-white/5 backdrop-blur-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500/40 transition-all text-sm appearance-none cursor-pointer';

  const btnClass =
    'relative w-full py-3.5 rounded-2xl text-white font-semibold text-sm bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40 transition-all duration-300 disabled:opacity-60 disabled:cursor-not-allowed flex items-center justify-center gap-2 group overflow-hidden';

  if (!mounted) return <div className="min-h-screen bg-[#0a0e17]" />;

  return (
    <div className="relative min-h-screen flex items-center justify-center overflow-hidden px-4 py-8">
      {/* Gradient Background */}
      <div className="fixed inset-0 bg-gradient-to-br from-slate-50 via-blue-50/50 to-indigo-50 dark:from-[#0a0e17] dark:via-[#0d1321] dark:to-[#0a0e17]" />

      {/* Animated Orbs */}
      <div className="fixed inset-0 overflow-hidden pointer-events-none">
        <div className="absolute -top-40 -left-40 w-96 h-96 bg-blue-400/20 dark:bg-blue-600/20 rounded-full blur-3xl animate-pulse" />
        <div
          className="absolute top-1/4 -right-20 w-80 h-80 bg-purple-400/15 dark:bg-purple-600/15 rounded-full blur-3xl animate-pulse"
          style={{ animationDelay: '1s' }}
        />
        <div
          className="absolute -bottom-32 left-1/4 w-96 h-96 bg-indigo-400/15 dark:bg-indigo-600/15 rounded-full blur-3xl animate-pulse"
          style={{ animationDelay: '2s' }}
        />
        <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-cyan-400/8 dark:bg-cyan-600/8 rounded-full blur-3xl" />
        <div
          className="absolute inset-0 opacity-[0.015] dark:opacity-[0.03]"
          style={{
            backgroundImage:
              'linear-gradient(rgba(59,130,246,.5) 1px,transparent 1px),linear-gradient(90deg,rgba(59,130,246,.5) 1px,transparent 1px)',
            backgroundSize: '60px 60px',
          }}
        />
      </div>

      {/* Main Card */}
      <div className="relative w-full max-w-md z-10">
        {/* Logo */}
        <div className="text-center mb-8">
          <div className="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500 to-purple-600 shadow-lg shadow-blue-500/25 mb-4">
            <Sparkles className="w-8 h-8 text-white" />
          </div>
          <h1 className="text-2xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 dark:from-blue-400 dark:to-purple-400 bg-clip-text text-transparent">
            {tr.platformName}
          </h1>
          <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">{tr.platformTagline}</p>
        </div>

        {/* Glass Card */}
        <div className="rounded-3xl border border-white/20 dark:border-white/10 bg-white/60 dark:bg-white/5 backdrop-blur-xl backdrop-saturate-150 shadow-2xl shadow-black/5 dark:shadow-black/30 p-8">
          {/* Step Indicator */}
          <div className="flex items-center justify-center gap-0 mb-8">
            {[
              { num: 1, icon: Phone, label: tr.stepPhone },
              { num: 2, icon: ShieldCheck, label: tr.stepOtp },
              { num: 3, icon: UserPlus, label: tr.stepProfile },
            ].map((s, i) => (
              <div key={s.num} className="flex items-center">
                <div className="flex flex-col items-center gap-1">
                  <div
                    className={`w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300 ${
                      step > s.num
                        ? 'bg-green-500 text-white shadow-lg shadow-green-500/30'
                        : step === s.num
                          ? 'bg-gradient-to-br from-blue-500 to-purple-600 text-white shadow-lg shadow-blue-500/30'
                          : 'bg-gray-200/50 dark:bg-white/10 text-gray-400 dark:text-gray-600'
                    }`}
                  >
                    {step > s.num ? <CheckCircle2 className="w-5 h-5" /> : <s.icon className="w-5 h-5" />}
                  </div>
                  <span
                    className={`text-[10px] font-medium transition-colors ${
                      step >= s.num ? 'text-gray-700 dark:text-gray-300' : 'text-gray-400 dark:text-gray-600'
                    }`}
                  >
                    {s.label}
                  </span>
                </div>
                {i < 2 && (
                  <div
                    className={`w-12 h-0.5 mx-1 mb-4 rounded-full transition-colors ${
                      step > s.num ? 'bg-green-500' : 'bg-gray-200/50 dark:bg-white/10'
                    }`}
                  />
                )}
              </div>
            ))}
          </div>

          {/* Error Alert */}
          {error && (
            <div className="mb-6 p-4 rounded-2xl bg-red-500/10 border border-red-500/20 backdrop-blur-sm">
              <p className="text-sm text-red-600 dark:text-red-400 text-center">{error}</p>
            </div>
          )}

          {/* ──────── STEP 1: Phone ──────── */}
          {step === 1 && (
            <form onSubmit={handleSendOtp} className="space-y-5">
              <div className="text-center mb-2">
                <h2 className="text-xl font-bold text-gray-900 dark:text-white">{tr.loginSubtitle}</h2>
              </div>

              <div>
                <label htmlFor="phone" className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  {tr.phoneLabel}
                </label>
                <div className="relative">
                  <div className="absolute inset-y-0 start-0 flex items-center ps-4 pointer-events-none">
                    <Phone className="w-5 h-5 text-gray-400 dark:text-gray-500" />
                  </div>
                  <input
                    id="phone"
                    type="tel"
                    value={phone}
                    onChange={(e) => setPhone(e.target.value.replace(/[^\d+]/g, ''))}
                    required
                    placeholder={tr.phonePlaceholder}
                    className={`${inputClass} ps-12 pe-4`}
                    dir="ltr"
                    autoFocus
                  />
                </div>
              </div>

              <button type="submit" disabled={loading || phone.length < 10} className={btnClass}>
                <div className="absolute inset-0 -translate-x-full group-hover:translate-x-full transition-transform duration-700 bg-gradient-to-r from-transparent via-white/10 to-transparent" />
                {loading ? (
                  <Loader2 className="w-5 h-5 animate-spin" />
                ) : (
                  <>
                    <span>{tr.sendOtp}</span>
                    <ArrowIcon className="w-4 h-4 group-hover:translate-x-1 rtl:group-hover:-translate-x-1 transition-transform" />
                  </>
                )}
              </button>
            </form>
          )}

          {/* ──────── STEP 2: OTP ──────── */}
          {step === 2 && (
            <div className="space-y-5">
              <div className="text-center mb-2">
                <h2 className="text-xl font-bold text-gray-900 dark:text-white">{tr.otpLabel}</h2>
                <p className="text-sm text-gray-500 dark:text-gray-400 mt-1" dir="ltr">
                  {phone}
                </p>
              </div>

              {/* 4-digit OTP boxes */}
              <div className="flex justify-center gap-3" dir="ltr">
                {otp.map((digit, i) => (
                  <input
                    key={i}
                    ref={(el) => {
                      otpRefs.current[i] = el;
                    }}
                    type="text"
                    inputMode="numeric"
                    maxLength={1}
                    value={digit}
                    onChange={(e) => handleOtpChange(i, e.target.value)}
                    onKeyDown={(e) => handleOtpKeyDown(i, e)}
                    onPaste={i === 0 ? handleOtpPaste : undefined}
                    className={`w-14 h-14 text-center text-2xl font-bold rounded-2xl border transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500/40 ${
                      digit
                        ? 'border-blue-500/50 bg-blue-50/50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400'
                        : 'border-white/30 dark:border-white/10 bg-white/50 dark:bg-white/5 text-gray-900 dark:text-white'
                    }`}
                  />
                ))}
              </div>

              {/* Verify button */}
              <button onClick={() => handleVerifyOtp()} disabled={loading || otp.join('').length < 4} className={btnClass}>
                {loading ? (
                  <Loader2 className="w-5 h-5 animate-spin" />
                ) : (
                  <>
                    <ShieldCheck className="w-5 h-5" />
                    <span>{tr.verifyOtp}</span>
                  </>
                )}
              </button>

              {/* Resend */}
              <div className="text-center">
                {countdown > 0 ? (
                  <p className="text-sm text-gray-400 dark:text-gray-500">
                    {tr.resendIn} <span className="font-mono font-bold text-blue-500">{countdown}</span> {tr.seconds}
                  </p>
                ) : (
                  <button
                    onClick={handleResendOtp}
                    disabled={loading}
                    className="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-semibold transition-colors"
                  >
                    {tr.resendOtp}
                  </button>
                )}
              </div>

              {/* Back */}
              <button
                onClick={() => {
                  setStep(1);
                  setError('');
                  setOtp(['', '', '', '']);
                }}
                className="w-full text-center text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition-colors"
              >
                {isRtl ? '← تغيير الرقم' : 'Change number →'}
              </button>
            </div>
          )}

          {/* ──────── STEP 3: Registration ──────── */}
          {step === 3 && (
            <form onSubmit={handleCompleteRegistration} className="space-y-4">
              <div className="text-center mb-2">
                <h2 className="text-lg font-bold text-gray-900 dark:text-white">{tr.step3Title}</h2>
                <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">{tr.step3Subtitle}</p>
              </div>

              {/* Name */}
              <div>
                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                  {tr.nameLabel}
                </label>
                <input
                  type="text"
                  value={name}
                  onChange={(e) => setName(e.target.value)}
                  required
                  placeholder={tr.namePlaceholder}
                  className={`${inputClass} px-4`}
                />
              </div>

              {/* Business Name */}
              <div>
                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                  {tr.businessNameLabel}
                </label>
                <input
                  type="text"
                  value={businessName}
                  onChange={(e) => setBusinessName(e.target.value)}
                  placeholder={tr.businessNamePlaceholder}
                  className={`${inputClass} px-4`}
                />
              </div>

              {/* Business Type */}
              <div>
                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                  {tr.businessTypeLabel}
                </label>
                <div className="relative">
                  <select value={businessType} onChange={(e) => setBusinessType(e.target.value)} className={selectClass}>
                    <option value="">{tr.businessTypePlaceholder}</option>
                    {businessTypes.map((bt) => (
                      <option key={bt.en} value={bt.en}>
                        {locale === 'ar' ? bt.ar : bt.en}
                      </option>
                    ))}
                  </select>
                  <ChevronDown className="absolute end-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" />
                </div>
              </div>

              {/* Region */}
              <div>
                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                  {tr.regionLabel}
                </label>
                <div className="relative">
                  <select value={region} onChange={(e) => setRegion(e.target.value)} className={selectClass}>
                    <option value="">{tr.regionPlaceholder}</option>
                    {regions.map((r) => (
                      <option key={r.en} value={r.en}>
                        {locale === 'ar' ? r.ar : r.en}
                      </option>
                    ))}
                  </select>
                  <ChevronDown className="absolute end-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" />
                </div>
              </div>

              <button type="submit" disabled={loading || !name.trim()} className={btnClass}>
                <div className="absolute inset-0 -translate-x-full group-hover:translate-x-full transition-transform duration-700 bg-gradient-to-r from-transparent via-white/10 to-transparent" />
                {loading ? (
                  <Loader2 className="w-5 h-5 animate-spin" />
                ) : (
                  <>
                    <UserPlus className="w-5 h-5" />
                    <span>{tr.completeRegistration}</span>
                    <ArrowIcon className="w-4 h-4 group-hover:translate-x-1 rtl:group-hover:-translate-x-1 transition-transform" />
                  </>
                )}
              </button>
            </form>
          )}
        </div>

        {/* Footer */}
        <p className="text-center text-xs text-gray-400 dark:text-gray-600 mt-6">{tr.copyright}</p>
      </div>
    </div>
  );
}
