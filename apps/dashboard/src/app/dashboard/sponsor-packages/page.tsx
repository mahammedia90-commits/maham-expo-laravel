'use client';

import { useState, useEffect } from 'react';
import GlassCard from '@/components/ui/GlassCard';
import DataTable, { Column } from '@/components/ui/DataTable';
import StatusBadge from '@/components/ui/StatusBadge';
import { expoApi } from '@/lib/api';
import { formatDate, formatCurrency } from '@/lib/utils';
import { Plus, Edit, Trash2, Package, Crown } from 'lucide-react';

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
  is_active: boolean;
  created_at: string;
}

export default function SponsorPackagesPage() {
  const [packages, setPackages] = useState<SponsorPackage[]>([]);
  const [loading, setLoading] = useState(true);
  const [locale, setLocale] = useState('ar');
  const [pagination, setPagination] = useState({ current_page: 1, last_page: 1, total: 0, per_page: 15 });
  const [showModal, setShowModal] = useState(false);
  const [editingPkg, setEditingPkg] = useState<SponsorPackage | null>(null);
  const [formData, setFormData] = useState({ name: '', name_ar: '', event_id: '', tier: 'gold', price: 0, max_sponsors: 5, benefits: '', is_active: true });

  const isRtl = locale === 'ar';

  useEffect(() => { setLocale(localStorage.getItem('locale') || 'ar'); }, []);
  useEffect(() => { fetchPackages(); }, [pagination.current_page]);

  const fetchPackages = async () => {
    setLoading(true);
    try {
      const res = await expoApi.get('/manage/sponsor-packages', { params: { page: pagination.current_page, per_page: pagination.per_page } });
      setPackages(res.data.data || []);
      if (res.data.pagination) setPagination(res.data.pagination);
    } catch { setPackages([]); } finally { setLoading(false); }
  };

  const handleCreate = () => {
    setEditingPkg(null);
    setFormData({ name: '', name_ar: '', event_id: '', tier: 'gold', price: 0, max_sponsors: 5, benefits: '', is_active: true });
    setShowModal(true);
  };

  const handleEdit = (pkg: SponsorPackage) => {
    setEditingPkg(pkg);
    setFormData({ name: pkg.name, name_ar: pkg.name_ar, event_id: pkg.event_id, tier: pkg.tier, price: pkg.price, max_sponsors: pkg.max_sponsors, benefits: (pkg.benefits || []).join('\n'), is_active: pkg.is_active });
    setShowModal(true);
  };

  const handleSubmit = async () => {
    try {
      const payload = { ...formData, benefits: formData.benefits.split('\n').filter(Boolean) };
      if (editingPkg) await expoApi.put(`/manage/sponsor-packages/${editingPkg.id}`, payload);
      else await expoApi.post('/manage/sponsor-packages', payload);
      setShowModal(false); fetchPackages();
    } catch { /* silent */ }
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
            <p className="text-xs text-gray-500">{item.event?.name || item.event_id}</p>
          </div>
        </div>
      ),
    },
    {
      key: 'tier',
      header: isRtl ? 'المستوى' : 'Tier',
      render: (item) => (
        <span className={`inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold uppercase bg-gradient-to-r ${tierColors[item.tier] || 'from-blue-400 to-blue-600'} text-white shadow-sm`}>
          {item.tier}
        </span>
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
            <div className="h-full rounded-full bg-gradient-to-r from-blue-500 to-cyan-500" style={{ width: `${Math.min(100, (item.current_sponsors / item.max_sponsors) * 100)}%` }} />
          </div>
          <span className="text-xs text-gray-500">{item.current_sponsors}/{item.max_sponsors}</span>
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

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900 dark:text-white">{isRtl ? 'باقات الرعاية' : 'Sponsor Packages'}</h1>
          <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">{isRtl ? 'إدارة باقات الرعاية للفعاليات' : 'Manage event sponsorship packages'}</p>
        </div>
        <button onClick={handleCreate}
          className="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-yellow-600 text-white text-sm font-medium shadow-lg shadow-amber-500/25 hover:shadow-xl transition-all duration-300 hover:scale-[1.02]">
          <Plus className="w-4 h-4" />{isRtl ? 'إنشاء باقة' : 'Create Package'}
        </button>
      </div>

      <DataTable columns={columns} data={packages as unknown as Record<string, unknown>[]} loading={loading} pagination={pagination}
        onPageChange={(page) => setPagination(p => ({ ...p, current_page: page }))} emptyMessage={isRtl ? 'لا توجد باقات' : 'No packages found'} />

      {showModal && (
        <div className="fixed inset-0 z-50 flex items-center justify-center">
          <div className="fixed inset-0 bg-black/50 backdrop-blur-sm" onClick={() => setShowModal(false)} />
          <div className="relative w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto rounded-2xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/95 dark:bg-gray-900/95 backdrop-blur-2xl shadow-2xl p-6">
            <h2 className="text-lg font-bold text-gray-900 dark:text-white mb-6">
              {editingPkg ? (isRtl ? 'تعديل الباقة' : 'Edit Package') : (isRtl ? 'إنشاء باقة جديدة' : 'Create Package')}
            </h2>
            <div className="space-y-4">
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name (EN)</label>
                  <input type="text" value={formData.name} onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                    className="w-full px-4 py-2 rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30" />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">الاسم (AR)</label>
                  <input type="text" value={formData.name_ar} onChange={(e) => setFormData({ ...formData, name_ar: e.target.value })}
                    className="w-full px-4 py-2 rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30" dir="rtl" />
                </div>
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{isRtl ? 'معرف الفعالية' : 'Event ID'}</label>
                <input type="text" value={formData.event_id} onChange={(e) => setFormData({ ...formData, event_id: e.target.value })}
                  className="w-full px-4 py-2 rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30" />
              </div>
              <div className="grid grid-cols-3 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{isRtl ? 'المستوى' : 'Tier'}</label>
                  <select value={formData.tier} onChange={(e) => setFormData({ ...formData, tier: e.target.value })}
                    className="w-full px-4 py-2 rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                    {tiers.map(t => <option key={t} value={t}>{t}</option>)}
                  </select>
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{isRtl ? 'السعر' : 'Price'}</label>
                  <input type="number" value={formData.price} onChange={(e) => setFormData({ ...formData, price: parseFloat(e.target.value) || 0 })}
                    className="w-full px-4 py-2 rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30" />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{isRtl ? 'الحد الأقصى' : 'Max Sponsors'}</label>
                  <input type="number" value={formData.max_sponsors} onChange={(e) => setFormData({ ...formData, max_sponsors: parseInt(e.target.value) || 1 })}
                    className="w-full px-4 py-2 rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30" />
                </div>
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{isRtl ? 'المزايا (سطر لكل ميزة)' : 'Benefits (one per line)'}</label>
                <textarea value={formData.benefits} onChange={(e) => setFormData({ ...formData, benefits: e.target.value })} rows={4}
                  className="w-full px-4 py-2 rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30" />
              </div>
            </div>
            <div className="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-200/60 dark:border-white/10">
              <button onClick={() => setShowModal(false)} className="px-4 py-2 rounded-xl text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/5">{isRtl ? 'إلغاء' : 'Cancel'}</button>
              <button onClick={handleSubmit} className="px-6 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-yellow-600 text-white text-sm font-medium shadow-lg shadow-amber-500/25 hover:shadow-xl transition-all duration-300">
                {editingPkg ? (isRtl ? 'تحديث' : 'Update') : (isRtl ? 'إنشاء' : 'Create')}
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
