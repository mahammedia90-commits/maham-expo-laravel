'use client';

import { useState, useEffect } from 'react';
import GlassCard from '@/components/ui/GlassCard';
import DataTable, { Column } from '@/components/ui/DataTable';
import StatusBadge from '@/components/ui/StatusBadge';
import { expoApi } from '@/lib/api';
import { formatDate } from '@/lib/utils';
import { Event } from '@/types';
import { Plus, Search, Filter, Calendar, Eye, Edit, Trash2 } from 'lucide-react';

export default function EventsPage() {
  const [events, setEvents] = useState<Event[]>([]);
  const [loading, setLoading] = useState(true);
  const [locale, setLocale] = useState('ar');
  const [pagination, setPagination] = useState({ current_page: 1, last_page: 1, total: 0, per_page: 15 });
  const [statusFilter, setStatusFilter] = useState('');
  const [categoryFilter, setCategoryFilter] = useState('');
  const [searchQuery, setSearchQuery] = useState('');

  const isRtl = locale === 'ar';

  useEffect(() => {
    setLocale(localStorage.getItem('locale') || 'ar');
  }, []);

  useEffect(() => {
    fetchEvents();
  }, [pagination.current_page, statusFilter, categoryFilter, searchQuery]);

  const fetchEvents = async () => {
    setLoading(true);
    try {
      const params: Record<string, string | number> = { page: pagination.current_page, per_page: pagination.per_page };
      if (statusFilter) params.status = statusFilter;
      if (categoryFilter) params.category_id = categoryFilter;
      if (searchQuery) params.search = searchQuery;
      const res = await expoApi.get('/manage/events', { params });
      setEvents(res.data.data || []);
      if (res.data.pagination) {
        setPagination(res.data.pagination);
      }
    } catch {
      setEvents([]);
    } finally {
      setLoading(false);
    }
  };

  const handleDelete = async (id: string) => {
    if (!confirm(isRtl ? 'هل أنت متأكد من الحذف؟' : 'Are you sure you want to delete?')) return;
    try {
      await expoApi.delete(`/manage/events/${id}`);
      fetchEvents();
    } catch {
      // handle error silently
    }
  };

  const columns: Column<Event>[] = [
    {
      key: 'name',
      header: isRtl ? 'الاسم' : 'Name',
      render: (item) => (
        <div className="flex items-center gap-3">
          <div className="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500/20 to-purple-500/20 flex items-center justify-center">
            <Calendar className="w-5 h-5 text-blue-500" />
          </div>
          <div>
            <p className="font-medium text-gray-900 dark:text-white">{isRtl ? item.name_ar : item.name}</p>
            <p className="text-xs text-gray-500">{isRtl ? item.name : item.name_ar}</p>
          </div>
        </div>
      ),
    },
    {
      key: 'category',
      header: isRtl ? 'التصنيف' : 'Category',
      render: (item) => (
        <span className="text-sm text-gray-600 dark:text-gray-300">
          {isRtl ? item.category?.name_ar : item.category?.name}
        </span>
      ),
    },
    {
      key: 'city',
      header: isRtl ? 'المدينة' : 'City',
      render: (item) => (
        <span className="text-sm text-gray-600 dark:text-gray-300">
          {isRtl ? item.city?.name_ar : item.city?.name}
        </span>
      ),
    },
    {
      key: 'status',
      header: isRtl ? 'الحالة' : 'Status',
      render: (item) => <StatusBadge status={item.status} />,
    },
    {
      key: 'start_date',
      header: isRtl ? 'تاريخ البدء' : 'Start Date',
      render: (item) => <span className="text-sm">{formatDate(item.start_date, locale)}</span>,
    },
    {
      key: 'end_date',
      header: isRtl ? 'تاريخ الانتهاء' : 'End Date',
      render: (item) => <span className="text-sm">{formatDate(item.end_date, locale)}</span>,
    },
    {
      key: 'spaces',
      header: isRtl ? 'المساحات' : 'Spaces',
      render: (item) => (
        <span className="text-sm font-medium">
          {item.available_spaces_count}/{item.total_spaces_count}
        </span>
      ),
    },
    {
      key: 'views',
      header: isRtl ? 'المشاهدات' : 'Views',
      render: (item) => (
        <div className="flex items-center gap-1 text-sm text-gray-500">
          <Eye className="w-3.5 h-3.5" />
          {item.views_count?.toLocaleString() || 0}
        </div>
      ),
    },
    {
      key: 'actions',
      header: isRtl ? 'الإجراءات' : 'Actions',
      render: (item) => (
        <div className="flex items-center gap-1">
          <button
            onClick={(e) => { e.stopPropagation(); window.location.href = `/dashboard/events/${item.id}`; }}
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
            {isRtl ? 'الفعاليات' : 'Events'}
          </h1>
          <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
            {isRtl ? 'إدارة جميع الفعاليات والمعارض' : 'Manage all events and exhibitions'}
          </p>
        </div>
        <button
          onClick={() => window.location.href = '/dashboard/events/create'}
          className="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-blue-500 to-purple-600 text-white text-sm font-medium shadow-lg shadow-blue-500/25 hover:shadow-xl hover:shadow-blue-500/30 transition-all duration-300 hover:scale-[1.02]"
        >
          <Plus className="w-4 h-4" />
          {isRtl ? 'إنشاء فعالية' : 'Create Event'}
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
              placeholder={isRtl ? 'بحث عن فعالية...' : 'Search events...'}
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
              <option value="draft">{isRtl ? 'مسودة' : 'Draft'}</option>
              <option value="published">{isRtl ? 'منشور' : 'Published'}</option>
              <option value="ended">{isRtl ? 'منتهي' : 'Ended'}</option>
              <option value="cancelled">{isRtl ? 'ملغي' : 'Cancelled'}</option>
            </select>
            <select
              value={categoryFilter}
              onChange={(e) => setCategoryFilter(e.target.value)}
              className="px-3 py-2 rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-all text-gray-700 dark:text-gray-300"
            >
              <option value="">{isRtl ? 'جميع التصنيفات' : 'All Categories'}</option>
            </select>
          </div>
        </div>
      </GlassCard>

      {/* Data Table */}
      <DataTable
        columns={columns}
        data={events as unknown as Record<string, unknown>[]}
        loading={loading}
        pagination={pagination}
        onPageChange={(page) => setPagination((prev) => ({ ...prev, current_page: page }))}
        emptyMessage={isRtl ? 'لا توجد فعاليات' : 'No events found'}
      />
    </div>
  );
}
