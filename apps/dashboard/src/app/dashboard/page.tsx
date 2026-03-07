'use client';

import { useState, useEffect } from 'react';
import {
  Calendar,
  Users,
  DollarSign,
  MapPin,
  ClipboardList,
  TrendingUp,
  Eye,
  MessageSquare,
  ArrowUpRight,
  Clock,
  CheckCircle2,
  AlertCircle,
  Plus,
  FileText,
  Settings,
  BarChart3,
} from 'lucide-react';
import {
  BarChart,
  Bar,
  XAxis,
  YAxis,
  CartesianGrid,
  Tooltip,
  ResponsiveContainer,
} from 'recharts';
import StatsCard from '@/components/ui/StatsCard';
import GlassCard from '@/components/ui/GlassCard';
import { expoApi } from '@/lib/api';
import { cn } from '@/lib/utils';

// ── Translations ──────────────────────────────────────────────────────────────
const t = {
  en: {
    dashboardTitle: 'Dashboard Overview',
    dashboardSubtitle: 'Welcome back! Here is what is happening across your platform.',
    totalEvents: 'Total Events',
    totalUsers: 'Total Users',
    totalRevenue: 'Total Revenue',
    activeSpaces: 'Active Spaces',
    pendingRequests: 'Pending Requests',
    occupancyRate: 'Occupancy Rate',
    todaysVisitors: "Today's Visitors",
    openTickets: 'Open Tickets',
    revenueOverview: 'Revenue Overview',
    monthlyRevenue: 'Monthly revenue for the current year (SAR)',
    recentActivity: 'Recent Activity',
    quickActions: 'Quick Actions',
    pendingApprovals: 'Pending Approvals',
    createEvent: 'Create Event',
    addSpace: 'Add Space',
    viewReports: 'View Reports',
    manageSettings: 'Manage Settings',
    viewAll: 'View All',
    approve: 'Approve',
    review: 'Review',
    hoursAgo: 'hours ago',
    minutesAgo: 'minutes ago',
    jan: 'Jan', feb: 'Feb', mar: 'Mar', apr: 'Apr', may: 'May', jun: 'Jun',
    jul: 'Jul', aug: 'Aug', sep: 'Sep', oct: 'Oct', nov: 'Nov', dec: 'Dec',
    activities: [
      { text: 'New rental request for Booth A-12 at Tech Summit', time: '2 hours ago', type: 'request' },
      { text: 'Payment received: SAR 12,500 from Al-Faisal Group', time: '4 hours ago', type: 'payment' },
      { text: 'Visit request approved for Design Conference', time: '5 hours ago', type: 'approval' },
      { text: 'Support ticket #1042 escalated to admin', time: '6 hours ago', type: 'ticket' },
      { text: 'New user registration: Nora Al-Harbi', time: '8 hours ago', type: 'user' },
    ],
    approvals: [
      { title: 'Business Profile: Al-Rashid Trading', type: 'Profile Verification', urgency: 'high' },
      { title: 'Rental Contract #RC-2024-089', type: 'Contract Approval', urgency: 'medium' },
      { title: 'Event: Saudi Food Festival 2025', type: 'Event Publishing', urgency: 'low' },
      { title: 'Sponsor Package: Gold Tier - STC', type: 'Sponsor Approval', urgency: 'medium' },
    ],
  },
  ar: {
    dashboardTitle: 'نظرة عامة على لوحة التحكم',
    dashboardSubtitle: 'مرحبا بعودتك! إليك ما يحدث عبر منصتك.',
    totalEvents: 'إجمالي الفعاليات',
    totalUsers: 'إجمالي المستخدمين',
    totalRevenue: 'إجمالي الإيرادات',
    activeSpaces: 'المساحات النشطة',
    pendingRequests: 'الطلبات المعلقة',
    occupancyRate: 'معدل الإشغال',
    todaysVisitors: 'زوار اليوم',
    openTickets: 'التذاكر المفتوحة',
    revenueOverview: 'نظرة عامة على الإيرادات',
    monthlyRevenue: 'الإيرادات الشهرية للعام الحالي (ر.س)',
    recentActivity: 'النشاط الأخير',
    quickActions: 'إجراءات سريعة',
    pendingApprovals: 'الموافقات المعلقة',
    createEvent: 'إنشاء فعالية',
    addSpace: 'إضافة مساحة',
    viewReports: 'عرض التقارير',
    manageSettings: 'إدارة الإعدادات',
    viewAll: 'عرض الكل',
    approve: 'موافقة',
    review: 'مراجعة',
    hoursAgo: 'ساعات مضت',
    minutesAgo: 'دقائق مضت',
    jan: 'يناير', feb: 'فبراير', mar: 'مارس', apr: 'أبريل', may: 'مايو', jun: 'يونيو',
    jul: 'يوليو', aug: 'أغسطس', sep: 'سبتمبر', oct: 'أكتوبر', nov: 'نوفمبر', dec: 'ديسمبر',
    activities: [
      { text: 'طلب تأجير جديد للجناح A-12 في قمة التقنية', time: 'منذ ساعتين', type: 'request' },
      { text: 'تم استلام دفعة: 12,500 ر.س من مجموعة الفيصل', time: 'منذ 4 ساعات', type: 'payment' },
      { text: 'تمت الموافقة على طلب زيارة مؤتمر التصميم', time: 'منذ 5 ساعات', type: 'approval' },
      { text: 'تذكرة الدعم #1042 تم تصعيدها للمسؤول', time: 'منذ 6 ساعات', type: 'ticket' },
      { text: 'تسجيل مستخدم جديد: نورة الحربي', time: 'منذ 8 ساعات', type: 'user' },
    ],
    approvals: [
      { title: 'ملف تجاري: شركة الراشد للتجارة', type: 'التحقق من الملف', urgency: 'high' },
      { title: 'عقد تأجير #RC-2024-089', type: 'الموافقة على العقد', urgency: 'medium' },
      { title: 'فعالية: مهرجان الطعام السعودي 2025', type: 'نشر الفعالية', urgency: 'low' },
      { title: 'حزمة رعاية: الفئة الذهبية - STC', type: 'الموافقة على الرعاية', urgency: 'medium' },
    ],
  },
};

