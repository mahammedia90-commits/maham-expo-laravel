'use client';

import { useState, useEffect, useRef } from 'react';
import GlassCard from '@/components/ui/GlassCard';
import DataTable, { Column } from '@/components/ui/DataTable';
import StatusBadge from '@/components/ui/StatusBadge';
import { expoApi } from '@/lib/api';
import { formatDate } from '@/lib/utils';
import { Event } from '@/types';
import { Plus, Search, Filter, Calendar, Eye, Edit, Trash2, Upload, X, ImageIcon } from 'lucide-react';

interface EventFormData {
  name: string;
  name_ar: string;
  description: string;
  description_ar: string;
  category_id: string;
  city_id: string;
  address: string;
  address_ar: string;
  start_date: string;
  end_date: string;
  opening_time: string;
  closing_time: string;
  status: string;
  is_featured: boolean;
  organizer_name: string;
  organizer_phone: string;
  organizer_email: string;
  website: string;
}

const defaultFormData: EventFormData = {
  name: '',
  name_ar: '',
  description: '',
  description_ar: '',
  category_id: '',
  city_id: '',
  address: '',
  address_ar: '',
  start_date: '',
  end_date: '',
  opening_time: '09:00',
  closing_time: '22:00',
  status: 'draft',
  is_featured: false,
  organizer_name: '',
  organizer_phone: '',
  organizer_email: '',
  website: '',
};

