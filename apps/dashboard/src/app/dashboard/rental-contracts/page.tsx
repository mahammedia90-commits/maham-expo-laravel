'use client';

import { useState, useEffect } from 'react';
import GlassCard from '@/components/ui/GlassCard';
import DataTable, { Column } from '@/components/ui/DataTable';
import StatusBadge from '@/components/ui/StatusBadge';
import { expoApi } from '@/lib/api';
import { RentalContract } from '@/types';
import { formatCurrency, formatDate } from '@/lib/utils';
import { Search, FileSignature, CheckCircle, XCircle, Ban, Filter } from 'lucide-react';

export default function RentalContractsPage() {
  const [contracts, setContracts] = useState<RentalContract[]>([]);
  const [loading, setLoading] = useState(true);
  const [locale, setLocale] = useState('ar');
  const [pagination, setPagination] = useState({ current_page: 1, last_page: 1, total: 0, per_page: 15 });
  const [statusFilter, setStatusFilter] = useState('');

  const isRtl = locale === 'ar';

  useEffect(() => { setLocale(localStorage.getItem('locale') || 'ar'); }, []);
  useEffect(() => { fetchContracts(); }, [pagination.current_page, statusFilter]);

  const fetchContracts = async () => {
    setLoading(true);
    try {
      const params: Record<string, string | number> = { page: pagination.current_page, per_page: pagination.per_page };
      if (statusFilter) params.status = statusFilter;
      const res = await expoApi.get('/manage/rental-contracts', { params });
      setContracts(res.data.data || []);
      if (res.data.pagination) setPagination(res.data.pagination);
    } catch { setContracts([]); } finally { setLoading(false); }
  };

  const handleAction = async (id: string, action: string) => {
    try {
      await expoApi.put(`/manage/rental-contracts/${id}/${action}`);
      fetchContracts();
    } catch { /* silent */ }
  };

  const columns: Column<RentalContract>[] = [
    {
      key: 'id',
      header: isRtl ? 'رقم العقد' : 'Contract #',
      render: (item) => (
        <div className="flex items-center gap-3">
          <div className="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500/20 to-purple-500/20 flex items-center justify-center">
            <FileSignature className="w-5 h-5 text-indigo-500" />
          </div>
          <span className="font-mono text-sm text-gray-900 dark:text-white">
            {item.id?.slice(0, 8)}...
          </span>
        </div>
      ),
    },
    {
      key: 'status',
      header: isRtl ? 'الحالة' : 'Status',
      render: (item) => <StatusBadge status={item.status} />,
    },
    {
      key: 'start_date',
      header: isRtl ? 'تاريخ البداية' : 'Start Date',
      render: (item) => <span className="text-sm">{item.start_date ? formatDate(item.start_date, locale) : '-'}</span>,
    },
    {
      key: 'end_date',
      header: isRtl ? 'تاريخ النهاية' : 'End Date',
      render: (item) => <span className="text-sm">{item.end_date ? formatDate(item.end_date, locale) : '-'}</span>,
    },
    {
      key: 'total_amount',
      header: isRtl ? 'المبلغ' : 'Amount',
      render: (item) => (
        <span className="font-semibold text-gray-900 dark:text-white">
          {formatCurrency(item.total_amount, locale)}
        </span>
      ),
    },
    {
      key: 'actions',
      header: isRtl ? 'الإجراءات' : 'Actions',
      render: (item) => (
        <div className="flex items-center gap-1">
          {item.status === 'pending' && (
            <>
              <button onClick={(e) => { e.stopPropagation(); handleAction(item.id, 'approve'); }}
                className="p-2 rounded-lg hover:bg-emerald-500/10 text-emerald-500 transition-colors" title={isRtl ? 'موافقة' : 'Approve'}>
                <CheckCircle className="w-4 h-4" />
              </button>
              <button onClick={(e) => { e.stopPropagation(); handleAction(item.id, 'reject'); }}
                className="p-2 rounded-lg hover:bg-red-500/10 text-red-500 transition-colors" title={isRtl ? 'رفض' : 'Reject'}>
                <XCircle className="w-4 h-4" />
              </button>
            </>
          )}
          {item.status === 'active' && (
            <button onClick={(e) => { e.stopPropagation(); handleAction(item.id, 'terminate'); }}
              className="p-2 rounded-lg hover:bg-amber-500/10 text-amber-500 transition-colors" title={isRtl ? 'إنهاء' : 'Terminate'}>
              <Ban className="w-4 h-4" />
            </button>
          )}
        </div>
      ),
    },
  ];

  const statuses = ['draft', 'pending', 'active', 'expired', 'cancelled', 'terminated'];

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900 dark:text-white">{isRtl ? 'عقود التأجير' : 'Rental Contracts'}</h1>
          <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">{isRtl ? 'إدارة عقود التأجير' : 'Manage rental contracts'}</p>
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

      <DataTable columns={columns} data={contracts as unknown as Record<string, unknown>[]} loading={loading} pagination={pagination}
        onPageChange={(page) => setPagination(p => ({ ...p, current_page: page }))} emptyMessage={isRtl ? 'لا توجد عقود' : 'No contracts found'} />
    </div>
  );
}
