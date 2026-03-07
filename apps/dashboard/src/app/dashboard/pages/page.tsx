'use client';

import { useState, useEffect } from 'react';
import GlassCard from '@/components/ui/GlassCard';
import DataTable, { Column } from '@/components/ui/DataTable';
import { expoApi } from '@/lib/api';
import { Page } from '@/types';
import StatusBadge from '@/components/ui/StatusBadge';
import { Plus, Edit, Trash2, FileText, Check, X } from 'lucide-react';

export default function PagesPage() {
  const [pages, setPages] = useState<Page[]>([]);
  const [loading, setLoading] = useState(true);
  const [locale, setLocale] = useState('ar');
  const [pagination, setPagination] = useState({ current_page: 1, last_page: 1, total: 0, per_page: 15 });
  const [showModal, setShowModal] = useState(false);
  const [editingPage, setEditingPage] = useState<Page | null>(null);
  const [formData, setFormData] = useState({ title: '', title_ar: '', slug: '', content: '', content_ar: '', type: 'custom', is_active: true });

  const isRtl = locale === 'ar';

  useEffect(() => { setLocale(localStorage.getItem('locale') || 'ar'); }, []);
  useEffect(() => { fetchPages(); }, [pagination.current_page]);

  const fetchPages = async () => {
    setLoading(true);
    try {
      const res = await expoApi.get('/manage/pages', { params: { page: pagination.current_page, per_page: pagination.per_page } });
      setPages(res.data.data || []);
      if (res.data.pagination) setPagination(res.data.pagination);
    } catch { setPages([]); } finally { setLoading(false); }
  };

  const handleCreate = () => {
    setEditingPage(null);
    setFormData({ title: '', title_ar: '', slug: '', content: '', content_ar: '', type: 'custom', is_active: true });
    setShowModal(true);
  };

  const handleEdit = (page: Page) => {
    setEditingPage(page);
    setFormData({ title: page.title, title_ar: page.title_ar, slug: page.slug, content: page.content, content_ar: page.content_ar, type: page.type, is_active: page.is_active });
    setShowModal(true);
  };

  const handleSubmit = async () => {
    try {
      if (editingPage) await expoApi.put(`/manage/pages/${editingPage.id}`, formData);
      else await expoApi.post('/manage/pages', formData);
      setShowModal(false); fetchPages();
    } catch { /* silent */ }
  };

  const handleDelete = async (id: string) => {
    if (!confirm(isRtl ? 'هل أنت متأكد من الحذف؟' : 'Are you sure?')) return;
    try { await expoApi.delete(`/manage/pages/${id}`); fetchPages(); } catch { /* silent */ }
  };

  const pageTypes = ['about', 'terms', 'privacy', 'faq', 'contact', 'custom'];

  const columns: Column<Page>[] = [
    {
      key: 'title',
      header: isRtl ? 'العنوان' : 'Title',
      render: (item) => (
        <div className="flex items-center gap-3">
          <div className="w-10 h-10 rounded-xl bg-gradient-to-br from-cyan-500/20 to-blue-500/20 flex items-center justify-center">
            <FileText className="w-5 h-5 text-cyan-500" />
          </div>
          <div>
            <span className="font-medium text-gray-900 dark:text-white">{item.title}</span>
            <p className="text-xs text-gray-500" dir="rtl">{item.title_ar}</p>
          </div>
        </div>
      ),
    },
    {
      key: 'slug',
      header: isRtl ? 'الرابط' : 'Slug',
      render: (item) => <span className="font-mono text-sm text-gray-500">/{item.slug}</span>,
    },
    {
      key: 'type',
      header: isRtl ? 'النوع' : 'Type',
      render: (item) => <span className="text-sm capitalize text-gray-600 dark:text-gray-300">{item.type}</span>,
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
          <h1 className="text-2xl font-bold text-gray-900 dark:text-white">{isRtl ? 'الصفحات' : 'CMS Pages'}</h1>
          <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">{isRtl ? 'إدارة صفحات الموقع' : 'Manage website pages'}</p>
        </div>
        <button onClick={handleCreate}
          className="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 text-white text-sm font-medium shadow-lg shadow-cyan-500/25 hover:shadow-xl transition-all duration-300 hover:scale-[1.02]">
          <Plus className="w-4 h-4" />{isRtl ? 'إنشاء صفحة' : 'Create Page'}
        </button>
      </div>

      <DataTable columns={columns} data={pages as unknown as Record<string, unknown>[]} loading={loading} pagination={pagination}
        onPageChange={(page) => setPagination(p => ({ ...p, current_page: page }))} emptyMessage={isRtl ? 'لا توجد صفحات' : 'No pages found'} />

      {showModal && (
        <div className="fixed inset-0 z-50 flex items-center justify-center">
          <div className="fixed inset-0 bg-black/50 backdrop-blur-sm" onClick={() => setShowModal(false)} />
          <div className="relative w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto rounded-2xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/95 dark:bg-gray-900/95 backdrop-blur-2xl shadow-2xl p-6">
            <h2 className="text-lg font-bold text-gray-900 dark:text-white mb-6">
              {editingPage ? (isRtl ? 'تعديل الصفحة' : 'Edit Page') : (isRtl ? 'إنشاء صفحة جديدة' : 'Create New Page')}
            </h2>
            <div className="space-y-4">
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{isRtl ? 'العنوان (EN)' : 'Title (EN)'}</label>
                  <input type="text" value={formData.title} onChange={(e) => setFormData({ ...formData, title: e.target.value })}
                    className="w-full px-4 py-2 rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30" />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{isRtl ? 'العنوان (AR)' : 'Title (AR)'}</label>
                  <input type="text" value={formData.title_ar} onChange={(e) => setFormData({ ...formData, title_ar: e.target.value })}
                    className="w-full px-4 py-2 rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30" dir="rtl" />
                </div>
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{isRtl ? 'الرابط' : 'Slug'}</label>
                  <input type="text" value={formData.slug} onChange={(e) => setFormData({ ...formData, slug: e.target.value })}
                    className="w-full px-4 py-2 rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30" />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{isRtl ? 'النوع' : 'Type'}</label>
                  <select value={formData.type} onChange={(e) => setFormData({ ...formData, type: e.target.value })}
                    className="w-full px-4 py-2 rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                    {pageTypes.map(t => <option key={t} value={t}>{t}</option>)}
                  </select>
                </div>
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{isRtl ? 'المحتوى (EN)' : 'Content (EN)'}</label>
                <textarea value={formData.content} onChange={(e) => setFormData({ ...formData, content: e.target.value })} rows={5}
                  className="w-full px-4 py-2 rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30" />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{isRtl ? 'المحتوى (AR)' : 'Content (AR)'}</label>
                <textarea value={formData.content_ar} onChange={(e) => setFormData({ ...formData, content_ar: e.target.value })} rows={5} dir="rtl"
                  className="w-full px-4 py-2 rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30" />
              </div>
              <div className="flex items-center gap-3">
                <label className="relative inline-flex items-center cursor-pointer">
                  <input type="checkbox" checked={formData.is_active} onChange={(e) => setFormData({ ...formData, is_active: e.target.checked })} className="sr-only peer" />
                  <div className="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-500/25 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-500"></div>
                </label>
                <span className="text-sm text-gray-700 dark:text-gray-300">{isRtl ? 'نشط' : 'Active'}</span>
              </div>
            </div>
            <div className="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-200/60 dark:border-white/10">
              <button onClick={() => setShowModal(false)} className="px-4 py-2 rounded-xl text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/5">{isRtl ? 'إلغاء' : 'Cancel'}</button>
              <button onClick={handleSubmit} className="px-6 py-2 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 text-white text-sm font-medium shadow-lg shadow-cyan-500/25 hover:shadow-xl transition-all duration-300">
                {editingPage ? (isRtl ? 'تحديث' : 'Update') : (isRtl ? 'إنشاء' : 'Create')}
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
