'use client';

import { useState } from 'react';
import Link from 'next/link';
import { usePathname } from 'next/navigation';
import { cn } from '@/lib/utils';
import {
  LayoutDashboard,
  Calendar,
  MapPin,
  Users,
  Tags,
  Building2,
  Wrench,
  Settings,
  FileText,
  HelpCircle,
  Image,
  BarChart3,
  CreditCard,
  Receipt,
  ClipboardList,
  Eye,
  Handshake,
  Award,
  Package,
  MessageSquare,
  Star,
  ChevronDown,
  ChevronLeft,
  ChevronRight,
  Briefcase,
  FileSignature,
  ShieldCheck,
} from 'lucide-react';

interface NavItem {
  label: string;
  labelAr: string;
  href?: string;
  icon: React.ElementType;
  children?: { label: string; labelAr: string; href: string; icon: React.ElementType }[];
}

const navItems: NavItem[] = [
  { label: 'Dashboard', labelAr: 'لوحة التحكم', href: '/dashboard', icon: LayoutDashboard },
  { label: 'Account Verification', labelAr: 'توثيق الحساب', href: '/dashboard/verification', icon: ShieldCheck },
  {
    label: 'Management', labelAr: 'الإدارة', icon: Briefcase,
    children: [
      { label: 'Events', labelAr: 'الفعاليات', href: '/dashboard/events', icon: Calendar },
      { label: 'Spaces', labelAr: 'المساحات', href: '/dashboard/spaces', icon: MapPin },
      { label: 'Categories', labelAr: 'التصنيفات', href: '/dashboard/categories', icon: Tags },
      { label: 'Cities', labelAr: 'المدن', href: '/dashboard/cities', icon: Building2 },
      { label: 'Services', labelAr: 'الخدمات', href: '/dashboard/services', icon: Wrench },
    ],
  },
  {
    label: 'Users & Profiles', labelAr: 'المستخدمون', icon: Users,
    children: [
      { label: 'Users', labelAr: 'المستخدمون', href: '/dashboard/users', icon: Users },
      { label: 'Business Profiles', labelAr: 'الملفات التجارية', href: '/dashboard/profiles', icon: Briefcase },
    ],
  },
  {
    label: 'Requests', labelAr: 'الطلبات', icon: ClipboardList,
    children: [
      { label: 'Rental Requests', labelAr: 'طلبات التأجير', href: '/dashboard/rental-requests', icon: ClipboardList },
      { label: 'Visit Requests', labelAr: 'طلبات الزيارة', href: '/dashboard/visit-requests', icon: Eye },
      { label: 'Rental Contracts', labelAr: 'عقود التأجير', href: '/dashboard/rental-contracts', icon: FileSignature },
    ],
  },
  {
    label: 'Finance', labelAr: 'المالية', icon: CreditCard,
    children: [
      { label: 'Invoices', labelAr: 'الفواتير', href: '/dashboard/invoices', icon: Receipt },
      { label: 'Payments', labelAr: 'المدفوعات', href: '/dashboard/payments', icon: CreditCard },
    ],
  },
  {
    label: 'Sponsors', labelAr: 'الرعاة', icon: Handshake,
    children: [
      { label: 'Sponsors', labelAr: 'الرعاة', href: '/dashboard/sponsors', icon: Handshake },
      { label: 'Packages', labelAr: 'الحزم', href: '/dashboard/sponsor-packages', icon: Package },
      { label: 'Contracts', labelAr: 'العقود', href: '/dashboard/sponsor-contracts', icon: FileSignature },
      { label: 'Benefits', labelAr: 'المزايا', href: '/dashboard/sponsor-benefits', icon: Award },
      { label: 'Assets', labelAr: 'الأصول', href: '/dashboard/sponsor-assets', icon: Image },
    ],
  },
  {
    label: 'Content', labelAr: 'المحتوى', icon: FileText,
    children: [
      { label: 'CMS Pages', labelAr: 'الصفحات', href: '/dashboard/pages', icon: FileText },
      { label: 'FAQs', labelAr: 'الأسئلة الشائعة', href: '/dashboard/faqs', icon: HelpCircle },
      { label: 'Banners', labelAr: 'البانرات', href: '/dashboard/banners', icon: Image },
    ],
  },
  { label: 'Support Tickets', labelAr: 'تذاكر الدعم', href: '/dashboard/support-tickets', icon: MessageSquare },
  { label: 'Ratings', labelAr: 'التقييمات', href: '/dashboard/ratings', icon: Star },
  { label: 'Analytics', labelAr: 'التحليلات', href: '/dashboard/analytics', icon: BarChart3 },
  { label: 'Settings', labelAr: 'الإعدادات', href: '/dashboard/settings', icon: Settings },
];

