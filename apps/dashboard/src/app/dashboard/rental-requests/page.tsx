'use client';

import { useState, useEffect } from 'react';
import GlassCard from '@/components/ui/GlassCard';
import DataTable, { Column } from '@/components/ui/DataTable';
import StatusBadge from '@/components/ui/StatusBadge';
import { expoApi } from '@/lib/api';
import { formatDate } from '@/lib/utils';
import { RentalRequest } from '@/types';
import { Search, Filter, ClipboardList, Eye, CheckCircle, XCircle } from 'lucide-react';

export default function RentalRequestsPage() {
  const [requests, setRequests] = useState<RentalRequest[]>([]);
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
    fetchRequests();
  }, [pagination.current_page, statusFilter]);

  const fetchRequests = async () => {
    setLoading(true);
    try {
      const params: Record<string, string | number> = { page: pagination.current_page, per_page: pagination.per_page };
      if (statusFilter) params.status = statusFilter;
      const res = await expoApi.get('/manage/rental-requests', { params });
      setRequests(res.data.data || []);
      if (res.data.pagination) {
        setPagination(res.data.pagination);
      }
    } catch {
      setRequests([]);
    } finally {
      setLoading(false);
    }
  };

  const handleApprove = async (id: string) => {
    try {
      await expoApi.post(`/manage/rental-requests/${id}/approve`);
      fetchRequests();
    } catch {
      // handle error silently
    }
  };

  const handleReject = async (id: string) => {
    if (!confirm(isRtl ? 'هل أنت متأكد من الرفض؟' : 'Are you sure you want to reject?')) return;
    try {
      await expoApi.post(`/manage/rental-requests/${id}/reject`);
      fetchRequests();
    } catch {
      // handle error silently
    }
  };

  const columns: Column<RentalRequest>[] = [
    {
      key: 'id',
      header: '#',
      render: (item) => (
        <div className="flex items-center gap-3">
          <div className="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-500/20 to-red-500/20 flex items-center justify-center">
            <ClipboardList className="w-5 h-5 text-orange-500" />
          </div>
          <span className="text-sm font-mono text-gray-500">#{item.id?.slice(-6)}</span>
        </div>
      ),
    },
    {
      key: 'user',
      header: isRtl ? 'المستخدم' : 'User',
      render: (item) => (
        <span className="text-sm text-gray-700 dark:text-gray-300">{item.user?.name || '-'}</span>
      ),
    },
    {
      key: 'space',
      header: isRtl ? 'المساحة' : 'Space',
      render: (item) => (
        <span className="text-sm text-gray-700 dark:text-gray-300">
          {isRtl ? item.space?.name_ar || item.space?.name : item.space?.name || '-'}
        </span>
      ),
    },
    {
      key: 'dates',
      header: isRtl ? 'التواريخ' : 'Dates',
      render: (item) => (
        <div className="text-sm">
          <p className="text-gray-700 dark:text-gray-300">{formatDate(item.start_date, locale)}</p>
          <p className="text-xs text-gray-500">{isRtl ? 'إلى' : 'to'} {formatDate(item.end_date, locale)}</p>
        </div>
      ),
    },
    {
      key: 'status',
      header: isRtl ? 'الحالة' : 'Status',
      render: (item) => <StatusBadge status={item.status} />,
    },
    {
      key: 'created_at',
      header: isRtl ? 'تاريخ الطلب' : 'Requested',
      render: (item) => (
        <span className="text-sm text-gray-500">{item.created_at ? formatDate(item.created_at, locale) : '-'}</span>
      ),
    },
    {
      key: 'actions',
      header: isRtl ? 'الإجراءات' : 'Actions',
      render: (item) => (
        <div className="flex items-center gap-1">
          <button
            onClick={(e) => { e.stopPropagation(); window.location.href = `/dashboard/rental-requests/${item.id}`; }}
            className="p-2 rounded-lg hover:bg-blue-500/10 text-blue-500 transition-colors"
            title={isRtl ? 'عرض' : 'View'}
          >
            <Eye className="w-4 h-4" />
          </button>
          {item.status === 'pending' && (
            <>
              <button
                onClick={(e) => { e.stopPropagation(); handleApprove(item.id); }}
                className="p-2 rounded-lg hover:bg-emerald-500/10 text-emerald-500 transition-colors"
                title={isRtl ? 'قبول' : 'Approve'}
              >
                <CheckCircle className="w-4 h-4" />
              </button>
              <button
                onClick={(e) => { e.stopPropagation(); handleReject(item.id); }}
                className="p-2 rounded-lg hover:bg-red-500/10 text-red-500 transition-colors"
                title={isRtl ? 'رفض' : 'Reject'}
              >
                <XCircle className="w-4 h-4" />
              </button>
            </>
          )}
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
            {isRtl ? 'طلبات التأجير' : 'Rental Requests'}
          </h1>
          <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
            {isRtl ? 'مراجعة وإدارة طلبات تأجير المساحات' : 'Review and manage space rental requests'}
          </p>
        </div>
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
              placeholder={isRtl ? 'بحث عن طلب...' : 'Search requests...'}
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
              <option value="pending">{isRtl ? 'قيد الانتظار' : 'Pending'}</option>
              <option value="approved">{isRtl ? 'مقبول' : 'Approved'}</option>
              <option value="rejected">{isRtl ? 'مرفوض' : 'Rejected'}</option>
              <option value="cancelled">{isRtl ? 'ملغي' : 'Cancelled'}</option>
            </select>
          </div>
        </div>
      </GlassCard>

      {/* Data Table */}
      <DataTable
        columns={columns}
        data={requests as unknown as Record<string, unknown>[]}
        loading={loading}
        pagination={pagination}
        onPageChange={(page) => setPagination((prev) => ({ ...prev, current_page: page }))}
        emptyMessage={isRtl ? 'لا توجد طلبات تأجير' : 'No rental requests found'}
      />
    </div>
  );
}
