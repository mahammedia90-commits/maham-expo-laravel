'use client';

import { useState, useEffect, useRef } from 'react';
import GlassCard from '@/components/ui/GlassCard';
import DataTable, { Column } from '@/components/ui/DataTable';
import StatusBadge from '@/components/ui/StatusBadge';
import { expoApi } from '@/lib/api';
import { Sponsor } from '@/types';
import { Plus, Search, Edit, Trash2, Handshake, CheckCircle, ShieldCheck, ShieldOff, Filter, Upload, X } from 'lucide-react';

interface EventOption { id: string; name: string; name_ar: string; }

export default function SponsorsPage() {
  const [sponsors, setSponsors] = useState<Sponsor[]>([]);
  const [loading, setLoading] = useState(true);
  const [locale, setLocale] = useState('ar');
  const [pagination, setPagination] = useState({ current_page: 1, last_page: 1, total: 0, per_page: 15 });
  const [statusFilter, setStatusFilter] = useState('');
  const [showModal, setShowModal] = useState(false);
  const [editingSponsor, setEditingSponsor] = useState<Sponsor | null>(null);
  const [events, setEvents] = useState<EventOption[]>([]);
  const [formData, setFormData] = useState({
    event_id: '', name: '', name_ar: '', company_name: '', company_name_ar: '',
    description: '', description_ar: '', contact_person: '', contact_email: '',
    contact_phone: '', website: '', status: 'pending',
  });
  const [logoFile, setLogoFile] = useState<File | null>(null);
  const [logoPreview, setLogoPreview] = useState('');
  const [existingLogo, setExistingLogo] = useState('');
  const [submitting, setSubmitting] = useState(false);
  const [errors, setErrors] = useState<Record<string, string[]>>({});
  const fileInputRef = useRef<HTMLInputElement>(null);

  const isRtl = locale === 'ar';

  useEffect(() => { setLocale(localStorage.getItem('locale') || 'ar'); fetchEvents(); }, []);
  useEffect(() => { fetchSponsors(); }, [pagination.current_page, statusFilter]);

  const fetchEvents = async () => {
    try { const res = await expoApi.get('/manage/events', { params: { per_page: 100 } }); setEvents(res.data.data || []); } catch { /* silent */ }
  };

  const fetchSponsors = async () => {
    setLoading(true);
    try {
      const params: Record<string, string | number> = { page: pagination.current_page, per_page: pagination.per_page };
      if (statusFilter) params.status = statusFilter;
      const res = await expoApi.get('/manage/sponsors', { params });
      setSponsors(res.data.data || []);
      if (res.data.pagination) setPagination(res.data.pagination);
    } catch { setSponsors([]); } finally { setLoading(false); }
  };

  const handleCreate = () => {
    setEditingSponsor(null);
    setFormData({
      event_id: '', name: '', name_ar: '', company_name: '', company_name_ar: '',
      description: '', description_ar: '', contact_person: '', contact_email: '',
      contact_phone: '', website: '', status: 'pending',
    });
    setLogoFile(null); setLogoPreview(''); setExistingLogo(''); setErrors({});
    setShowModal(true);
  };

  const handleEdit = (sponsor: Sponsor) => {
    setEditingSponsor(sponsor);
    setFormData({
      event_id: sponsor.event_id || '',
      name: sponsor.name || '',
      name_ar: sponsor.name_ar || '',
      company_name: sponsor.company_name || '',
      company_name_ar: sponsor.company_name_ar || '',
      description: sponsor.description || '',
      description_ar: sponsor.description_ar || '',
      contact_person: sponsor.contact_person || '',
      contact_email: sponsor.contact_email || '',
      contact_phone: sponsor.contact_phone || '',
      website: sponsor.website || '',
      status: sponsor.status || 'pending',
    });
    setLogoFile(null); setLogoPreview(''); setExistingLogo(sponsor.logo || ''); setErrors({});
    setShowModal(true);
  };

  const handleLogoSelect = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (!file) return;
    setLogoFile(file);
    const reader = new FileReader();
    reader.onload = (ev) => setLogoPreview(ev.target?.result as string);
    reader.readAsDataURL(file);
    setExistingLogo('');
    if (fileInputRef.current) fileInputRef.current.value = '';
  };

  const handleSubmit = async () => {
    setSubmitting(true);
    setErrors({});
    try {
      const fd = new FormData();
      // Required fields
      fd.append('event_id', formData.event_id);
      fd.append('name', formData.name);
      fd.append('name_ar', formData.name_ar);
      // Optional fields
      if (formData.company_name) fd.append('company_name', formData.company_name);
      if (formData.company_name_ar) fd.append('company_name_ar', formData.company_name_ar);
      if (formData.description) fd.append('description', formData.description);
      if (formData.description_ar) fd.append('description_ar', formData.description_ar);
      if (formData.contact_person) fd.append('contact_person', formData.contact_person);
      if (formData.contact_email) fd.append('contact_email', formData.contact_email);
      if (formData.contact_phone) fd.append('contact_phone', formData.contact_phone);
      if (formData.website) fd.append('website', formData.website);
      if (formData.status) fd.append('status', formData.status);
      if (logoFile) fd.append('logo', logoFile);

      if (editingSponsor) {
        fd.append('_method', 'PUT');
        await expoApi.post(`/manage/sponsors/${editingSponsor.id}`, fd, {
          headers: { 'Content-Type': 'multipart/form-data' },
        });
      } else {
        await expoApi.post('/manage/sponsors', fd, {
          headers: { 'Content-Type': 'multipart/form-data' },
        });
      }
      setShowModal(false);
      fetchSponsors();
    } catch (err: unknown) {
      const error = err as { response?: { data?: { errors?: Record<string, string[]> } } };
      if (error.response?.data?.errors) setErrors(error.response.data.errors);
    } finally {
      setSubmitting(false);
    }
  };

  const handleAction = async (id: string, action: string) => {
    try { await expoApi.put(`/manage/sponsors/${id}/${action}`); fetchSponsors(); } catch { /* silent */ }
  };

  const handleDelete = async (id: string) => {
    if (!confirm(isRtl ? 'هل أنت متأكد من الحذف؟' : 'Are you sure you want to delete?')) return;
    try { await expoApi.delete(`/manage/sponsors/${id}`); fetchSponsors(); } catch { /* silent */ }
  };

  const columns: Column<Sponsor>[] = [
    {
      key: 'name',
      header: isRtl ? 'الاسم' : 'Name',
      render: (item) => (
        <div className="flex items-center gap-3">
          <div className="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500/20 to-yellow-500/20 flex items-center justify-center overflow-hidden">
            {item.logo ? <img src={item.logo} alt="" className="w-full h-full object-cover" /> : <Handshake className="w-5 h-5 text-amber-500" />}
          </div>
          <div>
            <span className="font-medium text-gray-900 dark:text-white">{isRtl ? item.name_ar : item.name}</span>
            <p className="text-xs text-gray-500">{item.company_name || (isRtl ? item.name : item.name_ar)}</p>
          </div>
        </div>
      ),
    },
    {
      key: 'event',
      header: isRtl ? 'الفعالية' : 'Event',
      render: (item) => <span className="text-sm text-gray-600 dark:text-gray-300">{item.event?.name || '-'}</span>,
    },
    {
      key: 'status',
      header: isRtl ? 'الحالة' : 'Status',
      render: (item) => <StatusBadge status={item.status} />,
    },
    {
      key: 'actions',
      header: isRtl ? 'الإجراءات' : 'Actions',
      render: (item) => (
        <div className="flex items-center gap-1">
          {item.status === 'pending' && (
            <button onClick={(e) => { e.stopPropagation(); handleAction(item.id, 'approve'); }}
              className="p-2 rounded-lg hover:bg-emerald-500/10 text-emerald-500 transition-colors" title="Approve"><CheckCircle className="w-4 h-4" /></button>
          )}
          {item.status === 'approved' && (
            <button onClick={(e) => { e.stopPropagation(); handleAction(item.id, 'activate'); }}
              className="p-2 rounded-lg hover:bg-blue-500/10 text-blue-500 transition-colors" title="Activate"><ShieldCheck className="w-4 h-4" /></button>
          )}
          {item.status === 'active' && (
            <button onClick={(e) => { e.stopPropagation(); handleAction(item.id, 'suspend'); }}
              className="p-2 rounded-lg hover:bg-amber-500/10 text-amber-500 transition-colors" title="Suspend"><ShieldOff className="w-4 h-4" /></button>
          )}
          <button onClick={(e) => { e.stopPropagation(); handleEdit(item); }}
            className="p-2 rounded-lg hover:bg-blue-500/10 text-blue-500 transition-colors"><Edit className="w-4 h-4" /></button>
          <button onClick={(e) => { e.stopPropagation(); handleDelete(item.id); }}
            className="p-2 rounded-lg hover:bg-red-500/10 text-red-500 transition-colors"><Trash2 className="w-4 h-4" /></button>
        </div>
      ),
    },
  ];

  const statuses = ['pending', 'approved', 'active', 'suspended', 'inactive'];
  const inputClass = "w-full px-4 py-2 rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30";

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900 dark:text-white">{isRtl ? 'الرعاة' : 'Sponsors'}</h1>
          <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">{isRtl ? 'إدارة رعاة المعارض' : 'Manage event sponsors'}</p>
        </div>
        <button onClick={handleCreate}
          className="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-orange-600 text-white text-sm font-medium shadow-lg shadow-amber-500/25 hover:shadow-xl hover:shadow-amber-500/30 transition-all duration-300 hover:scale-[1.02]">
          <Plus className="w-4 h-4" />{isRtl ? 'إضافة راعي' : 'Add Sponsor'}
        </button>
      </div>

      <GlassCard>
        <div className="flex flex-wrap items-center gap-4">
          <Filter className="w-4 h-4 text-gray-400" />
          <select value={statusFilter} onChange={(e) => { setStatusFilter(e.target.value); setPagination(p => ({ ...p, current_page: 1 })); }}
            className="px-3 py-2 rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-all">
            <option value="">{isRtl ? 'جميع الحالات' : 'All Statuses'}</option>
            {statuses.map(s => <option key={s} value={s}>{s}</option>)}
          </select>
        </div>
      </GlassCard>

      <DataTable columns={columns} data={sponsors as unknown as Record<string, unknown>[]} loading={loading} pagination={pagination}
        onPageChange={(page) => setPagination(p => ({ ...p, current_page: page }))} emptyMessage={isRtl ? 'لا يوجد رعاة' : 'No sponsors found'} />

      {showModal && (
        <div className="fixed inset-0 z-50 flex items-center justify-center">
          <div className="fixed inset-0 bg-black/50 backdrop-blur-sm" onClick={() => setShowModal(false)} />
          <div className="relative w-full max-w-3xl mx-4 max-h-[90vh] overflow-y-auto rounded-2xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/95 dark:bg-gray-900/95 backdrop-blur-2xl shadow-2xl p-6">
            <div className="flex items-center justify-between mb-6">
              <h2 className="text-lg font-bold text-gray-900 dark:text-white">
                {editingSponsor ? (isRtl ? 'تعديل الراعي' : 'Edit Sponsor') : (isRtl ? 'إضافة راعي جديد' : 'Add New Sponsor')}
              </h2>
              <button onClick={() => setShowModal(false)} className="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-white/10 transition-colors">
                <X className="w-5 h-5 text-gray-500" />
              </button>
            </div>
            <div className="space-y-4">
              {/* Event Selection */}
              <div>
                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{isRtl ? 'الفعالية' : 'Event'} *</label>
                <select value={formData.event_id} onChange={(e) => setFormData({ ...formData, event_id: e.target.value })} className={inputClass}>
                  <option value="">{isRtl ? 'اختر الفعالية' : 'Select Event'}</option>
                  {events.map(ev => <option key={ev.id} value={ev.id}>{isRtl ? ev.name_ar : ev.name}</option>)}
                </select>
                {errors.event_id && <p className="text-xs text-red-500 mt-1">{errors.event_id[0]}</p>}
              </div>

              {/* Names */}
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name (EN) *</label>
                  <input type="text" value={formData.name} onChange={(e) => setFormData({ ...formData, name: e.target.value })} className={inputClass} />
                  {errors.name && <p className="text-xs text-red-500 mt-1">{errors.name[0]}</p>}
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">الاسم (AR) *</label>
                  <input type="text" value={formData.name_ar} onChange={(e) => setFormData({ ...formData, name_ar: e.target.value })} className={inputClass} dir="rtl" />
                  {errors.name_ar && <p className="text-xs text-red-500 mt-1">{errors.name_ar[0]}</p>}
                </div>
              </div>

              {/* Company Names */}
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{isRtl ? 'اسم الشركة (EN)' : 'Company Name (EN)'}</label>
                  <input type="text" value={formData.company_name} onChange={(e) => setFormData({ ...formData, company_name: e.target.value })} className={inputClass} />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{isRtl ? 'اسم الشركة (AR)' : 'Company Name (AR)'}</label>
                  <input type="text" value={formData.company_name_ar} onChange={(e) => setFormData({ ...formData, company_name_ar: e.target.value })} className={inputClass} dir="rtl" />
                </div>
              </div>

              {/* Descriptions */}
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{isRtl ? 'الوصف (EN)' : 'Description (EN)'}</label>
                  <textarea value={formData.description} onChange={(e) => setFormData({ ...formData, description: e.target.value })} rows={2} className={inputClass} />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{isRtl ? 'الوصف (AR)' : 'Description (AR)'}</label>
                  <textarea value={formData.description_ar} onChange={(e) => setFormData({ ...formData, description_ar: e.target.value })} rows={2} className={inputClass} dir="rtl" />
                </div>
              </div>

              {/* Logo Upload */}
              <div>
                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{isRtl ? 'الشعار' : 'Logo'}</label>
                {(logoPreview || existingLogo) ? (
                  <div className="relative w-32 h-32 rounded-xl overflow-hidden border border-gray-200/60 dark:border-white/10 group">
                    <img src={logoPreview || existingLogo} alt="" className="w-full h-full object-contain bg-white p-2" />
                    <div className="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                      <button type="button" onClick={() => fileInputRef.current?.click()} className="px-2 py-1 rounded-lg bg-white/90 text-gray-900 text-xs">{isRtl ? 'تغيير' : 'Change'}</button>
                      <button type="button" onClick={() => { setLogoFile(null); setLogoPreview(''); setExistingLogo(''); }} className="px-2 py-1 rounded-lg bg-red-500/90 text-white text-xs">{isRtl ? 'حذف' : 'Remove'}</button>
                    </div>
                  </div>
                ) : (
                  <div onClick={() => fileInputRef.current?.click()} className="flex flex-col items-center justify-center gap-2 p-6 w-32 h-32 border-2 border-dashed border-gray-300/60 dark:border-white/10 rounded-xl cursor-pointer hover:border-amber-400/60 transition-all">
                    <Upload className="w-6 h-6 text-amber-500" />
                    <p className="text-xs text-gray-500">{isRtl ? 'شعار' : 'Logo'}</p>
                  </div>
                )}
                <input ref={fileInputRef} type="file" accept="image/*" onChange={handleLogoSelect} className="hidden" />
                {errors.logo && <p className="text-xs text-red-500 mt-1">{errors.logo[0]}</p>}
              </div>

              {/* Contact Info */}
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{isRtl ? 'جهة الاتصال' : 'Contact Person'}</label>
                  <input type="text" value={formData.contact_person} onChange={(e) => setFormData({ ...formData, contact_person: e.target.value })} className={inputClass} />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{isRtl ? 'البريد' : 'Contact Email'}</label>
                  <input type="email" value={formData.contact_email} onChange={(e) => setFormData({ ...formData, contact_email: e.target.value })} className={inputClass} />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{isRtl ? 'الهاتف' : 'Contact Phone'}</label>
                  <input type="text" value={formData.contact_phone} onChange={(e) => setFormData({ ...formData, contact_phone: e.target.value })} className={inputClass} placeholder="+966..." />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{isRtl ? 'الموقع' : 'Website'}</label>
                  <input type="url" value={formData.website} onChange={(e) => setFormData({ ...formData, website: e.target.value })} className={inputClass} placeholder="https://..." />
                </div>
              </div>
            </div>
            <div className="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-200/60 dark:border-white/10">
              <button onClick={() => setShowModal(false)} className="px-4 py-2 rounded-xl text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">{isRtl ? 'إلغاء' : 'Cancel'}</button>
              <button onClick={handleSubmit} disabled={submitting}
                className="px-6 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-orange-600 text-white text-sm font-medium shadow-lg shadow-amber-500/25 hover:shadow-xl transition-all duration-300 disabled:opacity-50">
                {submitting ? (isRtl ? 'جاري الحفظ...' : 'Saving...') : editingSponsor ? (isRtl ? 'تحديث' : 'Update') : (isRtl ? 'إنشاء' : 'Create')}
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
