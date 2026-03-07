import { clsx, type ClassValue } from 'clsx';

export function cn(...inputs: ClassValue[]) {
  return clsx(inputs);
}

export function formatCurrency(amount: number, locale = 'ar') {
  return new Intl.NumberFormat(locale === 'ar' ? 'ar-SA' : 'en-SA', {
    style: 'currency',
    currency: 'SAR',
  }).format(amount);
}

export function formatDate(date: string, locale = 'ar') {
  return new Intl.DateTimeFormat(locale === 'ar' ? 'ar-SA' : 'en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  }).format(new Date(date));
}

export function getStatusColor(status: string): string {
  const colors: Record<string, string> = {
    active: 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
    approved: 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
    published: 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
    paid: 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
    captured: 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
    completed: 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
    resolved: 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
    available: 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
    pending: 'bg-amber-500/20 text-amber-400 border-amber-500/30',
    draft: 'bg-slate-500/20 text-slate-400 border-slate-500/30',
    in_progress: 'bg-blue-500/20 text-blue-400 border-blue-500/30',
    waiting_reply: 'bg-purple-500/20 text-purple-400 border-purple-500/30',
    reserved: 'bg-blue-500/20 text-blue-400 border-blue-500/30',
    initiated: 'bg-blue-500/20 text-blue-400 border-blue-500/30',
    rejected: 'bg-red-500/20 text-red-400 border-red-500/30',
    cancelled: 'bg-red-500/20 text-red-400 border-red-500/30',
    failed: 'bg-red-500/20 text-red-400 border-red-500/30',
    suspended: 'bg-red-500/20 text-red-400 border-red-500/30',
    overdue: 'bg-red-500/20 text-red-400 border-red-500/30',
    rented: 'bg-indigo-500/20 text-indigo-400 border-indigo-500/30',
    unavailable: 'bg-gray-500/20 text-gray-400 border-gray-500/30',
    ended: 'bg-gray-500/20 text-gray-400 border-gray-500/30',
    closed: 'bg-gray-500/20 text-gray-400 border-gray-500/30',
    inactive: 'bg-gray-500/20 text-gray-400 border-gray-500/30',
    open: 'bg-blue-500/20 text-blue-400 border-blue-500/30',
    issued: 'bg-blue-500/20 text-blue-400 border-blue-500/30',
    partially_paid: 'bg-amber-500/20 text-amber-400 border-amber-500/30',
  };
  return colors[status.toLowerCase()] || 'bg-gray-500/20 text-gray-400 border-gray-500/30';
}
