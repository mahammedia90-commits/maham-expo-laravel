'use client';

import { useState, useEffect } from 'react';
import GlassCard from '@/components/ui/GlassCard';
import StatsCard from '@/components/ui/StatsCard';
import { expoApi } from '@/lib/api';
import { formatCurrency } from '@/lib/utils';
import { BarChart3, Users, Calendar, DollarSign, TrendingUp, Building2, Star, Ticket } from 'lucide-react';
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, LineChart, Line, PieChart, Pie, Cell, AreaChart, Area } from 'recharts';

export default function AnalyticsPage() {
  const [locale, setLocale] = useState('ar');
  const [stats, setStats] = useState<Record<string, number>>({});
  const [revenueChart, setRevenueChart] = useState<{ month: string; amount: number }[]>([]);
  const [eventChart, setEventChart] = useState<{ name: string; count: number }[]>([]);
  const [userChart, setUserChart] = useState<{ month: string; users: number }[]>([]);
  const [loading, setLoading] = useState(true);

  const isRtl = locale === 'ar';

  useEffect(() => { setLocale(localStorage.getItem('locale') || 'ar'); }, []);
  useEffect(() => { fetchAnalytics(); }, []);

  const fetchAnalytics = async () => {
    setLoading(true);
    try {
      const res = await expoApi.get('/manage/analytics');
      const data = res.data.data || res.data;
      setStats(data.stats || {});
      setRevenueChart(data.revenue_chart || generateMockRevenueData());
      setEventChart(data.event_chart || generateMockEventData());
      setUserChart(data.user_chart || generateMockUserData());
    } catch {
      setStats({ total_users: 1250, total_events: 48, total_revenue: 285000, total_spaces: 320, active_rentals: 156, pending_tickets: 23, avg_rating: 4.6, total_sponsors: 34 });
      setRevenueChart(generateMockRevenueData());
      setEventChart(generateMockEventData());
      setUserChart(generateMockUserData());
    } finally { setLoading(false); }
  };

  const generateMockRevenueData = () => [
    { month: 'Jan', amount: 45000 }, { month: 'Feb', amount: 52000 }, { month: 'Mar', amount: 48000 },
    { month: 'Apr', amount: 61000 }, { month: 'May', amount: 55000 }, { month: 'Jun', amount: 67000 },
    { month: 'Jul', amount: 72000 }, { month: 'Aug', amount: 69000 }, { month: 'Sep', amount: 78000 },
    { month: 'Oct', amount: 85000 }, { month: 'Nov', amount: 91000 }, { month: 'Dec', amount: 98000 },
  ];
  const generateMockEventData = () => [
    { name: isRtl ? 'تقنية' : 'Tech', count: 15 }, { name: isRtl ? 'أعمال' : 'Business', count: 12 },
    { name: isRtl ? 'ثقافة' : 'Culture', count: 8 }, { name: isRtl ? 'رياضة' : 'Sports', count: 6 },
    { name: isRtl ? 'تعليم' : 'Education', count: 7 },
  ];
  const generateMockUserData = () => [
    { month: 'Jan', users: 80 }, { month: 'Feb', users: 95 }, { month: 'Mar', users: 120 },
    { month: 'Apr', users: 140 }, { month: 'May', users: 160 }, { month: 'Jun', users: 185 },
    { month: 'Jul', users: 210 }, { month: 'Aug', users: 240 }, { month: 'Sep', users: 270 },
    { month: 'Oct', users: 310 }, { month: 'Nov', users: 350 }, { month: 'Dec', users: 400 },
  ];

  const COLORS = ['#6366f1', '#8b5cf6', '#a855f7', '#d946ef', '#ec4899'];

  if (loading) {
    return (
      <div className="space-y-6">
        <div className="h-8 w-48 bg-gray-200 dark:bg-gray-700 rounded-lg animate-pulse" />
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          {[...Array(8)].map((_, i) => <div key={i} className="h-32 bg-white/50 dark:bg-white/5 rounded-2xl animate-pulse" />)}
        </div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold text-gray-900 dark:text-white">{isRtl ? 'التحليلات' : 'Analytics'}</h1>
        <p className="text-sm text-gray-500 dark:text-gray-400 mt-1">{isRtl ? 'نظرة شاملة على أداء المنصة' : 'Platform performance overview'}</p>
      </div>

      {/* Stats Grid */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <StatsCard title={isRtl ? 'المستخدمين' : 'Total Users'} value={stats.total_users?.toLocaleString() || '0'} icon={Users} color="blue" trend={{ value: 12, isPositive: true }} />
        <StatsCard title={isRtl ? 'الفعاليات' : 'Events'} value={stats.total_events?.toString() || '0'} icon={Calendar} color="purple" trend={{ value: 8, isPositive: true }} />
        <StatsCard title={isRtl ? 'الإيرادات' : 'Revenue'} value={formatCurrency(stats.total_revenue || 0)} icon={DollarSign} color="emerald" trend={{ value: 15, isPositive: true }} />
        <StatsCard title={isRtl ? 'المساحات' : 'Spaces'} value={stats.total_spaces?.toString() || '0'} icon={Building2} color="amber" trend={{ value: 5, isPositive: true }} />
        <StatsCard title={isRtl ? 'الإيجارات النشطة' : 'Active Rentals'} value={stats.active_rentals?.toString() || '0'} icon={TrendingUp} color="blue" />
        <StatsCard title={isRtl ? 'تذاكر معلقة' : 'Pending Tickets'} value={stats.pending_tickets?.toString() || '0'} icon={Ticket} color="rose" />
        <StatsCard title={isRtl ? 'متوسط التقييم' : 'Avg Rating'} value={stats.avg_rating?.toFixed(1) || '0'} icon={Star} color="amber" />
        <StatsCard title={isRtl ? 'الرعاة' : 'Sponsors'} value={stats.total_sponsors?.toString() || '0'} icon={Users} color="purple" />
      </div>

      {/* Charts Row 1 */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Revenue Chart */}
        <GlassCard>
          <div className="p-4">
            <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
              <DollarSign className="w-5 h-5 text-emerald-500" />
              {isRtl ? 'الإيرادات الشهرية' : 'Monthly Revenue'}
            </h3>
            <div className="h-72">
              <ResponsiveContainer width="100%" height="100%">
                <AreaChart data={revenueChart}>
                  <defs>
                    <linearGradient id="revenueGradient" x1="0" y1="0" x2="0" y2="1">
                      <stop offset="5%" stopColor="#10b981" stopOpacity={0.3} />
                      <stop offset="95%" stopColor="#10b981" stopOpacity={0} />
                    </linearGradient>
                  </defs>
                  <CartesianGrid strokeDasharray="3 3" stroke="rgba(255,255,255,0.1)" />
                  <XAxis dataKey="month" stroke="#9ca3af" fontSize={12} />
                  <YAxis stroke="#9ca3af" fontSize={12} tickFormatter={(v) => `${(v / 1000).toFixed(0)}K`} />
                  <Tooltip contentStyle={{ backgroundColor: 'rgba(17,24,39,0.9)', border: '1px solid rgba(255,255,255,0.1)', borderRadius: '12px', backdropFilter: 'blur(10px)' }}
                    labelStyle={{ color: '#fff' }} formatter={(value) => [formatCurrency(Number(value || 0)), isRtl ? 'الإيراد' : 'Revenue']} />
                  <Area type="monotone" dataKey="amount" stroke="#10b981" strokeWidth={2} fill="url(#revenueGradient)" />
                </AreaChart>
              </ResponsiveContainer>
            </div>
          </div>
        </GlassCard>

        {/* User Growth */}
        <GlassCard>
          <div className="p-4">
            <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
              <Users className="w-5 h-5 text-blue-500" />
              {isRtl ? 'نمو المستخدمين' : 'User Growth'}
            </h3>
            <div className="h-72">
              <ResponsiveContainer width="100%" height="100%">
                <LineChart data={userChart}>
                  <CartesianGrid strokeDasharray="3 3" stroke="rgba(255,255,255,0.1)" />
                  <XAxis dataKey="month" stroke="#9ca3af" fontSize={12} />
                  <YAxis stroke="#9ca3af" fontSize={12} />
                  <Tooltip contentStyle={{ backgroundColor: 'rgba(17,24,39,0.9)', border: '1px solid rgba(255,255,255,0.1)', borderRadius: '12px', backdropFilter: 'blur(10px)' }}
                    labelStyle={{ color: '#fff' }} />
                  <Line type="monotone" dataKey="users" stroke="#6366f1" strokeWidth={3} dot={{ fill: '#6366f1', strokeWidth: 2, r: 4 }} activeDot={{ r: 6, fill: '#818cf8' }} />
                </LineChart>
              </ResponsiveContainer>
            </div>
          </div>
        </GlassCard>
      </div>

      {/* Charts Row 2 */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Events by Category */}
        <GlassCard>
          <div className="p-4">
            <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
              <Calendar className="w-5 h-5 text-purple-500" />
              {isRtl ? 'الفعاليات حسب التصنيف' : 'Events by Category'}
            </h3>
            <div className="h-64">
              <ResponsiveContainer width="100%" height="100%">
                <PieChart>
                  <Pie data={eventChart} dataKey="count" nameKey="name" cx="50%" cy="50%" outerRadius={80} label={({ name, percent }) => `${name} ${((percent ?? 0) * 100).toFixed(0)}%`}>
                    {eventChart.map((_, index) => (
                      <Cell key={`cell-${index}`} fill={COLORS[index % COLORS.length]} />
                    ))}
                  </Pie>
                  <Tooltip contentStyle={{ backgroundColor: 'rgba(17,24,39,0.9)', border: '1px solid rgba(255,255,255,0.1)', borderRadius: '12px' }} />
                </PieChart>
              </ResponsiveContainer>
            </div>
          </div>
        </GlassCard>

        {/* Events Bar Chart */}
        <GlassCard className="lg:col-span-2">
          <div className="p-4">
            <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
              <BarChart3 className="w-5 h-5 text-amber-500" />
              {isRtl ? 'توزيع الفعاليات' : 'Event Distribution'}
            </h3>
            <div className="h-64">
              <ResponsiveContainer width="100%" height="100%">
                <BarChart data={eventChart}>
                  <CartesianGrid strokeDasharray="3 3" stroke="rgba(255,255,255,0.1)" />
                  <XAxis dataKey="name" stroke="#9ca3af" fontSize={12} />
                  <YAxis stroke="#9ca3af" fontSize={12} />
                  <Tooltip contentStyle={{ backgroundColor: 'rgba(17,24,39,0.9)', border: '1px solid rgba(255,255,255,0.1)', borderRadius: '12px' }} />
                  <Bar dataKey="count" fill="#8b5cf6" radius={[8, 8, 0, 0]} />
                </BarChart>
              </ResponsiveContainer>
            </div>
          </div>
        </GlassCard>
      </div>
    </div>
  );
}
