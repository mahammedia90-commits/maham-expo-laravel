'use client';

import { useState, useEffect } from 'react';
import GlassCard from '@/components/ui/GlassCard';
import DataTable, { Column } from '@/components/ui/DataTable';
import StatusBadge from '@/components/ui/StatusBadge';
import { expoApi } from '@/lib/api';
import { formatDate } from '@/lib/utils';
import { Plus, Search, Filter, Briefcase, Eye, CheckCircle, XCircle } from 'lucide-react';

interface BusinessProfile {
  id: string;
  company_name: string;
  company_name_ar: string;
  business_type: string;
  commercial_registration: string;
  status: string;
  created_at: string;
  user?: { id: string; name: string; email: string };
}

export default function ProfilesPage() {
  const [profiles, setProfiles] = useState<BusinessProfile[]>([]);
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
    fetchProfiles();
  }, [pagination.current_page, statusFilter, searchQuery]);

  const fetchProfiles = async () => {
    setLoading(true);
    try {
      const params: Record<string, string | number> = { page: pagination.current_page, per_page: pagination.per_page };
      if (statusFilter) params.status = statusFilter;
      if (searchQuery) params.search = searchQuery;
      const res = await expoApi.get('/manage/profiles', { params });
      setProfiles(res.data.data || []);
      if (res.data.pagination) {
        setPagination(res.data.pagination);
      }
    } catch {
      setProfiles([]);
    } finally {
      setLoading(false);
    }
  };

  const handleApprove = async (id: string) => {
    try {
      await expoApi.post(`/manage/profiles/${id}/approve`);
      fetchProfiles();
    } catch {
      // handle error silently
    }
  };

  const handleReject = async (id: string) => {
    if (!confirm(isRtl ? 'هل أنت متأكد من الرفض؟' : 'Are you sure you want to reject?')) return;
    try {
      await expoApi.post(`/manage/profiles/${id}/reject`);
      fetchProfiles();
    } catch {
      // handle error silently
    }
  };

  const columns: Column<BusinessProfile>[] = [
    {
      key: 'company_name',
      header: isRtl ? 'اسم الشركة' : 'Company Name',
      render: (item) => (
        <div className="flex items-center gap-3">
          <div className="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500/20 to-blue-500/20 flex items-center justify-center">
            <Briefcase className="w-5 h-5 text-indigo-500" />
          </div>
          <div>
            <p className="font-medium text-gray-900 dark:text-white">
              {isRtl ? item.company_name_ar || item.company_name : item.company_name}
            </p>
            <p className="text-xs text-gray-500">
              {isRtl ? item.company_name : item.company_name_ar}
            </p>
          </div>
        </div>
      ),
    },
    {
      key: 'user',
      header: isRtl ? 'المستخدم' : 'User',
      render: (item) => (
        <div>
          <p className="text-sm text-gray-700 dark:text-gray-300">{item.user?.name || '-'}</p>
          <p className="text-xs text-gray-500">{item.user?.email || ''}</p>
        </div>
      ),
    },
    {
      key: 'business_type',
      header: isRtl ? 'نوع النشاط' : 'Business Type',
      render: (item) => (
        <span className="inline-flex items-center px-2.5 py-1 rounded-lg bg-purple-500/10 text-purple-600 dark:text-purple-400 text-xs font-medium">
          {item.business_type}
        </span>
      ),
    },
    {
      key: 'status',
      header: isRtl ? 'الحالة' : 'Status',
      render: (item) => <StatusBadge status={item.status} />,
    },
    {
      key: 'created_at',
      header: isRtl ? 'تاريخ الإنشاء' : 'Created',
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
            onClick={(e) => { e.stopPropagation(); window.location.href = `/dashboard/profiles/${item.id}`; }}
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
            {isRtl ? 'الملفات التجارية' : 'Business Profiles'}
          </h1>
          <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
            {isRtl ? 'مراجعة وإدارة الملفات التجارية' : 'Review and manage business profiles'}
          </p>
        </div>
        <button
          onClick={() => window.location.href = '/dashboard/profiles/create'}
          className="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-blue-500 to-purple-600 text-white text-sm font-medium shadow-lg shadow-blue-500/25 hover:shadow-xl hover:shadow-blue-500/30 transition-all duration-300 hover:scale-[1.02]"
        >
          <Plus className="w-4 h-4" />
          {isRtl ? 'إنشاء ملف تجاري' : 'Create Profile'}
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
              placeholder={isRtl ? 'بحث عن ملف تجاري...' : 'Search profiles...'}
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
              <option value="pending">{isRtl ? 'قيد المراجعة' : 'Pending'}</option>
              <option value="approved">{isRtl ? 'مقبول' : 'Approved'}</option>
              <option value="rejected">{isRtl ? 'مرفوض' : 'Rejected'}</option>
            </select>
          </div>
        </div>
      </GlassCard>

      {/* Data Table */}
      <DataTable
        columns={columns}
        data={profiles as unknown as Record<string, unknown>[]}
        loading={loading}
        pagination={pagination}
        onPageChange={(page) => setPagination((prev) => ({ ...prev, current_page: page }))}
        emptyMessage={isRtl ? 'لا توجد ملفات تجارية' : 'No business profiles found'}
      />
    </div>
  );
}
