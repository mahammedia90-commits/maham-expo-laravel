'use client';

import { useState, useEffect } from 'react';
import GlassCard from '@/components/ui/GlassCard';
import DataTable, { Column } from '@/components/ui/DataTable';
import StatusBadge from '@/components/ui/StatusBadge';
import { expoApi } from '@/lib/api';
import { Sponsor } from '@/types';
import { Plus, Search, Edit, Trash2, Handshake, CheckCircle, XCircle, ShieldCheck, ShieldOff, Filter } from 'lucide-react';

export default function SponsorsPage() {
  const [sponsors, setSponsors] = useState<Sponsor[]>([]);
  const [loading, setLoading] = useState(true);
  const [locale, setLocale] = useState('ar');
  const [pagination, setPagination] = useState({ current_page: 1, last_page: 1, total: 0, per_page: 15 });
  const [statusFilter, setStatusFilter] = useState('');
  const [showModal, setShowModal] = useState(false);
  const [editingSponsor, setEditingSponsor] = useState<Sponsor | null>(null);
  const [formData, setFormData] = useState({ name: '', name_ar: '', tier: 'gold' });

  const isRtl = locale === 'ar';

  useEffect(() => { setLocale(localStorage.getItem('locale') || 'ar'); }, []);
  useEffect(() => { fetchSponsors(); }, [pagination.current_page, statusFilter]);

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

  const handleCreate = () => { setEditingSponsor(null); setFormData({ name: '', name_ar: '', tier: 'gold' }); setShowModal(true); };

  const handleEdit = (sponsor: Sponsor) => {
    setEditingSponsor(sponsor);
    setFormData({ name: sponsor.name, name_ar: sponsor.name_ar, tier: sponsor.tier });
    setShowModal(true);
  };

  const handleSubmit = async () => {
    try {
      if (editingSponsor) await expoApi.put(`/manage/sponsors/${editingSponsor.id}`, formData);
      else await expoApi.post('/manage/sponsors', formData);
      setShowModal(false); fetchSponsors();
    } catch { /* silent */ }
  };

  const handleAction = async (id: string, action: string) => {
    try { await expoApi.put(`/manage/sponsors/${id}/${action}`); fetchSponsors(); } catch { /* silent */ }
  };

  const handleDelete = async (id: string) => {
    if (!confirm(isRtl ? 'هل أنت متأكد من الحذف؟' : 'Are you sure you want to delete?')) return;
    try { await expoApi.delete(`/manage/sponsors/${id}`); fetchSponsors(); } catch { /* silent */ }
  };

  const tierColors: Record<string, string> = {
    platinum: 'bg-slate-300/20 text-slate-300 border-slate-300/30',
    gold: 'bg-amber-500/20 text-amber-400 border-amber-500/30',
    silver: 'bg-gray-400/20 text-gray-400 border-gray-400/30',
    bronze: 'bg-orange-700/20 text-orange-400 border-orange-700/30',
    media_partner: 'bg-purple-500/20 text-purple-400 border-purple-500/30',
    strategic_partner: 'bg-blue-500/20 text-blue-400 border-blue-500/30',
  };

  const columns: Column<Sponsor>[] = [
    {
      key: 'name',
      header: isRtl ? 'الاسم' : 'Name',
      render: (item) => (
        <div className="flex items-center gap-3">
          <div className="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500/20 to-yellow-500/20 flex items-center justify-center">
            <Handshake className="w-5 h-5 text-amber-500" />
          </div>
          <div>
            <span className="font-medium text-gray-900 dark:text-white">{item.name}</span>
            <p className="text-xs text-gray-500" dir="rtl">{item.name_ar}</p>
          </div>
        </div>
      ),
    },
    {
      key: 'tier',
      header: isRtl ? 'المستوى' : 'Tier',
      render: (item) => (
        <span className={`inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border ${tierColors[item.tier] || tierColors.gold}`}>
          {item.tier}
        </span>
      ),
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
              className="p-2 rounded-lg hover:bg-emerald-500/10 text-emerald-500 transition-colors"><CheckCircle className="w-4 h-4" /></button>
          )}
          {item.status === 'approved' && (
            <button onClick={(e) => { e.stopPropagation(); handleAction(item.id, 'activate'); }}
              className="p-2 rounded-lg hover:bg-blue-500/10 text-blue-500 transition-colors"><ShieldCheck className="w-4 h-4" /></button>
          )}
          {item.status === 'active' && (
            <button onClick={(e) => { e.stopPropagation(); handleAction(item.id, 'suspend'); }}
              className="p-2 rounded-lg hover:bg-amber-500/10 text-amber-500 transition-colors"><ShieldOff className="w-4 h-4" /></button>
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
  const tiers = ['platinum', 'gold', 'silver', 'bronze', 'media_partner', 'strategic_partner'];

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
          <div className="flex items-center gap-2">
            <Filter className="w-4 h-4 text-gray-400" />
            <select value={statusFilter} onChange={(e) => { setStatusFilter(e.target.value); setPagination(p => ({ ...p, current_page: 1 })); }}
              className="px-3 py-2 rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-all">
              <option value="">{isRtl ? 'جميع الحالات' : 'All Statuses'}</option>
              {statuses.map(s => <option key={s} value={s}>{s}</option>)}
            </select>
          </div>
        </div>
      </GlassCard>

      <DataTable columns={columns} data={sponsors as unknown as Record<string, unknown>[]} loading={loading} pagination={pagination}
        onPageChange={(page) => setPagination(p => ({ ...p, current_page: page }))} emptyMessage={isRtl ? 'لا يوجد رعاة' : 'No sponsors found'} />

      {showModal && (
        <div className="fixed inset-0 z-50 flex items-center justify-center">
          <div className="fixed inset-0 bg-black/50 backdrop-blur-sm" onClick={() => setShowModal(false)} />
          <div className="relative w-full max-w-lg mx-4 rounded-2xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/95 dark:bg-gray-900/95 backdrop-blur-2xl shadow-2xl p-6">
            <h2 className="text-lg font-bold text-gray-900 dark:text-white mb-6">
              {editingSponsor ? (isRtl ? 'تعديل الراعي' : 'Edit Sponsor') : (isRtl ? 'إضافة راعي جديد' : 'Add New Sponsor')}
            </h2>
            <div className="space-y-4">
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{isRtl ? 'الاسم (EN)' : 'Name (EN)'}</label>
                  <input type="text" value={formData.name} onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                    className="w-full px-4 py-2 rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30" />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{isRtl ? 'الاسم (AR)' : 'Name (AR)'}</label>
                  <input type="text" value={formData.name_ar} onChange={(e) => setFormData({ ...formData, name_ar: e.target.value })}
                    className="w-full px-4 py-2 rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30" dir="rtl" />
                </div>
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{isRtl ? 'المستوى' : 'Tier'}</label>
                <select value={formData.tier} onChange={(e) => setFormData({ ...formData, tier: e.target.value })}
                  className="w-full px-4 py-2 rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                  {tiers.map(t => <option key={t} value={t}>{t}</option>)}
                </select>
              </div>
            </div>
            <div className="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-200/60 dark:border-white/10">
              <button onClick={() => setShowModal(false)}
                className="px-4 py-2 rounded-xl text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">{isRtl ? 'إلغاء' : 'Cancel'}</button>
              <button onClick={handleSubmit}
                className="px-6 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-orange-600 text-white text-sm font-medium shadow-lg shadow-amber-500/25 hover:shadow-xl transition-all duration-300">
                {editingSponsor ? (isRtl ? 'تحديث' : 'Update') : (isRtl ? 'إنشاء' : 'Create')}
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
