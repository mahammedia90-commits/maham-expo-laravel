'use client';

import Sidebar from '@/components/layout/Sidebar';
import Navbar from '@/components/layout/Navbar';

export default function DashboardLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  return (
    <div className="min-h-screen bg-mesh-light">
      {/* Sidebar */}
      <Sidebar />

      {/* Main Content Area */}
      <div className="ps-72 min-h-screen transition-all duration-300">
        {/* Navbar */}
        <Navbar />

        {/* Page Content */}
        <main className="p-6">
          {children}
        </main>
      </div>
    </div>
  );
}
