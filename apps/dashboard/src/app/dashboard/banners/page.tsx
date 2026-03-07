'use client';

import { useState, useEffect } from 'react';
import GlassCard from '@/components/ui/GlassCard';
import DataTable, { Column } from '@/components/ui/DataTable';
import { expoApi } from '@/lib/api';
import { Banner } from '@/types';
import { formatDate } from '@/lib/utils';
import { Plus, Edit, Trash2, Image, ExternalLink, Check, X } from 'lucide-react';

export default function BannersPage() {
  const [banners, setBanners] = useState<Banner[]>([]);
  const [loading, setLoading] = useState(true);
  const [locale, setLocale] = useState('ar');
  const [pagination, setPagination] = useState({ current_page: 1, last_page: 1, total: 0, per_page: 15 });
  const [showModal, setShowModal] = useState(false);
  const [editingBanner, setEditingBanner] = useState<Banner | null>(null);
  const [formData, setFormData] = useState({ title: '', title_ar: '', image_url: '', link_url: '', position: 'home_top', sort_order: 0, is_active: true, starts_at: '', ends_at: '' });

  const isRtl = locale === 'ar';

  useEffect(() => { setLocale(localStorage.getItem('locale') || 'ar'); }, []);
  useEffect(() => { fetchBanners(); }, [pagination.current_page]);

  const fetchBanners = async () => {
    setLoading(true);
    try {
      const res = await expoApi.get('/manage/banners', { params: { page: pagination.current_page, per_page: pagination.per_page } });
      setBanners(res.data.data || []);
      if (res.data.pagination) setPagination(res.data.pagination);
    } catch { setBanners([]); } finally { setLoading(false); }
  };

  const handleCreate = () => {
    setEditingBanner(null);
    setFormData({ title: '', title_ar: '', image_url: '', link_url: '', position: 'home_top', sort_order: 0, is_active: true, starts_at: '', ends_at: '' });
    setShowModal(true);
  };

  const handleEdit = (banner: Banner) => {
    setEditingBanner(banner);
    setFormData({ title: banner.title, title_ar: banner.title_ar, image_url: banner.image_url, link_url: banner.link_url || '', position: banner.position, sort_order: banner.sort_order, is_active: banner.is_active, starts_at: banner.starts_at || '', ends_at: banner.ends_at || '' });
    setShowModal(true);
  };

  const handleSubmit = async () => {
    try {
      if (editingBanner) await expoApi.put(`/manage/banners/${editingBanner.id}`, formData);
      else await expoApi.post('/manage/banners', formData);
      setShowModal(false); fetchBanners();
    } catch { /* silent */ }
  };

  const handleDelete = async (id: string) => {
    if (!confirm(isRtl ? 'هل أنت متأكد من الحذف؟' : 'Are you sure?')) return;
    try { await expoApi.delete(`/manage/banners/${id}`); fetchBanners(); } catch { /* silent */ }
  };

  const positions = ['home_top', 'home_middle', 'home_bottom', 'events_top', 'events_sidebar', 'spaces_top'];

  const columns: Column<Banner>[] = [
    {
      key: 'title',
      header: isRtl ? 'البانر' : 'Banner',
      render: (item) => (
        <div className="flex items-center gap-3">
          <div className="w-16 h-10 rounded-lg overflow-hidden bg-gradient-to-br from-pink-500/20 to-orange-500/20 flex items-center justify-center">
            {item.image_url ? (
              <img src={item.image_url} alt={item.title} className="w-full h-full object-cover" />
            ) : (
              <Image className="w-5 h-5 text-pink-500" />
            )}
          </div>
          <div>
            <span className="font-medium text-gray-900 dark:text-white text-sm">{isRtl ? item.title_ar : item.title}</span>
            {item.link_url && (
              <div className="flex items-center gap-1 text-xs text-blue-500">
                <ExternalLink className="w-3 h-3" />
                <span className="truncate max-w-[150px]">{item.link_url}</span>
              </div>
            )}
          </div>
        </div>
      ),
    },
    {
      key: 'position',
      header: isRtl ? 'الموضع' : 'Position',
      render: (item) => <span className="px-2.5 py-1 rounded-lg bg-blue-500/10 text-blue-500 text-xs font-medium">{item.position.replace(/_/g, ' ')}</span>,
    },
    {
      key: 'sort_order',
      header: isRtl ? 'الترتيب' : 'Order',
      render: (item) => <span className="text-sm font-mono text-gray-500">{item.sort_order}</span>,
    },
    {
      key: 'period',
      header: isRtl ? 'الفترة' : 'Period',
      render: (item) => (
        <div className="text-xs text-gray-500">
          {item.starts_at && <div>{formatDate(item.starts_at, locale)}</div>}
          {item.ends_at && <div>→ {formatDate(item.ends_at, locale)}</div>}
          {!item.starts_at && !item.ends_at && '-'}
        </div>
      ),
    },
    {
      key: 'is_active',
      header: isRtl ? 'نشط' : 'Active',
      render: (item) => item.is_active
        ? <span className="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-emerald-500/10 text-emerald-500 text-xs font-medium"><Check className="w-3 h-3" />{isRtl ? 'نشط' : 'Active'}</span>
        : <span className="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-red-500/10 text-red-500 text-xs font-medium"><X className="w-3 h-3" />{isRtl ? 'غير نشط' : 'Inactive'}</span>,
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

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900 dark:text-white">{isRtl ? 'البانرات' : 'Banners'}</h1>
          <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">{isRtl ? 'إدارة البانرات الإعلانية' : 'Manage advertisement banners'}</p>
        </div>
        <button onClick={handleCreate}
          className="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-pink-500 to-orange-600 text-white text-sm font-medium shadow-lg shadow-pink-500/25 hover:shadow-xl transition-all duration-300 hover:scale-[1.02]">
          <Plus className="w-4 h-4" />{isRtl ? 'إضافة بانر' : 'Add Banner'}
        </button>
      </div>

      <DataTable columns={columns} data={banners as unknown as Record<string, unknown>[]} loading={loading} pagination={pagination}
        onPageChange={(page) => setPagination(p => ({ ...p, current_page: page }))} emptyMessage={isRtl ? 'لا توجد بانرات' : 'No banners found'} />

      {showModal && (
        <div className="fixed inset-0 z-50 flex items-center justify-center">
          <div className="fixed inset-0 bg-black/50 backdrop-blur-sm" onClick={() => setShowModal(false)} />
          <div className="relative w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto rounded-2xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/95 dark:bg-gray-900/95 backdrop-blur-2xl shadow-2xl p-6">
            <h2 className="text-lg font-bold text-gray-900 dark:text-white mb-6">
              {editingBanner ? (isRtl ? 'تعديل البانر' : 'Edit Banner') : (isRtl ? 'إضافة بانر جديد' : 'Add New Banner')}
            </h2>
            <div className="space-y-4">
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title (EN)</label>
                  <input type="text" value={formData.title} onChange={(e) => setFormData({ ...formData, title: e.target.value })}
                    className="w-full px-4 py-2 rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30" />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">العنوان (AR)</label>
                  <input type="text" value={formData.title_ar} onChange={(e) => setFormData({ ...formData, title_ar: e.target.value })}
                    className="w-full px-4 py-2 rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30" dir="rtl" />
                </div>
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{isRtl ? 'رابط الصورة' : 'Image URL'}</label>
                <input type="url" value={formData.image_url} onChange={(e) => setFormData({ ...formData, image_url: e.target.value })}
                  className="w-full px-4 py-2 rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30" />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{isRtl ? 'رابط الإعلان' : 'Link URL'}</label>
                <input type="url" value={formData.link_url} onChange={(e) => setFormData({ ...formData, link_url: e.target.value })}
                  className="w-full px-4 py-2 rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30" />
              </div>
              <div className="grid grid-cols-3 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{isRtl ? 'الموضع' : 'Position'}</label>
                  <select value={formData.position} onChange={(e) => setFormData({ ...formData, position: e.target.value })}
                    className="w-full px-4 py-2 rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                    {positions.map(p => <option key={p} value={p}>{p.replace(/_/g, ' ')}</option>)}
                  </select>
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{isRtl ? 'الترتيب' : 'Sort Order'}</label>
                  <input type="number" value={formData.sort_order} onChange={(e) => setFormData({ ...formData, sort_order: parseInt(e.target.value) || 0 })}
                    className="w-full px-4 py-2 rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30" />
                </div>
                <div className="flex items-end">
                  <label className="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" checked={formData.is_active} onChange={(e) => setFormData({ ...formData, is_active: e.target.checked })} className="sr-only peer" />
                    <div className="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-pink-500/25 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-pink-500"></div>
                    <span className="ms-2 text-sm text-gray-700 dark:text-gray-300">{isRtl ? 'نشط' : 'Active'}</span>
                  </label>
                </div>
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{isRtl ? 'تاريخ البدء' : 'Start Date'}</label>
                  <input type="datetime-local" value={formData.starts_at} onChange={(e) => setFormData({ ...formData, starts_at: e.target.value })}
                    className="w-full px-4 py-2 rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30" />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{isRtl ? 'تاريخ الانتهاء' : 'End Date'}</label>
                  <input type="datetime-local" value={formData.ends_at} onChange={(e) => setFormData({ ...formData, ends_at: e.target.value })}
                    className="w-full px-4 py-2 rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30" />
                </div>
              </div>
            </div>
            <div className="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-200/60 dark:border-white/10">
              <button onClick={() => setShowModal(false)} className="px-4 py-2 rounded-xl text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/5">{isRtl ? 'إلغاء' : 'Cancel'}</button>
              <button onClick={handleSubmit} className="px-6 py-2 rounded-xl bg-gradient-to-r from-pink-500 to-orange-600 text-white text-sm font-medium shadow-lg shadow-pink-500/25 hover:shadow-xl transition-all duration-300">
                {editingBanner ? (isRtl ? 'تحديث' : 'Update') : (isRtl ? 'إنشاء' : 'Create')}
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
