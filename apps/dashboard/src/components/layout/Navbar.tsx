'use client';

import { useState, useEffect } from 'react';
import { useTheme } from 'next-themes';
import { useAuthStore } from '@/stores/auth';
import { useRouter } from 'next/navigation';
import {
  Sun,
  Moon,
  Bell,
  Search,
  Globe,
  LogOut,
  User,
  Menu,
  X,
} from 'lucide-react';
import { cn } from '@/lib/utils';

export default function Navbar({ onMenuToggle }: { onMenuToggle?: () => void }) {
  const { theme, setTheme } = useTheme();
  const { user, logout } = useAuthStore();
  const router = useRouter();
  const [mounted, setMounted] = useState(false);
  const [showUserMenu, setShowUserMenu] = useState(false);
  const [showMobileMenu, setShowMobileMenu] = useState(false);
  const [locale, setLocale] = useState('ar');
  const [searchQuery, setSearchQuery] = useState('');

  useEffect(() => {
    setMounted(true);
    setLocale(localStorage.getItem('locale') || 'ar');
  }, []);

  const toggleLocale = () => {
    const newLocale = locale === 'ar' ? 'en' : 'ar';
    localStorage.setItem('locale', newLocale);
    setLocale(newLocale);
    document.documentElement.dir = newLocale === 'ar' ? 'rtl' : 'ltr';
    document.documentElement.lang = newLocale;
    window.location.reload();
  };

  const handleLogout = () => {
    logout();
    router.push('/login');
  };

  const isRtl = locale === 'ar';

  return (
    <header className="sticky top-0 z-30 border-b border-white/10 dark:border-white/10 border-gray-200/60 bg-white/80 dark:bg-gray-950/80 backdrop-blur-2xl backdrop-saturate-150">
      <div className="flex items-center justify-between h-16 px-6">
        {/* Mobile Menu Toggle */}
        <button
          onClick={onMenuToggle}
          className="lg:hidden p-2 rounded-xl hover:bg-white/50 dark:hover:bg-white/5 transition-colors"
        >
          {showMobileMenu ? <X className="w-5 h-5" /> : <Menu className="w-5 h-5" />}
        </button>

        {/* Search */}
        <div className="hidden md:flex items-center flex-1 max-w-md">
          <div className="relative w-full">
            <Search className="absolute start-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
            <input
              type="text"
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              placeholder={isRtl ? 'بحث...' : 'Search...'}
              className="w-full ps-10 pe-4 py-2 rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 backdrop-blur-sm text-sm text-gray-700 dark:text-gray-300 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500/30 transition-all"
            />
          </div>
        </div>

        {/* Right Actions */}
        <div className="flex items-center gap-2">
          {/* Language Toggle */}
          <button
            onClick={toggleLocale}
            className="flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-white/50 dark:hover:bg-white/5 border border-transparent hover:border-white/10 dark:hover:border-white/10 transition-all"
          >
            <Globe className="w-4 h-4" />
            <span>{locale === 'ar' ? 'EN' : 'عربي'}</span>
          </button>

          {/* Theme Toggle */}
          {mounted && (
            <button
              onClick={() => setTheme(theme === 'dark' ? 'light' : 'dark')}
              className="p-2.5 rounded-xl text-gray-600 dark:text-gray-400 hover:bg-white/50 dark:hover:bg-white/5 border border-transparent hover:border-white/10 dark:hover:border-white/10 transition-all"
            >
              {theme === 'dark' ? <Sun className="w-5 h-5" /> : <Moon className="w-5 h-5" />}
            </button>
          )}

          {/* Notifications */}
          <button className="relative p-2.5 rounded-xl text-gray-600 dark:text-gray-400 hover:bg-white/50 dark:hover:bg-white/5 border border-transparent hover:border-white/10 dark:hover:border-white/10 transition-all">
            <Bell className="w-5 h-5" />
            <span className="absolute top-1.5 end-1.5 w-2 h-2 bg-red-500 rounded-full animate-pulse" />
          </button>

          {/* User Menu */}
          <div className="relative">
            <button
              onClick={() => setShowUserMenu(!showUserMenu)}
              className="flex items-center gap-3 px-3 py-1.5 rounded-xl hover:bg-white/50 dark:hover:bg-white/5 border border-transparent hover:border-white/10 dark:hover:border-white/10 transition-all"
            >
              <div className="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center shadow-md">
                <span className="text-white text-sm font-bold">
                  {user?.name?.charAt(0) || 'A'}
                </span>
              </div>
              <div className="hidden md:block text-start">
                <p className="text-sm font-medium text-gray-700 dark:text-gray-200">
                  {user?.name || (isRtl ? 'المشرف' : 'Admin')}
                </p>
                <p className="text-xs text-gray-500 dark:text-gray-400">
                  {user?.roles?.[0] || 'super-admin'}
                </p>
              </div>
            </button>

            {showUserMenu && (
              <>
                <div className="fixed inset-0" onClick={() => setShowUserMenu(false)} />
                <div className="absolute end-0 mt-2 w-56 rounded-2xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/90 dark:bg-gray-900/90 backdrop-blur-2xl shadow-2xl overflow-hidden">
                  <div className="p-2">
                    <button
                      onClick={() => { setShowUserMenu(false); router.push('/dashboard/profile'); }}
                      className="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-gray-700 dark:text-gray-300 hover:bg-white/50 dark:hover:bg-white/5 transition-colors"
                    >
                      <User className="w-4 h-4" />
                      {isRtl ? 'الملف الشخصي' : 'Profile'}
                    </button>
                    <button
                      onClick={handleLogout}
                      className="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors"
                    >
                      <LogOut className="w-4 h-4" />
                      {isRtl ? 'تسجيل الخروج' : 'Logout'}
                    </button>
                  </div>
                </div>
              </>
            )}
          </div>
        </div>
      </div>
    </header>
  );
}