// ── Mock revenue chart data ───────────────────────────────────────────────────
const getRevenueData = (locale: string) => {
  const labels = locale === 'ar' ? t.ar : t.en;
  return [
    { month: labels.jan, revenue: 32000 },
    { month: labels.feb, revenue: 41000 },
    { month: labels.mar, revenue: 38000 },
    { month: labels.apr, revenue: 52000 },
    { month: labels.may, revenue: 47000 },
    { month: labels.jun, revenue: 61000 },
    { month: labels.jul, revenue: 55000 },
    { month: labels.aug, revenue: 44000 },
    { month: labels.sep, revenue: 39000 },
    { month: labels.oct, revenue: 48000 },
    { month: labels.nov, revenue: 28000 },
    { month: labels.dec, revenue: 23000 },
  ];
};

// ── Activity icon map ─────────────────────────────────────────────────────────
const activityIcons: Record<string, { icon: React.ElementType; color: string }> = {
  request: { icon: ClipboardList, color: 'text-blue-500 bg-blue-500/10' },
  payment: { icon: DollarSign, color: 'text-emerald-500 bg-emerald-500/10' },
  approval: { icon: CheckCircle2, color: 'text-green-500 bg-green-500/10' },
  ticket: { icon: MessageSquare, color: 'text-amber-500 bg-amber-500/10' },
  user: { icon: Users, color: 'text-purple-500 bg-purple-500/10' },
};

// ── Urgency colors ────────────────────────────────────────────────────────────
const urgencyColors: Record<string, string> = {
  high: 'bg-red-500/10 text-red-500 border-red-500/20',
  medium: 'bg-amber-500/10 text-amber-500 border-amber-500/20',
  low: 'bg-blue-500/10 text-blue-500 border-blue-500/20',
};

