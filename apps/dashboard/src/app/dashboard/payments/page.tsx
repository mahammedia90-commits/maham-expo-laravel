'use client';

import { useState, useEffect } from 'react';
import GlassCard from '@/components/ui/GlassCard';
import DataTable, { Column } from '@/components/ui/DataTable';
import StatusBadge from '@/components/ui/StatusBadge';
import { expoApi } from '@/lib/api';
import { formatCurrency, formatDate } from '@/lib/utils';
import { CreditCard, Filter } from 'lucide-react';

interface Payment {
  id: string;
  payment_number: string;
  amount: number;
  status: string;
  charge_id: string;
  card_brand: string;
  card_last_four: string;
  paid_at: string | null;
  created_at: string;
  invoice?: { id: string; invoice_number: string };
  user?: { id: string; name: string };
}

export default function PaymentsPage() {
  const [payments, setPayments] = useState<Payment[]>([]);
  const [loading, setLoading] = useState(true);
  const [locale, setLocale] = useState('ar');
  const [pagination, setPagination] = useState({ current_page: 1, last_page: 1, total: 0, per_page: 15 });
  const [statusFilter, setStatusFilter] = useState('');

  const isRtl = locale === 'ar';

  useEffect(() => { setLocale(localStorage.getItem('locale') || 'ar'); }, []);
  useEffect(() => { fetchPayments(); }, [pagination.current_page, statusFilter]);

  const fetchPayments = async () => {
    setLoading(true);
    try {
      const params: Record<string, string | number> = { page: pagination.current_page, per_page: pagination.per_page };
      if (statusFilter) params.status = statusFilter;
      const res = await expoApi.get('/manage/invoices', { params });
      setPayments(res.data.data || []);
      if (res.data.pagination) setPagination(res.data.pagination);
    } catch { setPayments([]); } finally { setLoading(false); }
  };

  const columns: Column<Payment>[] = [
    {
      key: 'payment_number',
      header: isRtl ? 'رقم الدفعة' : 'Payment #',
      render: (item) => (
        <div className="flex items-center gap-3">
          <div className="w-10 h-10 rounded-xl bg-gradient-to-br from-green-500/20 to-emerald-500/20 flex items-center justify-center">
            <CreditCard className="w-5 h-5 text-green-500" />
          </div>
          <div>
            <span className="font-mono text-sm font-medium text-gray-900 dark:text-white">{item.payment_number || item.id?.slice(0, 8)}</span>
            {item.invoice && <p className="text-xs text-gray-500">{item.invoice.invoice_number}</p>}
          </div>
        </div>
      ),
    },
    {
      key: 'user',
      header: isRtl ? 'المستخدم' : 'User',
      render: (item) => <span className="text-sm">{item.user?.name || '-'}</span>,
    },
    {
      key: 'amount',
      header: isRtl ? 'المبلغ' : 'Amount',
      render: (item) => <span className="font-semibold text-gray-900 dark:text-white">{formatCurrency(item.amount, locale)}</span>,
    },
    {
      key: 'status',
      header: isRtl ? 'الحالة' : 'Status',
      render: (item) => <StatusBadge status={item.status} />,
    },
    {
      key: 'card',
      header: isRtl ? 'البطاقة' : 'Card',
      render: (item) => item.card_brand ? (
        <span className="text-sm text-gray-500">
          {item.card_brand} •••• {item.card_last_four}
        </span>
      ) : <span className="text-gray-400">-</span>,
    },
    {
      key: 'paid_at',
      header: isRtl ? 'تاريخ الدفع' : 'Paid At',
      render: (item) => <span className="text-sm">{item.paid_at ? formatDate(item.paid_at, locale) : '-'}</span>,
    },
  ];

  const statuses = ['INITIATED', 'PENDING', 'CAPTURED', 'FAILED', 'CANCELLED', 'REFUNDED'];

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold text-gray-900 dark:text-white">{isRtl ? 'المدفوعات' : 'Payments'}</h1>
        <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">{isRtl ? 'عرض جميع المدفوعات' : 'View all payments'}</p>
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

      <DataTable columns={columns} data={payments as unknown as Record<string, unknown>[]} loading={loading} pagination={pagination}
        onPageChange={(page) => setPagination(p => ({ ...p, current_page: page }))} emptyMessage={isRtl ? 'لا توجد مدفوعات' : 'No payments found'} />
    </div>
  );
}
