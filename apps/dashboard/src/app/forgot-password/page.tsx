'use client';

import { useState, useEffect, FormEvent } from 'react';
import Link from 'next/link';
import { authApi, ApiResponse } from '@/lib/api';
import {
  Mail,
  Loader2,
  ArrowRight,
  ArrowLeft,
  Sparkles,
  KeyRound,
  CheckCircle2,
  Send,
} from 'lucide-react';

interface ForgotPasswordTranslations {
  forgotTitle: string;
  forgotSubtitle: string;
  email: string;
  emailPlaceholder: string;
  sendResetLink: string;
  backToLogin: string;
  platformName: string;
  platformTagline: string;
  requestFailed: string;
  successTitle: string;
  successMessage: string;
  sendAgain: string;
}

const translations: Record<string, ForgotPasswordTranslations> = {
  en: {
    forgotTitle: 'Reset Password',
    forgotSubtitle: 'Enter your email to receive a password reset link',
    email: 'Email Address',
    emailPlaceholder: 'example@email.com',
    sendResetLink: 'Send Reset Link',
    backToLogin: 'Back to Login',
    platformName: 'Maham Expo',
    platformTagline: 'Exhibition & Event Management Platform',
    requestFailed: 'Failed to send reset link. Please try again.',
    successTitle: 'Check Your Email',
    successMessage: 'We have sent a password reset link to your email address. Please check your inbox and follow the instructions.',
    sendAgain: 'Send Again',
  },
  ar: {
    forgotTitle: 'إعادة تعيين كلمة المرور',
    forgotSubtitle: 'أدخل بريدك الإلكتروني لاستلام رابط إعادة التعيين',
    email: 'البريد الإلكتروني',
    emailPlaceholder: 'example@email.com',
    sendResetLink: 'إرسال رابط إعادة التعيين',
    backToLogin: 'العودة لتسجيل الدخول',
    platformName: 'معرض مهام',
    platformTagline: 'منصة إدارة المعارض والفعاليات',
    requestFailed: 'فشل إرسال رابط إعادة التعيين. يرجى المحاولة مرة أخرى.',
    successTitle: 'تحقق من بريدك الإلكتروني',
    successMessage: 'تم إرسال رابط إعادة تعيين كلمة المرور إلى بريدك الإلكتروني. يرجى التحقق من صندوق الوارد واتباع التعليمات.',
    sendAgain: 'إرسال مرة أخرى',
  },
};