export default function EventsPage() {
  const [events, setEvents] = useState<Event[]>([]);
  const [loading, setLoading] = useState(true);
  const [locale, setLocale] = useState('ar');
  const [pagination, setPagination] = useState({ current_page: 1, last_page: 1, total: 0, per_page: 15 });
  const [statusFilter, setStatusFilter] = useState('');
  const [categoryFilter, setCategoryFilter] = useState('');
  const [searchQuery, setSearchQuery] = useState('');
  const [showModal, setShowModal] = useState(false);
  const [editingEvent, setEditingEvent] = useState<Event | null>(null);
  const [formData, setFormData] = useState<EventFormData>(defaultFormData);
  const [imageFiles, setImageFiles] = useState<File[]>([]);
  const [imagePreviews, setImagePreviews] = useState<string[]>([]);
  const [existingImages, setExistingImages] = useState<string[]>([]);
  const [image360Files, setImage360Files] = useState<File[]>([]);
  const [image360Previews, setImage360Previews] = useState<string[]>([]);
  const [existingImages360, setExistingImages360] = useState<string[]>([]);
  const [categories, setCategories] = useState<{ id: string; name: string; name_ar: string }[]>([]);
  const [cities, setCities] = useState<{ id: string; name: string; name_ar: string }[]>([]);
  const [submitting, setSubmitting] = useState(false);
  const [errors, setErrors] = useState<Record<string, string[]>>({});
  const fileInputRef = useRef<HTMLInputElement>(null);
  const file360InputRef = useRef<HTMLInputElement>(null);

  const isRtl = locale === 'ar';

  useEffect(() => {
    setLocale(localStorage.getItem('locale') || 'ar');
    fetchCategories();
    fetchCities();
  }, []);

  useEffect(() => {
    fetchEvents();
  }, [pagination.current_page, statusFilter, categoryFilter, searchQuery]);

  const fetchCategories = async () => {
    try {
      const res = await expoApi.get('/categories');
      setCategories(res.data.data || []);
    } catch { /* silent */ }
  };

  const fetchCities = async () => {
    try {
      const res = await expoApi.get('/cities');
      setCities(res.data.data || []);
    } catch { /* silent */ }
  };

  const fetchEvents = async () => {
    setLoading(true);
    try {
      const params: Record<string, string | number> = { page: pagination.current_page, per_page: pagination.per_page };
      if (statusFilter) params.status = statusFilter;
      if (categoryFilter) params.category_id = categoryFilter;
      if (searchQuery) params.search = searchQuery;
      const res = await expoApi.get('/manage/events', { params });
      setEvents(res.data.data || []);
      if (res.data.pagination) {
        setPagination(res.data.pagination);
      }
    } catch {
      setEvents([]);
    } finally {
      setLoading(false);
    }
  };

  const handleCreate = () => {
    setEditingEvent(null);
    setFormData(defaultFormData);
    setImageFiles([]);
    setImagePreviews([]);
    setExistingImages([]);
    setImage360Files([]);
    setImage360Previews([]);
    setExistingImages360([]);
    setErrors({});
    setShowModal(true);
  };

  const handleEdit = async (event: Event) => {
    setEditingEvent(event);
    setFormData({
      name: event.name || '',
      name_ar: event.name_ar || '',
      description: event.description || '',
      description_ar: event.description_ar || '',
      category_id: event.category?.id || '',
      city_id: event.city?.id || '',
      address: event.address || '',
      address_ar: event.address_ar || '',
      start_date: event.start_date || '',
      end_date: event.end_date || '',
      opening_time: event.opening_time || '09:00',
      closing_time: event.closing_time || '22:00',
      status: event.status || 'draft',
      is_featured: event.is_featured || false,
      organizer_name: event.organizer_name || '',
      organizer_phone: event.organizer_phone || '',
      organizer_email: event.organizer_email || '',
      website: event.website || '',
    });
    setImageFiles([]);
    setImagePreviews([]);
    setExistingImages(event.images || []);
    setImage360Files([]);
    setImage360Previews([]);
    setExistingImages360(event.images_360 || []);
    setErrors({});
    setShowModal(true);
  };

  const handleImageSelect = (e: React.ChangeEvent<HTMLInputElement>) => {
    const files = Array.from(e.target.files || []);
    if (files.length === 0) return;

    setImageFiles(prev => [...prev, ...files]);

    files.forEach(file => {
      const reader = new FileReader();
      reader.onload = (ev) => {
        setImagePreviews(prev => [...prev, ev.target?.result as string]);
      };
      reader.readAsDataURL(file);
    });

    if (fileInputRef.current) fileInputRef.current.value = '';
  };

  const removeNewImage = (index: number) => {
    setImageFiles(prev => prev.filter((_, i) => i !== index));
    setImagePreviews(prev => prev.filter((_, i) => i !== index));
  };

  const removeExistingImage = (index: number) => {
    setExistingImages(prev => prev.filter((_, i) => i !== index));
  };

  const handle360ImageSelect = (e: React.ChangeEvent<HTMLInputElement>) => {
    const files = Array.from(e.target.files || []);
    if (files.length === 0) return;
    setImage360Files(prev => [...prev, ...files]);
    files.forEach(file => {
      const reader = new FileReader();
      reader.onload = (ev) => {
        setImage360Previews(prev => [...prev, ev.target?.result as string]);
      };
      reader.readAsDataURL(file);
    });
    if (file360InputRef.current) file360InputRef.current.value = '';
  };

  const removeNew360Image = (index: number) => {
    setImage360Files(prev => prev.filter((_, i) => i !== index));
    setImage360Previews(prev => prev.filter((_, i) => i !== index));
  };

  const removeExisting360Image = (index: number) => {
    setExistingImages360(prev => prev.filter((_, i) => i !== index));
  };

  const handleSubmit = async () => {
    setSubmitting(true);
    setErrors({});
    try {
      const fd = new FormData();
      Object.entries(formData).forEach(([key, value]) => {
        if (value !== '' && value !== null && value !== undefined) {
          if (key === 'is_featured') {
            fd.append(key, value ? '1' : '0');
          } else {
            fd.append(key, String(value));
          }
        }
      });

      imageFiles.forEach((file) => {
        fd.append('images[]', file);
      });

      image360Files.forEach((file) => {
        fd.append('images_360[]', file);
      });

      if (editingEvent && existingImages.length > 0) {
        existingImages.forEach((url) => {
          fd.append('existing_images[]', url);
        });
      }

      if (editingEvent && existingImages360.length > 0) {
        existingImages360.forEach((url) => {
          fd.append('existing_images_360[]', url);
        });
      }

      if (editingEvent) {
        fd.append('_method', 'PUT');
        await expoApi.post(`/manage/events/${editingEvent.id}`, fd, {
          headers: { 'Content-Type': 'multipart/form-data' },
        });
      } else {
        await expoApi.post('/manage/events', fd, {
          headers: { 'Content-Type': 'multipart/form-data' },
        });
      }

      setShowModal(false);
      fetchEvents();
    } catch (err: unknown) {
      const error = err as { response?: { data?: { errors?: Record<string, string[]>; message?: string } } };
      if (error.response?.data?.errors) {
        setErrors(error.response.data.errors);
      }
    } finally {
      setSubmitting(false);
    }
  };

  const handleDelete = async (id: string) => {
    if (!confirm(isRtl ? 'هل أنت متأكد من الحذف؟' : 'Are you sure you want to delete?')) return;
    try {
      await expoApi.delete(`/manage/events/${id}`);
      fetchEvents();
    } catch { /* silent */ }
  };

  const columns: Column<Event>[] = [
    {
      key: 'name',
      header: isRtl ? 'الاسم' : 'Name',
      render: (item) => (
        <div className="flex items-center gap-3">
          <div className="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500/20 to-purple-500/20 flex items-center justify-center overflow-hidden">
            {item.images && item.images[0] ? (
              <img src={item.images[0]} alt="" className="w-full h-full object-cover" />
            ) : (
              <Calendar className="w-5 h-5 text-blue-500" />
            )}
          </div>
          <div>
            <p className="font-medium text-gray-900 dark:text-white">{isRtl ? item.name_ar : item.name}</p>
            <p className="text-xs text-gray-500">{isRtl ? item.name : item.name_ar}</p>
          </div>
        </div>
      ),
    },
    {
      key: 'category',
      header: isRtl ? 'التصنيف' : 'Category',
      render: (item) => (
        <span className="text-sm text-gray-600 dark:text-gray-300">
          {isRtl ? item.category?.name_ar : item.category?.name}
        </span>
      ),
    },
    {
      key: 'city',
      header: isRtl ? 'المدينة' : 'City',
      render: (item) => (
        <span className="text-sm text-gray-600 dark:text-gray-300">
          {isRtl ? item.city?.name_ar : item.city?.name}
        </span>
      ),
    },
    {
      key: 'status',
      header: isRtl ? 'الحالة' : 'Status',
      render: (item) => <StatusBadge status={item.status} />,
    },
    {
      key: 'start_date',
      header: isRtl ? 'تاريخ البدء' : 'Start Date',
      render: (item) => <span className="text-sm">{item.start_date ? formatDate(item.start_date, locale) : '-'}</span>,
    },
    {
      key: 'end_date',
      header: isRtl ? 'تاريخ الانتهاء' : 'End Date',
      render: (item) => <span className="text-sm">{item.end_date ? formatDate(item.end_date, locale) : '-'}</span>,
    },
    {
      key: 'spaces',
      header: isRtl ? 'المساحات' : 'Spaces',
      render: (item) => (
        <span className="text-sm font-medium">
          {item.available_spaces_count}/{item.total_spaces_count}
        </span>
      ),
    },
    {
      key: 'views',
      header: isRtl ? 'المشاهدات' : 'Views',
      render: (item) => (
        <div className="flex items-center gap-1 text-sm text-gray-500">
          <Eye className="w-3.5 h-3.5" />
          {item.views_count?.toLocaleString() || 0}
        </div>
      ),
    },
    {
      key: 'actions',
      header: isRtl ? 'الإجراءات' : 'Actions',
      render: (item) => (
        <div className="flex items-center gap-1">
          <button
            onClick={(e) => { e.stopPropagation(); handleEdit(item); }}
            className="p-2 rounded-lg hover:bg-blue-500/10 text-blue-500 transition-colors"
            title={isRtl ? 'تعديل' : 'Edit'}
          >
            <Edit className="w-4 h-4" />
          </button>
          <button
            onClick={(e) => { e.stopPropagation(); handleDelete(item.id); }}
            className="p-2 rounded-lg hover:bg-red-500/10 text-red-500 transition-colors"
            title={isRtl ? 'حذف' : 'Delete'}
          >
            <Trash2 className="w-4 h-4" />
          </button>
        </div>
      ),
    },
  ];

  const inputClass = "w-full px-4 py-2 rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-all";
  const labelClass = "block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1";

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900 dark:text-white">
            {isRtl ? 'الفعاليات' : 'Events'}
          </h1>
          <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">
            {isRtl ? 'إدارة جميع الفعاليات والمعارض' : 'Manage all events and exhibitions'}
          </p>
        </div>
        <button
          onClick={handleCreate}
          className="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-blue-500 to-purple-600 text-white text-sm font-medium shadow-lg shadow-blue-500/25 hover:shadow-xl hover:shadow-blue-500/30 transition-all duration-300 hover:scale-[1.02]"
        >
          <Plus className="w-4 h-4" />
          {isRtl ? 'إنشاء فعالية' : 'Create Event'}
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
              placeholder={isRtl ? 'بحث عن فعالية...' : 'Search events...'}
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
              <option value="draft">{isRtl ? 'مسودة' : 'Draft'}</option>
              <option value="published">{isRtl ? 'منشور' : 'Published'}</option>
              <option value="ended">{isRtl ? 'منتهي' : 'Ended'}</option>
              <option value="cancelled">{isRtl ? 'ملغي' : 'Cancelled'}</option>
            </select>
            <select
              value={categoryFilter}
              onChange={(e) => setCategoryFilter(e.target.value)}
              className="px-3 py-2 rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-all text-gray-700 dark:text-gray-300"
            >
              <option value="">{isRtl ? 'جميع التصنيفات' : 'All Categories'}</option>
              {categories.map(cat => (
                <option key={cat.id} value={cat.id}>{isRtl ? cat.name_ar : cat.name}</option>
              ))}
            </select>
          </div>
        </div>
      </GlassCard>

      {/* Data Table */}
      <DataTable
        columns={columns}
        data={events as unknown as Record<string, unknown>[]}
        loading={loading}
        pagination={pagination}
        onPageChange={(page) => setPagination((prev) => ({ ...prev, current_page: page }))}
        emptyMessage={isRtl ? 'لا توجد فعاليات' : 'No events found'}
      />

      {/* Create/Edit Modal */}
      {showModal && (
        <div className="fixed inset-0 z-50 flex items-center justify-center">
          <div className="fixed inset-0 bg-black/50 backdrop-blur-sm" onClick={() => setShowModal(false)} />
          <div className="relative w-full max-w-4xl mx-4 max-h-[90vh] overflow-y-auto rounded-2xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/95 dark:bg-gray-900/95 backdrop-blur-2xl shadow-2xl p-6">
            <div className="flex items-center justify-between mb-6">
              <h2 className="text-lg font-bold text-gray-900 dark:text-white">
                {editingEvent ? (isRtl ? 'تعديل الفعالية' : 'Edit Event') : (isRtl ? 'إنشاء فعالية جديدة' : 'Create New Event')}
              </h2>
              <button onClick={() => setShowModal(false)} className="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-white/10 transition-colors">
                <X className="w-5 h-5 text-gray-500" />
              </button>
            </div>

            <div className="space-y-6">
              {/* Names */}
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label className={labelClass}>Event Name (EN) *</label>
                  <input type="text" value={formData.name} onChange={(e) => setFormData({ ...formData, name: e.target.value })} className={inputClass} />
                  {errors.name && <p className="text-xs text-red-500 mt-1">{errors.name[0]}</p>}
                </div>
                <div>
                  <label className={labelClass}>اسم الفعالية (AR) *</label>
                  <input type="text" value={formData.name_ar} onChange={(e) => setFormData({ ...formData, name_ar: e.target.value })} className={inputClass} dir="rtl" />
                  {errors.name_ar && <p className="text-xs text-red-500 mt-1">{errors.name_ar[0]}</p>}
                </div>
              </div>

              {/* Descriptions */}
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label className={labelClass}>Description (EN)</label>
                  <textarea value={formData.description} onChange={(e) => setFormData({ ...formData, description: e.target.value })} rows={3} className={inputClass} />
                </div>
                <div>
                  <label className={labelClass}>الوصف (AR)</label>
                  <textarea value={formData.description_ar} onChange={(e) => setFormData({ ...formData, description_ar: e.target.value })} rows={3} className={inputClass} dir="rtl" />
                </div>
              </div>

              {/* Category & City */}
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label className={labelClass}>{isRtl ? 'التصنيف' : 'Category'} *</label>
                  <select value={formData.category_id} onChange={(e) => setFormData({ ...formData, category_id: e.target.value })} className={inputClass}>
                    <option value="">{isRtl ? 'اختر التصنيف' : 'Select Category'}</option>
                    {categories.map(cat => (
                      <option key={cat.id} value={cat.id}>{isRtl ? cat.name_ar : cat.name}</option>
                    ))}
                  </select>
                  {errors.category_id && <p className="text-xs text-red-500 mt-1">{errors.category_id[0]}</p>}
                </div>
                <div>
                  <label className={labelClass}>{isRtl ? 'المدينة' : 'City'} *</label>
                  <select value={formData.city_id} onChange={(e) => setFormData({ ...formData, city_id: e.target.value })} className={inputClass}>
                    <option value="">{isRtl ? 'اختر المدينة' : 'Select City'}</option>
                    {cities.map(city => (
                      <option key={city.id} value={city.id}>{isRtl ? city.name_ar : city.name}</option>
                    ))}
                  </select>
                  {errors.city_id && <p className="text-xs text-red-500 mt-1">{errors.city_id[0]}</p>}
                </div>
              </div>

              {/* Address */}
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label className={labelClass}>Address (EN)</label>
                  <input type="text" value={formData.address} onChange={(e) => setFormData({ ...formData, address: e.target.value })} className={inputClass} />
                </div>
                <div>
                  <label className={labelClass}>العنوان (AR)</label>
                  <input type="text" value={formData.address_ar} onChange={(e) => setFormData({ ...formData, address_ar: e.target.value })} className={inputClass} dir="rtl" />
                </div>
              </div>

              {/* Dates & Times */}
              <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                  <label className={labelClass}>{isRtl ? 'تاريخ البدء' : 'Start Date'} *</label>
                  <input type="date" value={formData.start_date} onChange={(e) => setFormData({ ...formData, start_date: e.target.value })} className={inputClass} />
                  {errors.start_date && <p className="text-xs text-red-500 mt-1">{errors.start_date[0]}</p>}
                </div>
                <div>
                  <label className={labelClass}>{isRtl ? 'تاريخ الانتهاء' : 'End Date'} *</label>
                  <input type="date" value={formData.end_date} onChange={(e) => setFormData({ ...formData, end_date: e.target.value })} className={inputClass} />
                  {errors.end_date && <p className="text-xs text-red-500 mt-1">{errors.end_date[0]}</p>}
                </div>
                <div>
                  <label className={labelClass}>{isRtl ? 'وقت الافتتاح' : 'Opening Time'}</label>
                  <input type="time" value={formData.opening_time} onChange={(e) => setFormData({ ...formData, opening_time: e.target.value })} className={inputClass} />
                </div>
                <div>
                  <label className={labelClass}>{isRtl ? 'وقت الإغلاق' : 'Closing Time'}</label>
                  <input type="time" value={formData.closing_time} onChange={(e) => setFormData({ ...formData, closing_time: e.target.value })} className={inputClass} />
                </div>
              </div>

              {/* Status & Featured */}
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label className={labelClass}>{isRtl ? 'الحالة' : 'Status'}</label>
                  <select value={formData.status} onChange={(e) => setFormData({ ...formData, status: e.target.value })} className={inputClass}>
                    <option value="draft">{isRtl ? 'مسودة' : 'Draft'}</option>
                    <option value="published">{isRtl ? 'منشور' : 'Published'}</option>
                  </select>
                </div>
                <div className="flex items-end pb-1">
                  <label className="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" checked={formData.is_featured} onChange={(e) => setFormData({ ...formData, is_featured: e.target.checked })} className="sr-only peer" />
                    <div className="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-500/25 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-500" />
                    <span className="ms-2 text-sm text-gray-700 dark:text-gray-300">{isRtl ? 'مميز' : 'Featured'}</span>
                  </label>
                </div>
              </div>

              {/* Organizer Info */}
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label className={labelClass}>{isRtl ? 'اسم المنظم' : 'Organizer Name'}</label>
                  <input type="text" value={formData.organizer_name} onChange={(e) => setFormData({ ...formData, organizer_name: e.target.value })} className={inputClass} />
                </div>
                <div>
                  <label className={labelClass}>{isRtl ? 'هاتف المنظم' : 'Organizer Phone'}</label>
                  <input type="text" value={formData.organizer_phone} onChange={(e) => setFormData({ ...formData, organizer_phone: e.target.value })} className={inputClass} placeholder="+966..." />
                </div>
                <div>
                  <label className={labelClass}>{isRtl ? 'بريد المنظم' : 'Organizer Email'}</label>
                  <input type="email" value={formData.organizer_email} onChange={(e) => setFormData({ ...formData, organizer_email: e.target.value })} className={inputClass} />
                </div>
                <div>
                  <label className={labelClass}>{isRtl ? 'الموقع الإلكتروني' : 'Website'}</label>
                  <input type="url" value={formData.website} onChange={(e) => setFormData({ ...formData, website: e.target.value })} className={inputClass} placeholder="https://..." />
                </div>
              </div>

              {/* Images Upload */}
              <div>
                <label className={labelClass}>{isRtl ? 'صور الفعالية' : 'Event Images'}</label>
                <div className="space-y-3">
                  {/* Existing images (editing) */}
                  {existingImages.length > 0 && (
                    <div className="flex flex-wrap gap-3">
                      {existingImages.map((url, idx) => (
                        <div key={`existing-${idx}`} className="relative w-24 h-24 rounded-xl overflow-hidden border border-gray-200/60 dark:border-white/10 group">
                          <img src={url} alt="" className="w-full h-full object-cover" />
                          <button
                            type="button"
                            onClick={() => removeExistingImage(idx)}
                            className="absolute top-1 end-1 p-1 rounded-full bg-red-500 text-white opacity-0 group-hover:opacity-100 transition-opacity"
                          >
                            <X className="w-3 h-3" />
                          </button>
                        </div>
                      ))}
                    </div>
                  )}

                  {/* New image previews */}
                  {imagePreviews.length > 0 && (
                    <div className="flex flex-wrap gap-3">
                      {imagePreviews.map((src, idx) => (
                        <div key={`new-${idx}`} className="relative w-24 h-24 rounded-xl overflow-hidden border border-blue-300/60 dark:border-blue-500/30 group">
                          <img src={src} alt="" className="w-full h-full object-cover" />
                          <button
                            type="button"
                            onClick={() => removeNewImage(idx)}
                            className="absolute top-1 end-1 p-1 rounded-full bg-red-500 text-white opacity-0 group-hover:opacity-100 transition-opacity"
                          >
                            <X className="w-3 h-3" />
                          </button>
                          <div className="absolute bottom-0 inset-x-0 bg-blue-500/80 text-white text-[10px] text-center py-0.5">
                            {isRtl ? 'جديد' : 'New'}
                          </div>
                        </div>
                      ))}
                    </div>
                  )}

                  {/* Upload area */}
                  <div
                    onClick={() => fileInputRef.current?.click()}
                    className="flex flex-col items-center justify-center gap-2 p-6 border-2 border-dashed border-gray-300/60 dark:border-white/10 rounded-xl cursor-pointer hover:border-blue-400/60 hover:bg-blue-50/30 dark:hover:bg-blue-500/5 transition-all"
                  >
                    <div className="w-12 h-12 rounded-full bg-blue-500/10 flex items-center justify-center">
                      <Upload className="w-6 h-6 text-blue-500" />
                    </div>
                    <p className="text-sm text-gray-500">{isRtl ? 'اضغط لاختيار الصور' : 'Click to select images'}</p>
                    <p className="text-xs text-gray-400">{isRtl ? 'PNG, JPG, WEBP (أقصى 5 ميجا)' : 'PNG, JPG, WEBP (max 5MB each)'}</p>
                  </div>
                  <input
                    ref={fileInputRef}
                    type="file"
                    multiple
                    accept="image/*"
                    onChange={handleImageSelect}
                    className="hidden"
                  />
                  {errors.images && <p className="text-xs text-red-500 mt-1">{errors.images[0]}</p>}
                </div>
              </div>

              {/* 360° Images Upload */}
              <div>
                <label className={labelClass}>{isRtl ? 'صور 360°' : '360° Images'}</label>
                <div className="space-y-3">
                  {existingImages360.length > 0 && (
                    <div className="flex flex-wrap gap-3">
                      {existingImages360.map((url, idx) => (
                        <div key={`existing360-${idx}`} className="relative w-24 h-24 rounded-xl overflow-hidden border border-gray-200/60 dark:border-white/10 group">
                          <img src={url} alt="" className="w-full h-full object-cover" />
                          <button type="button" onClick={() => removeExisting360Image(idx)} className="absolute top-1 end-1 p-1 rounded-full bg-red-500 text-white opacity-0 group-hover:opacity-100 transition-opacity"><X className="w-3 h-3" /></button>
                        </div>
                      ))}
                    </div>
                  )}
                  {image360Previews.length > 0 && (
                    <div className="flex flex-wrap gap-3">
                      {image360Previews.map((src, idx) => (
                        <div key={`new360-${idx}`} className="relative w-24 h-24 rounded-xl overflow-hidden border border-purple-300/60 dark:border-purple-500/30 group">
                          <img src={src} alt="" className="w-full h-full object-cover" />
                          <button type="button" onClick={() => removeNew360Image(idx)} className="absolute top-1 end-1 p-1 rounded-full bg-red-500 text-white opacity-0 group-hover:opacity-100 transition-opacity"><X className="w-3 h-3" /></button>
                          <div className="absolute bottom-0 inset-x-0 bg-purple-500/80 text-white text-[10px] text-center py-0.5">360°</div>
                        </div>
                      ))}
                    </div>
                  )}
                  <div onClick={() => file360InputRef.current?.click()} className="flex flex-col items-center justify-center gap-2 p-6 border-2 border-dashed border-gray-300/60 dark:border-white/10 rounded-xl cursor-pointer hover:border-purple-400/60 hover:bg-purple-50/30 dark:hover:bg-purple-500/5 transition-all">
                    <div className="w-12 h-12 rounded-full bg-purple-500/10 flex items-center justify-center">
                      <Upload className="w-6 h-6 text-purple-500" />
                    </div>
                    <p className="text-sm text-gray-500">{isRtl ? 'اضغط لاختيار صور 360°' : 'Click to select 360° images'}</p>
                    <p className="text-xs text-gray-400">{isRtl ? 'PNG, JPG, WEBP (أقصى 10 ميجا)' : 'PNG, JPG, WEBP (max 10MB each)'}</p>
                  </div>
                  <input ref={file360InputRef} type="file" multiple accept="image/*" onChange={handle360ImageSelect} className="hidden" />
                  {errors.images_360 && <p className="text-xs text-red-500 mt-1">{errors.images_360[0]}</p>}
                </div>
              </div>
            </div>

            {/* Footer */}
            <div className="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-200/60 dark:border-white/10">
              <button onClick={() => setShowModal(false)} className="px-4 py-2 rounded-xl text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/5">
                {isRtl ? 'إلغاء' : 'Cancel'}
              </button>
              <button
                onClick={handleSubmit}
                disabled={submitting}
                className="px-6 py-2 rounded-xl bg-gradient-to-r from-blue-500 to-purple-600 text-white text-sm font-medium shadow-lg shadow-blue-500/25 hover:shadow-xl transition-all duration-300 disabled:opacity-50"
              >
                {submitting
                  ? (isRtl ? 'جاري الحفظ...' : 'Saving...')
                  : editingEvent
                    ? (isRtl ? 'تحديث' : 'Update')
                    : (isRtl ? 'إنشاء' : 'Create')
                }
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
