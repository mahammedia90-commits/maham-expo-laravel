'use client';

import { cn } from '@/lib/utils';
import { LucideIcon } from 'lucide-react';

interface StatsCardProps {
  title: string;
  value: string | number;
  icon: LucideIcon;
  trend?: { value: number; isPositive: boolean };
  color?: 'blue' | 'emerald' | 'purple' | 'amber' | 'rose' | 'indigo';
}

const colorMap = {
  blue: 'from-blue-500/20 to-blue-600/10 border-blue-500/20 text-blue-500',
  emerald: 'from-emerald-500/20 to-emerald-600/10 border-emerald-500/20 text-emerald-500',
  purple: 'from-purple-500/20 to-purple-600/10 border-purple-500/20 text-purple-500',
  amber: 'from-amber-500/20 to-amber-600/10 border-amber-500/20 text-amber-500',
  rose: 'from-rose-500/20 to-rose-600/10 border-rose-500/20 text-rose-500',
  indigo: 'from-indigo-500/20 to-indigo-600/10 border-indigo-500/20 text-indigo-500',
};

const iconBgMap = {
  blue: 'bg-blue-500/20 text-blue-400',
  emerald: 'bg-emerald-500/20 text-emerald-400',
  purple: 'bg-purple-500/20 text-purple-400',
  amber: 'bg-amber-500/20 text-amber-400',
  rose: 'bg-rose-500/20 text-rose-400',
  indigo: 'bg-indigo-500/20 text-indigo-400',
};

export default function StatsCard({ title, value, icon: Icon, trend, color = 'blue' }: StatsCardProps) {
  return (
    <div
      className={cn(
        'relative overflow-hidden rounded-2xl border p-6',
        'bg-gradient-to-br',
        'bg-white/70 dark:bg-white/5',
        'backdrop-blur-xl backdrop-saturate-150',
        'shadow-lg shadow-black/5 dark:shadow-black/20',
        'transition-all duration-300 hover:scale-[1.02] hover:shadow-xl',
        colorMap[color]
      )}
    >
      <div className="flex items-start justify-between">
        <div className="space-y-2">
          <p className="text-sm font-medium text-gray-500 dark:text-gray-400">{title}</p>
          <p className="text-3xl font-bold text-gray-900 dark:text-white">{value}</p>
          {trend && (
            <p className={cn('text-sm font-medium', trend.isPositive ? 'text-emerald-500' : 'text-red-500')}>
              {trend.isPositive ? '+' : ''}{trend.value}%
            </p>
          )}
        </div>
        <div className={cn('rounded-xl p-3', iconBgMap[color])}>
          <Icon className="w-6 h-6" />
        </div>
      </div>
      <div className="absolute -bottom-4 -end-4 opacity-5">
        <Icon className="w-24 h-24" />
      </div>
    </div>
  );
}