export default function ForgotPasswordPage() {
  const [locale, setLocale] = useState('ar');
  const [email, setEmail] = useState('');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');
  const [success, setSuccess] = useState(false);
  const [mounted, setMounted] = useState(false);

  const t = translations[locale] || translations.ar;
  const isRtl = locale === 'ar';
  const ArrowIcon = isRtl ? ArrowRight : ArrowLeft;

  useEffect(() => {
    setMounted(true);
    const savedLocale = localStorage.getItem('locale') || 'ar';
    setLocale(savedLocale);
    document.documentElement.dir = savedLocale === 'ar' ? 'rtl' : 'ltr';
    document.documentElement.lang = savedLocale;
  }, []);

  const handleSubmit = async (e: FormEvent) => {
    e.preventDefault();
    setError('');
    setLoading(true);

    try {
      await authApi.post<ApiResponse>('/auth/forgot-password', { email });
      setSuccess(true);
    } catch (err: unknown) {
      const axiosError = err as { response?: { data?: { message?: string } } };
      setError(axiosError.response?.data?.message || t.requestFailed);
    } finally {
      setLoading(false);
    }
  };

  const handleSendAgain = () => {
    setSuccess(false);
    setEmail('');
  };

  if (!mounted) {
    return <div className="min-h-screen bg-[#0a0e17]" />;
  }

  return (
    <div className="relative min-h-screen flex items-center justify-center overflow-hidden px-4 py-8">
      {/* Gradient Background */}
      <div className="fixed inset-0 bg-gradient-to-br from-slate-50 via-amber-50/30 to-blue-50 dark:from-[#0a0e17] dark:via-[#13100d] dark:to-[#0a0e17]" />

      {/* Animated Mesh Gradient Orbs */}
      <div className="fixed inset-0 overflow-hidden pointer-events-none">
        <div className="absolute -top-40 left-1/3 w-96 h-96 bg-amber-400/15 dark:bg-amber-600/15 rounded-full blur-3xl animate-pulse" />
        <div className="absolute top-1/2 -right-20 w-80 h-80 bg-blue-400/15 dark:bg-blue-600/15 rounded-full blur-3xl animate-pulse" style={{ animationDelay: '1s' }} />
        <div className="absolute -bottom-32 -left-20 w-96 h-96 bg-indigo-400/10 dark:bg-indigo-600/10 rounded-full blur-3xl animate-pulse" style={{ animationDelay: '2s' }} />
        <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-orange-400/5 dark:bg-orange-600/5 rounded-full blur-3xl" />

        {/* Grid Pattern */}
        <div
          className="absolute inset-0 opacity-[0.015] dark:opacity-[0.03]"
          style={{
            backgroundImage: `linear-gradient(rgba(245, 158, 11, 0.5) 1px, transparent 1px), linear-gradient(90deg, rgba(245, 158, 11, 0.5) 1px, transparent 1px)`,
            backgroundSize: '60px 60px',
          }}
        />
      </div>

      {/* Content */}
      <div className="relative w-full max-w-md z-10">
        {/* Logo & Branding */}
        <div className="text-center mb-8">
          <div className="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-amber-500 to-blue-600 shadow-lg shadow-amber-500/25 mb-4">
            <Sparkles className="w-8 h-8 text-white" />
          </div>
          <h1 className="text-2xl font-bold bg-gradient-to-r from-amber-600 to-blue-600 dark:from-amber-400 dark:to-blue-400 bg-clip-text text-transparent">
            {t.platformName}
          </h1>
          <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
            {t.platformTagline}
          </p>
        </div>

        {/* Glass Card */}
        <div className="rounded-3xl border border-white/20 dark:border-white/10 bg-white/60 dark:bg-white/5 backdrop-blur-xl backdrop-saturate-150 shadow-2xl shadow-black/5 dark:shadow-black/30 p-8">
          {success ? (
            /* Success State */
            <div className="text-center py-4">
              <div className="inline-flex items-center justify-center w-20 h-20 rounded-full bg-emerald-500/10 border border-emerald-500/20 mb-6">
                <CheckCircle2 className="w-10 h-10 text-emerald-500" />
              </div>
              <h2 className="text-2xl font-bold text-gray-900 dark:text-white mb-3">
                {t.successTitle}
              </h2>
              <p className="text-gray-500 dark:text-gray-400 text-sm leading-relaxed mb-8">
                {t.successMessage}
              </p>
              <div className="space-y-3">
                <button
                  onClick={handleSendAgain}
                  className="w-full py-3 rounded-2xl text-sm font-medium border border-gray-200/50 dark:border-white/10 bg-white/50 dark:bg-white/5 text-gray-700 dark:text-gray-300 hover:bg-white/80 dark:hover:bg-white/10 transition-all"
                >
                  {t.sendAgain}
                </button>
                <Link
                  href="/login"
                  className="flex items-center justify-center gap-2 w-full py-3 rounded-2xl text-sm font-semibold text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 transition-colors"
                >
                  <ArrowIcon className="w-4 h-4" />
                  {t.backToLogin}
                </Link>
              </div>
            </div>
          ) : (
            /* Form State */
            <>
              {/* Header */}
              <div className="text-center mb-8">
                <div className="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-amber-500/10 border border-amber-500/20 mb-4">
                  <KeyRound className="w-7 h-7 text-amber-500 dark:text-amber-400" />
                </div>
                <h2 className="text-2xl font-bold text-gray-900 dark:text-white">
                  {t.forgotTitle}
                </h2>
                <p className="text-gray-500 dark:text-gray-400 mt-2 text-sm">
                  {t.forgotSubtitle}
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
                {/* Email Field */}
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
                      className="w-full ps-12 pe-4 py-3.5 rounded-2xl border border-white/30 dark:border-white/10 bg-white/50 dark:bg-white/5 backdrop-blur-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-amber-500/40 focus:border-amber-500/40 transition-all text-sm"
                      dir="ltr"
                    />
                  </div>
                </div>

                {/* Submit Button */}
                <button
                  type="submit"
                  disabled={loading}
                  className="relative w-full py-3.5 rounded-2xl text-white font-semibold text-sm bg-gradient-to-r from-amber-500 to-blue-600 hover:from-amber-600 hover:to-blue-700 shadow-lg shadow-amber-500/25 hover:shadow-amber-500/40 transition-all duration-300 disabled:opacity-60 disabled:cursor-not-allowed disabled:hover:shadow-amber-500/25 flex items-center justify-center gap-2 group overflow-hidden"
                >
                  {/* Shimmer effect */}
                  <div className="absolute inset-0 -translate-x-full group-hover:translate-x-full transition-transform duration-700 bg-gradient-to-r from-transparent via-white/10 to-transparent" />

                  {loading ? (
                    <Loader2 className="w-5 h-5 animate-spin" />
                  ) : (
                    <>
                      <Send className="w-5 h-5" />
                      <span>{t.sendResetLink}</span>
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

              {/* Back to Login Link */}
              <Link
                href="/login"
                className="flex items-center justify-center gap-2 text-sm text-gray-500 dark:text-gray-400 hover:text-amber-600 dark:hover:text-amber-400 font-medium transition-colors group"
              >
                <ArrowIcon className="w-4 h-4 group-hover:-translate-x-1 rtl:group-hover:translate-x-1 transition-transform" />
                {t.backToLogin}
              </Link>
            </>
          )}
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
