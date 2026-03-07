'use client';

import { useState, useEffect, FormEvent } from 'react';
import { useRouter } from 'next/navigation';
import Link from 'next/link';
import { useAuthStore } from '@/stores/auth';
import { authApi, ApiResponse } from '@/lib/api';
import {
  Mail,
  Lock,
  Eye,
  EyeOff,
  Loader2,
  LogIn,
  Sparkles,
  ArrowRight,
  ArrowLeft,
} from 'lucide-react';

interface LoginTranslations {
  loginTitle: string;
  loginSubtitle: string;
  emailOrPhone: string;
  password: string;
  rememberMe: string;
  forgotPassword: string;
  signIn: string;
  noAccount: string;
  signUp: string;
  invalidCredentials: string;
  platformName: string;
  platformTagline: string;
}

const translations: Record<string, LoginTranslations> = {
  en: {
    loginTitle: 'Welcome Back',
    loginSubtitle: 'Sign in to your admin dashboard',
    emailOrPhone: 'Email or Phone',
    password: 'Password',
    rememberMe: 'Remember me',
    forgotPassword: 'Forgot password?',
    signIn: 'Sign In',
    noAccount: "Don't have an account?",
    signUp: 'Sign Up',
    invalidCredentials: 'Invalid email or password',
    platformName: 'Maham Expo',
    platformTagline: 'Exhibition & Event Management Platform',
  },
  ar: {
    loginTitle: 'مرحباً بعودتك',
    loginSubtitle: 'سجل دخولك للوحة التحكم',
    emailOrPhone: 'البريد الإلكتروني أو الهاتف',
    password: 'كلمة المرور',
    rememberMe: 'تذكرني',
    forgotPassword: 'نسيت كلمة المرور؟',
    signIn: 'تسجيل الدخول',
    noAccount: 'ليس لديك حساب؟',
    signUp: 'تسجيل حساب',
    invalidCredentials: 'البريد الإلكتروني أو كلمة المرور غير صحيحة',
    platformName: 'معرض مهام',
    platformTagline: 'منصة إدارة المعارض والفعاليات',
  },
};

