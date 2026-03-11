'use client';

import { useState, useEffect, useRef } from 'react';
import GlassCard from '@/components/ui/GlassCard';
import DataTable, { Column } from '@/components/ui/DataTable';
import StatusBadge from '@/components/ui/StatusBadge';
import { expoApi } from '@/lib/api';
import { formatCurrency } from '@/lib/utils';
import { Space } from '@/types';
import { Plus, Search, Filter, MapPin, Edit, Trash2, Upload, X, ImageIcon, Star, Wrench } from 'lucide-react';

interface EventOption { id: string; name: string; name_ar: string; }
interface ServiceOption { id: string; name: string; name_ar: string; }

export default function SpacesPage() {
  const [spaces, setSpaces] = useState<Space[]>([]);
  const [loading, setLoading] = useState(true);
  const [locale, setLocale] = useState('ar');
  const [pagination, setPagination] = useState({ current_page: 1, last_page: 1, total: 0, per_page: 15 });
  const [statusFilter, setStatusFilter] = useState('');
  const [searchQuery, setSearchQuery] = useState('');

  // Event selector for listing/creating
  const [events, setEvents] = useState<EventOption[]>([]);
  const [selectedEventId, setSelectedEventId] = useState('');

  // Services
  const [services, setServices] = useState<ServiceOption[]>([]);
  const [selectedServices, setSelectedServices] = useState<string[]>([]);

  // Modal state
  const [showModal, setShowModal] = useState(false);
  const [editingSpace, setEditingSpace] = useState<Space | null>(null);
  const [submitting, setSubmitting] = useState(false);
  const [errors, setErrors] = useState<Record<string, string[]>>({});
  const [formData, setFormData] = useState({
    name: '', name_ar: '', location_code: '', area_sqm: '', price_total: '',
    price_per_day: '', description: '', description_ar: '', floor_number: '',
    space_type: '', status: 'available', amenities: '', is_featured: false,
  });

  // Image upload state
  const [imageFiles, setImageFiles] = useState<File[]>([]);
  const [imagePreviews, setImagePreviews] = useState<string[]>([]);
  const [existingImages, setExistingImages] = useState<string[]>([]);
  const [image360Files, setImage360Files] = useState<File[]>([]);
  const [image360Previews, setImage360Previews] = useState<string[]>([]);
  const [existingImages360, setExistingImages360] = useState<string[]>([]);
  const fileInputRef = useRef<HTMLInputElement>(null);
  const file360InputRef = useRef<HTMLInputElement>(null);

  const isRtl = locale === 'ar';

  useEffect(() => { setLocale(localStorage.getItem('locale') || 'ar'); fetchEvents(); fetchServices(); }, []);
  useEffect(() => { if (selectedEventId) fetchSpaces(); }, [selectedEventId, pagination.current_page, statusFilter, searchQuery]);

  const fetchEvents = async () => {
    try {
      const res = await expoApi.get('/manage/events', { params: { per_page: 100 } });
      const evts = res.data.data || [];
      setEvents(evts);
      if (evts.length > 0 && !selectedEventId) setSelectedEventId(evts[0].id);
    } catch { /* silent */ }
  };

  const fetchServices = async () => {
    try {
      const res = await expoApi.get('/manage/services', { params: { per_page: 100, is_active: 1 } });
      setServices(res.data.data || []);
    } catch { /* silent */ }
  };

  const fetchSpaces = async () => {
    if (!selectedEventId) return;
    setLoading(true);
    try {
      const params: Record<string, string | number> = { page: pagination.current_page, per_page: pagination.per_page };
      if (statusFilter) params.status = statusFilter;
      if (searchQuery) params.search = searchQuery;
      // Spaces are nested under events
      const res = await expoApi.get(`/manage/events/${selectedEventId}/spaces`, { params });
      setSpaces(res.data.data || []);
      if (res.data.pagination) setPagination(res.data.pagination);
    } catch { setSpaces([]); } finally { setLoading(false); }
  };

  const handleCreate = () => {
    setEditingSpace(null);
    setFormData({
      name: '', name_ar: '', location_code: '', area_sqm: '', price_total: '',
      price_per_day: '', description: '', description_ar: '', floor_number: '',
      space_type: '', status: 'available', amenities: '', is_featured: false,
    });
    setSelectedServices([]);
    setImageFiles([]); setImagePreviews([]); setExistingImages([]);
    setImage360Files([]); setImage360Previews([]); setExistingImages360([]);
    setErrors({});
    setShowModal(true);
  };

  const handleEdit = async (space: Space) => {
    setEditingSpace(space);
    setFormData({
      name: space.name || '',
      name_ar: space.name_ar || '',
      location_code: space.location_code || '',
      area_sqm: String(space.area_sqm || ''),
      price_total: String(space.price_total || ''),
      price_per_day: String(space.price_per_day || ''),
      description: space.description || '',
      description_ar: space.description_ar || '',
      floor_number: String(space.floor_number || ''),
      space_type: space.space_type || '',
      status: space.status || 'available',
      amenities: Array.isArray(space.amenities) ? space.amenities.join(', ') : '',
      is_featured: space.is_featured || false,
    });
    // Load space services from detail endpoint
    try {
      const res = await expoApi.get(`/manage/spaces/${space.id}`);
      const detail = res.data.data;
      setSelectedServices(detail.services?.map((s: ServiceOption) => s.id) || []);
    } catch {
      setSelectedServices(space.services?.map(s => s.id) || []);
    }
    setImageFiles([]); setImagePreviews([]);
    setExistingImages(space.images || []);
    setImage360Files([]); setImage360Previews([]);
    setExistingImages360(space.images_360 || []);
    setErrors({});
    setShowModal(true);
  };

  // Image handlers
  const handleImageSelect = (e: React.ChangeEvent<HTMLInputElement>) => {
    const files = Array.from(e.target.files || []);
    setImageFiles(prev => [...prev, ...files]);
    files.forEach(file => {
      const reader = new FileReader();
      reader.onload = (ev) => setImagePreviews(prev => [...prev, ev.target?.result as string]);
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
    setImage360Files(prev => [...prev, ...files]);
    files.forEach(file => {
      const reader = new FileReader();
      reader.onload = (ev) => setImage360Previews(prev => [...prev, ev.target?.result as string]);
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
      // Required fields
      fd.append('name', formData.name);
      fd.append('location_code', formData.location_code);
      fd.append('area_sqm', formData.area_sqm);
      fd.append('price_total', formData.price_total);
      // Optional fields
      if (formData.name_ar) fd.append('name_ar', formData.name_ar);
      if (formData.description) fd.append('description', formData.description);
      if (formData.description_ar) fd.append('description_ar', formData.description_ar);
      if (formData.price_per_day) fd.append('price_per_day', formData.price_per_day);
      if (formData.floor_number) fd.append('floor_number', formData.floor_number);
      if (formData.space_type) fd.append('space_type', formData.space_type);
      if (formData.status) fd.append('status', formData.status);
      if (formData.amenities) {
        formData.amenities.split(',').map(a => a.trim()).filter(Boolean).forEach(a => fd.append('amenities[]', a));
      }

      // Featured
      fd.append('is_featured', formData.is_featured ? '1' : '0');

      // Services
      selectedServices.forEach(serviceId => fd.append('services[]', serviceId));

      // Images
      imageFiles.forEach(file => fd.append('images[]', file));
      image360Files.forEach(file => fd.append('images_360[]', file));

      if (editingSpace) {
        existingImages.forEach(url => fd.append('existing_images[]', url));
        existingImages360.forEach(url => fd.append('existing_images_360[]', url));
        fd.append('_method', 'PUT');
        await expoApi.post(`/manage/spaces/${editingSpace.id}`, fd, {
          headers: { 'Content-Type': 'multipart/form-data' },
        });
      } else {
        // Create is nested under event
        await expoApi.post(`/manage/events/${selectedEventId}/spaces`, fd, {
          headers: { 'Content-Type': 'multipart/form-data' },
        });
      }
      setShowModal(false);
      fetchSpaces();
    } catch (err: unknown) {
      const error = err as { response?: { data?: { errors?: Record<string, string[]> } } };
      if (error.response?.data?.errors) setErrors(error.response.data.errors);
    } finally { setSubmitting(false); }
  };

  const handleDelete = async (id: string) => {
    if (!confirm(isRtl ? 'هل أنت متأكد من الحذف؟' : 'Are you sure you want to delete?')) return;
    try { await expoApi.delete(`/manage/spaces/${id}`); fetchSpaces(); } catch { /* silent */ }
  };

  const columns: Column<Space>[] = [
    {
      key: 'name',
      header: isRtl ? 'الاسم' : 'Name',
      render: (item) => (
        <div className="flex items-center gap-3">
          <div className="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500/20 to-teal-500/20 flex items-center justify-center overflow-hidden">
            {item.images?.[0] ? <img src={item.images[0]} alt="" className="w-full h-full object-cover" /> : <MapPin className="w-5 h-5 text-emerald-500" />}
          </div>
          <div>
            <p className="font-medium text-gray-900 dark:text-white">{isRtl ? item.name_ar || item.name : item.name}</p>
            <p className="text-xs text-gray-500">{item.location_code}</p>
          </div>
        </div>
      ),
    },
    {
      key: 'area',
      header: isRtl ? 'المساحة' : 'Area',
      render: (item) => <span className="text-sm">{item.area_sqm} {isRtl ? 'م²' : 'sqm'}</span>,
    },
    {
      key: 'price',
      header: isRtl ? 'السعر' : 'Price',
      render: (item) => <span className="text-sm font-medium text-gray-900 dark:text-white">{formatCurrency(item.price_total || item.price_per_day, locale)}</span>,
    },
    {
      key: 'type',
      header: isRtl ? 'النوع' : 'Type',
      render: (item) => (
        <div className="flex items-center gap-1">
          {item.is_featured && <Star className="w-3.5 h-3.5 text-amber-500 fill-amber-500" />}
          <span className="text-sm text-gray-600 dark:text-gray-300">{item.space_type || '-'}</span>
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
          <button onClick={(e) => { e.stopPropagation(); handleEdit(item); }}
            className="p-2 rounded-lg hover:bg-blue-500/10 text-blue-500 transition-colors"><Edit className="w-4 h-4" /></button>
          <button onClick={(e) => { e.stopPropagation(); handleDelete(item.id); }}
            className="p-2 rounded-lg hover:bg-red-500/10 text-red-500 transition-colors"><Trash2 className="w-4 h-4" /></button>
        </div>
      ),
    },
  ];

  const inputClass = "w-full px-4 py-2 rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30";

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900 dark:text-white">{isRtl ? 'المساحات' : 'Spaces'}</h1>
          <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">{isRtl ? 'إدارة المساحات والأجنحة' : 'Manage spaces and booths'}</p>
        </div>
        <button onClick={handleCreate} disabled={!selectedEventId}
          className="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 text-white text-sm font-medium shadow-lg shadow-emerald-500/25 hover:shadow-xl transition-all duration-300 hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed">
          <Plus className="w-4 h-4" />{isRtl ? 'إنشاء مساحة' : 'Create Space'}
        </button>
      </div>

      {/* Event Selector + Filters */}
      <GlassCard>
        <div className="flex flex-wrap items-center gap-4">
          <div className="min-w-[220px]">
            <label className="block text-xs font-medium text-gray-500 mb-1">{isRtl ? 'اختر الفعالية' : 'Select Event'}</label>
            <select value={selectedEventId} onChange={(e) => { setSelectedEventId(e.target.value); setPagination(p => ({ ...p, current_page: 1 })); }}
              className={inputClass}>
              <option value="">{isRtl ? 'اختر فعالية...' : 'Choose event...'}</option>
              {events.map(ev => <option key={ev.id} value={ev.id}>{isRtl ? ev.name_ar : ev.name}</option>)}
            </select>
          </div>
          <div className="relative flex-1 min-w-[200px]">
            <Search className="absolute start-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
            <input type="text" value={searchQuery} onChange={(e) => setSearchQuery(e.target.value)}
              placeholder={isRtl ? 'بحث...' : 'Search...'} className="w-full ps-10 pe-4 py-2 rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-all" />
          </div>
          <div className="flex items-center gap-2">
            <Filter className="w-4 h-4 text-gray-400" />
            <select value={statusFilter} onChange={(e) => setStatusFilter(e.target.value)} className={inputClass}>
              <option value="">{isRtl ? 'جميع الحالات' : 'All Statuses'}</option>
              <option value="available">{isRtl ? 'متاح' : 'Available'}</option>
              <option value="reserved">{isRtl ? 'محجوز' : 'Reserved'}</option>
              <option value="rented">{isRtl ? 'مؤجر' : 'Rented'}</option>
              <option value="unavailable">{isRtl ? 'غير متاح' : 'Unavailable'}</option>
            </select>
          </div>
        </div>
      </GlassCard>

      {!selectedEventId ? (
        <GlassCard>
          <div className="text-center py-12 text-gray-500">
            <MapPin className="w-12 h-12 mx-auto mb-3 opacity-30" />
            <p>{isRtl ? 'يرجى اختيار فعالية لعرض المساحات' : 'Please select an event to view spaces'}</p>
          </div>
        </GlassCard>
      ) : (
        <DataTable columns={columns} data={spaces as unknown as Record<string, unknown>[]} loading={loading} pagination={pagination}
          onPageChange={(page) => setPagination(p => ({ ...p, current_page: page }))} emptyMessage={isRtl ? 'لا توجد مساحات' : 'No spaces found'} />
      )}

      {/* Create/Edit Modal */}
      {showModal && (
        <div className="fixed inset-0 z-50 flex items-center justify-center">
          <div className="fixed inset-0 bg-black/50 backdrop-blur-sm" onClick={() => setShowModal(false)} />
          <div className="relative w-full max-w-3xl mx-4 max-h-[90vh] overflow-y-auto rounded-2xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/95 dark:bg-gray-900/95 backdrop-blur-2xl shadow-2xl p-6">
            <div className="flex items-center justify-between mb-6">
              <h2 className="text-lg font-bold text-gray-900 dark:text-white">
                {editingSpace ? (isRtl ? 'تعديل المساحة' : 'Edit Space') : (isRtl ? 'إنشاء مساحة جديدة' : 'Create New Space')}
              </h2>
              <button onClick={() => setShowModal(false)} className="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-white/10 transition-colors"><X className="w-5 h-5 text-gray-500" /></button>
            </div>
            <div className="space-y-4">
              {/* Names */}
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name (EN) *</label>
                  <input type="text" value={formData.name} onChange={(e) => setFormData({ ...formData, name: e.target.value })} className={inputClass} />
                  {errors.name && <p className="text-xs text-red-500 mt-1">{errors.name[0]}</p>}
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">الاسم (AR)</label>
                  <input type="text" value={formData.name_ar} onChange={(e) => setFormData({ ...formData, name_ar: e.target.value })} className={inputClass} dir="rtl" />
                </div>
              </div>

              {/* Location Code + Area + Price */}
              <div className="grid grid-cols-3 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{isRtl ? 'رمز الموقع' : 'Location Code'} *</label>
                  <input type="text" value={formData.location_code} onChange={(e) => setFormData({ ...formData, location_code: e.target.value })} className={inputClass} placeholder="A-101" />
                  {errors.location_code && <p className="text-xs text-red-500 mt-1">{errors.location_code[0]}</p>}
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{isRtl ? 'المساحة (م²)' : 'Area (sqm)'} *</label>
                  <input type="number" value={formData.area_sqm} onChange={(e) => setFormData({ ...formData, area_sqm: e.target.value })} className={inputClass} min="1" />
                  {errors.area_sqm && <p className="text-xs text-red-500 mt-1">{errors.area_sqm[0]}</p>}
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{isRtl ? 'السعر الكلي' : 'Total Price'} *</label>
                  <input type="number" value={formData.price_total} onChange={(e) => setFormData({ ...formData, price_total: e.target.value })} className={inputClass} min="0" />
                  {errors.price_total && <p className="text-xs text-red-500 mt-1">{errors.price_total[0]}</p>}
                </div>
              </div>

              {/* Optional fields row */}
              <div className="grid grid-cols-3 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{isRtl ? 'السعر / يوم' : 'Price / Day'}</label>
                  <input type="number" value={formData.price_per_day} onChange={(e) => setFormData({ ...formData, price_per_day: e.target.value })} className={inputClass} min="0" />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{isRtl ? 'الطابق' : 'Floor'}</label>
                  <input type="number" value={formData.floor_number} onChange={(e) => setFormData({ ...formData, floor_number: e.target.value })} className={inputClass} />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{isRtl ? 'النوع' : 'Type'}</label>
                  <select value={formData.space_type} onChange={(e) => setFormData({ ...formData, space_type: e.target.value })} className={inputClass}>
                    <option value="">{isRtl ? 'اختر...' : 'Select...'}</option>
                    <option value="booth">{isRtl ? 'كشك' : 'Booth'}</option>
                    <option value="shop">{isRtl ? 'محل' : 'Shop'}</option>
                    <option value="office">{isRtl ? 'مكتب' : 'Office'}</option>
                    <option value="hall">{isRtl ? 'قاعة' : 'Hall'}</option>
                    <option value="outdoor">{isRtl ? 'خارجي' : 'Outdoor'}</option>
                    <option value="other">{isRtl ? 'أخرى' : 'Other'}</option>
                  </select>
                </div>
              </div>

              {/* Descriptions */}
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{isRtl ? 'الوصف (EN)' : 'Description (EN)'}</label>
                  <textarea value={formData.description} onChange={(e) => setFormData({ ...formData, description: e.target.value })} rows={2} className={inputClass} />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{isRtl ? 'الوصف (AR)' : 'Description (AR)'}</label>
                  <textarea value={formData.description_ar} onChange={(e) => setFormData({ ...formData, description_ar: e.target.value })} rows={2} className={inputClass} dir="rtl" />
                </div>
              </div>

              {/* Featured Toggle */}
              <div className="flex items-center gap-3 py-2">
                <label className="relative inline-flex items-center cursor-pointer">
                  <input type="checkbox" checked={formData.is_featured} onChange={(e) => setFormData({ ...formData, is_featured: e.target.checked })} className="sr-only peer" />
                  <div className="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-amber-500/25 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500" />
                  <span className="ms-2 text-sm font-medium text-gray-700 dark:text-gray-300 flex items-center gap-1">
                    <Star className="w-4 h-4 text-amber-500" />
                    {isRtl ? 'مساحة مميزة' : 'Featured Space'}
                  </span>
                </label>
              </div>

              {/* Services */}
              {services.length > 0 && (
                <div>
                  <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 flex items-center gap-1">
                    <Wrench className="w-4 h-4" />
                    {isRtl ? 'الخدمات' : 'Services'}
                  </label>
                  <div className="grid grid-cols-2 sm:grid-cols-3 gap-2">
                    {services.map(service => (
                      <label key={service.id} className={`flex items-center gap-2 px-3 py-2 rounded-xl border cursor-pointer transition-all ${
                        selectedServices.includes(service.id)
                          ? 'border-emerald-400/60 bg-emerald-50/50 dark:bg-emerald-500/10'
                          : 'border-gray-200/60 dark:border-white/10 hover:border-emerald-300/40'
                      }`}>
                        <input type="checkbox" checked={selectedServices.includes(service.id)}
                          onChange={(e) => {
                            if (e.target.checked) setSelectedServices(prev => [...prev, service.id]);
                            else setSelectedServices(prev => prev.filter(id => id !== service.id));
                          }}
                          className="rounded border-gray-300 text-emerald-500 focus:ring-emerald-500/25" />
                        <span className="text-sm text-gray-700 dark:text-gray-300">{isRtl ? service.name_ar || service.name : service.name}</span>
                      </label>
                    ))}
                  </div>
                  {errors.services && <p className="text-xs text-red-500 mt-1">{errors.services[0]}</p>}
                </div>
              )}

              {/* Amenities */}
              <div>
                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{isRtl ? 'المرافق (مفصولة بفاصلة)' : 'Amenities (comma separated)'}</label>
                <input type="text" value={formData.amenities} onChange={(e) => setFormData({ ...formData, amenities: e.target.value })} className={inputClass} placeholder="WiFi, AC, Electricity" />
              </div>

              {/* Images Upload */}
              <div>
                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{isRtl ? 'الصور' : 'Images'}</label>
                <div className="flex flex-wrap gap-3">
                  {existingImages.map((url, i) => (
                    <div key={`existing-${i}`} className="relative w-24 h-24 rounded-xl overflow-hidden border border-gray-200/60 dark:border-white/10 group">
                      <img src={url} alt="" className="w-full h-full object-cover" />
                      <button type="button" onClick={() => removeExistingImage(i)}
                        className="absolute top-1 right-1 w-5 h-5 rounded-full bg-red-500 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"><X className="w-3 h-3" /></button>
                    </div>
                  ))}
                  {imagePreviews.map((src, i) => (
                    <div key={`new-${i}`} className="relative w-24 h-24 rounded-xl overflow-hidden border-2 border-emerald-400/50 group">
                      <img src={src} alt="" className="w-full h-full object-cover" />
                      <button type="button" onClick={() => removeNewImage(i)}
                        className="absolute top-1 right-1 w-5 h-5 rounded-full bg-red-500 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"><X className="w-3 h-3" /></button>
                    </div>
                  ))}
                  <div onClick={() => fileInputRef.current?.click()}
                    className="w-24 h-24 rounded-xl border-2 border-dashed border-gray-300/60 dark:border-white/10 flex flex-col items-center justify-center gap-1 cursor-pointer hover:border-emerald-400/60 transition-all">
                    <Upload className="w-5 h-5 text-emerald-500" />
                    <span className="text-[10px] text-gray-500">{isRtl ? 'إضافة' : 'Add'}</span>
                  </div>
                </div>
                <input ref={fileInputRef} type="file" accept="image/*" multiple onChange={handleImageSelect} className="hidden" />
                {errors.images && <p className="text-xs text-red-500 mt-1">{errors.images[0]}</p>}
              </div>

              {/* 360° Images Upload */}
              <div>
                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{isRtl ? 'صور 360°' : '360° Images'}</label>
                <div className="flex flex-wrap gap-3">
                  {existingImages360.map((url, i) => (
                    <div key={`existing360-${i}`} className="relative w-24 h-24 rounded-xl overflow-hidden border border-purple-200/60 dark:border-purple-500/30 group">
                      <img src={url} alt="" className="w-full h-full object-cover" />
                      <div className="absolute bottom-0 inset-x-0 bg-purple-600/80 text-white text-[8px] text-center py-0.5">360°</div>
                      <button type="button" onClick={() => removeExisting360Image(i)}
                        className="absolute top-1 right-1 w-5 h-5 rounded-full bg-red-500 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"><X className="w-3 h-3" /></button>
                    </div>
                  ))}
                  {image360Previews.map((src, i) => (
                    <div key={`new360-${i}`} className="relative w-24 h-24 rounded-xl overflow-hidden border-2 border-purple-400/50 group">
                      <img src={src} alt="" className="w-full h-full object-cover" />
                      <div className="absolute bottom-0 inset-x-0 bg-purple-600/80 text-white text-[8px] text-center py-0.5">360° NEW</div>
                      <button type="button" onClick={() => removeNew360Image(i)}
                        className="absolute top-1 right-1 w-5 h-5 rounded-full bg-red-500 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"><X className="w-3 h-3" /></button>
                    </div>
                  ))}
                  <div onClick={() => file360InputRef.current?.click()}
                    className="w-24 h-24 rounded-xl border-2 border-dashed border-purple-300/60 dark:border-purple-500/20 flex flex-col items-center justify-center gap-1 cursor-pointer hover:border-purple-400/60 transition-all">
                    <Upload className="w-5 h-5 text-purple-500" />
                    <span className="text-[10px] text-gray-500">360°</span>
                  </div>
                </div>
                <input ref={file360InputRef} type="file" accept="image/*" multiple onChange={handle360ImageSelect} className="hidden" />
                {errors.images_360 && <p className="text-xs text-red-500 mt-1">{errors.images_360[0]}</p>}
              </div>
            </div>

            <div className="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-200/60 dark:border-white/10">
              <button onClick={() => setShowModal(false)} className="px-4 py-2 rounded-xl text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/5 transition-colors">{isRtl ? 'إلغاء' : 'Cancel'}</button>
              <button onClick={handleSubmit} disabled={submitting}
                className="px-6 py-2 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 text-white text-sm font-medium shadow-lg shadow-emerald-500/25 hover:shadow-xl transition-all duration-300 disabled:opacity-50">
                {submitting ? (isRtl ? 'جاري الحفظ...' : 'Saving...') : editingSpace ? (isRtl ? 'تحديث' : 'Update') : (isRtl ? 'إنشاء' : 'Create')}
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
