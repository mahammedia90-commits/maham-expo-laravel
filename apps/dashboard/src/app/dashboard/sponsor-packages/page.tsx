'use client';

import { useState, useEffect } from 'react';
import GlassCard from '@/components/ui/GlassCard';
import DataTable, { Column } from '@/components/ui/DataTable';
import { expoApi } from '@/lib/api';
import { formatCurrency } from '@/lib/utils';
import { Plus, Edit, Trash2, Crown, X } from 'lucide-react';

interface EventOption { id: string; name: string; name_ar: string; }

interface SponsorPackage {
  id: string;
  event_id: string;
  event?: { name: string; name_ar: string };
  name: string;
  name_ar: string;
  tier: string;
  price: number;
  max_sponsors: number;
  current_sponsors: number;
  benefits: string[];
  description: string;
  description_ar: string;
  display_screens_count: number;
  banners_count: number;
  vip_invitations_count: number;
  booth_area_sqm: number;
  is_active: boolean;
  sort_order: number;
  created_at: string;
}

export default function SponsorPackagesPage() {
  const [packages, setPackages] = useState<SponsorPackage[]>([]);
  const [loading, setLoading] = useState(true);
  const [locale, setLocale] = useState('ar');
  const [pagination, setPagination] = useState({ current_page: 1, last_page: 1, total: 0, per_page: 15 });
  const [showModal, setShowModal] = useState(false);
  const [editingPkg, setEditingPkg] = useState<SponsorPackage | null>(null);
  const [events, setEvents] = useState<EventOption[]>([]);
  const [selectedEventId, setSelectedEventId] = useState('');
  const [submitting, setSubmitting] = useState(false);
  const [errors, setErrors] = useState<Record<string, string[]>>({});
  const [formData, setFormData] = useState({
    name: '', name_ar: '', tier: 'gold', price: '', max_sponsors: '5',
    benefits: '', description: '', description_ar: '',
    display_screens_count: '', banners_count: '', vip_invitations_count: '',
    booth_area_sqm: '', is_active: true, sort_order: '0',
  });

  const isRtl = locale === 'ar';

  useEffect(() => { setLocale(localStorage.getItem('locale') || 'ar'); fetchEvents(); }, []);
  useEffect(() => { if (selectedEventId) fetchPackages(); }, [selectedEventId, pagination.current_page]);

  const fetchEvents = async () => {
    try {
      const res = await expoApi.get('/manage/events', { params: { per_page: 100 } });
      const evts = res.data.data || [];
      setEvents(evts);
      if (evts.length > 0 && !selectedEventId) setSelectedEventId(evts[0].id);
    } catch { /* silent */ }
  };

  const fetchPackages = async () => {
    if (!selectedEventId) return;
    setLoading(true);
    try {
      // Sponsor packages are nested under events
      const res = await expoApi.get(`/manage/events/${selectedEventId}/sponsor-packages`, {
        params: { page: pagination.current_page, per_page: pagination.per_page },
      });
      setPackages(res.data.data || []);
      if (res.data.pagination) setPagination(res.data.pagination);
    } catch { setPackages([]); } finally { setLoading(false); }
  };

  const handleCreate = () => {
    setEditingPkg(null);
    setFormData({
      name: '', name_ar: '', tier: 'gold', price: '', max_sponsors: '5',
      benefits: '', description: '', description_ar: '',
      display_screens_count: '', banners_count: '', vip_invitations_count: '',
      booth_area_sqm: '', is_active: true, sort_order: '0',
    });
    setErrors({});
    setShowModal(true);
  };

  const handleEdit = (pkg: SponsorPackage) => {
    setEditingPkg(pkg);
    setFormData({
      name: pkg.name, name_ar: pkg.name_ar, tier: pkg.tier,
      price: String(pkg.price || ''), max_sponsors: String(pkg.max_sponsors || '5'),
      benefits: (pkg.benefits || []).join('\n'),
      description: pkg.description || '', description_ar: pkg.description_ar || '',
      display_screens_count: String(pkg.display_screens_count || ''),
      banners_count: String(pkg.banners_count || ''),
      vip_invitations_count: String(pkg.vip_invitations_count || ''),
      booth_area_sqm: String(pkg.booth_area_sqm || ''),
      is_active: pkg.is_active, sort_order: String(pkg.sort_order || 0),
    });
    setErrors({});
    setShowModal(true);
  };

  const handleSubmit = async () => {
    setSubmitting(true);
    setErrors({});
    try {
      const payload: Record<string, unknown> = {
        name: formData.name,
        name_ar: formData.name_ar,
        tier: formData.tier,
        price: parseFloat(formData.price) || 0,
        is_active: formData.is_active,
        benefits: formData.benefits.split('\n').filter(Boolean),
      };
      if (formData.max_sponsors) payload.max_sponsors = parseInt(formData.max_sponsors);
      if (formData.description) payload.description = formData.description;
      if (formData.description_ar) payload.description_ar = formData.description_ar;
      if (formData.display_screens_count) payload.display_screens_count = parseInt(formData.display_screens_count);
      if (formData.banners_count) payload.banners_count = parseInt(formData.banners_count);
      if (formData.vip_invitations_count) payload.vip_invitations_count = parseInt(formData.vip_invitations_count);
      if (formData.booth_area_sqm) payload.booth_area_sqm = parseFloat(formData.booth_area_sqm);
      if (formData.sort_order) payload.sort_order = parseInt(formData.sort_order);

      if (editingPkg) {
        // Update uses top-level route
        await expoApi.put(`/manage/sponsor-packages/${editingPkg.id}`, payload);
      } else {
        // Create is nested under event
        await expoApi.post(`/manage/events/${selectedEventId}/sponsor-packages`, payload);
      }
      setShowModal(false);
      fetchPackages();
    } catch (err: unknown) {
      const error = err as { response?: { data?: { errors?: Record<string, string[]> } } };
      if (error.response?.data?.errors) setErrors(error.response.data.errors);
    } finally { setSubmitting(false); }
  };

  const handleDelete = async (id: string) => {
    if (!confirm(isRtl ? 'هل أنت متأكد من الحذف؟' : 'Are you sure?')) return;
    try { await expoApi.delete(`/manage/sponsor-packages/${id}`); fetchPackages(); } catch { /* silent */ }
  };

  const tierColors: Record<string, string> = {
    platinum: 'from-slate-400 to-gray-500',
    gold: 'from-amber-400 to-yellow-500',
    silver: 'from-gray-300 to-slate-400',
    bronze: 'from-amber-700 to-orange-800',
    media: 'from-blue-400 to-blue-600',
    strategic: 'from-purple-400 to-purple-600',
  };

  const columns: Column<SponsorPackage>[] = [
    {
      key: 'name',
      header: isRtl ? 'الباقة' : 'Package',
      render: (item) => (
        <div className="flex items-center gap-3">
          <div className={`w-10 h-10 rounded-xl bg-gradient-to-br ${tierColors[item.tier] || 'from-blue-400 to-blue-600'} flex items-center justify-center shadow-lg`}>
            <Crown className="w-5 h-5 text-white" />
          </div>
          <div>
            <span className="font-medium text-gray-900 dark:text-white">{isRtl ? item.name_ar : item.name}</span>
            <p className="text-xs text-gray-500 capitalize">{item.tier}</p>
          </div>
        </div>
      ),
    },
    {
      key: 'price',
      header: isRtl ? 'السعر' : 'Price',
      render: (item) => <span className="font-semibold text-gray-900 dark:text-white">{formatCurrency(item.price)}</span>,
    },
    {
      key: 'slots',
      header: isRtl ? 'المقاعد' : 'Slots',
      render: (item) => (
        <div className="flex items-center gap-2">
          <div className="flex-1 h-2 rounded-full bg-gray-200 dark:bg-gray-700 max-w-[80px]">
            <div className="h-full rounded-full bg-gradient-to-r from-blue-500 to-cyan-500" style={{ width: `${Math.min(100, ((item.current_sponsors || 0) / (item.max_sponsors || 1)) * 100)}%` }} />
          </div>
          <span className="text-xs text-gray-500">{item.current_sponsors || 0}/{item.max_sponsors || 0}</span>
        </div>
      ),
    },
    {
      key: 'actions',
      header: isRtl ? 'الإجراءات' : 'Actions',
      render: (item) => (
        <div className="flex items-center gap-1">
          <button onClick={(e) => { e.stopPropagation(); handleEdit(item); }}
            className="p-2 rounded-lg hover:bg-blue-500/10 text-blue-500 transition-colors"><Edit className="w-4 h-4" /></button>
          <button onClick={(e) => { e.stopPropagation(); handleDelete(item.id); }}
            className="p-2 rounded-lg hover:bg-red-500/10 text-red-500 transition-colors"><Trash2 className="w-4 h-4" /></button>
        </div>
      ),
    },
  ];

  const tiers = ['platinum', 'gold', 'silver', 'bronze', 'media', 'strategic'];
  const inputClass = "w-full px-4 py-2 rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30";

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900 dark:text-white">{isRtl ? 'باقات الرعاية' : 'Sponsor Packages'}</h1>
          <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">{isRtl ? 'إدارة باقات الرعاية للفعاليات' : 'Manage event sponsorship packages'}</p>
        </div>
        <button onClick={handleCreate} disabled={!selectedEventId}
          className="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-yellow-600 text-white text-sm font-medium shadow-lg shadow-amber-500/25 hover:shadow-xl transition-all duration-300 hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed">
          <Plus className="w-4 h-4" />{isRtl ? 'إنشاء باقة' : 'Create Package'}
        </button>
      </div>

      {/* Event Selector */}
      <GlassCard>
        <div className="flex flex-wrap items-center gap-4">
          <div className="min-w-[220px]">
            <label className="block text-xs font-medium text-gray-500 mb-1">{isRtl ? 'اختر الفعالية' : 'Select Event'}</label>
            <select value={selectedEventId} onChange={(e) => { setSelectedEventId(e.target.value); setPagination(p => ({ ...p, current_page: 1 })); }} className={inputClass}>
              <option value="">{isRtl ? 'اختر فعالية...' : 'Choose event...'}</option>
              {events.map(ev => <option key={ev.id} value={ev.id}>{isRtl ? ev.name_ar : ev.name}</option>)}
            </select>
          </div>
        </div>
      </GlassCard>

      {!selectedEventId ? (
        <GlassCard>
          <div className="text-center py-12 text-gray-500">
            <Crown className="w-12 h-12 mx-auto mb-3 opacity-30" />
            <p>{isRtl ? 'يرجى اختيار فعالية لعرض الباقات' : 'Please select an event to view packages'}</p>
          </div>
        </GlassCard>
      ) : (
        <DataTable columns={columns} data={packages as unknown as Record<string, unknown>[]} loading={loading} pagination={pagination}
          onPageChange={(page) => setPagination(p => ({ ...p, current_page: page }))} emptyMessage={isRtl ? 'لا توجد باقات' : 'No packages found'} />
      )}

      {showModal && (
        <div className="fixed inset-0 z-50 flex items-center justify-center">
          <div className="fixed inset-0 bg-black/50 backdrop-blur-sm" onClick={() => setShowModal(false)} />
          <div className="relative w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto rounded-2xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/95 dark:bg-gray-900/95 backdrop-blur-2xl shadow-2xl p-6">
            <div className="flex items-center justify-between mb-6">
              <h2 className="text-lg font-bold text-gray-900 dark:text-white">
                {editingPkg ? (isRtl ? 'تعديل الباقة' : 'Edit Package') : (isRtl ? 'إنشاء باقة جديدة' : 'Create Package')}
              </h2>
              <button onClick={() => setShowModal(false)} className="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-white/10 transition-colors"><X className="w-5 h-5 text-gray-500" /></button>
            </div>
            <div className="space-y-4">
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

              {/* Tier + Price + Max */}
              <div className="grid grid-cols-3 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{isRtl ? 'المستوى' : 'Tier'} *</label>
                  <select value={formData.tier} onChange={(e) => setFormData({ ...formData, tier: e.target.value })} className={inputClass}>
                    {tiers.map(t => <option key={t} value={t}>{t.charAt(0).toUpperCase() + t.slice(1)}</option>)}
                  </select>
                  {errors.tier && <p className="text-xs text-red-500 mt-1">{errors.tier[0]}</p>}
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{isRtl ? 'السعر' : 'Price'} *</label>
                  <input type="number" value={formData.price} onChange={(e) => setFormData({ ...formData, price: e.target.value })} className={inputClass} min="0" />
                  {errors.price && <p className="text-xs text-red-500 mt-1">{errors.price[0]}</p>}
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{isRtl ? 'الحد الأقصى' : 'Max Sponsors'}</label>
                  <input type="number" value={formData.max_sponsors} onChange={(e) => setFormData({ ...formData, max_sponsors: e.target.value })} className={inputClass} min="1" />
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

              {/* Counts */}
              <div className="grid grid-cols-4 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{isRtl ? 'شاشات' : 'Screens'}</label>
                  <input type="number" value={formData.display_screens_count} onChange={(e) => setFormData({ ...formData, display_screens_count: e.target.value })} className={inputClass} min="0" />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{isRtl ? 'لافتات' : 'Banners'}</label>
                  <input type="number" value={formData.banners_count} onChange={(e) => setFormData({ ...formData, banners_count: e.target.value })} className={inputClass} min="0" />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{isRtl ? 'دعوات VIP' : 'VIP Invites'}</label>
                  <input type="number" value={formData.vip_invitations_count} onChange={(e) => setFormData({ ...formData, vip_invitations_count: e.target.value })} className={inputClass} min="0" />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{isRtl ? 'مساحة الجناح' : 'Booth (sqm)'}</label>
                  <input type="number" value={formData.booth_area_sqm} onChange={(e) => setFormData({ ...formData, booth_area_sqm: e.target.value })} className={inputClass} min="0" />
                </div>
              </div>

              {/* Benefits */}
              <div>
                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{isRtl ? 'المزايا (سطر لكل ميزة)' : 'Benefits (one per line)'}</label>
                <textarea value={formData.benefits} onChange={(e) => setFormData({ ...formData, benefits: e.target.value })} rows={3} className={inputClass} />
              </div>

              {/* Active + Sort */}
              <div className="flex items-center gap-6">
                <div className="flex items-center gap-3">
                  <label className="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" checked={formData.is_active} onChange={(e) => setFormData({ ...formData, is_active: e.target.checked })} className="sr-only peer" />
                    <div className="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-500"></div>
                  </label>
                  <span className="text-sm text-gray-700 dark:text-gray-300">{isRtl ? 'نشط' : 'Active'}</span>
                </div>
                <div className="flex items-center gap-2">
                  <label className="text-sm font-medium text-gray-700 dark:text-gray-300">{isRtl ? 'الترتيب' : 'Sort'}</label>
                  <input type="number" value={formData.sort_order} onChange={(e) => setFormData({ ...formData, sort_order: e.target.value })} className="w-20 px-3 py-2 rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30" />
                </div>
              </div>
            </div>

            <div className="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-200/60 dark:border-white/10">
              <button onClick={() => setShowModal(false)} className="px-4 py-2 rounded-xl text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/5">{isRtl ? 'إلغاء' : 'Cancel'}</button>
              <button onClick={handleSubmit} disabled={submitting}
                className="px-6 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-yellow-600 text-white text-sm font-medium shadow-lg shadow-amber-500/25 hover:shadow-xl transition-all duration-300 disabled:opacity-50">
                {submitting ? (isRtl ? 'جاري الحفظ...' : 'Saving...') : editingPkg ? (isRtl ? 'تحديث' : 'Update') : (isRtl ? 'إنشاء' : 'Create')}
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
