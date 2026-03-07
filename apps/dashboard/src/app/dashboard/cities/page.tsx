'use client';

import { useState, useEffect } from 'react';
import GlassCard from '@/components/ui/GlassCard';
import DataTable, { Column } from '@/components/ui/DataTable';
import { expoApi } from '@/lib/api';
import { City } from '@/types';
import { Plus, Search, Edit, Trash2, Building2, Check, X } from 'lucide-react';

export default function CitiesPage() {
  const [cities, setCities] = useState<City[]>([]);
  const [loading, setLoading] = useState(true);
  const [locale, setLocale] = useState('ar');
  const [pagination, setPagination] = useState({ current_page: 1, last_page: 1, total: 0, per_page: 15 });
  const [searchQuery, setSearchQuery] = useState('');
  const [showModal, setShowModal] = useState(false);
  const [editingCity, setEditingCity] = useState<City | null>(null);
  const [formData, setFormData] = useState({ name: '', name_ar: '', region: '', region_ar: '', is_active: true });

  const isRtl = locale === 'ar';

  useEffect(() => {
    setLocale(localStorage.getItem('locale') || 'ar');
  }, []);

  useEffect(() => {
    fetchCities();
  }, [pagination.current_page]);

  const fetchCities = async () => {
    setLoading(true);
    try {
      const params: Record<string, string | number> = { page: pagination.current_page, per_page: pagination.per_page };
      const res = await expoApi.get('/manage/cities', { params });
      setCities(res.data.data || []);
      if (res.data.pagination) {
        setPagination(res.data.pagination);
      }
    } catch {
      setCities([]);
    } finally {
      setLoading(false);
    }
  };

  const handleCreate = () => {
    setEditingCity(null);
    setFormData({ name: '', name_ar: '', region: '', region_ar: '', is_active: true });
    setShowModal(true);
  };

  const handleEdit = (city: City) => {
    setEditingCity(city);
    setFormData({
      name: city.name,
      name_ar: city.name_ar,
      region: city.region,
      region_ar: city.region_ar || '',
      is_active: city.is_active,
    });
    setShowModal(true);
  };

  const handleSubmit = async () => {
    try {
      if (editingCity) {
        await expoApi.put(`/manage/cities/${editingCity.id}`, formData);
      } else {
        await expoApi.post('/manage/cities', formData);
      }
      setShowModal(false);
      fetchCities();
    } catch {
      // handle error silently
    }
  };

  const handleDelete = async (id: string) => {
    if (!confirm(isRtl ? 'هل أنت متأكد من الحذف؟' : 'Are you sure you want to delete?')) return;
    try {
      await expoApi.delete(`/manage/cities/${id}`);
      fetchCities();
    } catch {
      // handle error silently
    }
  };

  const columns: Column<City>[] = [
    {
      key: 'name',
      header: isRtl ? 'الاسم (EN)' : 'Name (EN)',
      render: (item) => (
        <div className="flex items-center gap-3">
          <div className="w-10 h-10 rounded-xl bg-gradient-to-br from-cyan-500/20 to-blue-500/20 flex items-center justify-center">
            <Building2 className="w-5 h-5 text-cyan-500" />
          </div>
          <span className="font-medium text-gray-900 dark:text-white">{item.name}</span>
        </div>
      ),
    },
    {
      key: 'name_ar',
      header: isRtl ? 'الاسم (AR)' : 'Name (AR)',
      render: (item) => (
        <span className="text-sm text-gray-600 dark:text-gray-300" dir="rtl">{item.name_ar}</span>
      ),
    },
    {
      key: 'region',
      header: isRtl ? 'المنطقة' : 'Region',
      render: (item) => (
        <span className="inline-flex items-center px-2.5 py-1 rounded-lg bg-purple-500/10 text-purple-600 dark:text-purple-400 text-xs font-medium">
          {isRtl ? item.region_ar || item.region : item.region}
        </span>
      ),
    },
    {
      key: 'is_active',
      header: isRtl ? 'نشط' : 'Active',
      render: (item) => (
        item.is_active ? (
          <span className="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-emerald-500/10 text-emerald-500 text-xs font-medium">
            <Check className="w-3 h-3" /> {isRtl ? 'نشط' : 'Active'}
          </span>
        ) : (
          <span className="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-red-500/10 text-red-500 text-xs font-medium">
            <X className="w-3 h-3" /> {isRtl ? 'غير نشط' : 'Inactive'}
          </span>
        )
      ),
    },
    {
      key: 'actions',
      header: isRtl ? 'الإجراءات' : 'Actions',
      render: (item) => (
        <div className="flex items-center gap-1">
          <button
            onClick={(e) => { e.stopPropagation(); handleEdit(item); }}
            className="p-2 rounded-lg hover:bg-blue-500/10 text-blue-500 transition-colors"
            title={isRtl ? 'تعديل' : 'Edit'}
          >
            <Edit className="w-4 h-4" />
          </button>
          <button
            onClick={(e) => { e.stopPropagation(); handleDelete(item.id); }}
            className="p-2 rounded-lg hover:bg-red-500/10 text-red-500 transition-colors"
            title={isRtl ? 'حذف' : 'Delete'}
          >
            <Trash2 className="w-4 h-4" />
          </button>
        </div>
      ),
    },
  ];

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900 dark:text-white">
            {isRtl ? 'المدن' : 'Cities'}
          </h1>
          <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
            {isRtl ? 'إدارة المدن والمناطق' : 'Manage cities and regions'}
          </p>
        </div>
        <button
          onClick={handleCreate}
          className="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-blue-500 to-purple-600 text-white text-sm font-medium shadow-lg shadow-blue-500/25 hover:shadow-xl hover:shadow-blue-500/30 transition-all duration-300 hover:scale-[1.02]"
        >
          <Plus className="w-4 h-4" />
          {isRtl ? 'إنشاء مدينة' : 'Create City'}
        </button>
      </div>

      {/* Search */}
      <GlassCard>
        <div className="flex flex-wrap items-center gap-4">
          <div className="relative flex-1 min-w-[200px]">
            <Search className="absolute start-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
            <input
              type="text"
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              placeholder={isRtl ? 'بحث عن مدينة...' : 'Search cities...'}
              className="w-full ps-10 pe-4 py-2 rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-all"
            />
          </div>
        </div>
      </GlassCard>

      {/* Data Table */}
      <DataTable
        columns={columns}
        data={cities as unknown as Record<string, unknown>[]}
        loading={loading}
        pagination={pagination}
        onPageChange={(page) => setPagination((prev) => ({ ...prev, current_page: page }))}
        emptyMessage={isRtl ? 'لا توجد مدن' : 'No cities found'}
      />

      {/* Create/Edit Modal */}
      {showModal && (
        <div className="fixed inset-0 z-50 flex items-center justify-center">
          <div className="fixed inset-0 bg-black/50 backdrop-blur-sm" onClick={() => setShowModal(false)} />
          <div className="relative w-full max-w-lg mx-4 rounded-2xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/95 dark:bg-gray-900/95 backdrop-blur-2xl shadow-2xl p-6">
            <h2 className="text-lg font-bold text-gray-900 dark:text-white mb-6">
              {editingCity
                ? (isRtl ? 'تعديل المدينة' : 'Edit City')
                : (isRtl ? 'إنشاء مدينة جديدة' : 'Create New City')}
            </h2>
            <div className="space-y-4">
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {isRtl ? 'الاسم (EN)' : 'Name (EN)'}
                  </label>
                  <input
                    type="text"
                    value={formData.name}
                    onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                    className="w-full px-4 py-2 rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-all"
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {isRtl ? 'الاسم (AR)' : 'Name (AR)'}
                  </label>
                  <input
                    type="text"
                    value={formData.name_ar}
                    onChange={(e) => setFormData({ ...formData, name_ar: e.target.value })}
                    className="w-full px-4 py-2 rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-all"
                    dir="rtl"
                  />
                </div>
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {isRtl ? 'المنطقة (EN)' : 'Region (EN)'}
                  </label>
                  <input
                    type="text"
                    value={formData.region}
                    onChange={(e) => setFormData({ ...formData, region: e.target.value })}
                    className="w-full px-4 py-2 rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-all"
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {isRtl ? 'المنطقة (AR)' : 'Region (AR)'}
                  </label>
                  <input
                    type="text"
                    value={formData.region_ar}
                    onChange={(e) => setFormData({ ...formData, region_ar: e.target.value })}
                    className="w-full px-4 py-2 rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-all"
                    dir="rtl"
                  />
                </div>
              </div>
              <div className="flex items-center gap-3">
                <label className="relative inline-flex items-center cursor-pointer">
                  <input
                    type="checkbox"
                    checked={formData.is_active}
                    onChange={(e) => setFormData({ ...formData, is_active: e.target.checked })}
                    className="sr-only peer"
                  />
                  <div className="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-500/25 dark:peer-focus:ring-blue-500/25 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-500"></div>
                </label>
                <span className="text-sm text-gray-700 dark:text-gray-300">{isRtl ? 'نشط' : 'Active'}</span>
              </div>
            </div>
            <div className="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-200/60 dark:border-white/10">
              <button
                onClick={() => setShowModal(false)}
                className="px-4 py-2 rounded-xl text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/5 transition-colors"
              >
                {isRtl ? 'إلغاء' : 'Cancel'}
              </button>
              <button
                onClick={handleSubmit}
                className="px-4 py-2 rounded-xl bg-gradient-to-r from-blue-500 to-purple-600 text-white text-sm font-medium shadow-lg shadow-blue-500/25 hover:shadow-xl transition-all"
              >
                {editingCity ? (isRtl ? 'تحديث' : 'Update') : (isRtl ? 'إنشاء' : 'Create')}
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
