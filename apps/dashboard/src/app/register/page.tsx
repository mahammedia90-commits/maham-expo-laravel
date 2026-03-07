'use client';

import { useState, useEffect, FormEvent } from 'react';
import { useRouter } from 'next/navigation';
import Link from 'next/link';
import { authApi, ApiResponse } from '@/lib/api';
import { useAuthStore } from '@/stores/auth';
import {
  User,
  Mail,
  Phone,
  Lock,
  Eye,
  EyeOff,
  Loader2,
  UserPlus,
  Sparkles,
  ArrowRight,
  ArrowLeft,
  CheckCircle2,
} from 'lucide-react';

interface RegisterTranslations {
  registerTitle: string;
  registerSubtitle: string;
  fullName: string;
  email: string;
  phone: string;
  password: string;
  confirmPassword: string;
  signUp: string;
  hasAccount: string;
  signIn: string;
  platformName: string;
  platformTagline: string;
  passwordMismatch: string;
  registrationFailed: string;
  namePlaceholder: string;
  emailPlaceholder: string;
  phonePlaceholder: string;
  passwordRequirements: string;
  minLength: string;
  hasUppercase: string;
  hasNumber: string;
}

const translations: Record<string, RegisterTranslations> = {
  en: {
    registerTitle: 'Create Account',
    registerSubtitle: 'Register for a new account',
    fullName: 'Full Name',
    email: 'Email Address',
    phone: 'Phone Number',
    password: 'Password',
    confirmPassword: 'Confirm Password',
    signUp: 'Create Account',
    hasAccount: 'Already have an account?',
    signIn: 'Sign In',
    platformName: 'Maham Expo',
    platformTagline: 'Exhibition & Event Management Platform',
    passwordMismatch: 'Passwords do not match',
    registrationFailed: 'Registration failed. Please try again.',
    namePlaceholder: 'Enter your full name',
    emailPlaceholder: 'example@email.com',
    phonePlaceholder: '05xxxxxxxx',
    passwordRequirements: 'Password requirements',
    minLength: 'At least 8 characters',
    hasUppercase: 'One uppercase letter',
    hasNumber: 'One number',
  },
  ar: {
    registerTitle: 'إنشاء حساب',
    registerSubtitle: 'سجل حساباً جديداً',
    fullName: 'الاسم الكامل',
    email: 'البريد الإلكتروني',
    phone: 'رقم الهاتف',
    password: 'كلمة المرور',
    confirmPassword: 'تأكيد كلمة المرور',
    signUp: 'إنشاء حساب',
    hasAccount: 'لديك حساب بالفعل؟',
    signIn: 'تسجيل الدخول',
    platformName: 'معرض مهام',
    platformTagline: 'منصة إدارة المعارض والفعاليات',
    passwordMismatch: 'كلمتا المرور غير متطابقتين',
    registrationFailed: 'فشل التسجيل. يرجى المحاولة مرة أخرى.',
    namePlaceholder: 'أدخل اسمك الكامل',
    emailPlaceholder: 'example@email.com',
    phonePlaceholder: '05xxxxxxxx',
    passwordRequirements: 'متطلبات كلمة المرور',
    minLength: '8 أحرف على الأقل',
    hasUppercase: 'حرف كبير واحد',
    hasNumber: 'رقم واحد',
  },
};

