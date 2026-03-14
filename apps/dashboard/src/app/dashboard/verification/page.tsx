'use client';

import { useState, useEffect, useRef } from 'react';
import { expoApi, ApiResponse } from '@/lib/api';
import {
  User, Building2, Landmark, FileUp, Scale,
  CheckCircle2, AlertCircle, Clock, XCircle,
  Upload, Loader2, ChevronDown, ShieldCheck,
  ArrowLeft, ArrowRight,
} from 'lucide-react';

/* ─── Types ─── */
interface Profile {
  id: string;
  full_name: string;
  company_name: string;
  company_name_en: string;
  commercial_registration_number: string;
  national_id_number: string;
  contact_phone: string;
  contact_email: string;
  date_of_birth: string;
  nationality: string;
  city: string;
  company_address: string;
  website: string;
  business_activity_type: string;
  establishment_year: number;
  vat_number: string;
  national_address: string;
  employee_count: string;
  bank_name: string;
  iban: string;
  account_holder_name: string;
  account_number: string;
  status: string;
  status_label: string;
  is_approved: boolean;
  is_pending: boolean;
  is_rejected: boolean;
  rejection_reason: string;
  kyc_step: number;
  kyc_submitted_at: string;
  legal_declaration_accepted: boolean;
  national_id_image: string;
  commercial_registration_image: string;
  vat_certificate_image: string;
  authorization_letter_image: string;
  national_address_doc: string;
  bank_letter_image: string;
  company_profile_doc: string;
  product_catalog_doc: string;
}

/* ─── Translations ─── */
const tr = {
  ar: {
    title: 'توثيق الحساب',
    subtitle: 'Know Your Customer — مطلوب قبل الحجز',
    step1: 'البيانات الشخصية', step2: 'بيانات الشركة', step3: 'الحساب البنكي',
    step4: 'رفع المستندات', step5: 'الإقرار القانوني',
    next: 'التالي', prev: 'السابق', submit: 'إرسال الطلب',
    step1Note: 'يجب أن تتطابق البيانات مع المعلومات المسجلة في أبشر ومركز المعلومات الوطني',
    fullName: 'الاسم الكامل', nationalId: 'رقم الهوية / الإقامة',
    phone: 'رقم الجوال', email: 'البريد الإلكتروني',
    dob: 'تاريخ الميلاد', nationality: 'الجنسية', city: 'المدينة', address: 'العنوان',
    companyName: 'اسم الشركة / المؤسسة', crNumber: 'رقم السجل التجاري',
    activityType: 'نوع النشاط', estYear: 'سنة التأسيس',
    vatNumber: 'الرقم الضريبي (VAT)', natAddress: 'العنوان الوطني (اختياري)',
    employees: 'عدد الموظفين', website: 'الموقع الإلكتروني', selectActivity: 'اختر النشاط',
    step3Note: 'بيانات الحساب البنكي مشفرة بتقنية AES-256 ومحمية بالكامل وفق معايير PCI DSS',
    bankName: 'اسم البنك', iban: 'رقم الآيبان (IBAN)',
    accountHolder: 'اسم صاحب الحساب', accountNumber: 'رقم الحساب',
    bankNote: 'يجب أن يكون الحساب البنكي باسم الشركة المسجلة. سيتم تحويل مستحقاتك المالية إلى هذا الحساب.',
    step4Note: 'المستندات المطلوبة لإتمام التحقق. يجب تقديمها خلال 10 أيام من تاريخ التسجيل.',
    uploadFile: 'رفع الملف', uploaded: 'تم الرفع', required: 'مطلوب', optional: 'اختياري',
    declaration: 'الإقرار القانوني',
    declarationText: 'أقر وأتعهد بأن جميع البيانات والمعلومات المقدمة صحيحة ودقيقة، وأنني أتحمل المسؤولية الكاملة عن أي معلومات غير صحيحة. كما أوافق على شروط وأحكام منصة مهام إكسبو وسياسة الخصوصية.',
    acceptDeclaration: 'أوافق على الإقرار القانوني وشروط الاستخدام',
    statusPending: 'قيد المراجعة', statusApproved: 'تم التوثيق', statusRejected: 'مرفوض',
    pendingMsg: 'تم إرسال طلب التوثيق وهو قيد المراجعة. سيتم إعلامك فور الانتهاء.',
    approvedMsg: 'تم توثيق حسابك بنجاح. يمكنك الآن حجز المساحات والفعاليات.',
    rejectedMsg: 'تم رفض طلب التوثيق. يرجى تصحيح البيانات وإعادة الإرسال.',
    rejectionReason: 'سبب الرفض', editAndResubmit: 'تعديل وإعادة الإرسال',
  },
  en: {
    title: 'Account Verification',
    subtitle: 'Know Your Customer — Required before booking',
    step1: 'Personal Data', step2: 'Company Data', step3: 'Bank Account',
    step4: 'Upload Documents', step5: 'Legal Declaration',
    next: 'Next', prev: 'Previous', submit: 'Submit',
    step1Note: 'Data must match your records in Absher and the National Information Center',
    fullName: 'Full Name', nationalId: 'National ID / Iqama Number',
    phone: 'Phone Number', email: 'Email',
    dob: 'Date of Birth', nationality: 'Nationality', city: 'City', address: 'Address',
    companyName: 'Company / Establishment Name', crNumber: 'Commercial Registration Number',
    activityType: 'Business Activity Type', estYear: 'Establishment Year',
    vatNumber: 'VAT Number', natAddress: 'National Address (optional)',
    employees: 'Number of Employees', website: 'Website', selectActivity: 'Select activity',
    step3Note: 'Bank data is encrypted with AES-256 and fully protected per PCI DSS standards',
    bankName: 'Bank Name', iban: 'IBAN',
    accountHolder: 'Account Holder Name', accountNumber: 'Account Number',
    bankNote: 'Bank account must be in the registered company name. Payments will be transferred to this account.',
    step4Note: 'Required documents for verification. Must be submitted within 10 days of registration.',
    uploadFile: 'Upload', uploaded: 'Uploaded', required: 'Required', optional: 'Optional',
    declaration: 'Legal Declaration',
    declarationText: 'I declare and undertake that all data and information provided is true and accurate, and I bear full responsibility for any incorrect information. I also agree to the terms and conditions of Maham Expo platform and the privacy policy.',
    acceptDeclaration: 'I accept the legal declaration and terms of use',
    statusPending: 'Under Review', statusApproved: 'Verified', statusRejected: 'Rejected',
    pendingMsg: 'Your verification request has been submitted and is under review.',
    approvedMsg: 'Your account has been verified. You can now book spaces and events.',
    rejectedMsg: 'Your verification request was rejected. Please correct and resubmit.',
    rejectionReason: 'Rejection Reason', editAndResubmit: 'Edit & Resubmit',
  },
};

