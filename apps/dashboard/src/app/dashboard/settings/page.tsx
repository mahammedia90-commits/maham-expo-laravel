'use client';

import { useState, useEffect, useRef } from 'react';
import GlassCard from '@/components/ui/GlassCard';
import { expoApi } from '@/lib/api';
import { Settings as SettingsIcon, Save, Globe, CreditCard, MessageSquare, Bell, Shield, Palette, Upload, X } from 'lucide-react';

export default function SettingsPage() {
  const [locale, setLocale] = useState('ar');
  const [settings, setSettings] = useState<Record<string, string | number | boolean>>({});
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [activeTab, setActiveTab] = useState('general');
  const [logoFile, setLogoFile] = useState<File | null>(null);
  const [logoPreview, setLogoPreview] = useState<string>('');
  const [faviconFile, setFaviconFile] = useState<File | null>(null);
  const [faviconPreview, setFaviconPreview] = useState<string>('');
  const logoInputRef = useRef<HTMLInputElement>(null);
  const faviconInputRef = useRef<HTMLInputElement>(null);

  const isRtl = locale === 'ar';

  useEffect(() => { setLocale(localStorage.getItem('locale') || 'ar'); }, []);
  useEffect(() => { fetchSettings(); }, []);

  const fetchSettings = async () => {
    setLoading(true);
    try {
      const res = await expoApi.get('/manage/settings');
      setSettings(res.data.data || res.data || {});
    } catch { /* silent */ } finally { setLoading(false); }
  };

  const handleSave = async () => {
    setSaving(true);
    try {
      if (logoFile || faviconFile) {
        const fd = new FormData();
        Object.entries(settings).forEach(([key, value]) => {
          if (value !== null && value !== undefined) {
            fd.append(`settings[${key}]`, String(value));
          }
        });
        if (logoFile) fd.append('logo', logoFile);
        if (faviconFile) fd.append('favicon', faviconFile);
        fd.append('_method', 'PUT');
        await expoApi.post('/manage/settings', fd, {
          headers: { 'Content-Type': 'multipart/form-data' },
        });
      } else {
        await expoApi.put('/manage/settings', { settings });
      }
      setLogoFile(null);
      setFaviconFile(null);
      setLogoPreview('');
      setFaviconPreview('');
      fetchSettings();
    } catch { /* silent */ } finally { setSaving(false); }
  };

  const updateSetting = (key: string, value: string | number | boolean) => {
    setSettings(prev => ({ ...prev, [key]: value }));
  };

  const handleFileSelect = (type: 'logo' | 'favicon', e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = (ev) => {
      if (type === 'logo') {
        setLogoFile(file);
        setLogoPreview(ev.target?.result as string);
      } else {
        setFaviconFile(file);
        setFaviconPreview(ev.target?.result as string);
      }
    };
    reader.readAsDataURL(file);
  };

  const tabs = [
    { id: 'general', label: isRtl ? 'عام' : 'General', icon: Globe },
    { id: 'payment', label: isRtl ? 'الدفع' : 'Payment', icon: CreditCard },
    { id: 'sms', label: isRtl ? 'الرسائل' : 'SMS', icon: MessageSquare },
    { id: 'notifications', label: isRtl ? 'الإشعارات' : 'Notifications', icon: Bell },
    { id: 'security', label: isRtl ? 'الأمان' : 'Security', icon: Shield },
    { id: 'appearance', label: isRtl ? 'المظهر' : 'Appearance', icon: Palette },
  ];

  const InputField = ({ label, settingKey, type = 'text', placeholder = '' }: { label: string; settingKey: string; type?: string; placeholder?: string }) => (
    <div>
      <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{label}</label>
      <input type={type} value={String(settings[settingKey] ?? '')}
        onChange={(e) => updateSetting(settingKey, type === 'number' ? parseFloat(e.target.value) || 0 : e.target.value)}
        placeholder={placeholder}
        className="w-full px-4 py-2.5 rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-all" />
    </div>
  );

  const ToggleField = ({ label, description, settingKey }: { label: string; description?: string; settingKey: string }) => (
    <div className="flex items-center justify-between py-3 border-b border-gray-200/30 dark:border-white/5 last:border-0">
      <div>
        <span className="text-sm font-medium text-gray-700 dark:text-gray-300">{label}</span>
        {description && <p className="text-xs text-gray-500 mt-0.5">{description}</p>}
      </div>
      <label className="relative inline-flex items-center cursor-pointer">
        <input type="checkbox" checked={!!settings[settingKey]}
          onChange={(e) => updateSetting(settingKey, e.target.checked)} className="sr-only peer" />
        <div className="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-500/25 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-500"></div>
      </label>
    </div>
  );

  const ImageUploadField = ({ label, type, file, preview, existingUrl, inputRef }: {
    label: string;
    type: 'logo' | 'favicon';
    file: File | null;
    preview: string;
    existingUrl: string;
    inputRef: React.RefObject<HTMLInputElement | null>;
  }) => {
    const displayImage = preview || existingUrl;
    return (
      <div>
        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{label}</label>
        <div className="flex items-center gap-4">
          {displayImage ? (
            <div className="relative group">
              <div className={`${type === 'favicon' ? 'w-12 h-12' : 'w-32 h-16'} rounded-xl overflow-hidden border border-gray-200/60 dark:border-white/10 bg-white/50 dark:bg-white/5 flex items-center justify-center`}>
                <img src={displayImage} alt="" className="max-w-full max-h-full object-contain p-1" />
              </div>
              <button
                type="button"
                onClick={() => {
                  if (type === 'logo') { setLogoFile(null); setLogoPreview(''); updateSetting('logo_url', ''); }
                  else { setFaviconFile(null); setFaviconPreview(''); updateSetting('favicon_url', ''); }
                }}
                className="absolute -top-2 -end-2 p-1 rounded-full bg-red-500 text-white opacity-0 group-hover:opacity-100 transition-opacity shadow"
              >
                <X className="w-3 h-3" />
              </button>
            </div>
          ) : null}
          <button
            type="button"
            onClick={() => inputRef.current?.click()}
            className="flex items-center gap-2 px-4 py-2.5 rounded-xl border border-dashed border-gray-300/60 dark:border-white/10 hover:border-blue-400/60 hover:bg-blue-50/30 dark:hover:bg-blue-500/5 transition-all text-sm text-gray-600 dark:text-gray-400"
          >
            <Upload className="w-4 h-4" />
            {displayImage ? (isRtl ? 'تغيير' : 'Change') : (isRtl ? 'رفع' : 'Upload')}
          </button>
          <input ref={inputRef} type="file" accept="image/*" onChange={(e) => handleFileSelect(type, e)} className="hidden" />
          {file && <span className="text-xs text-blue-500">{file.name}</span>}
        </div>
      </div>
    );
  };

  if (loading) {
    return (
      <div className="space-y-6">
        <div className="h-8 w-48 bg-gray-200 dark:bg-gray-700 rounded-lg animate-pulse" />
        <div className="h-96 bg-white/50 dark:bg-white/5 rounded-2xl animate-pulse" />
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
            <SettingsIcon className="w-7 h-7" />
            {isRtl ? 'الإعدادات' : 'Settings'}
          </h1>
          <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">{isRtl ? 'إدارة إعدادات المنصة' : 'Manage platform settings'}</p>
        </div>
        <button onClick={handleSave} disabled={saving}
          className="flex items-center gap-2 px-6 py-2.5 rounded-xl bg-gradient-to-r from-blue-500 to-cyan-600 text-white text-sm font-medium shadow-lg shadow-blue-500/25 hover:shadow-xl transition-all duration-300 hover:scale-[1.02] disabled:opacity-50">
          <Save className="w-4 h-4" />{saving ? (isRtl ? 'جاري الحفظ...' : 'Saving...') : (isRtl ? 'حفظ الإعدادات' : 'Save Settings')}
        </button>
      </div>

      <div className="flex flex-col lg:flex-row gap-6">
        {/* Tabs Sidebar */}
        <div className="lg:w-56 shrink-0">
          <GlassCard>
            <nav className="space-y-1 p-2">
              {tabs.map(tab => (
                <button key={tab.id} onClick={() => setActiveTab(tab.id)}
                  className={`w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 ${
                    activeTab === tab.id
                      ? 'bg-gradient-to-r from-blue-500/10 to-cyan-500/10 text-blue-600 dark:text-blue-400 shadow-sm'
                      : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100/50 dark:hover:bg-white/5'
                  }`}>
                  <tab.icon className="w-4 h-4" />
                  {tab.label}
                </button>
              ))}
            </nav>
          </GlassCard>
        </div>

        {/* Content */}
        <div className="flex-1">
          <GlassCard>
            <div className="p-6 space-y-6">
              {activeTab === 'general' && (
                <>
                  <h3 className="text-lg font-semibold text-gray-900 dark:text-white">{isRtl ? 'الإعدادات العامة' : 'General Settings'}</h3>
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <InputField label={isRtl ? 'اسم المنصة (EN)' : 'Platform Name (EN)'} settingKey="platform_name" />
                    <InputField label={isRtl ? 'اسم المنصة (AR)' : 'Platform Name (AR)'} settingKey="platform_name_ar" />
                    <InputField label={isRtl ? 'البريد الإلكتروني' : 'Support Email'} settingKey="support_email" type="email" />
                    <InputField label={isRtl ? 'رقم الهاتف' : 'Support Phone'} settingKey="support_phone" />
                    <InputField label={isRtl ? 'العملة الافتراضية' : 'Default Currency'} settingKey="default_currency" placeholder="SAR" />
                    <InputField label={isRtl ? 'المنطقة الزمنية' : 'Timezone'} settingKey="timezone" placeholder="Asia/Riyadh" />
                  </div>
                </>
              )}

              {activeTab === 'payment' && (
                <>
                  <h3 className="text-lg font-semibold text-gray-900 dark:text-white">{isRtl ? 'إعدادات الدفع' : 'Payment Settings'}</h3>
                  <div className="space-y-4">
                    <ToggleField label={isRtl ? 'تفعيل بوابة Tap' : 'Enable Tap Gateway'} settingKey="tap_enabled" description={isRtl ? 'بوابة الدفع الرئيسية' : 'Primary payment gateway'} />
                    <InputField label={isRtl ? 'مفتاح Tap السري' : 'Tap Secret Key'} settingKey="tap_secret_key" type="password" />
                    <InputField label={isRtl ? 'مفتاح Tap العام' : 'Tap Public Key'} settingKey="tap_public_key" />
                    <ToggleField label={isRtl ? 'وضع الاختبار' : 'Sandbox Mode'} settingKey="payment_sandbox" description={isRtl ? 'استخدام بيئة الاختبار' : 'Use test environment'} />
                    <InputField label={isRtl ? 'نسبة الضريبة %' : 'Tax Rate %'} settingKey="tax_rate" type="number" />
                  </div>
                </>
              )}

              {activeTab === 'sms' && (
                <>
                  <h3 className="text-lg font-semibold text-gray-900 dark:text-white">{isRtl ? 'إعدادات الرسائل' : 'SMS Settings'}</h3>
                  <div className="space-y-4">
                    <ToggleField label={isRtl ? 'تفعيل Twilio' : 'Enable Twilio SMS'} settingKey="twilio_enabled" />
                    <InputField label="Twilio Account SID" settingKey="twilio_sid" />
                    <InputField label="Twilio Auth Token" settingKey="twilio_auth_token" type="password" />
                    <InputField label={isRtl ? 'رقم المرسل' : 'Twilio From Number'} settingKey="twilio_from" placeholder="+966..." />
                    <InputField label={isRtl ? 'مدة OTP (ثواني)' : 'OTP Expiry (seconds)'} settingKey="otp_expiry" type="number" />
                  </div>
                </>
              )}

              {activeTab === 'notifications' && (
                <>
                  <h3 className="text-lg font-semibold text-gray-900 dark:text-white">{isRtl ? 'إعدادات الإشعارات' : 'Notification Settings'}</h3>
                  <div className="space-y-2">
                    <ToggleField label={isRtl ? 'إشعارات البريد الإلكتروني' : 'Email Notifications'} settingKey="email_notifications" />
                    <ToggleField label={isRtl ? 'إشعارات SMS' : 'SMS Notifications'} settingKey="sms_notifications" />
                    <ToggleField label={isRtl ? 'إشعارات الدفع' : 'Push Notifications'} settingKey="push_notifications" />
                    <ToggleField label={isRtl ? 'إشعارات الطلبات الجديدة' : 'New Request Notifications'} settingKey="notify_new_requests" />
                    <ToggleField label={isRtl ? 'إشعارات الدفع' : 'Payment Notifications'} settingKey="notify_payments" />
                    <ToggleField label={isRtl ? 'إشعارات التقييمات' : 'Rating Notifications'} settingKey="notify_ratings" />
                  </div>
                </>
              )}

              {activeTab === 'security' && (
                <>
                  <h3 className="text-lg font-semibold text-gray-900 dark:text-white">{isRtl ? 'إعدادات الأمان' : 'Security Settings'}</h3>
                  <div className="space-y-4">
                    <ToggleField label={isRtl ? 'التحقق بخطوتين' : 'Two-Factor Authentication'} settingKey="two_factor_enabled" />
                    <InputField label={isRtl ? 'مدة انتهاء الجلسة (دقائق)' : 'Session Timeout (minutes)'} settingKey="session_timeout" type="number" />
                    <InputField label={isRtl ? 'الحد الأقصى لمحاولات تسجيل الدخول' : 'Max Login Attempts'} settingKey="max_login_attempts" type="number" />
                    <ToggleField label={isRtl ? 'فرض كلمة مرور قوية' : 'Enforce Strong Passwords'} settingKey="strong_passwords" />
                    <InputField label={isRtl ? 'النطاقات المسموحة (CORS)' : 'Allowed Origins (CORS)'} settingKey="cors_origins" placeholder="https://dashboard.mahamexpo.sa" />
                  </div>
                </>
              )}

              {activeTab === 'appearance' && (
                <>
                  <h3 className="text-lg font-semibold text-gray-900 dark:text-white">{isRtl ? 'إعدادات المظهر' : 'Appearance Settings'}</h3>
                  <div className="space-y-6">
                    <ImageUploadField
                      label={isRtl ? 'شعار المنصة' : 'Platform Logo'}
                      type="logo"
                      file={logoFile}
                      preview={logoPreview}
                      existingUrl={String(settings.logo_url || '')}
                      inputRef={logoInputRef}
                    />
                    <ImageUploadField
                      label={isRtl ? 'أيقونة الموقع (Favicon)' : 'Favicon'}
                      type="favicon"
                      file={faviconFile}
                      preview={faviconPreview}
                      existingUrl={String(settings.favicon_url || '')}
                      inputRef={faviconInputRef}
                    />
                    <InputField label={isRtl ? 'اللون الرئيسي' : 'Primary Color'} settingKey="primary_color" placeholder="#3b82f6" />
                    <ToggleField label={isRtl ? 'الوضع الداكن افتراضي' : 'Default Dark Mode'} settingKey="default_dark_mode" />
                    <ToggleField label={isRtl ? 'إظهار شعار التذييل' : 'Show Footer Logo'} settingKey="show_footer_logo" />
                  </div>
                </>
              )}
            </div>
          </GlassCard>
        </div>
      </div>
    </div>
  );
}