export default function RegisterPage() {
  const router = useRouter();
  const { isAuthenticated } = useAuthStore();

  const [locale, setLocale] = useState('ar');
  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [phone, setPhone] = useState('');
  const [password, setPassword] = useState('');
  const [confirmPassword, setConfirmPassword] = useState('');
  const [showPassword, setShowPassword] = useState(false);
  const [showConfirmPassword, setShowConfirmPassword] = useState(false);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');
  const [fieldErrors, setFieldErrors] = useState<Record<string, string[]>>({});
  const [mounted, setMounted] = useState(false);

  const t = translations[locale] || translations.ar;
  const isRtl = locale === 'ar';
  const ArrowIcon = isRtl ? ArrowLeft : ArrowRight;

  // Password strength checks
  const passChecks = {
    minLength: password.length >= 8,
    hasUppercase: /[A-Z]/.test(password),
    hasNumber: /[0-9]/.test(password),
  };

  useEffect(() => {
    setMounted(true);
    const savedLocale = localStorage.getItem('locale') || 'ar';
    setLocale(savedLocale);
    document.documentElement.dir = savedLocale === 'ar' ? 'rtl' : 'ltr';
    document.documentElement.lang = savedLocale;
  }, []);

  useEffect(() => {
    if (isAuthenticated) {
      router.replace('/dashboard');
    }
  }, [isAuthenticated, router]);

  const handleSubmit = async (e: FormEvent) => {
    e.preventDefault();
    setError('');
    setFieldErrors({});

    if (password !== confirmPassword) {
      setError(t.passwordMismatch);
      return;
    }

    setLoading(true);

    try {
      await authApi.post<ApiResponse>('/auth/register', {
        name,
        email,
        phone,
        password,
        password_confirmation: confirmPassword,
      });

      router.push('/login');
    } catch (err: unknown) {
      const axiosError = err as {
        response?: {
          data?: {
            message?: string;
            errors?: Record<string, string[]>;
          };
        };
      };
      if (axiosError.response?.data?.errors) {
        setFieldErrors(axiosError.response.data.errors);
      }
      setError(axiosError.response?.data?.message || t.registrationFailed);
    } finally {
      setLoading(false);
    }
  };

  if (!mounted) {
    return <div className="min-h-screen bg-[#0a0e17]" />;
  }

  return (
    <div className="relative min-h-screen flex items-center justify-center overflow-hidden px-4 py-8">
      {/* Gradient Background */}
      <div className="fixed inset-0 bg-gradient-to-br from-slate-50 via-purple-50/50 to-indigo-50 dark:from-[#0a0e17] dark:via-[#110d21] dark:to-[#0a0e17]" />

      {/* Animated Mesh Gradient Orbs */}
      <div className="fixed inset-0 overflow-hidden pointer-events-none">
        <div className="absolute -top-40 -right-40 w-96 h-96 bg-purple-400/20 dark:bg-purple-600/20 rounded-full blur-3xl animate-pulse" />
        <div className="absolute top-1/3 -left-20 w-80 h-80 bg-blue-400/15 dark:bg-blue-600/15 rounded-full blur-3xl animate-pulse" style={{ animationDelay: '1s' }} />
        <div className="absolute -bottom-32 right-1/4 w-96 h-96 bg-indigo-400/15 dark:bg-indigo-600/15 rounded-full blur-3xl animate-pulse" style={{ animationDelay: '2s' }} />
        <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-violet-400/8 dark:bg-violet-600/8 rounded-full blur-3xl" />

        {/* Grid Pattern */}
        <div
          className="absolute inset-0 opacity-[0.015] dark:opacity-[0.03]"
          style={{
            backgroundImage: `linear-gradient(rgba(147, 51, 234, 0.5) 1px, transparent 1px), linear-gradient(90deg, rgba(147, 51, 234, 0.5) 1px, transparent 1px)`,
            backgroundSize: '60px 60px',
          }}
        />
      </div>

      {/* Register Card */}
      <div className="relative w-full max-w-md z-10">
        {/* Logo & Branding */}
        <div className="text-center mb-8">
          <div className="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-purple-500 to-blue-600 shadow-lg shadow-purple-500/25 mb-4">
            <Sparkles className="w-8 h-8 text-white" />
          </div>
          <h1 className="text-2xl font-bold bg-gradient-to-r from-purple-600 to-blue-600 dark:from-purple-400 dark:to-blue-400 bg-clip-text text-transparent">
            {t.platformName}
          </h1>
          <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
            {t.platformTagline}
          </p>
        </div>

        {/* Glass Card */}
        <div className="rounded-3xl border border-white/20 dark:border-white/10 bg-white/60 dark:bg-white/5 backdrop-blur-xl backdrop-saturate-150 shadow-2xl shadow-black/5 dark:shadow-black/30 p-8">
          {/* Header */}
          <div className="text-center mb-8">
            <h2 className="text-2xl font-bold text-gray-900 dark:text-white">
              {t.registerTitle}
            </h2>
            <p className="text-gray-500 dark:text-gray-400 mt-2 text-sm">
              {t.registerSubtitle}
            </p>
          </div>

          {/* Error Alert */}
          {error && (
            <div className="mb-6 p-4 rounded-2xl bg-red-500/10 border border-red-500/20 backdrop-blur-sm">
              <p className="text-sm text-red-600 dark:text-red-400 text-center">
                {error}
              </p>
            </div>
          )}

          {/* Form */}
          <form onSubmit={handleSubmit} className="space-y-4">
            {/* Full Name */}
            <div>
              <label
                htmlFor="name"
                className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
              >
                {t.fullName}
              </label>
              <div className="relative">
                <div className="absolute inset-y-0 start-0 flex items-center ps-4 pointer-events-none">
                  <User className="w-5 h-5 text-gray-400 dark:text-gray-500" />
                </div>
                <input
                  id="name"
                  type="text"
                  value={name}
                  onChange={(e) => setName(e.target.value)}
                  required
                  placeholder={t.namePlaceholder}
                  className="w-full ps-12 pe-4 py-3.5 rounded-2xl border border-white/30 dark:border-white/10 bg-white/50 dark:bg-white/5 backdrop-blur-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500/40 focus:border-purple-500/40 transition-all text-sm"
                />
              </div>
              {fieldErrors.name && (
                <p className="mt-1.5 text-xs text-red-500">{fieldErrors.name[0]}</p>
              )}
            </div>

            {/* Email */}
            <div>
              <label
                htmlFor="email"
                className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
              >
                {t.email}
              </label>
              <div className="relative">
                <div className="absolute inset-y-0 start-0 flex items-center ps-4 pointer-events-none">
                  <Mail className="w-5 h-5 text-gray-400 dark:text-gray-500" />
                </div>
                <input
                  id="email"
                  type="email"
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                  required
                  placeholder={t.emailPlaceholder}
                  className="w-full ps-12 pe-4 py-3.5 rounded-2xl border border-white/30 dark:border-white/10 bg-white/50 dark:bg-white/5 backdrop-blur-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500/40 focus:border-purple-500/40 transition-all text-sm"
                  dir="ltr"
                />
              </div>
              {fieldErrors.email && (
                <p className="mt-1.5 text-xs text-red-500">{fieldErrors.email[0]}</p>
              )}
            </div>

            {/* Phone */}
            <div>
              <label
                htmlFor="phone"
                className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
              >
                {t.phone}
              </label>
              <div className="relative">
                <div className="absolute inset-y-0 start-0 flex items-center ps-4 pointer-events-none">
                  <Phone className="w-5 h-5 text-gray-400 dark:text-gray-500" />
                </div>
                <input
                  id="phone"
                  type="tel"
                  value={phone}
                  onChange={(e) => setPhone(e.target.value)}
                  required
                  placeholder={t.phonePlaceholder}
                  className="w-full ps-12 pe-4 py-3.5 rounded-2xl border border-white/30 dark:border-white/10 bg-white/50 dark:bg-white/5 backdrop-blur-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500/40 focus:border-purple-500/40 transition-all text-sm"
                  dir="ltr"
                />
              </div>
              {fieldErrors.phone && (
                <p className="mt-1.5 text-xs text-red-500">{fieldErrors.phone[0]}</p>
              )}
            </div>

            {/* Password */}
            <div>
              <label
                htmlFor="password"
                className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
              >
                {t.password}
              </label>
              <div className="relative">
                <div className="absolute inset-y-0 start-0 flex items-center ps-4 pointer-events-none">
                  <Lock className="w-5 h-5 text-gray-400 dark:text-gray-500" />
                </div>
                <input
                  id="password"
                  type={showPassword ? 'text' : 'password'}
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  required
                  placeholder="••••••••"
                  className="w-full ps-12 pe-12 py-3.5 rounded-2xl border border-white/30 dark:border-white/10 bg-white/50 dark:bg-white/5 backdrop-blur-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500/40 focus:border-purple-500/40 transition-all text-sm"
                  dir="ltr"
                />
                <button
                  type="button"
                  onClick={() => setShowPassword(!showPassword)}
                  className="absolute inset-y-0 end-0 flex items-center pe-4 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                >
                  {showPassword ? <EyeOff className="w-5 h-5" /> : <Eye className="w-5 h-5" />}
                </button>
              </div>
              {fieldErrors.password && (
                <p className="mt-1.5 text-xs text-red-500">{fieldErrors.password[0]}</p>
              )}

              {/* Password Strength Indicators */}
              {password.length > 0 && (
                <div className="mt-3 space-y-1.5">
                  <p className="text-xs font-medium text-gray-500 dark:text-gray-400">{t.passwordRequirements}</p>
                  <div className="flex flex-wrap gap-2">
                    {[
                      { check: passChecks.minLength, label: t.minLength },
                      { check: passChecks.hasUppercase, label: t.hasUppercase },
                      { check: passChecks.hasNumber, label: t.hasNumber },
                    ].map((item) => (
                      <span
                        key={item.label}
                        className={`inline-flex items-center gap-1 text-xs px-2 py-1 rounded-lg transition-colors ${
                          item.check
                            ? 'text-emerald-600 dark:text-emerald-400 bg-emerald-500/10'
                            : 'text-gray-400 dark:text-gray-500 bg-gray-500/10'
                        }`}
                      >
                        <CheckCircle2 className="w-3 h-3" />
                        {item.label}
                      </span>
                    ))}
                  </div>
                </div>
              )}
            </div>

            {/* Confirm Password */}
            <div>
              <label
                htmlFor="confirmPassword"
                className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
              >
                {t.confirmPassword}
              </label>
              <div className="relative">
                <div className="absolute inset-y-0 start-0 flex items-center ps-4 pointer-events-none">
                  <Lock className="w-5 h-5 text-gray-400 dark:text-gray-500" />
                </div>
                <input
                  id="confirmPassword"
                  type={showConfirmPassword ? 'text' : 'password'}
                  value={confirmPassword}
                  onChange={(e) => setConfirmPassword(e.target.value)}
                  required
                  placeholder="••••••••"
                  className={`w-full ps-12 pe-12 py-3.5 rounded-2xl border backdrop-blur-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 transition-all text-sm ${
                    confirmPassword && confirmPassword !== password
                      ? 'border-red-500/40 focus:ring-red-500/40 focus:border-red-500/40 bg-red-50/50 dark:bg-red-500/5'
                      : confirmPassword && confirmPassword === password
                      ? 'border-emerald-500/40 focus:ring-emerald-500/40 focus:border-emerald-500/40 bg-emerald-50/50 dark:bg-emerald-500/5'
                      : 'border-white/30 dark:border-white/10 bg-white/50 dark:bg-white/5 focus:ring-purple-500/40 focus:border-purple-500/40'
                  }`}
                  dir="ltr"
                />
                <button
                  type="button"
                  onClick={() => setShowConfirmPassword(!showConfirmPassword)}
                  className="absolute inset-y-0 end-0 flex items-center pe-4 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                >
                  {showConfirmPassword ? <EyeOff className="w-5 h-5" /> : <Eye className="w-5 h-5" />}
                </button>
              </div>
            </div>

            {/* Submit Button */}
            <button
              type="submit"
              disabled={loading}
              className="relative w-full py-3.5 mt-2 rounded-2xl text-white font-semibold text-sm bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 shadow-lg shadow-purple-500/25 hover:shadow-purple-500/40 transition-all duration-300 disabled:opacity-60 disabled:cursor-not-allowed disabled:hover:shadow-purple-500/25 flex items-center justify-center gap-2 group overflow-hidden"
            >
              {/* Shimmer effect */}
              <div className="absolute inset-0 -translate-x-full group-hover:translate-x-full transition-transform duration-700 bg-gradient-to-r from-transparent via-white/10 to-transparent" />

              {loading ? (
                <Loader2 className="w-5 h-5 animate-spin" />
              ) : (
                <>
                  <UserPlus className="w-5 h-5" />
                  <span>{t.signUp}</span>
                  <ArrowIcon className="w-4 h-4 group-hover:translate-x-1 rtl:group-hover:-translate-x-1 transition-transform" />
                </>
              )}
            </button>
          </form>

          {/* Divider */}
          <div className="relative my-8">
            <div className="absolute inset-0 flex items-center">
              <div className="w-full border-t border-gray-200/50 dark:border-white/10" />
            </div>
          </div>

          {/* Login Link */}
          <p className="text-center text-sm text-gray-500 dark:text-gray-400">
            {t.hasAccount}{' '}
            <Link
              href="/login"
              className="text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300 font-semibold transition-colors"
            >
              {t.signIn}
            </Link>
          </p>
        </div>

        {/* Footer */}
        <p className="text-center text-xs text-gray-400 dark:text-gray-600 mt-6">
          {isRtl
            ? '\u00A9 2025 معرض مهام. جميع الحقوق محفوظة.'
            : '\u00A9 2025 Maham Expo. All rights reserved.'}
        </p>
      </div>
    </div>
  );
}
