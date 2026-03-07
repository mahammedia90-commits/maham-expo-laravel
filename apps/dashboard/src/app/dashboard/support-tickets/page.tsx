'use client';

import { useState, useEffect } from 'react';
import GlassCard from '@/components/ui/GlassCard';
import DataTable, { Column } from '@/components/ui/DataTable';
import StatusBadge from '@/components/ui/StatusBadge';
import { expoApi } from '@/lib/api';
import { SupportTicket } from '@/types';
import { formatDate } from '@/lib/utils';
import { MessageSquare, Filter, UserCheck, CheckCircle, XCircle, Reply, AlertTriangle } from 'lucide-react';

export default function SupportTicketsPage() {
  const [tickets, setTickets] = useState<SupportTicket[]>([]);
  const [loading, setLoading] = useState(true);
  const [locale, setLocale] = useState('ar');
  const [pagination, setPagination] = useState({ current_page: 1, last_page: 1, total: 0, per_page: 15 });
  const [statusFilter, setStatusFilter] = useState('');

  const isRtl = locale === 'ar';

  useEffect(() => { setLocale(localStorage.getItem('locale') || 'ar'); }, []);
  useEffect(() => { fetchTickets(); }, [pagination.current_page, statusFilter]);

  const fetchTickets = async () => {
    setLoading(true);
    try {
      const params: Record<string, string | number> = { page: pagination.current_page, per_page: pagination.per_page };
      if (statusFilter) params.status = statusFilter;
      const res = await expoApi.get('/manage/support-tickets', { params });
      setTickets(res.data.data || []);
      if (res.data.pagination) setPagination(res.data.pagination);
    } catch { setTickets([]); } finally { setLoading(false); }
  };

  const handleAction = async (id: string, action: string) => {
    try { await expoApi.put(`/manage/support-tickets/${id}/${action}`); fetchTickets(); } catch { /* silent */ }
  };

  const priorityColors: Record<string, string> = {
    low: 'bg-slate-500/20 text-slate-400',
    medium: 'bg-blue-500/20 text-blue-400',
    high: 'bg-amber-500/20 text-amber-400',
    urgent: 'bg-red-500/20 text-red-400',
  };

  const columns: Column<SupportTicket>[] = [
    {
      key: 'subject',
      header: isRtl ? 'الموضوع' : 'Subject',
      render: (item) => (
        <div className="flex items-center gap-3">
          <div className="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-500/20 to-pink-500/20 flex items-center justify-center">
            <MessageSquare className="w-5 h-5 text-purple-500" />
          </div>
          <div>
            <span className="font-medium text-gray-900 dark:text-white">{isRtl ? item.subject_ar : item.subject}</span>
            <p className="text-xs text-gray-500">{item.user?.name || '-'} • {item.user?.email || ''}</p>
          </div>
        </div>
      ),
    },
    {
      key: 'category',
      header: isRtl ? 'التصنيف' : 'Category',
      render: (item) => <span className="text-sm capitalize text-gray-600 dark:text-gray-300">{item.category}</span>,
    },
    {
      key: 'priority',
      header: isRtl ? 'الأولوية' : 'Priority',
      render: (item) => (
        <span className={`inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium ${priorityColors[item.priority] || priorityColors.medium}`}>
          {item.priority === 'urgent' && <AlertTriangle className="w-3 h-3" />}
          {item.priority}
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
      header: isRtl ? 'التاريخ' : 'Date',
      render: (item) => <span className="text-sm">{item.created_at ? formatDate(item.created_at, locale) : '-'}</span>,
    },
    {
      key: 'actions',
      header: isRtl ? 'الإجراءات' : 'Actions',
      render: (item) => (
        <div className="flex items-center gap-1">
          {item.status === 'open' && (
            <button onClick={(e) => { e.stopPropagation(); handleAction(item.id, 'assign'); }}
              className="p-2 rounded-lg hover:bg-blue-500/10 text-blue-500 transition-colors" title={isRtl ? 'تعيين' : 'Assign'}>
              <UserCheck className="w-4 h-4" />
            </button>
          )}
          {(item.status === 'open' || item.status === 'in_progress') && (
            <button onClick={(e) => { e.stopPropagation(); handleAction(item.id, 'resolve'); }}
              className="p-2 rounded-lg hover:bg-emerald-500/10 text-emerald-500 transition-colors" title={isRtl ? 'حل' : 'Resolve'}>
              <CheckCircle className="w-4 h-4" />
            </button>
          )}
          {item.status !== 'closed' && (
            <button onClick={(e) => { e.stopPropagation(); handleAction(item.id, 'close'); }}
              className="p-2 rounded-lg hover:bg-red-500/10 text-red-500 transition-colors" title={isRtl ? 'إغلاق' : 'Close'}>
              <XCircle className="w-4 h-4" />
            </button>
          )}
        </div>
      ),
    },
  ];

  const statuses = ['open', 'in_progress', 'waiting_reply', 'resolved', 'closed'];

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold text-gray-900 dark:text-white">{isRtl ? 'تذاكر الدعم' : 'Support Tickets'}</h1>
        <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">{isRtl ? 'إدارة تذاكر الدعم الفني' : 'Manage support tickets'}</p>
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

      <DataTable columns={columns} data={tickets as unknown as Record<string, unknown>[]} loading={loading} pagination={pagination}
        onPageChange={(page) => setPagination(p => ({ ...p, current_page: page }))} emptyMessage={isRtl ? 'لا توجد تذاكر' : 'No tickets found'} />
    </div>
  );
}