export default function LoginPage() {
  const router = useRouter();
  const { setAuth, isAuthenticated } = useAuthStore();

  const [locale, setLocale] = useState('ar');
  const [emailOrPhone, setEmailOrPhone] = useState('');
  const [password, setPassword] = useState('');
  const [rememberMe, setRememberMe] = useState(false);
  const [showPassword, setShowPassword] = useState(false);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');
  const [mounted, setMounted] = useState(false);

  const t = translations[locale] || translations.ar;
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
    if (isAuthenticated) {
      router.replace('/dashboard');
    }
  }, [isAuthenticated, router]);

  const handleSubmit = async (e: FormEvent) => {
    e.preventDefault();
    setError('');
    setLoading(true);

    try {
      const response = await authApi.post<ApiResponse<{ user: typeof import('@/stores/auth').useAuthStore extends () => infer S ? S extends { user: infer U } ? U : never : never; token: string }>>('/auth/login', {
        email: emailOrPhone,
        password,
        remember_me: rememberMe,
      });

      const { user, token } = response.data.data;
      setAuth(user, token);
      router.push('/dashboard');
    } catch (err: unknown) {
      const axiosError = err as { response?: { data?: { message?: string } } };
      setError(axiosError.response?.data?.message || t.invalidCredentials);
    } finally {
      setLoading(false);
    }
  };

  if (!mounted) {
    return (
      <div className="min-h-screen bg-[#0a0e17]" />
    );
  }

  return (
    <div className="relative min-h-screen flex items-center justify-center overflow-hidden px-4 py-8">
      {/* Gradient Background */}
      <div className="fixed inset-0 bg-gradient-to-br from-slate-50 via-blue-50/50 to-indigo-50 dark:from-[#0a0e17] dark:via-[#0d1321] dark:to-[#0a0e17]" />

      {/* Animated Mesh Gradient Orbs */}
      <div className="fixed inset-0 overflow-hidden pointer-events-none">
        <div className="absolute -top-40 -left-40 w-96 h-96 bg-blue-400/20 dark:bg-blue-600/20 rounded-full blur-3xl animate-pulse" />
        <div className="absolute top-1/4 -right-20 w-80 h-80 bg-purple-400/15 dark:bg-purple-600/15 rounded-full blur-3xl animate-pulse" style={{ animationDelay: '1s' }} />
        <div className="absolute -bottom-32 left-1/4 w-96 h-96 bg-indigo-400/15 dark:bg-indigo-600/15 rounded-full blur-3xl animate-pulse" style={{ animationDelay: '2s' }} />
        <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-cyan-400/8 dark:bg-cyan-600/8 rounded-full blur-3xl" />

        {/* Grid Pattern */}
        <div
          className="absolute inset-0 opacity-[0.015] dark:opacity-[0.03]"
          style={{
            backgroundImage: `linear-gradient(rgba(59, 130, 246, 0.5) 1px, transparent 1px), linear-gradient(90deg, rgba(59, 130, 246, 0.5) 1px, transparent 1px)`,
            backgroundSize: '60px 60px',
          }}
        />
      </div>

      {/* Login Card */}
      <div className="relative w-full max-w-md z-10">
        {/* Logo & Branding */}
        <div className="text-center mb-8">
          <div className="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500 to-purple-600 shadow-lg shadow-blue-500/25 mb-4">
            <Sparkles className="w-8 h-8 text-white" />
          </div>
          <h1 className="text-2xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 dark:from-blue-400 dark:to-purple-400 bg-clip-text text-transparent">
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
              {t.loginTitle}
            </h2>
            <p className="text-gray-500 dark:text-gray-400 mt-2 text-sm">
              {t.loginSubtitle}
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
          <form onSubmit={handleSubmit} className="space-y-5">
            {/* Email / Phone Field */}
            <div>
              <label
                htmlFor="emailOrPhone"
                className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"
              >
                {t.emailOrPhone}
              </label>
              <div className="relative">
                <div className="absolute inset-y-0 start-0 flex items-center ps-4 pointer-events-none">
                  <Mail className="w-5 h-5 text-gray-400 dark:text-gray-500" />
                </div>
                <input
                  id="emailOrPhone"
                  type="text"
                  value={emailOrPhone}
                  onChange={(e) => setEmailOrPhone(e.target.value)}
                  required
                  placeholder={isRtl ? 'example@email.com أو 05xxxxxxxx' : 'example@email.com or 05xxxxxxxx'}
                  className="w-full ps-12 pe-4 py-3.5 rounded-2xl border border-white/30 dark:border-white/10 bg-white/50 dark:bg-white/5 backdrop-blur-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500/40 transition-all text-sm"
                  dir={isRtl ? 'rtl' : 'ltr'}
                />
              </div>
            </div>

            {/* Password Field */}
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
                  className="w-full ps-12 pe-12 py-3.5 rounded-2xl border border-white/30 dark:border-white/10 bg-white/50 dark:bg-white/5 backdrop-blur-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500/40 focus:border-blue-500/40 transition-all text-sm"
                  dir="ltr"
                />
                <button
                  type="button"
                  onClick={() => setShowPassword(!showPassword)}
                  className="absolute inset-y-0 end-0 flex items-center pe-4 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                >
                  {showPassword ? (
                    <EyeOff className="w-5 h-5" />
                  ) : (
                    <Eye className="w-5 h-5" />
                  )}
                </button>
              </div>
            </div>

            {/* Remember Me & Forgot Password */}
            <div className="flex items-center justify-between">
              <label className="flex items-center gap-2 cursor-pointer group">
                <div className="relative">
                  <input
                    type="checkbox"
                    checked={rememberMe}
                    onChange={(e) => setRememberMe(e.target.checked)}
                    className="sr-only peer"
                  />
                  <div className="w-5 h-5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white/50 dark:bg-white/5 peer-checked:bg-blue-500 peer-checked:border-blue-500 transition-all flex items-center justify-center">
                    {rememberMe && (
                      <svg className="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={3}>
                        <path strokeLinecap="round" strokeLinejoin="round" d="M5 13l4 4L19 7" />
                      </svg>
                    )}
                  </div>
                </div>
                <span className="text-sm text-gray-600 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-gray-200 transition-colors">
                  {t.rememberMe}
                </span>
              </label>

              <Link
                href="/forgot-password"
                className="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors font-medium"
              >
                {t.forgotPassword}
              </Link>
            </div>

            {/* Submit Button */}
            <button
              type="submit"
              disabled={loading}
              className="relative w-full py-3.5 rounded-2xl text-white font-semibold text-sm bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40 transition-all duration-300 disabled:opacity-60 disabled:cursor-not-allowed disabled:hover:shadow-blue-500/25 flex items-center justify-center gap-2 group overflow-hidden"
            >
              {/* Shimmer effect */}
              <div className="absolute inset-0 -translate-x-full group-hover:translate-x-full transition-transform duration-700 bg-gradient-to-r from-transparent via-white/10 to-transparent" />

              {loading ? (
                <Loader2 className="w-5 h-5 animate-spin" />
              ) : (
                <>
                  <LogIn className="w-5 h-5" />
                  <span>{t.signIn}</span>
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

          {/* Register Link */}
          <p className="text-center text-sm text-gray-500 dark:text-gray-400">
            {t.noAccount}{' '}
            <Link
              href="/register"
              className="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-semibold transition-colors"
            >
              {t.signUp}
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