export default function Sidebar({ mobileOpen, onMobileClose }: { mobileOpen?: boolean; onMobileClose?: () => void }) {
  const pathname = usePathname();
  const [collapsed, setCollapsed] = useState(false);
  const [openGroups, setOpenGroups] = useState<string[]>(['Management', 'Users & Profiles']);
  const [locale, setLocale] = useState<string>(() => {
    if (typeof window !== 'undefined') return localStorage.getItem('locale') || 'ar';
    return 'ar';
  });

  // Listen for locale changes
  if (typeof window !== 'undefined') {
    const storedLocale = localStorage.getItem('locale') || 'ar';
    if (storedLocale !== locale) setLocale(storedLocale);
  }

  const isRtl = locale === 'ar';

  const toggleGroup = (label: string) => {
    setOpenGroups((prev) =>
      prev.includes(label) ? prev.filter((g) => g !== label) : [...prev, label]
    );
  };

  const isActive = (href: string) => pathname === href;

  return (
    <aside
      className={cn(
        'fixed top-0 start-0 z-40 h-screen transition-all duration-300 flex flex-col',
        'border-e border-white/10 dark:border-white/10 border-gray-200/60',
        'bg-white/80 dark:bg-gray-950/80',
        'backdrop-blur-2xl backdrop-saturate-150',
        collapsed ? 'w-20' : 'w-72',
        'hidden lg:flex',
        mobileOpen && '!flex'
      )}
    >
      {/* Logo */}
      <div className="flex items-center justify-between p-6 border-b border-white/10 dark:border-white/10 border-gray-200/60">
        {!collapsed && (
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center shadow-lg shadow-blue-500/25">
              <span className="text-white font-bold text-lg">M</span>
            </div>
            <div>
              <h1 className="text-lg font-bold text-gray-900 dark:text-white">
                {isRtl ? 'معرض مهام' : 'Maham Expo'}
              </h1>
              <p className="text-xs text-gray-500 dark:text-gray-400">
                {isRtl ? 'لوحة التحكم' : 'Dashboard'}
              </p>
            </div>
          </div>
        )}
        {collapsed && (
          <div className="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center shadow-lg shadow-blue-500/25 mx-auto">
            <span className="text-white font-bold text-lg">M</span>
          </div>
        )}
      </div>

      {/* Navigation */}
      <nav className="flex-1 overflow-y-auto py-4 px-3 scrollbar-thin">
        <div className="space-y-1">
          {navItems.map((item) => {
            if (item.children) {
              const isOpen = openGroups.includes(item.label);
              const hasActiveChild = item.children.some((c) => isActive(c.href));
              return (
                <div key={item.label}>
                  <button
                    onClick={() => !collapsed && toggleGroup(item.label)}
                    className={cn(
                      'w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200',
                      hasActiveChild
                        ? 'text-blue-600 dark:text-blue-400'
                        : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-white/50 dark:hover:bg-white/5'
                    )}
                  >
                    <item.icon className="w-5 h-5 shrink-0" />
                    {!collapsed && (
                      <>
                        <span className="flex-1 text-start">{isRtl ? item.labelAr : item.label}</span>
                        <ChevronDown
                          className={cn(
                            'w-4 h-4 transition-transform duration-200',
                            isOpen && 'rotate-180'
                          )}
                        />
                      </>
                    )}
                  </button>
                  {isOpen && !collapsed && (
                    <div className="ms-4 mt-1 space-y-0.5 border-s-2 border-gray-200/50 dark:border-white/10 ps-3">
                      {item.children.map((child) => (
                        <Link
                          key={child.href}
                          href={child.href}
                          className={cn(
                            'flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all duration-200',
                            isActive(child.href)
                              ? 'bg-blue-500/10 text-blue-600 dark:text-blue-400 font-medium border border-blue-500/20'
                              : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-white/50 dark:hover:bg-white/5'
                          )}
                        >
                          <child.icon className="w-4 h-4 shrink-0" />
                          <span>{isRtl ? child.labelAr : child.label}</span>
                        </Link>
                      ))}
                    </div>
                  )}
                </div>
              );
            }

            return (
              <Link
                key={item.href}
                href={item.href!}
                className={cn(
                  'flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200',
                  isActive(item.href!)
                    ? 'bg-gradient-to-r from-blue-500/20 to-purple-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20 shadow-sm'
                    : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-white/50 dark:hover:bg-white/5'
                )}
              >
                <item.icon className="w-5 h-5 shrink-0" />
                {!collapsed && <span>{isRtl ? item.labelAr : item.label}</span>}
              </Link>
            );
          })}
        </div>
      </nav>

      {/* Collapse Toggle */}
      <div className="p-3 border-t border-white/10 dark:border-white/10 border-gray-200/60">
        <button
          onClick={() => setCollapsed(!collapsed)}
          className="w-full flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl text-sm text-gray-500 dark:text-gray-400 hover:bg-white/50 dark:hover:bg-white/5 transition-colors"
        >
          {collapsed ? (
            isRtl ? <ChevronLeft className="w-5 h-5" /> : <ChevronRight className="w-5 h-5" />
          ) : (
            isRtl ? <ChevronRight className="w-5 h-5" /> : <ChevronLeft className="w-5 h-5" />
          )}
          {!collapsed && <span>{isRtl ? 'طي القائمة' : 'Collapse'}</span>}
        </button>
      </div>
    </aside>
  );
}
