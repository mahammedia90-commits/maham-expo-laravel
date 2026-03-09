'use client';

import { useState, useEffect } from 'react';
import DataTable, { Column } from '@/components/ui/DataTable';
import StatusBadge from '@/components/ui/StatusBadge';
import { expoApi } from '@/lib/api';
import { formatDate } from '@/lib/utils';
import { Image, CheckCircle, XCircle, Eye } from 'lucide-react';

interface SponsorAsset {
  id: string;
  sponsor?: { id: string; company_name?: string; name?: string };
  event?: { id: string; name?: string; name_ar?: string };
  type: string | { value: string };
  file_path: string;
  file_name: string;
  file_size: number;
  mime_type: string;
  is_approved: boolean;
  rejection_reason: string | null;
  approved_at: string | null;
  sort_order: number;
  created_at: string;
}

export default function SponsorAssetsPage() {
  const [assets, setAssets] = useState<SponsorAsset[]>([]);
  const [loading, setLoading] = useState(true);
  const [locale, setLocale] = useState('ar');
  const [pagination, setPagination] = useState({ current_page: 1, last_page: 1, total: 0, per_page: 15 });
  const [showRejectModal, setShowRejectModal] = useState(false);
  const [rejectingId, setRejectingId] = useState('');
  const [rejectionReason, setRejectionReason] = useState('');

  const isRtl = locale === 'ar';

  useEffect(() => { setLocale(localStorage.getItem('locale') || 'ar'); }, []);
  useEffect(() => { fetchAssets(); }, [pagination.current_page]);

  const fetchAssets = async () => {
    setLoading(true);
    try {
      const res = await expoApi.get('/manage/sponsor-assets', { params: { page: pagination.current_page, per_page: pagination.per_page } });
      setAssets(res.data.data || []);
      if (res.data.pagination) setPagination(res.data.pagination);
    } catch { setAssets([]); } finally { setLoading(false); }
  };

  const handleApprove = async (id: string) => {
    try { await expoApi.put(`/manage/sponsor-assets/${id}/approve`); fetchAssets(); } catch { /* silent */ }
  };

  const handleRejectClick = (id: string) => {
    setRejectingId(id);
    setRejectionReason('');
    setShowRejectModal(true);
  };

  const handleRejectSubmit = async () => {
    if (!rejectionReason.trim()) return;
    try {
      await expoApi.put(`/manage/sponsor-assets/${rejectingId}/reject`, { rejection_reason: rejectionReason });
      setShowRejectModal(false);
      fetchAssets();
    } catch { /* silent */ }
  };

  const getAssetStatus = (item: SponsorAsset): string => {
    if (item.is_approved) return 'approved';
    if (item.rejection_reason) return 'rejected';
    return 'pending';
  };

  const isPending = (item: SponsorAsset): boolean => {
    return !item.is_approved && !item.rejection_reason;
  };

  const getTypeValue = (type: string | { value: string }): string => {
    if (typeof type === 'object' && type !== null) return type.value || '-';
    return type || '-';
  };

  const typeIcons: Record<string, string> = {
    logo: 'from-blue-500/20 to-blue-600/20',
    banner: 'from-pink-500/20 to-pink-600/20',
    video: 'from-purple-500/20 to-purple-600/20',
    document: 'from-amber-500/20 to-amber-600/20',
  };

  const columns: Column<SponsorAsset>[] = [
    {
      key: 'file_name',
      header: isRtl ? 'الملف' : 'File',
      render: (item) => {
        const typeVal = getTypeValue(item.type);
        return (
          <div className="flex items-center gap-3">
            <div className={`w-10 h-10 rounded-xl bg-gradient-to-br ${typeIcons[typeVal] || 'from-gray-500/20 to-gray-600/20'} flex items-center justify-center`}>
              <Image className="w-5 h-5 text-gray-500" />
            </div>
            <div>
              <span className="font-medium text-gray-900 dark:text-white text-sm truncate max-w-[200px] block">{item.file_name || '-'}</span>
              <p className="text-xs text-gray-500">{item.sponsor?.company_name || item.sponsor?.name || '-'}</p>
            </div>
          </div>
        );
      },
    },
    {
      key: 'type',
      header: isRtl ? 'النوع' : 'Type',
      render: (item) => <span className="px-2.5 py-1 rounded-lg bg-blue-500/10 text-blue-500 text-xs font-medium capitalize">{getTypeValue(item.type)}</span>,
    },
    {
      key: 'event',
      header: isRtl ? 'الحدث' : 'Event',
      render: (item) => <span className="text-sm text-gray-600 dark:text-gray-300">{isRtl ? item.event?.name_ar : item.event?.name || '-'}</span>,
    },
    {
      key: 'created_at',
      header: isRtl ? 'تاريخ الرفع' : 'Uploaded',
      render: (item) => <span className="text-sm text-gray-500">{item.created_at ? formatDate(item.created_at, locale) : '-'}</span>,
    },
    {
      key: 'is_approved',
      header: isRtl ? 'الحالة' : 'Status',
      render: (item) => <StatusBadge status={getAssetStatus(item)} />,
    },
    {
      key: 'actions',
      header: isRtl ? 'الإجراءات' : 'Actions',
      render: (item) => (
        <div className="flex items-center gap-1">
          {item.file_path && (
            <a href={item.file_path} target="_blank" rel="noopener noreferrer"
              className="p-2 rounded-lg hover:bg-blue-500/10 text-blue-500 transition-colors"><Eye className="w-4 h-4" /></a>
          )}
          {isPending(item) && (
            <>
              <button onClick={(e) => { e.stopPropagation(); handleApprove(item.id); }}
                className="p-2 rounded-lg hover:bg-emerald-500/10 text-emerald-500 transition-colors"><CheckCircle className="w-4 h-4" /></button>
              <button onClick={(e) => { e.stopPropagation(); handleRejectClick(item.id); }}
                className="p-2 rounded-lg hover:bg-red-500/10 text-red-500 transition-colors"><XCircle className="w-4 h-4" /></button>
            </>
          )}
        </div>
      ),
    },
  ];

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold text-gray-900 dark:text-white">{isRtl ? 'أصول الرعاة' : 'Sponsor Assets'}</h1>
        <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">{isRtl ? 'مراجعة وإدارة ملفات الرعاة' : 'Review and manage sponsor uploaded assets'}</p>
      </div>
      <DataTable columns={columns} data={assets as unknown as Record<string, unknown>[]} loading={loading} pagination={pagination}
        onPageChange={(page) => setPagination(p => ({ ...p, current_page: page }))} emptyMessage={isRtl ? 'لا توجد أصول' : 'No assets found'} />

      {/* Rejection Reason Modal */}
      {showRejectModal && (
        <div className="fixed inset-0 z-50 flex items-center justify-center">
          <div className="fixed inset-0 bg-black/50 backdrop-blur-sm" onClick={() => setShowRejectModal(false)} />
          <div className="relative w-full max-w-md mx-4 rounded-2xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/95 dark:bg-gray-900/95 backdrop-blur-2xl shadow-2xl p-6">
            <h2 className="text-lg font-bold text-gray-900 dark:text-white mb-4">
              {isRtl ? 'سبب الرفض' : 'Rejection Reason'}
            </h2>
            <textarea
              value={rejectionReason}
              onChange={(e) => setRejectionReason(e.target.value)}
              rows={4}
              placeholder={isRtl ? 'اكتب سبب الرفض...' : 'Enter rejection reason...'}
              className="w-full px-4 py-2 rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500/30 transition-all resize-none"
              dir={isRtl ? 'rtl' : 'ltr'}
            />
            <div className="flex items-center justify-end gap-3 mt-4">
              <button onClick={() => setShowRejectModal(false)}
                className="px-4 py-2 rounded-xl text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">
                {isRtl ? 'إلغاء' : 'Cancel'}
              </button>
              <button onClick={handleRejectSubmit} disabled={!rejectionReason.trim()}
                className="px-6 py-2 rounded-xl bg-gradient-to-r from-red-500 to-rose-600 text-white text-sm font-medium shadow-lg shadow-red-500/25 hover:shadow-xl transition-all duration-300 disabled:opacity-50">
                {isRtl ? 'رفض' : 'Reject'}
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
