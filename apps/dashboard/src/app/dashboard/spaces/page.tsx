'use client';

import { useState, useEffect } from 'react';
import GlassCard from '@/components/ui/GlassCard';
import DataTable, { Column } from '@/components/ui/DataTable';
import StatusBadge from '@/components/ui/StatusBadge';
import { expoApi } from '@/lib/api';
import { formatCurrency } from '@/lib/utils';
import { Space } from '@/types';
import { Plus, Search, Filter, MapPin, Edit, Trash2 } from 'lucide-react';

export default function SpacesPage() {
  const [spaces, setSpaces] = useState<Space[]>([]);
  const [loading, setLoading] = useState(true);
  const [locale, setLocale] = useState('ar');
  const [pagination, setPagination] = useState({ current_page: 1, last_page: 1, total: 0, per_page: 15 });
  const [statusFilter, setStatusFilter] = useState('');
  const [searchQuery, setSearchQuery] = useState('');

  const isRtl = locale === 'ar';

  useEffect(() => {
    setLocale(localStorage.getItem('locale') || 'ar');
  }, []);

  useEffect(() => {
    fetchSpaces();
  }, [pagination.current_page, statusFilter, searchQuery]);

  const fetchSpaces = async () => {
    setLoading(true);
    try {
      const params: Record<string, string | number> = { page: pagination.current_page, per_page: pagination.per_page };
      if (statusFilter) params.status = statusFilter;
      if (searchQuery) params.search = searchQuery;
      const res = await expoApi.get('/manage/spaces', { params });
      setSpaces(res.data.data || []);
      if (res.data.pagination) {
        setPagination(res.data.pagination);
      }
    } catch {
      setSpaces([]);
    } finally {
      setLoading(false);
    }
  };

  const handleDelete = async (id: string) => {
    if (!confirm(isRtl ? 'هل أنت متأكد من الحذف؟' : 'Are you sure you want to delete?')) return;
    try {
      await expoApi.delete(`/manage/spaces/${id}`);
      fetchSpaces();
    } catch {
      // handle error silently
    }
  };

  const columns: Column<Space>[] = [
    {
      key: 'name',
      header: isRtl ? 'الاسم' : 'Name',
      render: (item) => (
        <div className="flex items-center gap-3">
          <div className="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500/20 to-teal-500/20 flex items-center justify-center">
            <MapPin className="w-5 h-5 text-emerald-500" />
          </div>
          <div>
            <p className="font-medium text-gray-900 dark:text-white">{isRtl ? item.name_ar : item.name}</p>
            <p className="text-xs text-gray-500">{isRtl ? item.name : item.name_ar}</p>
          </div>
        </div>
      ),
    },
    {
      key: 'event',
      header: isRtl ? 'الفعالية' : 'Event',
      render: (item) => (
        <span className="text-sm text-gray-600 dark:text-gray-300">{item.event_id}</span>
      ),
    },
    {
      key: 'location_code',
      header: isRtl ? 'رمز الموقع' : 'Location Code',
      render: (item) => (
        <span className="inline-flex items-center px-2.5 py-1 rounded-lg bg-blue-500/10 text-blue-600 dark:text-blue-400 text-xs font-mono font-medium">
          {item.location_code}
        </span>
      ),
    },
    {
      key: 'area',
      header: isRtl ? 'المساحة' : 'Area',
      render: (item) => (
        <span className="text-sm">{item.area_sqm} {isRtl ? 'م\u00B2' : 'sqm'}</span>
      ),
    },
    {
      key: 'price',
      header: isRtl ? 'السعر' : 'Price',
      render: (item) => (
        <span className="text-sm font-medium text-gray-900 dark:text-white">
          {formatCurrency(item.price_total || item.price_per_day, locale)}
        </span>
      ),
    },
    {
      key: 'type',
      header: isRtl ? 'النوع' : 'Type',
      render: (item) => (
        <span className="text-sm text-gray-600 dark:text-gray-300">{item.space_type}</span>
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
          <button
            onClick={(e) => { e.stopPropagation(); window.location.href = `/dashboard/spaces/${item.id}`; }}
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
            {isRtl ? 'المساحات' : 'Spaces'}
          </h1>
          <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
            {isRtl ? 'إدارة المساحات والأجنحة' : 'Manage spaces and booths'}
          </p>
        </div>
        <button
          onClick={() => window.location.href = '/dashboard/spaces/create'}
          className="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-blue-500 to-purple-600 text-white text-sm font-medium shadow-lg shadow-blue-500/25 hover:shadow-xl hover:shadow-blue-500/30 transition-all duration-300 hover:scale-[1.02]"
        >
          <Plus className="w-4 h-4" />
          {isRtl ? 'إنشاء مساحة' : 'Create Space'}
        </button>
      </div>

      {/* Filters */}
      <GlassCard>
        <div className="flex flex-wrap items-center gap-4">
          <div className="relative flex-1 min-w-[200px]">
            <Search className="absolute start-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
            <input
              type="text"
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              placeholder={isRtl ? 'بحث عن مساحة...' : 'Search spaces...'}
              className="w-full ps-10 pe-4 py-2 rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-all"
            />
          </div>
          <div className="flex items-center gap-2">
            <Filter className="w-4 h-4 text-gray-400" />
            <select
              value={statusFilter}
              onChange={(e) => setStatusFilter(e.target.value)}
              className="px-3 py-2 rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-all text-gray-700 dark:text-gray-300"
            >
              <option value="">{isRtl ? 'جميع الحالات' : 'All Statuses'}</option>
              <option value="available">{isRtl ? 'متاح' : 'Available'}</option>
              <option value="reserved">{isRtl ? 'محجوز' : 'Reserved'}</option>
              <option value="rented">{isRtl ? 'مؤجر' : 'Rented'}</option>
              <option value="unavailable">{isRtl ? 'غير متاح' : 'Unavailable'}</option>
            </select>
          </div>
        </div>
      </GlassCard>

      {/* Data Table */}
      <DataTable
        columns={columns}
        data={spaces as unknown as Record<string, unknown>[]}
        loading={loading}
        pagination={pagination}
        onPageChange={(page) => setPagination((prev) => ({ ...prev, current_page: page }))}
        emptyMessage={isRtl ? 'لا توجد مساحات' : 'No spaces found'}
      />
    </div>
  );
}
