'use client';

import { cn, getStatusColor } from '@/lib/utils';

interface StatusBadgeProps {
  status: string;
  label?: string;
}

export default function StatusBadge({ status, label }: StatusBadgeProps) {
  return (
    <span
      className={cn(
        'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border',
        getStatusColor(status)
      )}
    >
      {label || status}
    </span>
  );
}
