'use client';

import { cn } from '@/lib/utils';
import { ReactNode } from 'react';

interface GlassCardProps {
  children: ReactNode;
  className?: string;
  hover?: boolean;
  padding?: boolean;
}

export default function GlassCard({ children, className, hover = false, padding = true }: GlassCardProps) {
  return (
    <div
      className={cn(
        'rounded-2xl border border-white/10 dark:border-white/10 border-gray-200/60',
        'bg-white/70 dark:bg-white/5',
        'backdrop-blur-xl backdrop-saturate-150',
        'shadow-lg shadow-black/5 dark:shadow-black/20',
        hover && 'transition-all duration-300 hover:shadow-xl hover:shadow-black/10 dark:hover:shadow-black/30 hover:border-white/20 dark:hover:border-white/20 hover:scale-[1.01]',
        padding && 'p-6',
        className
      )}
    >
      {children}
    </div>
  );
}
