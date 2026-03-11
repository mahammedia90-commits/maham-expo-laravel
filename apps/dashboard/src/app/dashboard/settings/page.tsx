'use client';

import { useState, useEffect } from 'react';
import GlassCard from '@/components/ui/GlassCard';
import { expoApi } from '@/lib/api';
import { Settings as SettingsIcon, Save, Globe, CreditCard, MessageSquare, Shield } from 'lucide-react';

export default function SettingsPage() {
  const [locale, setLocale] = useState('ar');
  const [settings, setSettings] = useState<Record<string, string | number | boolean>>({});
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [activeTab, setActiveTab] = useState('general');
  const [saveMessage, setSaveMessage] = useState('');

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
    setSaveMessage('');
    try {
      // Clean settings: convert empty strings to null for optional fields
      const cleanSettings: Record<string, string | number | boolean | null> = {};
      for (const [key, value] of Object.entries(settings)) {
        cleanSettings[key] = value === '' ? null : value;
      }
      await expoApi.put('/manage/settings', cleanSettings);
      setSaveMessage(isRtl ? 'تم الحفظ بنجاح' : 'Settings saved successfully');
      fetchSettings();
      setTimeout(() => setSaveMessage(''), 3000);
    } catch (err: unknown) {
      const error = err as { response?: { data?: { message?: string; errors?: Record<string, string[]> } } };
      const msg = error?.response?.data?.message || (isRtl ? 'حدث خطأ أثناء الحفظ' : 'Error saving settings');
      setSaveMessage(msg);
      setTimeout(() => setSaveMessage(''), 5000);
    } finally { setSaving(false); }
  };

  const updateSetting = (key: string, value: string | number | boolean) => {
    setSettings(prev => ({ ...prev, [key]: value }));
  };

  const tabs = [
    { id: 'general', label: isRtl ? 'عام' : 'General', icon: Globe },
    { id: 'payment', label: isRtl ? 'الدفع' : 'Payment', icon: CreditCard },
    { id: 'sms', label: isRtl ? 'الرسائل' : 'SMS', icon: MessageSquare },
    { id: 'security', label: isRtl ? 'الأمان' : 'Security', icon: Shield },
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

  const SelectField = ({ label, settingKey, options }: { label: string; settingKey: string; options: { value: string; label: string }[] }) => (
    <div>
      <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{label}</label>
      <select value={String(settings[settingKey] ?? '')}
        onChange={(e) => updateSetting(settingKey, e.target.value)}
        className="w-full px-4 py-2.5 rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-all">
        {options.map(o => <option key={o.value} value={o.value}>{o.label}</option>)}
      </select>
    </div>
  );

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
        <div className="flex items-center gap-3">
          {saveMessage && (
            <span className={`text-sm font-medium ${saveMessage.includes('خطأ') || saveMessage.includes('Error') ? 'text-red-500' : 'text-emerald-500'}`}>
              {saveMessage}
            </span>
          )}
          <button onClick={handleSave} disabled={saving}
            className="flex items-center gap-2 px-6 py-2.5 rounded-xl bg-gradient-to-r from-blue-500 to-cyan-600 text-white text-sm font-medium shadow-lg shadow-blue-500/25 hover:shadow-xl transition-all duration-300 hover:scale-[1.02] disabled:opacity-50">
            <Save className="w-4 h-4" />{saving ? (isRtl ? 'جاري الحفظ...' : 'Saving...') : (isRtl ? 'حفظ الإعدادات' : 'Save Settings')}
          </button>
        </div>
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
                    <InputField label={isRtl ? 'اسم الموقع (EN)' : 'Site Name (EN)'} settingKey="site_name" />
                    <InputField label={isRtl ? 'اسم الموقع (AR)' : 'Site Name (AR)'} settingKey="site_name_ar" />
                    <InputField label={isRtl ? 'البريد الإلكتروني للتواصل' : 'Contact Email'} settingKey="contact_email" type="email" />
                    <InputField label={isRtl ? 'هاتف التواصل' : 'Contact Phone'} settingKey="contact_phone" />
                    <InputField label={isRtl ? 'بريد الدعم' : 'Support Email'} settingKey="support_email" type="email" />
                    <InputField label={isRtl ? 'العملة الافتراضية' : 'Default Currency'} settingKey="default_currency" placeholder="SAR" />
                    <InputField label={isRtl ? 'المنطقة الزمنية' : 'Timezone'} settingKey="timezone" placeholder="Asia/Riyadh" />
                    <InputField label={isRtl ? 'الحد الأقصى لطلبات الزيارة يومياً' : 'Max Visit Requests/Day'} settingKey="max_visit_requests_per_day" type="number" />
                    <InputField label={isRtl ? 'الحد الأقصى لطلبات التأجير لكل تاجر' : 'Max Rental Requests/Merchant'} settingKey="max_rental_requests_per_merchant" type="number" />
                  </div>
                  <div className="mt-4 space-y-2">
                    <ToggleField label={isRtl ? 'وضع الصيانة' : 'Maintenance Mode'} settingKey="maintenance_mode" description={isRtl ? 'تعطيل الوصول العام للموقع' : 'Disable public access to the site'} />
                    <ToggleField label={isRtl ? 'السماح بالتسجيل' : 'Allow Registration'} settingKey="allow_registration" description={isRtl ? 'السماح للمستخدمين الجدد بالتسجيل' : 'Allow new users to register'} />
                    <ToggleField label={isRtl ? 'الموافقة التلقائية على الملفات التجارية' : 'Auto Approve Profiles'} settingKey="auto_approve_profiles" description={isRtl ? 'قبول الملفات التجارية تلقائياً' : 'Automatically approve business profiles'} />
                  </div>
                </>
              )}

              {activeTab === 'payment' && (
                <>
                  <h3 className="text-lg font-semibold text-gray-900 dark:text-white">{isRtl ? 'إعدادات الدفع' : 'Payment Settings'}</h3>
                  <div className="space-y-4">
                    <ToggleField label={isRtl ? 'تفعيل الدفع' : 'Enable Payment'} settingKey="payment_enabled" description={isRtl ? 'تفعيل أو تعطيل بوابة الدفع' : 'Enable or disable the payment gateway'} />
                    <SelectField label={isRtl ? 'وضع بوابة الدفع' : 'Payment Gateway Mode'} settingKey="payment_gateway_mode"
                      options={[{ value: 'test', label: isRtl ? 'اختبار' : 'Test' }, { value: 'live', label: isRtl ? 'مباشر' : 'Live' }]} />
                    <InputField label={isRtl ? 'عملة الدفع الافتراضية' : 'Payment Default Currency'} settingKey="payment_default_currency" placeholder="SAR" />
                    <ToggleField label={isRtl ? 'الأمان ثلاثي الأبعاد (3D Secure)' : '3D Secure'} settingKey="payment_3d_secure" description={isRtl ? 'طلب تحقق إضافي من البطاقة' : 'Require additional card verification'} />
                  </div>
                </>
              )}

              {activeTab === 'sms' && (
                <>
                  <h3 className="text-lg font-semibold text-gray-900 dark:text-white">{isRtl ? 'إعدادات الرسائل' : 'SMS Settings'}</h3>
                  <div className="space-y-4">
                    <ToggleField label={isRtl ? 'تفعيل الرسائل' : 'Enable SMS'} settingKey="sms_enabled" description={isRtl ? 'تفعيل خدمة الرسائل القصيرة' : 'Enable SMS messaging service'} />
                    <SelectField label={isRtl ? 'القناة الافتراضية' : 'Default Channel'} settingKey="sms_default_channel"
                      options={[{ value: 'sms', label: 'SMS' }, { value: 'whatsapp', label: 'WhatsApp' }]} />
                    <InputField label={isRtl ? 'الحد الأقصى للمحاولات في الساعة' : 'Max Attempts Per Hour'} settingKey="sms_max_attempts_per_hour" type="number" />
                    <InputField label={isRtl ? 'طول رمز التحقق' : 'OTP Code Length'} settingKey="sms_code_length" type="number" />
                  </div>
                </>
              )}

              {activeTab === 'security' && (
                <>
                  <h3 className="text-lg font-semibold text-gray-900 dark:text-white">{isRtl ? 'إعدادات الأمان' : 'Security Settings'}</h3>
                  <div className="space-y-4">
                    <InputField label={isRtl ? 'النطاقات المسموحة (CORS)' : 'Allowed Origins (CORS)'} settingKey="cors_allowed_origins" placeholder="*" />
                    <ToggleField label={isRtl ? 'دعم بيانات الاعتماد (CORS)' : 'CORS Supports Credentials'} settingKey="cors_supports_credentials" />
                    <InputField label={isRtl ? 'مدة صلاحية CORS (ثواني)' : 'CORS Max Age (seconds)'} settingKey="cors_max_age" type="number" />
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
