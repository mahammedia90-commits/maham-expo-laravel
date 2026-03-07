'use client';

import { useState, useEffect } from 'react';
import GlassCard from '@/components/ui/GlassCard';
import DataTable, { Column } from '@/components/ui/DataTable';
import StatusBadge from '@/components/ui/StatusBadge';
import { expoApi } from '@/lib/api';
import { Invoice } from '@/types';
import { formatCurrency, formatDate } from '@/lib/utils';
import { Receipt, CheckCircle, Send, XCircle, Filter, DollarSign } from 'lucide-react';

export default function InvoicesPage() {
  const [invoices, setInvoices] = useState<Invoice[]>([]);
  const [loading, setLoading] = useState(true);
  const [locale, setLocale] = useState('ar');
  const [pagination, setPagination] = useState({ current_page: 1, last_page: 1, total: 0, per_page: 15 });
  const [statusFilter, setStatusFilter] = useState('');

  const isRtl = locale === 'ar';

  useEffect(() => { setLocale(localStorage.getItem('locale') || 'ar'); }, []);
  useEffect(() => { fetchInvoices(); }, [pagination.current_page, statusFilter]);

  const fetchInvoices = async () => {
    setLoading(true);
    try {
      const params: Record<string, string | number> = { page: pagination.current_page, per_page: pagination.per_page };
      if (statusFilter) params.status = statusFilter;
      const res = await expoApi.get('/manage/invoices', { params });
      setInvoices(res.data.data || []);
      if (res.data.pagination) setPagination(res.data.pagination);
    } catch { setInvoices([]); } finally { setLoading(false); }
  };

  const handleAction = async (id: string, action: string) => {
    try {
      await expoApi.put(`/manage/invoices/${id}/${action}`);
      fetchInvoices();
    } catch { /* silent */ }
  };

  const columns: Column<Invoice>[] = [
    {
      key: 'invoice_number',
      header: isRtl ? 'رقم الفاتورة' : 'Invoice #',
      render: (item) => (
        <div className="flex items-center gap-3">
          <div className="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500/20 to-teal-500/20 flex items-center justify-center">
            <Receipt className="w-5 h-5 text-emerald-500" />
          </div>
          <div>
            <span className="font-mono text-sm font-medium text-gray-900 dark:text-white">{item.invoice_number}</span>
            <p className="text-xs text-gray-500 dark:text-gray-400">{isRtl ? item.title_ar : item.title}</p>
          </div>
        </div>
      ),
    },
    {
      key: 'total_amount',
      header: isRtl ? 'المبلغ' : 'Amount',
      render: (item) => (
        <div>
          <span className="font-semibold text-gray-900 dark:text-white">{formatCurrency(item.total_amount, locale)}</span>
          {item.paid_amount > 0 && item.paid_amount < item.total_amount && (
            <p className="text-xs text-emerald-500">{isRtl ? 'مدفوع' : 'Paid'}: {formatCurrency(item.paid_amount, locale)}</p>
          )}
        </div>
      ),
    },
    {
      key: 'status',
      header: isRtl ? 'الحالة' : 'Status',
      render: (item) => <StatusBadge status={item.status} />,
    },
    {
      key: 'issue_date',
      header: isRtl ? 'تاريخ الإصدار' : 'Issue Date',
      render: (item) => <span className="text-sm">{item.issue_date ? formatDate(item.issue_date, locale) : '-'}</span>,
    },
    {
      key: 'due_date',
      header: isRtl ? 'تاريخ الاستحقاق' : 'Due Date',
      render: (item) => (
        <span className={`text-sm ${item.status === 'overdue' ? 'text-red-500 font-medium' : ''}`}>
          {item.due_date ? formatDate(item.due_date, locale) : '-'}
        </span>
      ),
    },
    {
      key: 'actions',
      header: isRtl ? 'الإجراءات' : 'Actions',
      render: (item) => (
        <div className="flex items-center gap-1">
          {item.status === 'draft' && (
            <button onClick={(e) => { e.stopPropagation(); handleAction(item.id, 'issue'); }}
              className="p-2 rounded-lg hover:bg-blue-500/10 text-blue-500 transition-colors" title={isRtl ? 'إصدار' : 'Issue'}>
              <Send className="w-4 h-4" />
            </button>
          )}
          {(item.status === 'issued' || item.status === 'partially_paid') && (
            <button onClick={(e) => { e.stopPropagation(); handleAction(item.id, 'mark-paid'); }}
              className="p-2 rounded-lg hover:bg-emerald-500/10 text-emerald-500 transition-colors" title={isRtl ? 'تأكيد الدفع' : 'Mark Paid'}>
              <CheckCircle className="w-4 h-4" />
            </button>
          )}
          {item.status !== 'paid' && item.status !== 'cancelled' && (
            <button onClick={(e) => { e.stopPropagation(); handleAction(item.id, 'cancel'); }}
              className="p-2 rounded-lg hover:bg-red-500/10 text-red-500 transition-colors" title={isRtl ? 'إلغاء' : 'Cancel'}>
              <XCircle className="w-4 h-4" />
            </button>
          )}
        </div>
      ),
    },
  ];

  const statuses = ['draft', 'issued', 'paid', 'partially_paid', 'overdue', 'cancelled', 'refunded'];

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900 dark:text-white">{isRtl ? 'الفواتير' : 'Invoices'}</h1>
          <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">{isRtl ? 'إدارة الفواتير والمدفوعات' : 'Manage invoices and payments'}</p>
        </div>
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

      <DataTable columns={columns} data={invoices as unknown as Record<string, unknown>[]} loading={loading} pagination={pagination}
        onPageChange={(page) => setPagination(p => ({ ...p, current_page: page }))} emptyMessage={isRtl ? 'لا توجد فواتير' : 'No invoices found'} />
    </div>
  );
}