const activityTypes = [
  { ar: 'تجارة عامة', en: 'General Trading' },
  { ar: 'مواد غذائية', en: 'Food & Beverages' },
  { ar: 'ملابس وأزياء', en: 'Clothing & Fashion' },
  { ar: 'إلكترونيات', en: 'Electronics' },
  { ar: 'عطور ومستحضرات', en: 'Perfumes & Cosmetics' },
  { ar: 'حرف يدوية', en: 'Handicrafts' },
  { ar: 'خدمات', en: 'Services' },
  { ar: 'أخرى', en: 'Other' },
];

const saudiBanks = [
  'البنك الأهلي السعودي', 'مصرف الراجحي', 'بنك الرياض', 'بنك الإنماء',
  'البنك السعودي الفرنسي', 'البنك العربي الوطني', 'بنك الجزيرة',
  'بنك البلاد', 'البنك السعودي للاستثمار', 'بنك ساب',
];

const documents = [
  { key: 'national_id_image', ar: 'صورة الهوية الوطنية / الإقامة', arDesc: 'صورة واضحة للوجهين', en: 'National ID / Iqama', enDesc: 'Clear copy of both sides', req: true },
  { key: 'commercial_registration_image', ar: 'السجل التجاري ساري المفعول', arDesc: 'يجب أن يكون ساري المفعول', en: 'Commercial Registration', enDesc: 'Must be valid', req: true },
  { key: 'vat_certificate_image', ar: 'شهادة تسجيل ضريبة القيمة المضافة', arDesc: 'صادرة من هيئة الزكاة والضريبة والجمارك (ZATCA)', en: 'VAT Registration Certificate', enDesc: 'Issued by ZATCA', req: true },
  { key: 'authorization_letter_image', ar: 'تفويض / وكالة رسمية', arDesc: 'للمفوض بالتوقيع على العقود', en: 'Authorization Letter', enDesc: 'For authorized signatory', req: true },
  { key: 'national_address_doc', ar: 'العنوان الوطني', arDesc: 'صادر من البريد السعودي — اختياري', en: 'National Address', enDesc: 'From Saudi Post — optional', req: false },
  { key: 'bank_letter_image', ar: 'خطاب تعريف بنكي', arDesc: 'يؤكد بيانات الحساب البنكي — اختياري', en: 'Bank Letter', enDesc: 'Confirms bank details — optional', req: false },
  { key: 'company_profile_doc', ar: 'بروفايل الشركة / الملف التعريفي', arDesc: 'ملف تعريفي للشركة يتضمن المنتجات والخدمات المقدمة', en: 'Company Profile', enDesc: 'Overview of products and services', req: true },
  { key: 'product_catalog_doc', ar: 'كتالوج المنتجات / الخدمات', arDesc: 'ملف يوضح المنتجات أو الخدمات التي ستعرضها في المعرض', en: 'Product / Service Catalog', enDesc: 'Products or services to exhibit', req: true },
];

