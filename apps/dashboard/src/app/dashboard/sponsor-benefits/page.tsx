'use client';

import { useState, useEffect } from 'react';
import DataTable, { Column } from '@/components/ui/DataTable';
import StatusBadge from '@/components/ui/StatusBadge';
import { expoApi } from '@/lib/api';
import { Plus, Edit, Gift, CheckCircle } from 'lucide-react';

interface SponsorBenefit {
  id: string;
  sponsor_contract_id: string;
  contract?: { id: string; sponsor?: { id: string; company_name?: string; name?: string } };
  benefit_type: string | { value: string };
  description: string;
  description_ar: string;
  quantity: number;
  delivered_quantity: number;
  status: string | { value: string };
  delivery_notes: string | null;
  created_at: string;
}

export default function SponsorBenefitsPage() {
  const [benefits, setBenefits] = useState<SponsorBenefit[]>([]);
  const [loading, setLoading] = useState(true);
  const [locale, setLocale] = useState('ar');
  const [pagination, setPagination] = useState({ current_page: 1, last_page: 1, total: 0, per_page: 15 });
  const [showModal, setShowModal] = useState(false);
  const [editingBenefit, setEditingBenefit] = useState<SponsorBenefit | null>(null);
  const [formData, setFormData] = useState({
    description: '', description_ar: '', benefit_type: 'booth', quantity: 1, sponsor_contract_id: ''
  });

  const isRtl = locale === 'ar';

  useEffect(() => { setLocale(localStorage.getItem('locale') || 'ar'); }, []);
  useEffect(() => { fetchBenefits(); }, [pagination.current_page]);

  const fetchBenefits = async () => {
    setLoading(true);
    try {
      const res = await expoApi.get('/manage/sponsor-benefits', { params: { page: pagination.current_page, per_page: pagination.per_page } });
      setBenefits(res.data.data || []);
      if (res.data.pagination) setPagination(res.data.pagination);
    } catch { setBenefits([]); } finally { setLoading(false); }
  };

  const getEnumValue = (val: string | { value: string } | undefined): string => {
    if (!val) return '-';
    if (typeof val === 'object' && val !== null) return val.value || '-';
    return val;
  };

  const handleCreate = () => {
    setEditingBenefit(null);
    setFormData({ description: '', description_ar: '', benefit_type: 'booth', quantity: 1, sponsor_contract_id: '' });
    setShowModal(true);
  };

  const handleEdit = (b: SponsorBenefit) => {
    setEditingBenefit(b);
    setFormData({
      description: b.description || '',
      description_ar: b.description_ar || '',
      benefit_type: getEnumValue(b.benefit_type),
      quantity: b.quantity,
      sponsor_contract_id: b.sponsor_contract_id || '',
    });
    setShowModal(true);
  };

  const handleSubmit = async () => {
    try {
      if (editingBenefit) {
        await expoApi.put(`/manage/sponsor-benefits/${editingBenefit.id}`, {
          description: formData.description,
          description_ar: formData.description_ar,
          quantity: formData.quantity,
        });
      } else {
        await expoApi.post('/manage/sponsor-benefits', formData);
      }
      setShowModal(false); fetchBenefits();
    } catch { /* silent */ }
  };

  const handleDeliver = async (id: string) => {
    try { await expoApi.put(`/manage/sponsor-benefits/${id}/deliver`); fetchBenefits(); } catch { /* silent */ }
  };

  const benefitTypes = ['booth', 'logo_placement', 'speaking_slot', 'social_media', 'email_blast', 'vip_access', 'custom'];

  const columns: Column<SponsorBenefit>[] = [
    {
      key: 'description',
      header: isRtl ? 'الميزة' : 'Benefit',
      render: (item) => (
        <div className="flex items-center gap-3">
          <div className="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500/20 to-teal-500/20 flex items-center justify-center">
            <Gift className="w-5 h-5 text-emerald-500" />
          </div>
          <div>
            <span className="font-medium text-gray-900 dark:text-white">
              {isRtl ? (item.description_ar || item.description) : (item.description || item.description_ar) || '-'}
            </span>
            <p className="text-xs text-gray-500">{item.contract?.sponsor?.company_name || item.contract?.sponsor?.name || '-'}</p>
          </div>
        </div>
      ),
    },
    {
      key: 'benefit_type',
      header: isRtl ? 'النوع' : 'Type',
      render: (item) => (
        <span className="px-2.5 py-1 rounded-lg bg-teal-500/10 text-teal-500 text-xs font-medium capitalize">
          {getEnumValue(item.benefit_type).replace(/_/g, ' ')}
        </span>
      ),
    },
    {
      key: 'delivery',
      header: isRtl ? 'التسليم' : 'Delivery',
      render: (item) => (
        <div className="flex items-center gap-2">
          <div className="flex-1 h-2 rounded-full bg-gray-200 dark:bg-gray-700 max-w-[80px]">
            <div className="h-full rounded-full bg-gradient-to-r from-emerald-500 to-teal-500"
              style={{ width: `${item.quantity > 0 ? Math.min(100, (item.delivered_quantity / item.quantity) * 100) : 0}%` }} />
          </div>
          <span className="text-xs text-gray-500">{item.delivered_quantity}/{item.quantity}</span>
        </div>
      ),
    },
    {
      key: 'status',
      header: isRtl ? 'الحالة' : 'Status',
      render: (item) => <StatusBadge status={getEnumValue(item.status)} />,
    },
    {
      key: 'actions',
      header: isRtl ? 'الإجراءات' : 'Actions',
      render: (item) => (
        <div className="flex items-center gap-1">
          {item.delivered_quantity < item.quantity && (
            <button onClick={(e) => { e.stopPropagation(); handleDeliver(item.id); }}
              className="p-2 rounded-lg hover:bg-emerald-500/10 text-emerald-500 transition-colors" title={isRtl ? 'تسليم' : 'Deliver'}>
              <CheckCircle className="w-4 h-4" />
            </button>
          )}
          <button onClick={(e) => { e.stopPropagation(); handleEdit(item); }}
            className="p-2 rounded-lg hover:bg-blue-500/10 text-blue-500 transition-colors"><Edit className="w-4 h-4" /></button>
        </div>
      ),
    },
  ];

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900 dark:text-white">{isRtl ? 'مزايا الرعاية' : 'Sponsor Benefits'}</h1>
          <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">{isRtl ? 'إدارة مزايا باقات الرعاية' : 'Manage sponsorship benefits'}</p>
        </div>
        <button onClick={handleCreate}
          className="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 text-white text-sm font-medium shadow-lg shadow-emerald-500/25 hover:shadow-xl transition-all duration-300 hover:scale-[1.02]">
          <Plus className="w-4 h-4" />{isRtl ? 'إضافة ميزة' : 'Add Benefit'}
        </button>
      </div>

      <DataTable columns={columns} data={benefits as unknown as Record<string, unknown>[]} loading={loading} pagination={pagination}
        onPageChange={(page) => setPagination(p => ({ ...p, current_page: page }))} emptyMessage={isRtl ? 'لا توجد مزايا' : 'No benefits found'} />

      {showModal && (
        <div className="fixed inset-0 z-50 flex items-center justify-center">
          <div className="fixed inset-0 bg-black/50 backdrop-blur-sm" onClick={() => setShowModal(false)} />
          <div className="relative w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto rounded-2xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/95 dark:bg-gray-900/95 backdrop-blur-2xl shadow-2xl p-6">
            <h2 className="text-lg font-bold text-gray-900 dark:text-white mb-6">
              {editingBenefit ? (isRtl ? 'تعديل الميزة' : 'Edit Benefit') : (isRtl ? 'إضافة ميزة جديدة' : 'Add Benefit')}
            </h2>
            <div className="space-y-4">
              {!editingBenefit && (
                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{isRtl ? 'معرّف عقد الرعاية' : 'Sponsor Contract ID'}</label>
                  <input type="text" value={formData.sponsor_contract_id} onChange={(e) => setFormData({ ...formData, sponsor_contract_id: e.target.value })}
                    placeholder={isRtl ? 'أدخل معرّف العقد' : 'Enter contract UUID'}
                    className="w-full px-4 py-2 rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30" />
                </div>
              )}
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{isRtl ? 'النوع' : 'Type'}</label>
                  <select value={formData.benefit_type} onChange={(e) => setFormData({ ...formData, benefit_type: e.target.value })}
                    disabled={!!editingBenefit}
                    className="w-full px-4 py-2 rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 disabled:opacity-50">
                    {benefitTypes.map(t => <option key={t} value={t}>{t.replace(/_/g, ' ')}</option>)}
                  </select>
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{isRtl ? 'الكمية' : 'Quantity'}</label>
                  <input type="number" value={formData.quantity} onChange={(e) => setFormData({ ...formData, quantity: parseInt(e.target.value) || 1 })}
                    className="w-full px-4 py-2 rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30" />
                </div>
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{isRtl ? 'الوصف (EN)' : 'Description (EN)'}</label>
                <textarea value={formData.description} onChange={(e) => setFormData({ ...formData, description: e.target.value })} rows={2}
                  className="w-full px-4 py-2 rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30" />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{isRtl ? 'الوصف (AR)' : 'Description (AR)'}</label>
                <textarea value={formData.description_ar} onChange={(e) => setFormData({ ...formData, description_ar: e.target.value })} rows={2} dir="rtl"
                  className="w-full px-4 py-2 rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30" />
              </div>
            </div>
            <div className="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-200/60 dark:border-white/10">
              <button onClick={() => setShowModal(false)} className="px-4 py-2 rounded-xl text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/5">{isRtl ? 'إلغاء' : 'Cancel'}</button>
              <button onClick={handleSubmit} className="px-6 py-2 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 text-white text-sm font-medium shadow-lg shadow-emerald-500/25 hover:shadow-xl transition-all duration-300">
                {editingBenefit ? (isRtl ? 'تحديث' : 'Update') : (isRtl ? 'إنشاء' : 'Create')}
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
