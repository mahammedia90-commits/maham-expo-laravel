'use client';

import { useState, useEffect } from 'react';
import GlassCard from '@/components/ui/GlassCard';
import DataTable, { Column } from '@/components/ui/DataTable';
import StatusBadge from '@/components/ui/StatusBadge';
import { authApi } from '@/lib/api';
import { formatDate } from '@/lib/utils';
import { Plus, Search, Filter, Users as UsersIcon, Edit, Shield, ShieldOff, Mail, Phone } from 'lucide-react';

interface User {
  id: string;
  name: string;
  email: string;
  phone: string;
  roles: string[];
  status: string;
  email_verified_at: string | null;
  created_at: string;
}

export default function UsersPage() {
  const [users, setUsers] = useState<User[]>([]);
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
    fetchUsers();
  }, [pagination.current_page, statusFilter]);

  const fetchUsers = async () => {
    setLoading(true);
    try {
      const params: Record<string, string | number> = { page: pagination.current_page, per_page: pagination.per_page };
      if (statusFilter) params.status = statusFilter;
      const res = await authApi.get('/users', { params });
      setUsers(res.data.data || []);
      if (res.data.pagination) {
        setPagination(res.data.pagination);
      }
    } catch {
      setUsers([]);
    } finally {
      setLoading(false);
    }
  };

  const handleApprove = async (id: string) => {
    try {
      await authApi.post(`/users/${id}/approve`);
      fetchUsers();
    } catch {
      // handle error silently
    }
  };

  const handleSuspend = async (id: string) => {
    if (!confirm(isRtl ? 'هل أنت متأكد من تعليق هذا المستخدم؟' : 'Are you sure you want to suspend this user?')) return;
    try {
      await authApi.post(`/users/${id}/suspend`);
      fetchUsers();
    } catch {
      // handle error silently
    }
  };

  const columns: Column<User>[] = [
    {
      key: 'name',
      header: isRtl ? 'الاسم' : 'Name',
      render: (item) => (
        <div className="flex items-center gap-3">
          <div className="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-500/20 to-pink-500/20 flex items-center justify-center">
            <span className="text-sm font-bold text-violet-500">{item.name?.charAt(0)?.toUpperCase() || 'U'}</span>
          </div>
          <div>
            <p className="font-medium text-gray-900 dark:text-white">{item.name}</p>
            <p className="text-xs text-gray-500">{isRtl ? 'عضو منذ' : 'Member since'} {item.created_at ? formatDate(item.created_at, locale) : '-'}</p>
          </div>
        </div>
      ),
    },
    {
      key: 'email',
      header: isRtl ? 'البريد الإلكتروني' : 'Email',
      render: (item) => (
        <div className="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
          <Mail className="w-3.5 h-3.5 text-gray-400" />
          {item.email}
        </div>
      ),
    },
    {
      key: 'phone',
      header: isRtl ? 'الهاتف' : 'Phone',
      render: (item) => (
        <div className="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300" dir="ltr">
          <Phone className="w-3.5 h-3.5 text-gray-400" />
          {item.phone || '-'}
        </div>
      ),
    },
    {
      key: 'roles',
      header: isRtl ? 'الأدوار' : 'Roles',
      render: (item) => (
        <div className="flex flex-wrap gap-1">
          {(item.roles || []).map((role) => (
            <span
              key={role}
              className="inline-flex items-center px-2 py-0.5 rounded-md bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 text-xs font-medium"
            >
              {role}
            </span>
          ))}
        </div>
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
            onClick={(e) => { e.stopPropagation(); window.location.href = `/dashboard/users/${item.id}`; }}
            className="p-2 rounded-lg hover:bg-blue-500/10 text-blue-500 transition-colors"
            title={isRtl ? 'تعديل' : 'Edit'}
          >
            <Edit className="w-4 h-4" />
          </button>
          {item.status !== 'active' && (
            <button
              onClick={(e) => { e.stopPropagation(); handleApprove(item.id); }}
              className="p-2 rounded-lg hover:bg-emerald-500/10 text-emerald-500 transition-colors"
              title={isRtl ? 'تفعيل' : 'Approve'}
            >
              <Shield className="w-4 h-4" />
            </button>
          )}
          {item.status === 'active' && (
            <button
              onClick={(e) => { e.stopPropagation(); handleSuspend(item.id); }}
              className="p-2 rounded-lg hover:bg-red-500/10 text-red-500 transition-colors"
              title={isRtl ? 'تعليق' : 'Suspend'}
            >
              <ShieldOff className="w-4 h-4" />
            </button>
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
            {isRtl ? 'المستخدمون' : 'Users'}
          </h1>
          <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
            {isRtl ? 'إدارة حسابات المستخدمين' : 'Manage user accounts'}
          </p>
        </div>
        <button
          onClick={() => window.location.href = '/dashboard/users/create'}
          className="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-blue-500 to-purple-600 text-white text-sm font-medium shadow-lg shadow-blue-500/25 hover:shadow-xl hover:shadow-blue-500/30 transition-all duration-300 hover:scale-[1.02]"
        >
          <Plus className="w-4 h-4" />
          {isRtl ? 'إنشاء مستخدم' : 'Create User'}
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
              placeholder={isRtl ? 'بحث عن مستخدم...' : 'Search users...'}
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
              <option value="active">{isRtl ? 'نشط' : 'Active'}</option>
              <option value="pending">{isRtl ? 'قيد الانتظار' : 'Pending'}</option>
              <option value="suspended">{isRtl ? 'معلق' : 'Suspended'}</option>
            </select>
          </div>
        </div>
      </GlassCard>

      {/* Data Table */}
      <DataTable
        columns={columns}
        data={users as unknown as Record<string, unknown>[]}
        loading={loading}
        pagination={pagination}
        onPageChange={(page) => setPagination((prev) => ({ ...prev, current_page: page }))}
        emptyMessage={isRtl ? 'لا يوجد مستخدمون' : 'No users found'}
      />
    </div>
  );
}