// ── Custom tooltip ────────────────────────────────────────────────────────────
function CustomTooltip({
  active,
  payload,
  label,
}: {
  active?: boolean;
  payload?: Array<{ value: number }>;
  label?: string;
}) {
  if (!active || !payload?.length) return null;
  return (
    <div className="rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/90 dark:bg-gray-900/90 backdrop-blur-xl px-4 py-3 shadow-2xl">
      <p className="text-sm font-medium text-gray-500 dark:text-gray-400">{label}</p>
      <p className="text-lg font-bold text-gray-900 dark:text-white">
        SAR {payload[0].value.toLocaleString()}
      </p>
    </div>
  );
}

// ── Dashboard Page ────────────────────────────────────────────────────────────
export default function DashboardPage() {
  const [locale, setLocale] = useState<string>('ar');
  const [mounted, setMounted] = useState(false);
  const [stats, setStats] = useState<{
    overview: { total_revenue: number; total_spaces: number; total_visit_requests: number; total_rental_requests: number };
    spaces: { total: number; by_status: { status: string; count: number; percentage: number }[] };
    revenue: { total: number; by_payment_status: { status: string; count: number; amount: number; percentage: number }[] };
    visit_requests: { total: number; pending: number; approved: number };
    rental_requests: { total: number; pending: number; approved: number };
    recent_activity: { latest_visit_requests: number; latest_rental_requests: number; latest_profiles_pending: number };
  } | null>(null);

  useEffect(() => {
    const stored = localStorage.getItem('locale') || 'ar';
    setLocale(stored);
    setMounted(true);
  }, []);

  useEffect(() => {
    if (mounted) {
      expoApi.get('/manage/dashboard').then(res => {
        if (res.data?.data) setStats(res.data.data);
      }).catch(() => {});
    }
  }, [mounted]);

  if (!mounted) {
    return (
      <div className="flex items-center justify-center min-h-[60vh]">
        <div className="w-8 h-8 border-4 border-blue-500/30 border-t-blue-500 rounded-full animate-spin" />
      </div>
    );
  }

  const labels = locale === 'ar' ? t.ar : t.en;
  const isRtl = locale === 'ar';
  const revenueData = getRevenueData(locale);

  // ── Quick actions data ────────────────────────────────────────────────────
  const quickActions = [
    {
      label: labels.createEvent,
      icon: Calendar,
      href: '/dashboard/events',
      gradient: 'from-blue-500 to-blue-600',
      shadow: 'shadow-blue-500/25',
    },
    {
      label: labels.addSpace,
      icon: MapPin,
      href: '/dashboard/spaces',
      gradient: 'from-emerald-500 to-emerald-600',
      shadow: 'shadow-emerald-500/25',
    },
    {
      label: labels.viewReports,
      icon: BarChart3,
      href: '/dashboard/analytics',
      gradient: 'from-purple-500 to-purple-600',
      shadow: 'shadow-purple-500/25',
    },
    {
      label: labels.manageSettings,
      icon: Settings,
      href: '/dashboard/settings',
      gradient: 'from-amber-500 to-amber-600',
      shadow: 'shadow-amber-500/25',
    },
  ];

  return (
    <div className="space-y-8">
      {/* ── Header ──────────────────────────────────────────────────────────── */}
      <div>
        <h1 className="text-3xl font-bold text-gray-900 dark:text-white">
          {labels.dashboardTitle}
        </h1>
        <p className="mt-1 text-gray-500 dark:text-gray-400">
          {labels.dashboardSubtitle}
        </p>
      </div>

      {/* ── Stats Cards (4 columns) ─────────────────────────────────────────── */}
      <div className="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">
        <StatsCard
          title={labels.totalEvents}
          value={stats?.overview?.total_rental_requests ?? 0}
          icon={Calendar}
          color="blue"
          trend={{ value: 12, isPositive: true }}
        />
        <StatsCard
          title={labels.totalUsers}
          value={stats?.overview?.total_visit_requests ?? 0}
          icon={Users}
          color="purple"
          trend={{ value: 8, isPositive: true }}
        />
        <StatsCard
          title={labels.totalRevenue}
          value={`SAR ${((stats?.overview?.total_revenue ?? 0) / 1000).toFixed(0)}K`}
          icon={DollarSign}
          color="emerald"
          trend={{ value: 23, isPositive: true }}
        />
        <StatsCard
          title={labels.activeSpaces}
          value={stats?.overview?.total_spaces ?? 0}
          icon={MapPin}
          color="amber"
          trend={{ value: 5, isPositive: true }}
        />
      </div>

      {/* ── Secondary stats row ─────────────────────────────────────────────── */}
      <div className="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">
        <StatsCard
          title={labels.pendingRequests}
          value={(stats?.rental_requests?.pending ?? 0) + (stats?.visit_requests?.pending ?? 0)}
          icon={ClipboardList}
          color="rose"
          trend={{ value: 3, isPositive: false }}
        />
        <StatsCard
          title={labels.occupancyRate}
          value={`${stats?.spaces?.by_status?.find(s => s.status === 'rented')?.percentage ?? 0}%`}
          icon={TrendingUp}
          color="indigo"
          trend={{ value: 6, isPositive: true }}
        />
        <StatsCard
          title={labels.todaysVisitors}
          value={stats?.recent_activity?.latest_visit_requests ?? 0}
          icon={Eye}
          color="blue"
          trend={{ value: 15, isPositive: true }}
        />
        <StatsCard
          title={labels.openTickets}
          value={stats?.recent_activity?.latest_profiles_pending ?? 0}
          icon={MessageSquare}
          color="amber"
          trend={{ value: 2, isPositive: false }}
        />
      </div>

      {/* ── Revenue Chart + Recent Activity (2 columns) ─────────────────────── */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Revenue Chart */}
        <GlassCard>
          <div className="flex items-center justify-between mb-6">
            <div>
              <h2 className="text-lg font-semibold text-gray-900 dark:text-white">
                {labels.revenueOverview}
              </h2>
              <p className="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                {labels.monthlyRevenue}
              </p>
            </div>
            <div className="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-500/10 text-emerald-500 text-sm font-medium">
              <TrendingUp className="w-4 h-4" />
              <span>+23%</span>
            </div>
          </div>
          <div className="h-72">
            <ResponsiveContainer width="100%" height="100%">
              <BarChart data={revenueData} barSize={28}>
                <defs>
                  <linearGradient id="barGradient" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stopColor="#3b82f6" stopOpacity={0.9} />
                    <stop offset="100%" stopColor="#8b5cf6" stopOpacity={0.7} />
                  </linearGradient>
                </defs>
                <CartesianGrid
                  strokeDasharray="3 3"
                  vertical={false}
                  stroke="rgba(128,128,128,0.15)"
                />
                <XAxis
                  dataKey="month"
                  axisLine={false}
                  tickLine={false}
                  tick={{ fill: '#9ca3af', fontSize: 12 }}
                  reversed={isRtl}
                />
                <YAxis
                  axisLine={false}
                  tickLine={false}
                  tick={{ fill: '#9ca3af', fontSize: 12 }}
                  tickFormatter={(v) => `${(v / 1000).toFixed(0)}K`}
                  orientation={isRtl ? 'right' : 'left'}
                />
                <Tooltip content={<CustomTooltip />} cursor={{ fill: 'rgba(59,130,246,0.06)' }} />
                <Bar
                  dataKey="revenue"
                  fill="url(#barGradient)"
                  radius={[8, 8, 0, 0]}
                />
              </BarChart>
            </ResponsiveContainer>
          </div>
        </GlassCard>

        {/* Recent Activity */}
        <GlassCard>
          <div className="flex items-center justify-between mb-6">
            <h2 className="text-lg font-semibold text-gray-900 dark:text-white">
              {labels.recentActivity}
            </h2>
            <button className="text-sm text-blue-500 hover:text-blue-600 font-medium transition-colors">
              {labels.viewAll}
            </button>
          </div>
          <div className="space-y-4">
            {labels.activities.map((activity, idx) => {
              const config = activityIcons[activity.type] || activityIcons.request;
              const IconComp = config.icon;
              return (
                <div
                  key={idx}
                  className="flex items-start gap-3 p-3 rounded-xl hover:bg-white/50 dark:hover:bg-white/5 transition-colors group"
                >
                  <div
                    className={cn(
                      'shrink-0 w-10 h-10 rounded-xl flex items-center justify-center',
                      config.color
                    )}
                  >
                    <IconComp className="w-5 h-5" />
                  </div>
                  <div className="flex-1 min-w-0">
                    <p className="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                      {activity.text}
                    </p>
                    <div className="flex items-center gap-1.5 mt-1.5">
                      <Clock className="w-3.5 h-3.5 text-gray-400" />
                      <span className="text-xs text-gray-400">{activity.time}</span>
                    </div>
                  </div>
                  <ArrowUpRight className="w-4 h-4 text-gray-300 dark:text-gray-600 opacity-0 group-hover:opacity-100 transition-opacity shrink-0 mt-1" />
                </div>
              );
            })}
          </div>
        </GlassCard>
      </div>

      {/* ── Quick Actions + Pending Approvals (2 columns) ──────────────────── */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Quick Actions */}
        <GlassCard>
          <h2 className="text-lg font-semibold text-gray-900 dark:text-white mb-6">
            {labels.quickActions}
          </h2>
          <div className="grid grid-cols-2 gap-4">
            {quickActions.map((action) => (
              <a
                key={action.label}
                href={action.href}
                className="group flex flex-col items-center gap-3 p-5 rounded-2xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 hover:bg-white/80 dark:hover:bg-white/10 transition-all duration-300 hover:shadow-lg hover:scale-[1.02]"
              >
                <div
                  className={cn(
                    'w-12 h-12 rounded-xl flex items-center justify-center bg-gradient-to-br shadow-lg',
                    action.gradient,
                    action.shadow
                  )}
                >
                  <action.icon className="w-6 h-6 text-white" />
                </div>
                <span className="text-sm font-medium text-gray-700 dark:text-gray-300 text-center">
                  {action.label}
                </span>
              </a>
            ))}
          </div>
        </GlassCard>

        {/* Pending Approvals */}
        <GlassCard>
          <div className="flex items-center justify-between mb-6">
            <div className="flex items-center gap-3">
              <h2 className="text-lg font-semibold text-gray-900 dark:text-white">
                {labels.pendingApprovals}
              </h2>
              <span className="px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-500/10 text-red-500 border border-red-500/20">
                {labels.approvals.length}
              </span>
            </div>
            <button className="text-sm text-blue-500 hover:text-blue-600 font-medium transition-colors">
              {labels.viewAll}
            </button>
          </div>
          <div className="space-y-3">
            {labels.approvals.map((item, idx) => (
              <div
                key={idx}
                className="flex items-center gap-4 p-4 rounded-xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/40 dark:bg-white/[0.03] hover:bg-white/70 dark:hover:bg-white/5 transition-all"
              >
                <div className="flex-shrink-0 w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center">
                  <AlertCircle className="w-5 h-5 text-amber-500" />
                </div>
                <div className="flex-1 min-w-0">
                  <p className="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">
                    {item.title}
                  </p>
                  <p className="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    {item.type}
                  </p>
                </div>
                <div className="flex items-center gap-2 shrink-0">
                  <span
                    className={cn(
                      'px-2 py-0.5 rounded-md text-xs font-medium border',
                      urgencyColors[item.urgency]
                    )}
                  >
                    {item.urgency === 'high'
                      ? isRtl ? 'عاجل' : 'Urgent'
                      : item.urgency === 'medium'
                        ? isRtl ? 'متوسط' : 'Medium'
                        : isRtl ? 'منخفض' : 'Low'}
                  </span>
                  <button className="px-3 py-1.5 rounded-lg text-xs font-medium bg-blue-500/10 text-blue-500 hover:bg-blue-500/20 border border-blue-500/20 transition-colors">
                    {labels.review}
                  </button>
                </div>
              </div>
            ))}
          </div>
        </GlassCard>
      </div>
    </div>
  );
}
