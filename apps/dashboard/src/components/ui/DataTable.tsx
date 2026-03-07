'use client';

import { ReactNode } from 'react';
import { cn } from '@/lib/utils';
import { ChevronLeft, ChevronRight } from 'lucide-react';

export interface Column<T> {
  key: string;
  header: string;
  render?: (item: T) => ReactNode;
  className?: string;
}

interface DataTableProps<T> {
  columns: Column<T>[];
  data: T[];
  loading?: boolean;
  pagination?: {
    current_page: number;
    last_page: number;
    total: number;
    per_page: number;
  };
  onPageChange?: (page: number) => void;
  emptyMessage?: string;
  onRowClick?: (item: T) => void;
}

export default function DataTable<T extends Record<string, unknown>>({
  columns,
  data,
  loading,
  pagination,
  onPageChange,
  emptyMessage = 'No data available',
  onRowClick,
}: DataTableProps<T>) {
  if (loading) {
    return (
      <div className="rounded-2xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/70 dark:bg-white/5 backdrop-blur-xl overflow-hidden">
        <div className="animate-pulse p-6 space-y-4">
          {[...Array(5)].map((_, i) => (
            <div key={i} className="h-12 bg-gray-200/50 dark:bg-white/10 rounded-lg" />
          ))}
        </div>
      </div>
    );
  }

  return (
    <div className="rounded-2xl border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/70 dark:bg-white/5 backdrop-blur-xl backdrop-saturate-150 shadow-lg overflow-hidden">
      <div className="overflow-x-auto">
        <table className="w-full">
          <thead>
            <tr className="border-b border-white/10 dark:border-white/10 border-gray-200/60">
              {columns.map((col) => (
                <th
                  key={col.key}
                  className={cn(
                    'px-6 py-4 text-start text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400',
                    col.className
                  )}
                >
                  {col.header}
                </th>
              ))}
            </tr>
          </thead>
          <tbody className="divide-y divide-white/5 dark:divide-white/5 divide-gray-100">
            {data.length === 0 ? (
              <tr>
                <td colSpan={columns.length} className="px-6 py-16 text-center text-gray-400">
                  {emptyMessage}
                </td>
              </tr>
            ) : (
              data.map((item, idx) => (
                <tr
                  key={(item.id as string) || idx}
                  onClick={() => onRowClick?.(item)}
                  className={cn(
                    'transition-colors duration-150',
                    'hover:bg-white/30 dark:hover:bg-white/5',
                    onRowClick && 'cursor-pointer'
                  )}
                >
                  {columns.map((col) => (
                    <td key={col.key} className={cn('px-6 py-4 text-sm text-gray-700 dark:text-gray-300', col.className)}>
                      {col.render ? col.render(item) : (item[col.key] as ReactNode)}
                    </td>
                  ))}
                </tr>
              ))
            )}
          </tbody>
        </table>
      </div>

      {pagination && pagination.last_page > 1 && (
        <div className="flex items-center justify-between px-6 py-4 border-t border-white/10 dark:border-white/10 border-gray-200/60">
          <p className="text-sm text-gray-500 dark:text-gray-400">
            {pagination.total} results
          </p>
          <div className="flex items-center gap-2">
            <button
              onClick={() => onPageChange?.(pagination.current_page - 1)}
              disabled={pagination.current_page <= 1}
              className="p-2 rounded-lg border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 disabled:opacity-30 hover:bg-white/80 dark:hover:bg-white/10 transition-colors"
            >
              <ChevronLeft className="w-4 h-4" />
            </button>
            <span className="text-sm text-gray-600 dark:text-gray-400 px-3">
              {pagination.current_page} / {pagination.last_page}
            </span>
            <button
              onClick={() => onPageChange?.(pagination.current_page + 1)}
              disabled={pagination.current_page >= pagination.last_page}
              className="p-2 rounded-lg border border-white/10 dark:border-white/10 border-gray-200/60 bg-white/50 dark:bg-white/5 disabled:opacity-30 hover:bg-white/80 dark:hover:bg-white/10 transition-colors"
            >
              <ChevronRight className="w-4 h-4" />
            </button>
          </div>
        </div>
      )}
    </div>
  );
}
