'use client';

import { useEffect } from 'react';
import { useRouter } from 'next/navigation';

export default function RootPage() {
  const router = useRouter();

  useEffect(() => {
    router.replace('/dashboard');
  }, [router]);

  return (
    <div className="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-50 via-blue-50/50 to-indigo-50 dark:from-[#0a0e17] dark:via-[#0d1321] dark:to-[#0a0e17]">
      <div className="flex flex-col items-center gap-4">
        {/* Spinner */}
        <div className="w-10 h-10 rounded-full border-3 border-blue-500/20 border-t-blue-500 animate-spin" />
      </div>
    </div>
  );
}
