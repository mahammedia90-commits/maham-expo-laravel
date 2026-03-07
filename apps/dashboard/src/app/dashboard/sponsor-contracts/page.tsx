'use client';

import { useState, useEffect } from 'react';
import DataTable, { Column } from '@/components/ui/DataTable';
import StatusBadge from '@/components/ui/StatusBadge';
import { expoApi } from '@/lib/api';
import { formatDate, formatCurrency } from '@/lib/utils';
import { FileSignature, CheckCircle, XCircle, Flag } from 'lucide-react';

interface SponsorContract {
  id: string;
  sponsor?: { company_name: string; company_name_ar: string };
  package?: { name: string; name_ar: string; tier: string };
  event?: { name: string; name_ar: string };
  contract_number: string;
  total_amount: number;
  paid_amount: number;
  status: string;
  signed_at: string | null;
  starts_at: string;
  ends_at: string;
  created_at: string;
}

export default function SponsorContractsPage() {
  const [contracts, setContracts] = useState<SponsorContract[]>([]);
  const [loading, setLoading] = useState(true);
  const [locale, setLocale] = useState('ar');
  const [pagination, setPagination] = useState({ current_page: 1, last_page: 1, total: 0, per_page: 15 });

  const isRtl = locale === 'ar';

  useEffect(() => { setLocale(localStorage.getItem('locale') || 'ar'); }, []);
  useEffect(() => { fetchContracts(); }, [pagination.current_page]);

  const fetchContracts = async () => {
    setLoading(true);
    try {
      const res = await expoApi.get('/manage/sponsor-contracts', { params: { page: pagination.current_page, per_page: pagination.per_page } });
      setContracts(res.data.data || []);
      if (res.data.pagination) setPagination(res.data.pagination);
    } catch { setContracts([]); } finally { setLoading(false); }
  };

  const handleAction = async (id: string, action: string) => {
    try { await expoApi.put(`/manage/sponsor-contracts/${id}/${action}`); fetchContracts(); } catch { /* silent */ }
  };

  const columns: Column<SponsorContract>[] = [
    {
      key: 'contract_number',
      header: isRtl ? 'رقم العقد' : 'Contract #',
      render: (item) => (
        <div className="flex items-center gap-3">
          <div className="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500/20 to-purple-500/20 flex items-center justify-center">
            <FileSignature className="w-5 h-5 text-indigo-500" />
          </div>
          <div>
            <span className="font-mono font-medium text-gray-900 dark:text-white">{item.contract_number}</span>
            <p className="text-xs text-gray-500">{item.sponsor?.company_name || '-'}</p>
          </div>
        </div>
      ),
    },
    {
      key: 'package',
      header: isRtl ? 'الباقة' : 'Package',
      render: (item) => <span className="text-sm text-gray-600 dark:text-gray-300">{isRtl ? item.package?.name_ar : item.package?.name}</span>,
    },
    {
      key: 'total_amount',
      header: isRtl ? 'المبلغ' : 'Amount',
      render: (item) => (
        <div>
          <span className="font-semibold text-gray-900 dark:text-white">{formatCurrency(item.total_amount)}</span>
          {item.paid_amount > 0 && item.paid_amount < item.total_amount && (
            <p className="text-xs text-amber-500">{isRtl ? 'مدفوع' : 'Paid'}: {formatCurrency(item.paid_amount)}</p>
          )}
        </div>
      ),
    },
    {
      key: 'period',
      header: isRtl ? 'الفترة' : 'Period',
      render: (item) => (
        <div className="text-xs text-gray-500">
          {item.starts_at && <div>{formatDate(item.starts_at, locale)}</div>}
          {item.ends_at && <div>→ {formatDate(item.ends_at, locale)}</div>}
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
          {item.status === 'pending' && (
            <>
              <button onClick={(e) => { e.stopPropagation(); handleAction(item.id, 'approve'); }}
                className="p-2 rounded-lg hover:bg-emerald-500/10 text-emerald-500 transition-colors"><CheckCircle className="w-4 h-4" /></button>
              <button onClick={(e) => { e.stopPropagation(); handleAction(item.id, 'reject'); }}
                className="p-2 rounded-lg hover:bg-red-500/10 text-red-500 transition-colors"><XCircle className="w-4 h-4" /></button>
            </>
          )}
          {item.status === 'active' && (
            <button onClick={(e) => { e.stopPropagation(); handleAction(item.id, 'complete'); }}
              className="p-2 rounded-lg hover:bg-blue-500/10 text-blue-500 transition-colors"><Flag className="w-4 h-4" /></button>
          )}
        </div>
      ),
    },
  ];

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold text-gray-900 dark:text-white">{isRtl ? 'عقود الرعاية' : 'Sponsor Contracts'}</h1>
        <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">{isRtl ? 'إدارة عقود الرعاية' : 'Manage sponsorship contracts'}</p>
      </div>
      <DataTable columns={columns} data={contracts as unknown as Record<string, unknown>[]} loading={loading} pagination={pagination}
        onPageChange={(page) => setPagination(p => ({ ...p, current_page: page }))} emptyMessage={isRtl ? 'لا توجد عقود' : 'No contracts found'} />
    </div>
  );
}
