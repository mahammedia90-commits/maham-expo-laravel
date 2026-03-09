'use client';

import { useState, useEffect } from 'react';
import GlassCard from '@/components/ui/GlassCard';
import DataTable, { Column } from '@/components/ui/DataTable';
import StatusBadge from '@/components/ui/StatusBadge';
import { expoApi } from '@/lib/api';
import { formatDate } from '@/lib/utils';
import { Star, CheckCircle, XCircle, Trash2, Filter } from 'lucide-react';

interface RatingItem {
  id: string;
  user_id: string;
  overall_rating: number;
  type: string | { value: string };
  comment: string;
  comment_ar: string;
  is_approved: boolean;
  rateable?: { id: string; name?: string; name_ar?: string };
  created_at: string;
}

export default function RatingsPage() {
  const [ratings, setRatings] = useState<RatingItem[]>([]);
  const [loading, setLoading] = useState(true);
  const [locale, setLocale] = useState('ar');
  const [pagination, setPagination] = useState({ current_page: 1, last_page: 1, total: 0, per_page: 15 });
  const [approvalFilter, setApprovalFilter] = useState('');

  const isRtl = locale === 'ar';

  useEffect(() => { setLocale(localStorage.getItem('locale') || 'ar'); }, []);
  useEffect(() => { fetchRatings(); }, [pagination.current_page, approvalFilter]);

  const fetchRatings = async () => {
    setLoading(true);
    try {
      const params: Record<string, string | number> = { page: pagination.current_page, per_page: pagination.per_page };
      if (approvalFilter !== '') params.is_approved = approvalFilter;
      const res = await expoApi.get('/manage/ratings', { params });
      setRatings(res.data.data || []);
      if (res.data.pagination) setPagination(res.data.pagination);
    } catch { setRatings([]); } finally { setLoading(false); }
  };

  const handleAction = async (id: string, action: string) => {
    try { await expoApi.put(`/manage/ratings/${id}/${action}`); fetchRatings(); } catch { /* silent */ }
  };

  const handleDelete = async (id: string) => {
    if (!confirm(isRtl ? 'هل أنت متأكد من الحذف؟' : 'Are you sure?')) return;
    try { await expoApi.delete(`/manage/ratings/${id}`); fetchRatings(); } catch { /* silent */ }
  };

  const getApprovalStatus = (item: RatingItem): string => {
    return item.is_approved ? 'approved' : 'pending';
  };

  const getTypeLabel = (type: string | { value: string }): string => {
    if (typeof type === 'object' && type !== null) return type.value || '-';
    return type || '-';
  };

  const renderStars = (rating: number) => (
    <div className="flex items-center gap-0.5">
      {[1, 2, 3, 4, 5].map((s) => (
        <Star key={s} className={`w-4 h-4 ${s <= rating ? 'fill-amber-400 text-amber-400' : 'text-gray-300 dark:text-gray-600'}`} />
      ))}
      <span className="ms-1 text-sm font-medium text-gray-600 dark:text-gray-300">{rating}</span>
    </div>
  );

  const columns: Column<RatingItem>[] = [
    {
      key: 'user_id',
      header: isRtl ? 'المستخدم' : 'User',
      render: (item) => (
        <div className="flex items-center gap-3">
          <div className="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500/20 to-yellow-500/20 flex items-center justify-center">
            <Star className="w-5 h-5 text-amber-500" />
          </div>
          <span className="font-medium text-gray-900 dark:text-white text-sm">{item.user_id?.slice(0, 8) || '-'}</span>
        </div>
      ),
    },
    {
      key: 'overall_rating',
      header: isRtl ? 'التقييم' : 'Rating',
      render: (item) => renderStars(item.overall_rating),
    },
    {
      key: 'type',
      header: isRtl ? 'النوع' : 'Type',
      render: (item) => <span className="text-sm capitalize text-gray-600 dark:text-gray-300">{getTypeLabel(item.type)}</span>,
    },
    {
      key: 'comment',
      header: isRtl ? 'التعليق' : 'Comment',
      render: (item) => (
        <p className="text-sm text-gray-600 dark:text-gray-300 max-w-xs truncate">
          {isRtl ? (item.comment_ar || item.comment) : (item.comment || item.comment_ar) || '-'}
        </p>
      ),
    },
    {
      key: 'is_approved',
      header: isRtl ? 'الحالة' : 'Status',
      render: (item) => <StatusBadge status={getApprovalStatus(item)} />,
    },
    {
      key: 'created_at',
      header: isRtl ? 'التاريخ' : 'Date',
      render: (item) => <span className="text-sm text-gray-500">{item.created_at ? formatDate(item.created_at, locale) : '-'}</span>,
    },
    {
      key: 'actions',
      header: isRtl ? 'الإجراءات' : 'Actions',
      render: (item) => (
        <div className="flex items-center gap-1">
          {!item.is_approved && (
            <>
              <button onClick={(e) => { e.stopPropagation(); handleAction(item.id, 'approve'); }}
                className="p-2 rounded-lg hover:bg-emerald-500/10 text-emerald-500 transition-colors" title={isRtl ? 'اعتماد' : 'Approve'}>
                <CheckCircle className="w-4 h-4" />
              </button>
              <button onClick={(e) => { e.stopPropagation(); handleAction(item.id, 'reject'); }}
                className="p-2 rounded-lg hover:bg-red-500/10 text-red-500 transition-colors" title={isRtl ? 'رفض' : 'Reject'}>
                <XCircle className="w-4 h-4" />
              </button>
            </>
          )}
          <button onClick={(e) => { e.stopPropagation(); handleDelete(item.id); }}
            className="p-2 rounded-lg hover:bg-red-500/10 text-red-500 transition-colors" title={isRtl ? 'حذف' : 'Delete'}>
            <Trash2 className="w-4 h-4" />
          </button>
        </div>
      ),
    },
  ];

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold text-gray-900 dark:text-white">{isRtl ? 'التقييمات' : 'Ratings'}</h1>
        <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">{isRtl ? 'إدارة تقييمات المستخدمين' : 'Manage user ratings & reviews'}</p>
      </div>

      <GlassCard>
        <div className="flex flex-wrap items-center gap-4">
          <Filter className="w-4 h-4 text-gray-400" />
          <select value={approvalFilter} onChange={(e) => { setApprovalFilter(e.target.value); setPagination(p => ({ ...p, current_page: 1 })); }}
            className="px-3 py-2 rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-all">
            <option value="">{isRtl ? 'جميع الحالات' : 'All Statuses'}</option>
            <option value="0">{isRtl ? 'بانتظار المراجعة' : 'Pending'}</option>
            <option value="1">{isRtl ? 'معتمد' : 'Approved'}</option>
          </select>
        </div>
      </GlassCard>

      <DataTable columns={columns} data={ratings as unknown as Record<string, unknown>[]} loading={loading} pagination={pagination}
        onPageChange={(page) => setPagination(p => ({ ...p, current_page: page }))} emptyMessage={isRtl ? 'لا توجد تقييمات' : 'No ratings found'} />
    </div>
  );
}