/* ─── Component ─── */
export default function VerificationPage() {
  const [locale, setLocale] = useState('ar');
  const [mounted, setMounted] = useState(false);
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [step, setStep] = useState(1);
  const [error, setError] = useState('');
  const [profile, setProfile] = useState<Profile | null>(null);
  const [submitted, setSubmitted] = useState(false);
  const fileInputRefs = useRef<Record<string, HTMLInputElement | null>>({});
  const [uploadedFiles, setUploadedFiles] = useState<Record<string, File | null>>({});

  const [form, setForm] = useState({
    full_name: '', national_id_number: '', contact_phone: '', contact_email: '',
    date_of_birth: '', nationality: 'سعودي', city: '', address: '',
    company_name: '', commercial_registration_number: '', business_activity_type: '',
    establishment_year: '', vat_number: '', national_address: '', employee_count: '', website: '',
    bank_name: '', iban: '', account_holder_name: '', account_number: '',
    legal_declaration_accepted: false,
  });

  const t2 = tr[locale as keyof typeof tr] || tr.ar;
  const isRtl = locale === 'ar';

  useEffect(() => {
    setMounted(true);
    const loc = localStorage.getItem('locale') || 'ar';
    setLocale(loc);
    loadProfile();
  }, []);

  const loadProfile = async () => {
    try {
      const res = await expoApi.get<ApiResponse<Profile>>('/profile');
      const p = res.data.data;
      setProfile(p);
      setForm({
        full_name: p.full_name || '', national_id_number: p.national_id_number || '',
        contact_phone: p.contact_phone || '', contact_email: p.contact_email || '',
        date_of_birth: p.date_of_birth || '', nationality: p.nationality || 'سعودي',
        city: p.city || '', address: p.company_address || '',
        company_name: p.company_name_en || p.company_name || '',
        commercial_registration_number: p.commercial_registration_number || '',
        business_activity_type: p.business_activity_type || '',
        establishment_year: p.establishment_year ? String(p.establishment_year) : '',
        vat_number: p.vat_number || '', national_address: p.national_address || '',
        employee_count: p.employee_count || '', website: p.website || '',
        bank_name: p.bank_name || '', iban: p.iban || '',
        account_holder_name: p.account_holder_name || '', account_number: p.account_number || '',
        legal_declaration_accepted: p.legal_declaration_accepted || false,
      });
      if (p.kyc_submitted_at) setSubmitted(true);
      setStep(Math.min(p.kyc_step || 1, 5));
    } catch {
      // No profile yet
    } finally {
      setLoading(false);
    }
  };

  const setField = (key: string, value: string | boolean) => setForm(prev => ({ ...prev, [key]: value }));

  const saveStep = async (nextStep?: number) => {
    setError('');
    setSaving(true);
    try {
      if (step === 4) {
        const fd = new FormData();
        fd.append('step', '4');
        Object.entries(uploadedFiles).forEach(([key, file]) => { if (file) fd.append(key, file); });
        const res = await expoApi.post<ApiResponse<Profile>>('/profile/kyc-step', fd, { headers: { 'Content-Type': 'multipart/form-data' } });
        setProfile(res.data.data);
      } else if (step === 5) {
        const res = await expoApi.post<ApiResponse<Profile>>('/profile/kyc-step', { step: 5, legal_declaration_accepted: form.legal_declaration_accepted });
        setProfile(res.data.data);
        setSubmitted(true);
        return;
      } else {
        const stepData: Record<string, unknown> = { step };
        if (step === 1) Object.assign(stepData, { full_name: form.full_name, national_id_number: form.national_id_number, contact_phone: form.contact_phone, contact_email: form.contact_email, date_of_birth: form.date_of_birth || null, nationality: form.nationality, city: form.city, address: form.address });
        else if (step === 2) Object.assign(stepData, { company_name: form.company_name, commercial_registration_number: form.commercial_registration_number, business_activity_type: form.business_activity_type, establishment_year: form.establishment_year ? Number(form.establishment_year) : null, vat_number: form.vat_number, national_address: form.national_address, employee_count: form.employee_count, website: form.website });
        else if (step === 3) Object.assign(stepData, { bank_name: form.bank_name, iban: form.iban, account_holder_name: form.account_holder_name, account_number: form.account_number });
        const res = await expoApi.post<ApiResponse<Profile>>('/profile/kyc-step', stepData);
        setProfile(res.data.data);
      }
      if (nextStep) setStep(nextStep);
    } catch (err: unknown) {
      const e = err as { response?: { data?: { message?: string } } };
      setError(e.response?.data?.message || 'حدث خطأ');
    } finally {
      setSaving(false);
    }
  };

  const handleNext = () => saveStep(step + 1);
  const handlePrev = () => { setError(''); setStep(step - 1); };
  const handleSubmit = () => saveStep();

  const inputCls = 'w-full py-3 px-4 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-white/5 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 transition-all text-sm';
  const labelCls = 'block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5';
  const selectCls = inputCls + ' appearance-none cursor-pointer';

  const steps = [
    { num: 1, icon: User, label: t2.step1 },
    { num: 2, icon: Building2, label: t2.step2 },
    { num: 3, icon: Landmark, label: t2.step3 },
    { num: 4, icon: FileUp, label: t2.step4 },
    { num: 5, icon: Scale, label: t2.step5 },
  ];

  if (!mounted || loading) return <div className="flex items-center justify-center min-h-[60vh]"><Loader2 className="w-8 h-8 animate-spin text-blue-500" /></div>;

  // ── Status Banner ──
  if (submitted || profile?.is_approved || (profile?.is_pending && profile?.kyc_submitted_at)) {
    const isApproved = profile?.is_approved;
    const isRejected = profile?.is_rejected;
    return (
      <div className="max-w-2xl mx-auto">
        <div className="text-center mb-8">
          <h1 className="text-2xl font-bold text-gray-900 dark:text-white">{t2.title}</h1>
          <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">{t2.subtitle}</p>
        </div>
        <div className={`rounded-2xl border p-8 text-center ${isApproved ? 'border-green-500/20 bg-green-50 dark:bg-green-500/10' : isRejected ? 'border-red-500/20 bg-red-50 dark:bg-red-500/10' : 'border-yellow-500/20 bg-yellow-50 dark:bg-yellow-500/10'}`}>
          <div className={`w-16 h-16 rounded-full mx-auto mb-4 flex items-center justify-center ${isApproved ? 'bg-green-500' : isRejected ? 'bg-red-500' : 'bg-yellow-500'}`}>
            {isApproved ? <CheckCircle2 className="w-8 h-8 text-white" /> : isRejected ? <XCircle className="w-8 h-8 text-white" /> : <Clock className="w-8 h-8 text-white" />}
          </div>
          <h2 className={`text-xl font-bold mb-2 ${isApproved ? 'text-green-700 dark:text-green-400' : isRejected ? 'text-red-700 dark:text-red-400' : 'text-yellow-700 dark:text-yellow-400'}`}>
            {isApproved ? t2.statusApproved : isRejected ? t2.statusRejected : t2.statusPending}
          </h2>
          <p className="text-gray-600 dark:text-gray-300 mb-4">{isApproved ? t2.approvedMsg : isRejected ? t2.rejectedMsg : t2.pendingMsg}</p>
          {isRejected && profile?.rejection_reason && (
            <div className="mt-4 p-4 rounded-xl bg-red-100 dark:bg-red-500/20 text-start">
              <p className="text-sm font-semibold text-red-700 dark:text-red-400 mb-1">{t2.rejectionReason}:</p>
              <p className="text-sm text-red-600 dark:text-red-300">{profile.rejection_reason}</p>
            </div>
          )}
          {isRejected && (
            <button onClick={() => { setSubmitted(false); setStep(1); }} className="mt-6 px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm transition-colors">
              {t2.editAndResubmit}
            </button>
          )}
        </div>
      </div>
    );
  }

  return (
    <div className="max-w-3xl mx-auto">
      {/* Header */}
      <div className="text-center mb-6">
        <div className="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-500 to-purple-600 shadow-lg shadow-blue-500/25 mb-3">
          <ShieldCheck className="w-7 h-7 text-white" />
        </div>
        <h1 className="text-2xl font-bold text-gray-900 dark:text-white">{t2.title}</h1>
        <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">{t2.subtitle}</p>
      </div>

      {/* Step Indicator */}
      <div className="flex items-center justify-center gap-0 mb-8 overflow-x-auto pb-2">
        {steps.map((s, i) => (
          <div key={s.num} className="flex items-center">
            <div className="flex flex-col items-center gap-1">
              <div className={`w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold transition-all ${step > s.num ? 'bg-green-500 text-white shadow-lg shadow-green-500/25' : step === s.num ? 'bg-gradient-to-br from-blue-500 to-purple-600 text-white shadow-lg shadow-blue-500/25' : 'bg-gray-200/60 dark:bg-white/10 text-gray-400 dark:text-gray-600'}`}>
                {step > s.num ? <CheckCircle2 className="w-5 h-5" /> : <s.icon className="w-5 h-5" />}
              </div>
              <span className={`text-[10px] font-medium whitespace-nowrap ${step >= s.num ? 'text-gray-700 dark:text-gray-300' : 'text-gray-400 dark:text-gray-600'}`}>{s.label}</span>
            </div>
            {i < 4 && <div className={`w-8 sm:w-12 h-0.5 mx-0.5 mb-4 rounded-full transition-colors ${step > s.num ? 'bg-green-500' : 'bg-gray-200/60 dark:bg-white/10'}`} />}
          </div>
        ))}
      </div>

      {/* Card */}
      <div className="rounded-2xl border border-gray-200/60 dark:border-white/10 bg-white dark:bg-gray-900/50 shadow-xl p-6 sm:p-8">
        {error && (
          <div className="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 flex items-start gap-3">
            <AlertCircle className="w-5 h-5 text-red-500 shrink-0 mt-0.5" />
            <p className="text-sm text-red-600 dark:text-red-400">{error}</p>
          </div>
        )}

        {/* Step 1: Personal Data */}
        {step === 1 && (
          <div className="space-y-5">
            <div>
              <h2 className="text-lg font-bold text-gray-900 dark:text-white">{t2.step1}</h2>
              <p className="text-xs text-gray-500 dark:text-gray-400 mt-1">{t2.step1Note}</p>
            </div>
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div className="sm:col-span-2"><label className={labelCls}>{t2.fullName}</label><input type="text" value={form.full_name} onChange={e => setField('full_name', e.target.value)} className={inputCls} /></div>
              <div><label className={labelCls}>{t2.nationalId}</label><input type="text" value={form.national_id_number} onChange={e => setField('national_id_number', e.target.value)} className={inputCls} dir="ltr" placeholder="1XXXXXXXXX" /></div>
              <div><label className={labelCls}>{t2.phone}</label><input type="tel" value={form.contact_phone} onChange={e => setField('contact_phone', e.target.value)} className={inputCls} dir="ltr" /></div>
              <div><label className={labelCls}>{t2.email}</label><input type="email" value={form.contact_email} onChange={e => setField('contact_email', e.target.value)} className={inputCls} dir="ltr" placeholder="email@example.com" /></div>
              <div><label className={labelCls}>{t2.dob}</label><input type="date" value={form.date_of_birth} onChange={e => setField('date_of_birth', e.target.value)} className={inputCls} /></div>
              <div><label className={labelCls}>{t2.nationality}</label><input type="text" value={form.nationality} onChange={e => setField('nationality', e.target.value)} className={inputCls} /></div>
              <div><label className={labelCls}>{t2.city}</label><input type="text" value={form.city} onChange={e => setField('city', e.target.value)} className={inputCls} /></div>
              <div className="sm:col-span-2"><label className={labelCls}>{t2.address}</label><input type="text" value={form.address} onChange={e => setField('address', e.target.value)} className={inputCls} /></div>
            </div>
          </div>
        )}

        {/* Step 2: Company Data */}
        {step === 2 && (
          <div className="space-y-5">
            <h2 className="text-lg font-bold text-gray-900 dark:text-white">{t2.step2}</h2>
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div className="sm:col-span-2"><label className={labelCls}>{t2.companyName}</label><input type="text" value={form.company_name} onChange={e => setField('company_name', e.target.value)} className={inputCls} /></div>
              <div><label className={labelCls}>{t2.crNumber}</label><input type="text" value={form.commercial_registration_number} onChange={e => setField('commercial_registration_number', e.target.value)} className={inputCls} dir="ltr" placeholder="XXXXXXXXXX" /></div>
              <div>
                <label className={labelCls}>{t2.activityType}</label>
                <div className="relative">
                  <select value={form.business_activity_type} onChange={e => setField('business_activity_type', e.target.value)} className={selectCls}>
                    <option value="">{t2.selectActivity}</option>
                    {activityTypes.map(a => <option key={a.en} value={a.en}>{locale === 'ar' ? a.ar : a.en}</option>)}
                  </select>
                  <ChevronDown className="absolute end-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" />
                </div>
              </div>
              <div><label className={labelCls}>{t2.estYear}</label><input type="number" value={form.establishment_year} onChange={e => setField('establishment_year', e.target.value)} className={inputCls} dir="ltr" min="1900" max={new Date().getFullYear()} placeholder="2020" /></div>
              <div><label className={labelCls}>{t2.vatNumber}</label><input type="text" value={form.vat_number} onChange={e => setField('vat_number', e.target.value)} className={inputCls} dir="ltr" placeholder="3XXXXXXXXXXXXX003" /></div>
              <div><label className={labelCls}>{t2.natAddress}</label><input type="text" value={form.national_address} onChange={e => setField('national_address', e.target.value)} className={inputCls} /></div>
              <div>
                <label className={labelCls}>{t2.employees}</label>
                <div className="relative">
                  <select value={form.employee_count} onChange={e => setField('employee_count', e.target.value)} className={selectCls}>
                    <option value="">—</option>
                    {['1-5','6-10','10-50','50-100','100-500','500+'].map(v => <option key={v} value={v}>{v}</option>)}
                  </select>
                  <ChevronDown className="absolute end-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" />
                </div>
              </div>
              <div className="sm:col-span-2"><label className={labelCls}>{t2.website}</label><input type="text" value={form.website} onChange={e => setField('website', e.target.value)} className={inputCls} dir="ltr" placeholder="www.example.com" /></div>
            </div>
          </div>
        )}

        {/* Step 3: Bank Account */}
        {step === 3 && (
          <div className="space-y-5">
            <div>
              <h2 className="text-lg font-bold text-gray-900 dark:text-white">{t2.step3}</h2>
              <div className="flex items-center gap-2 mt-2 p-3 rounded-xl bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/20">
                <ShieldCheck className="w-4 h-4 text-blue-500 shrink-0" />
                <p className="text-xs text-blue-700 dark:text-blue-300">{t2.step3Note}</p>
              </div>
            </div>
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div className="sm:col-span-2">
                <label className={labelCls}>{t2.bankName}</label>
                <div className="relative">
                  <select value={form.bank_name} onChange={e => setField('bank_name', e.target.value)} className={selectCls}>
                    <option value="">—</option>
                    {saudiBanks.map(b => <option key={b} value={b}>{b}</option>)}
                  </select>
                  <ChevronDown className="absolute end-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" />
                </div>
              </div>
              <div className="sm:col-span-2"><label className={labelCls}>{t2.iban}</label><input type="text" value={form.iban} onChange={e => setField('iban', e.target.value)} className={inputCls} dir="ltr" placeholder="SA XXXX XXXX XXXX XXXX XXXX" /></div>
              <div><label className={labelCls}>{t2.accountHolder}</label><input type="text" value={form.account_holder_name} onChange={e => setField('account_holder_name', e.target.value)} className={inputCls} /></div>
              <div><label className={labelCls}>{t2.accountNumber}</label><input type="text" value={form.account_number} onChange={e => setField('account_number', e.target.value)} className={inputCls} dir="ltr" /></div>
            </div>
            <p className="text-xs text-gray-500 dark:text-gray-400 mt-2">{t2.bankNote}</p>
          </div>
        )}

        {/* Step 4: Document Uploads */}
        {step === 4 && (
          <div className="space-y-5">
            <div>
              <h2 className="text-lg font-bold text-gray-900 dark:text-white">{t2.step4}</h2>
              <p className="text-xs text-gray-500 dark:text-gray-400 mt-1">{t2.step4Note}</p>
            </div>
            <div className="space-y-3">
              {documents.map(doc => {
                const docKey = doc.key as keyof Profile;
                const hasExisting = profile && profile[docKey];
                const hasNew = uploadedFiles[doc.key];
                const isUploaded = hasExisting || hasNew;
                return (
                  <div key={doc.key} className={`p-4 rounded-xl border transition-colors ${isUploaded ? 'border-green-500/30 bg-green-50/50 dark:bg-green-500/5' : 'border-gray-200 dark:border-white/10'}`}>
                    <div className="flex items-start justify-between gap-3">
                      <div className="flex-1 min-w-0">
                        <div className="flex items-center gap-2">
                          <h3 className="text-sm font-semibold text-gray-900 dark:text-white">{locale === 'ar' ? doc.ar : doc.en}</h3>
                          <span className={`text-[10px] px-2 py-0.5 rounded-full font-medium ${doc.req ? 'bg-red-100 text-red-600 dark:bg-red-500/20 dark:text-red-400' : 'bg-gray-100 text-gray-500 dark:bg-white/10 dark:text-gray-400'}`}>{doc.req ? t2.required : t2.optional}</span>
                        </div>
                        <p className="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{locale === 'ar' ? doc.arDesc : doc.enDesc}</p>
                        {hasNew && <p className="text-xs text-green-600 dark:text-green-400 mt-1">✓ {hasNew.name}</p>}
                      </div>
                      <div className="shrink-0">
                        {isUploaded && !hasNew && <span className="inline-flex items-center gap-1 text-xs text-green-600 dark:text-green-400 font-medium"><CheckCircle2 className="w-4 h-4" /> {t2.uploaded}</span>}
                        <input type="file" accept=".jpg,.jpeg,.png,.pdf" className="hidden" ref={el => { fileInputRefs.current[doc.key] = el; }} onChange={e => { const file = e.target.files?.[0] || null; setUploadedFiles(prev => ({ ...prev, [doc.key]: file })); }} />
                        <button onClick={() => fileInputRefs.current[doc.key]?.click()} className="mt-1 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-500/20 border border-blue-200 dark:border-blue-500/20 transition-colors">
                          <Upload className="w-3.5 h-3.5" /> {t2.uploadFile}
                        </button>
                      </div>
                    </div>
                  </div>
                );
              })}
            </div>
          </div>
        )}

        {/* Step 5: Legal Declaration */}
        {step === 5 && (
          <div className="space-y-5">
            <h2 className="text-lg font-bold text-gray-900 dark:text-white">{t2.declaration}</h2>
            <div className="p-5 rounded-xl bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10">
              <p className="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">{t2.declarationText}</p>
            </div>
            <label className="flex items-start gap-3 cursor-pointer select-none">
              <input type="checkbox" checked={form.legal_declaration_accepted} onChange={e => setField('legal_declaration_accepted', e.target.checked)} className="mt-1 w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
              <span className="text-sm font-medium text-gray-700 dark:text-gray-300">{t2.acceptDeclaration}</span>
            </label>
          </div>
        )}

        {/* Navigation Buttons */}
        <div className="flex items-center justify-between mt-8 pt-6 border-t border-gray-200/60 dark:border-white/10">
          {step > 1 ? (
            <button onClick={handlePrev} className="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
              {isRtl ? <ArrowRight className="w-4 h-4" /> : <ArrowLeft className="w-4 h-4" />} {t2.prev}
            </button>
          ) : <div />}
          {step < 5 ? (
            <button onClick={handleNext} disabled={saving} className="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 shadow-lg shadow-blue-500/25 transition-all disabled:opacity-60">
              {saving ? <Loader2 className="w-4 h-4 animate-spin" /> : <>{t2.next} {isRtl ? <ArrowLeft className="w-4 h-4" /> : <ArrowRight className="w-4 h-4" />}</>}
            </button>
          ) : (
            <button onClick={handleSubmit} disabled={saving || !form.legal_declaration_accepted} className="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 shadow-lg shadow-green-500/25 transition-all disabled:opacity-60">
              {saving ? <Loader2 className="w-4 h-4 animate-spin" /> : <><CheckCircle2 className="w-4 h-4" /> {t2.submit}</>}
            </button>
          )}
        </div>
      </div>
    </div>
  );
}
