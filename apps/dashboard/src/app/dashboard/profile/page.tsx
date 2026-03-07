'use client';

import { useState, useEffect } from 'react';
import GlassCard from '@/components/ui/GlassCard';
import { useAuthStore } from '@/stores/auth';
import { authApi } from '@/lib/api';
import { User, Mail, Phone, Shield, Camera, Save, Key, Eye, EyeOff } from 'lucide-react';

export default function ProfilePage() {
  const { user, setUser } = useAuthStore();
  const [locale, setLocale] = useState('ar');
  const [saving, setSaving] = useState(false);
  const [changingPassword, setChangingPassword] = useState(false);
  const [showCurrentPw, setShowCurrentPw] = useState(false);
  const [showNewPw, setShowNewPw] = useState(false);

  const [profileData, setProfileData] = useState({
    name: user?.name || '',
    email: user?.email || '',
    phone: user?.phone || '',
  });

  const [passwordData, setPasswordData] = useState({
    current_password: '',
    new_password: '',
    new_password_confirmation: '',
  });

  const isRtl = locale === 'ar';

  useEffect(() => { setLocale(localStorage.getItem('locale') || 'ar'); }, []);

  const handleProfileSave = async () => {
    setSaving(true);
    try {
      const res = await authApi.put('/profile', profileData);
      if (res.data.data) setUser(res.data.data);
    } catch { /* silent */ } finally { setSaving(false); }
  };

  const handlePasswordChange = async () => {
    setChangingPassword(true);
    try {
      await authApi.put('/change-password', passwordData);
      setPasswordData({ current_password: '', new_password: '', new_password_confirmation: '' });
    } catch { /* silent */ } finally { setChangingPassword(false); }
  };

  return (
    <div className="space-y-6 max-w-3xl mx-auto">
      <div>
        <h1 className="text-2xl font-bold text-gray-900 dark:text-white">{isRtl ? 'الملف الشخصي' : 'Profile'}</h1>
        <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">{isRtl ? 'إدارة معلوماتك الشخصية' : 'Manage your personal information'}</p>
      </div>

      {/* Avatar & Info */}
      <GlassCard>
        <div className="p-6">
          <div className="flex flex-col sm:flex-row items-center gap-6">
            <div className="relative group">
              <div className="w-24 h-24 rounded-2xl bg-gradient-to-br from-blue-500 to-cyan-600 flex items-center justify-center shadow-xl shadow-blue-500/30">
                <span className="text-3xl font-bold text-white">{user?.name?.charAt(0)?.toUpperCase() || 'A'}</span>
              </div>
              <button className="absolute inset-0 rounded-2xl bg-black/50 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity duration-200">
                <Camera className="w-6 h-6 text-white" />
              </button>
            </div>
            <div className="text-center sm:text-start">
              <h2 className="text-xl font-bold text-gray-900 dark:text-white">{user?.name || '-'}</h2>
              <p className="text-sm text-gray-500">{user?.email || '-'}</p>
              <div className="flex items-center gap-2 mt-2">
                {user?.roles?.map(role => (
                  <span key={role} className="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-gradient-to-r from-blue-500/10 to-cyan-500/10 text-blue-600 dark:text-blue-400">
                    <Shield className="w-3 h-3" />{role}
                  </span>
                ))}
              </div>
            </div>
          </div>
        </div>
      </GlassCard>

      {/* Profile Form */}
      <GlassCard>
        <div className="p-6">
          <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
            <User className="w-5 h-5 text-blue-500" />
            {isRtl ? 'المعلومات الشخصية' : 'Personal Information'}
          </h3>
          <div className="space-y-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{isRtl ? 'الاسم الكامل' : 'Full Name'}</label>
              <div className="relative">
                <User className="absolute start-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                <input type="text" value={profileData.name} onChange={(e) => setProfileData({ ...profileData, name: e.target.value })}
                  className="w-full ps-10 pe-4 py-2.5 rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-all" />
              </div>
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{isRtl ? 'البريد الإلكتروني' : 'Email'}</label>
              <div className="relative">
                <Mail className="absolute start-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                <input type="email" value={profileData.email} onChange={(e) => setProfileData({ ...profileData, email: e.target.value })}
                  className="w-full ps-10 pe-4 py-2.5 rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-all" />
              </div>
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{isRtl ? 'رقم الهاتف' : 'Phone Number'}</label>
              <div className="relative">
                <Phone className="absolute start-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                <input type="tel" value={profileData.phone} onChange={(e) => setProfileData({ ...profileData, phone: e.target.value })}
                  className="w-full ps-10 pe-4 py-2.5 rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-all" />
              </div>
            </div>
            <div className="flex justify-end pt-2">
              <button onClick={handleProfileSave} disabled={saving}
                className="flex items-center gap-2 px-6 py-2.5 rounded-xl bg-gradient-to-r from-blue-500 to-cyan-600 text-white text-sm font-medium shadow-lg shadow-blue-500/25 hover:shadow-xl transition-all duration-300 hover:scale-[1.02] disabled:opacity-50">
                <Save className="w-4 h-4" />{saving ? (isRtl ? 'جاري الحفظ...' : 'Saving...') : (isRtl ? 'حفظ التغييرات' : 'Save Changes')}
              </button>
            </div>
          </div>
        </div>
      </GlassCard>

      {/* Change Password */}
      <GlassCard>
        <div className="p-6">
          <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
            <Key className="w-5 h-5 text-amber-500" />
            {isRtl ? 'تغيير كلمة المرور' : 'Change Password'}
          </h3>
          <div className="space-y-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{isRtl ? 'كلمة المرور الحالية' : 'Current Password'}</label>
              <div className="relative">
                <Key className="absolute start-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                <input type={showCurrentPw ? 'text' : 'password'} value={passwordData.current_password}
                  onChange={(e) => setPasswordData({ ...passwordData, current_password: e.target.value })}
                  className="w-full ps-10 pe-10 py-2.5 rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-all" />
                <button type="button" onClick={() => setShowCurrentPw(!showCurrentPw)} className="absolute end-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                  {showCurrentPw ? <EyeOff className="w-4 h-4" /> : <Eye className="w-4 h-4" />}
                </button>
              </div>
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{isRtl ? 'كلمة المرور الجديدة' : 'New Password'}</label>
              <div className="relative">
                <Key className="absolute start-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                <input type={showNewPw ? 'text' : 'password'} value={passwordData.new_password}
                  onChange={(e) => setPasswordData({ ...passwordData, new_password: e.target.value })}
                  className="w-full ps-10 pe-10 py-2.5 rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-all" />
                <button type="button" onClick={() => setShowNewPw(!showNewPw)} className="absolute end-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                  {showNewPw ? <EyeOff className="w-4 h-4" /> : <Eye className="w-4 h-4" />}
                </button>
              </div>
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{isRtl ? 'تأكيد كلمة المرور' : 'Confirm New Password'}</label>
              <div className="relative">
                <Key className="absolute start-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                <input type="password" value={passwordData.new_password_confirmation}
                  onChange={(e) => setPasswordData({ ...passwordData, new_password_confirmation: e.target.value })}
                  className="w-full ps-10 pe-4 py-2.5 rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-all" />
              </div>
            </div>
            <div className="flex justify-end pt-2">
              <button onClick={handlePasswordChange} disabled={changingPassword}
                className="flex items-center gap-2 px-6 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-orange-600 text-white text-sm font-medium shadow-lg shadow-amber-500/25 hover:shadow-xl transition-all duration-300 hover:scale-[1.02] disabled:opacity-50">
                <Key className="w-4 h-4" />{changingPassword ? (isRtl ? 'جاري التغيير...' : 'Changing...') : (isRtl ? 'تغيير كلمة المرور' : 'Change Password')}
              </button>
            </div>
          </div>
        </div>
      </GlassCard>
    </div>
  );
}
