<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maham Expo API - API Documentation</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        ar: ['Tajawal', 'sans-serif'],
                        en: ['Inter', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    }
                }
            }
        }
    </script>
    <style>
        [dir="rtl"] body { font-family: 'Tajawal', sans-serif; }
        [dir="ltr"] body { font-family: 'Inter', sans-serif; }
        .sidebar { height: calc(100vh - 64px); }
        pre, code { font-family: 'JetBrains Mono', monospace; direction: ltr; text-align: left; }
        .method-get { background-color: #10B981; }
        .method-post { background-color: #3B82F6; }
        .method-put { background-color: #F59E0B; }
        .method-delete { background-color: #EF4444; }
        .code-block { background: #1e293b; border-radius: 8px; overflow: hidden; }
        .code-block .code-header { background: #334155; padding: 8px 16px; font-size: 12px; color: #94a3b8; display: flex; justify-content: space-between; align-items: center; }
        .code-block pre { padding: 16px; margin: 0; font-size: 13px; line-height: 1.6; overflow-x: auto; }
        .code-block code { color: #e2e8f0; }
        .json-key { color: #7dd3fc; }
        .json-string { color: #86efac; }
        .json-bool { color: #fbbf24; }
        .json-number { color: #c4b5fd; }
        .endpoint-card { border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; transition: box-shadow 0.2s; }
        .endpoint-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        .sidebar-link { transition: all 0.15s; border-right: 3px solid transparent; }
        [dir="ltr"] .sidebar-link { border-right: none; border-left: 3px solid transparent; }
        .sidebar-link.active, .sidebar-link:hover { border-color: #10B981; background: #ecfdf5; color: #059669; }
        .param-table th { font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em; }
        .badge { font-size: 10px; padding: 2px 8px; border-radius: 9999px; font-weight: 600; letter-spacing: 0.05em; }
        .perm-badge { font-size: 11px; background: #f3e8ff; color: #7c3aed; padding: 2px 10px; border-radius: 6px; font-family: 'JetBrains Mono', monospace; }
        .section-divider { border-top: 3px solid #e5e7eb; padding-top: 2rem; margin-top: 3rem; }
        @media (max-width: 768px) {
            .sidebar { display: none; }
            main { margin-right: 0 !important; margin-left: 0 !important; }
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-900">

    <!-- Header -->
    <header class="bg-gray-900 text-white h-16 fixed top-0 left-0 right-0 z-50 shadow-lg">
        <div class="container mx-auto px-6 h-full flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-emerald-500 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <div>
                    <h1 class="text-lg font-bold leading-tight">Maham Expo API</h1>
                    <p class="text-xs text-gray-400 leading-tight">API Documentation</p>
                </div>
                <span class="badge bg-emerald-500/20 text-emerald-400 mr-2">v{{ config('app.version', '1.0.0') }}</span>
            </div>
            <div class="flex items-center gap-4">
                <button onclick="toggleLang()" id="langBtn" class="flex items-center gap-2 bg-gray-800 hover:bg-gray-700 px-3 py-1.5 rounded-lg text-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/></svg>
                    <span id="langLabel">English</span>
                </button>
                <a href="{{ url('/api/health') }}" target="_blank" class="text-sm text-gray-400 hover:text-green-400 transition flex items-center gap-1">
                    <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                    Health
                </a>
            </div>
        </div>
    </header>

    <div class="flex pt-16">
        <!-- Sidebar -->
        <aside id="sidebar" class="w-72 bg-white border-l border-gray-200 fixed right-0 top-16 sidebar overflow-y-auto shadow-sm">
            <nav class="p-4 space-y-6">
                <div>
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-3" data-ar="البداية" data-en="Getting Started">البداية</h3>
                    <ul class="space-y-0.5">
                        <li><a href="#introduction" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="مقدمة" data-en="Introduction">مقدمة</a></li>
                        <li><a href="#headers" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="الهيدرات" data-en="Headers">الهيدرات</a></li>
                        <li><a href="#authentication" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="المصادقة" data-en="Authentication">المصادقة</a></li>
                        <li><a href="#response-format" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="صيغة الردود" data-en="Response Format">صيغة الردود</a></li>
                        <li><a href="#samples" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="أمثلة عملية" data-en="Sample Requests">أمثلة عملية</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-3" data-ar="عام (بدون تسجيل)" data-en="Public (No Auth)">عام</h3>
                    <ul class="space-y-0.5">
                        <li><a href="#public-events" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="المعارض" data-en="Events">المعارض</a></li>
                        <li><a href="#public-categories" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="الفئات" data-en="Categories">الفئات</a></li>
                        <li><a href="#public-cities" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="المدن" data-en="Cities">المدن</a></li>
                        <li><a href="#public-services" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="الخدمات" data-en="Services">الخدمات</a></li>
                        <li><a href="#public-statistics" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="الإحصائيات" data-en="Statistics">الإحصائيات</a></li>
                        <li><a href="#public-ratings" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="التقييمات" data-en="Ratings">التقييمات</a></li>
                        <li><a href="#public-content" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="المحتوى" data-en="Pages/FAQs/Banners">المحتوى</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-xs font-semibold text-cyan-600 uppercase tracking-wider mb-2 px-3" data-ar="التتبع" data-en="Tracking">📊 التتبع</h3>
                    <ul class="space-y-0.5">
                        <li><a href="#track-view" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="تسجيل مشاهدة" data-en="Track View">تسجيل مشاهدة</a></li>
                        <li><a href="#track-action" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="تسجيل حدث" data-en="Track Action">تسجيل حدث</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-3" data-ar="خدمة ذاتية" data-en="Self-Service">خدمة ذاتية</h3>
                    <ul class="space-y-0.5">
                        <li><a href="#self-profile" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="الملف التجاري" data-en="Business Profile">الملف التجاري</a></li>
                        <li><a href="#self-favorites" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="المفضلة" data-en="Favorites">المفضلة</a></li>
                        <li><a href="#self-notifications" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="الإشعارات" data-en="Notifications">الإشعارات</a></li>
                        <li><a href="#self-ratings" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="تقييماتي" data-en="My Ratings">تقييماتي</a></li>
                        <li><a href="#self-tickets" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="تذاكر الدعم" data-en="Support Tickets">تذاكر الدعم</a></li>
                        <li><a href="#self-invoices" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="فواتيري" data-en="My Invoices">فواتيري</a></li>
                        <li><a href="#self-visits" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="طلبات الزيارة" data-en="Visit Requests">طلبات الزيارة</a></li>
                        <li><a href="#self-rentals" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="طلبات الإيجار" data-en="Rental Requests">طلبات الإيجار</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-xs font-semibold text-emerald-600 uppercase tracking-wider mb-2 px-3" data-ar="بياناتي /my" data-en="Owner-Scoped /my">/my</h3>
                    <ul class="space-y-0.5">
                        <li><a href="#my-dashboard" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="لوحة التحكم" data-en="Dashboard">لوحة التحكم</a></li>
                        <li><a href="#my-spaces" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="مساحاتي" data-en="My Spaces">مساحاتي</a></li>
                        <li><a href="#my-received-visits" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="زيارات واردة" data-en="Received Visits">زيارات واردة</a></li>
                        <li><a href="#my-received-rentals" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="إيجارات واردة" data-en="Received Rentals">إيجارات واردة</a></li>
                        <li><a href="#my-payments" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="مدفوعاتي" data-en="My Payments">مدفوعاتي</a></li>
                        <li><a href="#my-rental-contracts" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="عقود الإيجار" data-en="Rental Contracts">عقود الإيجار</a></li>
                        <li><a href="#my-sponsor" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="الرعاية" data-en="Sponsor Data">الرعاية</a></li>
                        <li><a href="#my-activity" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="نشاطاتي" data-en="My Activity">نشاطاتي</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-xs font-semibold text-purple-600 uppercase tracking-wider mb-2 px-3" data-ar="إدارة /manage" data-en="Management /manage">/manage</h3>
                    <ul class="space-y-0.5">
                        <li><a href="#manage-dashboard" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="لوحة التحكم" data-en="Dashboard & Stats">لوحة التحكم</a></li>
                        <li><a href="#manage-events" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="المعارض" data-en="Events">المعارض</a></li>
                        <li><a href="#manage-sections-spaces" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="أقسام ومساحات" data-en="Sections & Spaces">أقسام ومساحات</a></li>
                        <li><a href="#manage-services" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="الخدمات" data-en="Services">الخدمات</a></li>
                        <li><a href="#manage-lookups" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="فئات / مدن / إعدادات" data-en="Categories/Cities/Settings">فئات / مدن</a></li>
                        <li><a href="#manage-users" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="المستخدمين" data-en="Users & Profiles">المستخدمين</a></li>
                        <li><a href="#manage-requests" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="زيارات وإيجارات" data-en="Visits & Rentals">زيارات وإيجارات</a></li>
                        <li><a href="#manage-contracts" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="العقود" data-en="Rental Contracts">العقود</a></li>
                        <li><a href="#manage-sponsors" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="الرعاة" data-en="Sponsors">الرعاة</a></li>
                        <li><a href="#manage-ratings-tickets" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="تقييمات / تذاكر" data-en="Ratings & Tickets">تقييمات / تذاكر</a></li>
                        <li><a href="#manage-invoices" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="الفواتير" data-en="Invoices">الفواتير</a></li>
                        <li><a href="#manage-content" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="المحتوى CMS" data-en="CMS Content">المحتوى</a></li>
                        <li><a href="#manage-analytics" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="التحليلات" data-en="Analytics">التحليلات</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-3" data-ar="المرجع" data-en="Reference">المرجع</h3>
                    <ul class="space-y-0.5">
                        <li><a href="#postman" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="مجموعات Postman" data-en="Postman Collections">📦 Postman</a></li>
                        <li><a href="#errors" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="رموز الحالة" data-en="Status Codes">رموز الحالة</a></li>
                        <li><a href="#error-codes" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="رموز الأخطاء" data-en="Error Codes">رموز الأخطاء</a></li>
                        <li><a href="#permissions-ref" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="جدول الصلاحيات" data-en="Permissions Map">جدول الصلاحيات</a></li>
                    </ul>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 mr-72 p-8 min-h-screen">
            <div class="max-w-4xl mx-auto">

                {{-- ============================================================ --}}
                {{-- INTRODUCTION --}}
                {{-- ============================================================ --}}
                <section id="introduction" class="mb-20">
                    <div class="mb-8">
                        <h1 class="text-4xl font-extrabold mb-3" data-ar="واجهة معارض محام" data-en="Maham Expo API">واجهة معارض محام</h1>
                        <p class="text-lg text-gray-500" data-ar="إدارة المعارض، المساحات، الإيجارات، الرعاة، والمزيد" data-en="Manage expos, spaces, rentals, sponsors, and more">إدارة المعارض، المساحات، الإيجارات، الرعاة، والمزيد</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                        <div class="bg-white rounded-xl p-5 border border-gray-200">
                            <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center mb-3">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <h3 class="font-bold mb-1" data-ar="إدارة المعارض" data-en="Expo Management">إدارة المعارض</h3>
                            <p class="text-sm text-gray-500" data-ar="معارض، أقسام، مساحات، خدمات" data-en="Events, sections, spaces, services">معارض، أقسام، مساحات</p>
                        </div>
                        <div class="bg-white rounded-xl p-5 border border-gray-200">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mb-3">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            </div>
                            <h3 class="font-bold mb-1" data-ar="إيجارات وعقود" data-en="Rentals & Contracts">إيجارات وعقود</h3>
                            <p class="text-sm text-gray-500" data-ar="طلبات زيارة، إيجار، عقود" data-en="Visit/rental requests, contracts">طلبات زيارة، إيجار، عقود</p>
                        </div>
                        <div class="bg-white rounded-xl p-5 border border-gray-200">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mb-3">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <h3 class="font-bold mb-1" data-ar="رعاة ومدفوعات" data-en="Sponsors & Payments">رعاة ومدفوعات</h3>
                            <p class="text-sm text-gray-500" data-ar="رعاة، عقود رعاية، فواتير" data-en="Sponsors, contracts, invoices">رعاة، عقود رعاية، فواتير</p>
                        </div>
                    </div>

                    <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-5 mb-6">
                        <h4 class="font-bold text-emerald-800 mb-2">Base URL</h4>
                        <code class="text-emerald-700 text-lg">{{ url('/api/v1') }}</code>
                    </div>

                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-5">
                        <h4 class="font-bold text-yellow-800 mb-2" data-ar="ملاحظة مهمة" data-en="Important Note">ملاحظة مهمة</h4>
                        <p class="text-sm text-yellow-700" data-ar="هذه الخدمة لا تدير المصادقة محلياً — جميع عمليات المصادقة تتم عبر Auth Service (المنفذ 8001). أرسل نفس توكن JWT في هيدر Authorization." data-en="This service does NOT handle auth locally — all authentication goes through the Auth Service (port 8001). Send the same JWT token in Authorization header.">هذه الخدمة لا تدير المصادقة محلياً — تتم عبر Auth Service.</p>
                    </div>
                </section>

                {{-- ============================================================ --}}
                {{-- HEADERS --}}
                {{-- ============================================================ --}}
                <section id="headers" class="mb-20">
                    <h2 class="text-3xl font-bold mb-4" data-ar="الهيدرات المطلوبة" data-en="Required Headers">الهيدرات المطلوبة</h2>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full">
                            <thead><tr class="bg-gray-50 border-b">
                                <th class="p-4 text-right text-sm font-semibold text-gray-500" data-ar="الهيدر" data-en="Header">الهيدر</th>
                                <th class="p-4 text-right text-sm font-semibold text-gray-500" data-ar="القيمة" data-en="Value">القيمة</th>
                                <th class="p-4 text-right text-sm font-semibold text-gray-500" data-ar="مطلوب" data-en="Required">مطلوب</th>
                                <th class="p-4 text-right text-sm font-semibold text-gray-500" data-ar="الوصف" data-en="Description">الوصف</th>
                            </tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-4"><code class="bg-gray-100 px-2 py-1 rounded text-sm">Content-Type</code></td><td class="p-4"><code class="text-sm">application/json</code></td><td class="p-4"><span class="text-red-500 font-bold">✓</span></td><td class="p-4 text-sm text-gray-600" data-ar="نوع المحتوى المرسل" data-en="Content type of request body">نوع المحتوى</td></tr>
                                <tr class="border-b"><td class="p-4"><code class="bg-gray-100 px-2 py-1 rounded text-sm">Accept</code></td><td class="p-4"><code class="text-sm">application/json</code></td><td class="p-4"><span class="text-red-500 font-bold">✓</span></td><td class="p-4 text-sm text-gray-600" data-ar="نوع الرد المطلوب" data-en="Expected response format">نوع الرد</td></tr>
                                <tr class="border-b"><td class="p-4"><code class="bg-gray-100 px-2 py-1 rounded text-sm">Authorization</code></td><td class="p-4"><code class="text-sm">Bearer {token}</code></td><td class="p-4"><span class="text-yellow-600 text-sm" data-ar="للمحمية" data-en="Protected">للمحمية</span></td><td class="p-4 text-sm text-gray-600" data-ar="توكن JWT من Auth Service" data-en="JWT token from Auth Service">توكن JWT من Auth Service</td></tr>
                                <tr class="border-b"><td class="p-4"><code class="bg-gray-100 px-2 py-1 rounded text-sm">Accept-Language</code></td><td class="p-4"><code class="text-sm">ar</code> | <code class="text-sm">en</code></td><td class="p-4"><span class="text-gray-400 text-sm" data-ar="اختياري" data-en="Optional">اختياري</span></td><td class="p-4 text-sm text-gray-600" data-ar="لغة الردود (الافتراضي: en)" data-en="Response language (default: en)">لغة الردود</td></tr>
                                <tr><td class="p-4"><code class="bg-purple-100 px-2 py-1 rounded text-sm">X-Platform</code></td><td class="p-4"><code class="text-sm">web</code> | <code class="text-sm">mobile</code> | <code class="text-sm">api</code></td><td class="p-4"><span class="text-gray-400 text-sm" data-ar="اختياري" data-en="Optional">اختياري</span></td><td class="p-4 text-sm text-gray-600" data-ar="مصدر الطلب للتتبع والتحليل (الافتراضي: web)" data-en="Request source for tracking & analytics (default: web)">مصدر الطلب — web / mobile / api</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- ============================================================ --}}
                {{-- AUTHENTICATION INFO --}}
                {{-- ============================================================ --}}
                <section id="authentication" class="mb-20">
                    <h2 class="text-3xl font-bold mb-4" data-ar="المصادقة" data-en="Authentication">المصادقة</h2>
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-5 mb-6">
                        <h4 class="font-bold text-blue-800 mb-2" data-ar="كيف تعمل المصادقة هنا؟" data-en="How Auth Works Here?">كيف تعمل المصادقة هنا؟</h4>
                        <ol class="text-sm text-blue-700 space-y-2 list-decimal list-inside">
                            <li data-ar="احصل على توكن JWT من Auth Service (المنفذ 8001)" data-en="Get JWT token from Auth Service (port 8001)">احصل على توكن JWT من Auth Service</li>
                            <li data-ar="أرسل التوكن في هيدر Authorization لهذه الخدمة" data-en="Send token in Authorization header to this service">أرسل التوكن في هيدر Authorization لهذه الخدمة</li>
                            <li data-ar="هذه الخدمة تتحقق من التوكن والصلاحيات عبر Auth Service" data-en="This service verifies token & permissions via Auth Service">هذه الخدمة تتحقق عبر Auth Service</li>
                        </ol>
                    </div>
                    <div class="code-block">
                        <div class="code-header"><span data-ar="مثال طلب محمي" data-en="Authenticated Request Example">مثال طلب محمي</span></div>
                        <pre><code>curl -X GET {{ url('/api/v1') }}/my/dashboard \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"</code></pre>
                    </div>
                </section>

                {{-- RESPONSE FORMAT --}}
                <section id="response-format" class="mb-20">
                    <h2 class="text-3xl font-bold mb-4" data-ar="صيغة الردود" data-en="Response Format">صيغة الردود</h2>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
                        <div class="code-block">
                            <div class="code-header"><span data-ar="رد ناجح" data-en="Success">رد ناجح</span><span class="badge bg-green-500/30 text-green-300">2xx</span></div>
                            <pre><code>{
  <span class="json-key">"success"</span>: <span class="json-bool">true</span>,
  <span class="json-key">"message"</span>: <span class="json-string">"Operation successful"</span>,
  <span class="json-key">"data"</span>: { ... }
}</code></pre>
                        </div>
                        <div class="code-block">
                            <div class="code-header"><span data-ar="رد مع ترقيم" data-en="Paginated">رد مع ترقيم</span><span class="badge bg-blue-500/30 text-blue-300">2xx</span></div>
                            <pre><code>{
  <span class="json-key">"success"</span>: <span class="json-bool">true</span>,
  <span class="json-key">"data"</span>: [ ... ],
  <span class="json-key">"meta"</span>: {
    <span class="json-key">"current_page"</span>: <span class="json-number">1</span>,
    <span class="json-key">"last_page"</span>: <span class="json-number">5</span>,
    <span class="json-key">"per_page"</span>: <span class="json-number">15</span>,
    <span class="json-key">"total"</span>: <span class="json-number">73</span>
  }
}</code></pre>
                        </div>
                    </div>
                </section>

                {{-- ============================================================ --}}
                {{--              SAMPLE REQUEST / RESPONSE EXAMPLES              --}}
                {{-- ============================================================ --}}
                <section id="samples" class="mb-20">
                    <h2 class="text-3xl font-bold mb-2" data-ar="📋 أمثلة عملية" data-en="📋 Sample Requests & Responses">📋 أمثلة عملية</h2>
                    <p class="text-gray-500 mb-8" data-ar="أمثلة حقيقية لطلبات API مع الردود المتوقعة" data-en="Real-world API request examples with expected responses">أمثلة حقيقية لطلبات API مع الردود المتوقعة</p>

                    {{-- Sample 1: GET Public Events --}}
                    <div class="mb-8">
                        <h4 class="text-lg font-bold mb-3 flex items-center gap-2"><span class="method-get text-xs px-2 py-1 rounded font-mono">GET</span> <span data-ar="جلب المعارض العامة" data-en="List Public Events">جلب المعارض العامة</span></h4>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div class="code-block">
                                <div class="code-header"><span data-ar="الطلب" data-en="Request">الطلب</span></div>
                                <pre><code><span class="json-key">GET</span> <span class="json-string">/api/v1/events?page=1&per_page=5</span>

<span class="json-key">Headers:</span>
  Accept: application/json</code></pre>
                            </div>
                            <div class="code-block">
                                <div class="code-header"><span data-ar="الرد" data-en="Response">الرد</span><span class="badge bg-green-500/30 text-green-300">200</span></div>
                                <pre><code>{
  <span class="json-key">"success"</span>: <span class="json-bool">true</span>,
  <span class="json-key">"data"</span>: [
    {
      <span class="json-key">"id"</span>: <span class="json-number">1</span>,
      <span class="json-key">"name"</span>: <span class="json-string">"معرض الرياض التجاري"</span>,
      <span class="json-key">"status"</span>: <span class="json-string">"active"</span>,
      <span class="json-key">"start_date"</span>: <span class="json-string">"2025-03-01"</span>,
      <span class="json-key">"end_date"</span>: <span class="json-string">"2025-03-05"</span>,
      <span class="json-key">"city"</span>: { <span class="json-key">"id"</span>: <span class="json-number">1</span>, <span class="json-key">"name"</span>: <span class="json-string">"الرياض"</span> }
    }
  ],
  <span class="json-key">"meta"</span>: { <span class="json-key">"current_page"</span>: <span class="json-number">1</span>, <span class="json-key">"last_page"</span>: <span class="json-number">3</span>, <span class="json-key">"total"</span>: <span class="json-number">12</span> }
}</code></pre>
                            </div>
                        </div>
                    </div>

                    {{-- Sample 2: POST Create Rental Request --}}
                    <div class="mb-8">
                        <h4 class="text-lg font-bold mb-3 flex items-center gap-2"><span class="method-post text-xs px-2 py-1 rounded font-mono">POST</span> <span data-ar="إنشاء طلب إيجار" data-en="Create Rental Request">إنشاء طلب إيجار</span></h4>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div class="code-block">
                                <div class="code-header"><span data-ar="الطلب" data-en="Request">الطلب</span></div>
                                <pre><code><span class="json-key">POST</span> <span class="json-string">/api/v1/rental-requests</span>

<span class="json-key">Headers:</span>
  Accept: application/json
  Authorization: Bearer {token}
  Content-Type: application/json

<span class="json-key">Body:</span>
{
  <span class="json-key">"space_id"</span>: <span class="json-number">5</span>,
  <span class="json-key">"event_id"</span>: <span class="json-number">1</span>,
  <span class="json-key">"notes"</span>: <span class="json-string">"أريد جناح بواجهة أمامية"</span>
}</code></pre>
                            </div>
                            <div class="code-block">
                                <div class="code-header"><span data-ar="الرد" data-en="Response">الرد</span><span class="badge bg-green-500/30 text-green-300">201</span></div>
                                <pre><code>{
  <span class="json-key">"success"</span>: <span class="json-bool">true</span>,
  <span class="json-key">"message"</span>: <span class="json-string">"Rental request created"</span>,
  <span class="json-key">"data"</span>: {
    <span class="json-key">"id"</span>: <span class="json-number">42</span>,
    <span class="json-key">"space_id"</span>: <span class="json-number">5</span>,
    <span class="json-key">"status"</span>: <span class="json-string">"pending"</span>,
    <span class="json-key">"notes"</span>: <span class="json-string">"أريد جناح بواجهة أمامية"</span>,
    <span class="json-key">"created_at"</span>: <span class="json-string">"2025-02-15T10:30:00Z"</span>
  }
}</code></pre>
                            </div>
                        </div>
                    </div>

                    {{-- Sample 3: PUT Update Event --}}
                    <div class="mb-8">
                        <h4 class="text-lg font-bold mb-3 flex items-center gap-2"><span class="method-put text-xs px-2 py-1 rounded font-mono">PUT</span> <span data-ar="تعديل معرض" data-en="Update Event">تعديل معرض</span></h4>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div class="code-block">
                                <div class="code-header"><span data-ar="الطلب" data-en="Request">الطلب</span></div>
                                <pre><code><span class="json-key">PUT</span> <span class="json-string">/api/v1/events/1</span>

<span class="json-key">Headers:</span>
  Accept: application/json
  Authorization: Bearer {token}
  Content-Type: application/json

<span class="json-key">Body:</span>
{
  <span class="json-key">"name"</span>: <span class="json-string">"معرض الرياض الدولي 2025"</span>,
  <span class="json-key">"status"</span>: <span class="json-string">"active"</span>,
  <span class="json-key">"end_date"</span>: <span class="json-string">"2025-03-10"</span>
}</code></pre>
                            </div>
                            <div class="code-block">
                                <div class="code-header"><span data-ar="الرد" data-en="Response">الرد</span><span class="badge bg-green-500/30 text-green-300">200</span></div>
                                <pre><code>{
  <span class="json-key">"success"</span>: <span class="json-bool">true</span>,
  <span class="json-key">"message"</span>: <span class="json-string">"Event updated successfully"</span>,
  <span class="json-key">"data"</span>: {
    <span class="json-key">"id"</span>: <span class="json-number">1</span>,
    <span class="json-key">"name"</span>: <span class="json-string">"معرض الرياض الدولي 2025"</span>,
    <span class="json-key">"status"</span>: <span class="json-string">"active"</span>,
    <span class="json-key">"end_date"</span>: <span class="json-string">"2025-03-10"</span>,
    <span class="json-key">"updated_at"</span>: <span class="json-string">"2025-02-15T11:00:00Z"</span>
  }
}</code></pre>
                            </div>
                        </div>
                    </div>

                    {{-- Sample 4: DELETE --}}
                    <div class="mb-4">
                        <h4 class="text-lg font-bold mb-3 flex items-center gap-2"><span class="method-delete text-xs px-2 py-1 rounded font-mono">DELETE</span> <span data-ar="حذف تذكرة دعم" data-en="Delete Support Ticket">حذف تذكرة دعم</span></h4>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div class="code-block">
                                <div class="code-header"><span data-ar="الطلب" data-en="Request">الطلب</span></div>
                                <pre><code><span class="json-key">DELETE</span> <span class="json-string">/api/v1/support-tickets/7</span>

<span class="json-key">Headers:</span>
  Accept: application/json
  Authorization: Bearer {token}</code></pre>
                            </div>
                            <div class="code-block">
                                <div class="code-header"><span data-ar="الرد" data-en="Response">الرد</span><span class="badge bg-green-500/30 text-green-300">200</span></div>
                                <pre><code>{
  <span class="json-key">"success"</span>: <span class="json-bool">true</span>,
  <span class="json-key">"message"</span>: <span class="json-string">"Ticket deleted successfully"</span>
}</code></pre>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- ============================================================ --}}
                {{--              PUBLIC ROUTES (no auth required)                --}}
                {{-- ============================================================ --}}
                <div class="section-divider">
                    <h2 class="text-2xl font-extrabold text-emerald-700 mb-2" data-ar="🌐 نقاط النهاية العامة" data-en="🌐 Public Endpoints">🌐 نقاط النهاية العامة</h2>
                    <p class="text-gray-500 mb-8" data-ar="لا تحتاج مصادقة — مفتوحة للجميع" data-en="No authentication required — open to everyone">لا تحتاج مصادقة</p>
                </div>

                {{-- PUBLIC: Events --}}
                <section id="public-events" class="mb-16">
                    <h3 class="text-2xl font-bold mb-4" data-ar="المعارض (عام)" data-en="Events (Public)">المعارض</h3>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/events</code></td><td class="p-3" data-ar="قائمة المعارض (مع ترقيم)" data-en="List events (paginated)">قائمة المعارض</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/events/featured</code></td><td class="p-3" data-ar="المعارض المميزة" data-en="Featured events">المعارض المميزة</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/events/{event}</code></td><td class="p-3" data-ar="تفاصيل معرض" data-en="Event details">تفاصيل معرض</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/events/{event}/spaces</code></td><td class="p-3" data-ar="مساحات المعرض" data-en="Event spaces">مساحات المعرض</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/events/{event}/sections</code></td><td class="p-3" data-ar="أقسام المعرض" data-en="Event sections">أقسام المعرض</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/events/{event}/sponsors</code></td><td class="p-3" data-ar="رعاة المعرض" data-en="Event sponsors">رعاة المعرض</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/events/{event}/sponsor-packages</code></td><td class="p-3" data-ar="باقات الرعاية" data-en="Sponsor packages">باقات الرعاية</td></tr>
                                <tr><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/spaces/{space}</code></td><td class="p-3" data-ar="تفاصيل مساحة" data-en="Space details">تفاصيل مساحة</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 flex flex-wrap items-center gap-1.5 px-1">
                        <span class="text-[10px] text-gray-400 font-bold">⚠️</span>
                        <code class="bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded text-[10px]">404 NOT_FOUND</code>
                        <code class="bg-purple-50 text-purple-600 px-1.5 py-0.5 rounded text-[10px]">429 RATE_LIMITED</code>
                    </div>
                </section>

                {{-- PUBLIC: Categories & Cities --}}
                <section id="public-categories" class="mb-16">
                    <h3 class="text-2xl font-bold mb-4" data-ar="الفئات" data-en="Categories">الفئات</h3>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/categories</code></td><td class="p-3" data-ar="قائمة الفئات" data-en="List categories">قائمة الفئات</td></tr>
                                <tr><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/categories/{category}</code></td><td class="p-3" data-ar="تفاصيل فئة" data-en="Category details">تفاصيل فئة</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 flex flex-wrap items-center gap-1.5 px-1">
                        <span class="text-[10px] text-gray-400 font-bold">⚠️</span>
                        <code class="bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded text-[10px]">404 NOT_FOUND</code>
                        <code class="bg-purple-50 text-purple-600 px-1.5 py-0.5 rounded text-[10px]">429 RATE_LIMITED</code>
                    </div>
                </section>

                <section id="public-cities" class="mb-16">
                    <h3 class="text-2xl font-bold mb-4" data-ar="المدن" data-en="Cities">المدن</h3>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/cities</code></td><td class="p-3" data-ar="قائمة المدن" data-en="List cities">قائمة المدن</td></tr>
                                <tr><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/cities/{city}</code></td><td class="p-3" data-ar="تفاصيل مدينة" data-en="City details">تفاصيل مدينة</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 flex flex-wrap items-center gap-1.5 px-1">
                        <span class="text-[10px] text-gray-400 font-bold">⚠️</span>
                        <code class="bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded text-[10px]">404 NOT_FOUND</code>
                        <code class="bg-purple-50 text-purple-600 px-1.5 py-0.5 rounded text-[10px]">429 RATE_LIMITED</code>
                    </div>
                </section>

                {{-- PUBLIC: Services, Statistics, Ratings --}}
                <section id="public-services" class="mb-16">
                    <h3 class="text-2xl font-bold mb-4" data-ar="الخدمات" data-en="Services">الخدمات</h3>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th></tr></thead>
                            <tbody>
                                <tr><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/services</code></td><td class="p-3" data-ar="قائمة الخدمات المتوفرة" data-en="List available services">قائمة الخدمات</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 flex flex-wrap items-center gap-1.5 px-1">
                        <span class="text-[10px] text-gray-400 font-bold">⚠️</span>
                        <code class="bg-purple-50 text-purple-600 px-1.5 py-0.5 rounded text-[10px]">429 RATE_LIMITED</code>
                    </div>
                </section>

                <section id="public-statistics" class="mb-16">
                    <h3 class="text-2xl font-bold mb-4" data-ar="الإحصائيات" data-en="Statistics">الإحصائيات</h3>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/statistics</code></td><td class="p-3" data-ar="إحصائيات عامة" data-en="General statistics">إحصائيات عامة</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/statistics/events</code></td><td class="p-3" data-ar="إحصائيات المعارض" data-en="Event statistics">إحصائيات المعارض</td></tr>
                                <tr><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/statistics/spaces</code></td><td class="p-3" data-ar="إحصائيات المساحات" data-en="Space statistics">إحصائيات المساحات</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 flex flex-wrap items-center gap-1.5 px-1">
                        <span class="text-[10px] text-gray-400 font-bold">⚠️</span>
                        <code class="bg-purple-50 text-purple-600 px-1.5 py-0.5 rounded text-[10px]">429 RATE_LIMITED</code>
                    </div>
                </section>

                <section id="public-ratings" class="mb-16">
                    <h3 class="text-2xl font-bold mb-4" data-ar="التقييمات" data-en="Ratings">التقييمات</h3>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/ratings</code></td><td class="p-3" data-ar="قائمة التقييمات" data-en="List ratings">قائمة التقييمات</td></tr>
                                <tr><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/ratings/summary</code></td><td class="p-3" data-ar="ملخص التقييمات" data-en="Rating summary">ملخص التقييمات</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 flex flex-wrap items-center gap-1.5 px-1">
                        <span class="text-[10px] text-gray-400 font-bold">⚠️</span>
                        <code class="bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded text-[10px]">404 NOT_FOUND</code>
                        <code class="bg-purple-50 text-purple-600 px-1.5 py-0.5 rounded text-[10px]">429 RATE_LIMITED</code>
                    </div>
                </section>

                {{-- PUBLIC: Pages, FAQs, Banners --}}
                <section id="public-content" class="mb-16">
                    <h3 class="text-2xl font-bold mb-4" data-ar="المحتوى العام" data-en="Public Content">المحتوى العام</h3>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th></tr></thead>
                            <tbody>
                                <tr class="border-b bg-gray-50/50"><td colspan="3" class="p-2 font-bold text-gray-600 text-xs uppercase tracking-wider" data-ar="الصفحات" data-en="Pages">الصفحات</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/pages</code></td><td class="p-3" data-ar="قائمة الصفحات" data-en="List pages">قائمة الصفحات</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/pages/{slug}</code></td><td class="p-3" data-ar="صفحة بالرابط" data-en="Page by slug">صفحة بالرابط</td></tr>
                                <tr class="border-b bg-gray-50/50"><td colspan="3" class="p-2 font-bold text-gray-600 text-xs uppercase tracking-wider" data-ar="الأسئلة الشائعة" data-en="FAQs">الأسئلة الشائعة</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/faqs</code></td><td class="p-3" data-ar="قائمة الأسئلة" data-en="List FAQs">قائمة الأسئلة</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/faqs/categories</code></td><td class="p-3" data-ar="فئات الأسئلة" data-en="FAQ categories">فئات الأسئلة</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/faqs/{faq}</code></td><td class="p-3" data-ar="تفاصيل سؤال" data-en="FAQ detail">تفاصيل سؤال</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/v1/faqs/{faq}/helpful</code></td><td class="p-3" data-ar="تقييم الفائدة" data-en="Mark helpful">تقييم الفائدة</td></tr>
                                <tr class="border-b bg-gray-50/50"><td colspan="3" class="p-2 font-bold text-gray-600 text-xs uppercase tracking-wider" data-ar="البانرات" data-en="Banners">البانرات</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/banners</code></td><td class="p-3" data-ar="قائمة البانرات" data-en="List banners">قائمة البانرات</td></tr>
                                <tr><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/v1/banners/{banner}/click</code></td><td class="p-3" data-ar="تسجيل نقرة" data-en="Record click">تسجيل نقرة</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 flex flex-wrap items-center gap-1.5 px-1">
                        <span class="text-[10px] text-gray-400 font-bold">⚠️</span>
                        <code class="bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded text-[10px]">404 NOT_FOUND</code>
                        <code class="bg-purple-50 text-purple-600 px-1.5 py-0.5 rounded text-[10px]">429 RATE_LIMITED</code>
                    </div>
                </section>


                {{-- ============================================================ --}}
                {{--           TRACKING (web & mobile analytics)                  --}}
                {{-- ============================================================ --}}
                <div class="section-divider">
                    <h2 class="text-2xl font-extrabold text-cyan-700 mb-2" data-ar="📊 التتبع والتحليل" data-en="📊 Tracking & Analytics">📊 التتبع والتحليل</h2>
                    <p class="text-gray-500 mb-2" data-ar="تسجيل المشاهدات والأحداث من الويب والموبايل — يعمل بدون أو مع تسجيل دخول" data-en="Record views & actions from web and mobile — works with or without authentication">تسجيل المشاهدات والأحداث — يعمل بدون أو مع تسجيل دخول</p>
                    <div class="bg-cyan-50 border border-cyan-200 rounded-xl p-4 mb-8">
                        <h4 class="font-bold text-cyan-800 mb-2" data-ar="هيدر المنصة X-Platform" data-en="X-Platform Header">هيدر المنصة X-Platform</h4>
                        <p class="text-sm text-cyan-700 mb-2" data-ar="أرسل هيدر X-Platform مع كل طلب لتحديد مصدر الطلب:" data-en="Send X-Platform header with every request to identify the source:">أرسل هيدر X-Platform مع كل طلب لتحديد مصدر الطلب:</p>
                        <div class="flex gap-3">
                            <code class="bg-cyan-100 text-cyan-800 px-3 py-1 rounded text-sm font-mono">X-Platform: web</code>
                            <code class="bg-cyan-100 text-cyan-800 px-3 py-1 rounded text-sm font-mono">X-Platform: mobile</code>
                            <code class="bg-cyan-100 text-cyan-800 px-3 py-1 rounded text-sm font-mono">X-Platform: api</code>
                        </div>
                    </div>
                </div>

                {{-- Track: Record View --}}
                <section id="track-view" class="mb-16">
                    <h3 class="text-2xl font-bold mb-4" data-ar="تسجيل مشاهدة" data-en="Record View">تسجيل مشاهدة</h3>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th><th class="p-3 text-right" data-ar="مصادقة" data-en="Auth">Auth</th></tr></thead>
                            <tbody>
                                <tr><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/v1/track/view</code></td><td class="p-3" data-ar="تسجيل مشاهدة لعنصر (معرض، مساحة، صفحة...)" data-en="Record a view for an entity (event, space, page...)">تسجيل مشاهدة لعنصر</td><td class="p-3"><span class="text-gray-400 text-xs" data-ar="اختياري" data-en="Optional">اختياري</span></td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="code-block mt-4">
                        <div class="code-header"><span data-ar="مثال: تسجيل مشاهدة معرض" data-en="Example: Track Event View">مثال: POST /v1/track/view</span></div>
                        <pre><code>{
  <span class="json-key">"resource_type"</span>: <span class="json-string">"event"</span>,       <span class="text-gray-500">// event | space | section | sponsor | page | banner | faq | service</span>
  <span class="json-key">"resource_id"</span>: <span class="json-string">"uuid-here"</span>,
  <span class="json-key">"session_id"</span>: <span class="json-string">"sess_abc123"</span>,   <span class="text-gray-500">// optional — لربط الأحداث بجلسة</span>
  <span class="json-key">"metadata"</span>: {                      <span class="text-gray-500">// optional</span>
    <span class="json-key">"source_page"</span>: <span class="json-string">"home"</span>
  }
}</code></pre>
                    </div>
                    <div class="mt-3 flex flex-wrap items-center gap-1.5 px-1">
                        <span class="text-[10px] text-gray-400 font-bold">⚠️</span>
                        <code class="bg-orange-50 text-orange-600 px-1.5 py-0.5 rounded text-[10px]">422 VALIDATION_ERROR</code>
                        <code class="bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded text-[10px]">400 INVALID_INPUT</code>
                        <code class="bg-purple-50 text-purple-600 px-1.5 py-0.5 rounded text-[10px]">429 RATE_LIMIT</code>
                    </div>
                </section>

                {{-- Track: Record Action --}}
                <section id="track-action" class="mb-16">
                    <h3 class="text-2xl font-bold mb-4" data-ar="تسجيل حدث / إجراء" data-en="Record Action">تسجيل حدث / إجراء</h3>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th><th class="p-3 text-right" data-ar="مصادقة" data-en="Auth">Auth</th></tr></thead>
                            <tbody>
                                <tr><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/v1/track/action</code></td><td class="p-3" data-ar="تسجيل إجراء (بحث، فلترة، مشاركة، نقرة...)" data-en="Record an action (search, filter, share, click...)">تسجيل إجراء</td><td class="p-3"><span class="text-gray-400 text-xs" data-ar="اختياري" data-en="Optional">اختياري</span></td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="code-block mt-4">
                        <div class="code-header"><span data-ar="مثال: تسجيل بحث" data-en="Example: Track Search Action">مثال: POST /v1/track/action</span></div>
                        <pre><code>{
  <span class="json-key">"action"</span>: <span class="json-string">"search"</span>,              <span class="text-gray-500">// view | search | click | share | filter | download | favorite | unfavorite | apply | submit | page_enter | page_exit</span>
  <span class="json-key">"resource_type"</span>: <span class="json-string">"event"</span>,       <span class="text-gray-500">// optional</span>
  <span class="json-key">"resource_id"</span>: <span class="json-string">"uuid-here"</span>,     <span class="text-gray-500">// required if resource_type sent</span>
  <span class="json-key">"session_id"</span>: <span class="json-string">"sess_abc123"</span>,
  <span class="json-key">"metadata"</span>: {
    <span class="json-key">"search_query"</span>: <span class="json-string">"معرض الرياض"</span>,
    <span class="json-key">"filter_params"</span>: { <span class="json-key">"city_id"</span>: <span class="json-string">"uuid"</span>, <span class="json-key">"category_id"</span>: <span class="json-string">"uuid"</span> },
    <span class="json-key">"source_page"</span>: <span class="json-string">"events_list"</span>
  }
}</code></pre>
                    </div>
                    <div class="mt-3 flex flex-wrap items-center gap-1.5 px-1">
                        <span class="text-[10px] text-gray-400 font-bold">⚠️</span>
                        <code class="bg-orange-50 text-orange-600 px-1.5 py-0.5 rounded text-[10px]">422 VALIDATION_ERROR</code>
                        <code class="bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded text-[10px]">400 INVALID_INPUT</code>
                        <code class="bg-purple-50 text-purple-600 px-1.5 py-0.5 rounded text-[10px]">429 RATE_LIMIT</code>
                    </div>
                </section>


                {{-- ============================================================ --}}
                {{--           SELF-SERVICE (authenticated, any user)             --}}
                {{-- ============================================================ --}}
                <div class="section-divider">
                    <h2 class="text-2xl font-extrabold text-blue-700 mb-2" data-ar="👤 خدمة ذاتية" data-en="👤 Self-Service">👤 خدمة ذاتية</h2>
                    <p class="text-gray-500 mb-8" data-ar="تحتاج تسجيل دخول — بيانات المستخدم الحالي فقط" data-en="Requires authentication — current user's own data only">تحتاج تسجيل دخول — بيانات المستخدم فقط</p>
                </div>

                {{-- Self: Business Profile --}}
                <section id="self-profile" class="mb-16">
                    <h3 class="text-2xl font-bold mb-4" data-ar="الملف التجاري" data-en="Business Profile">الملف التجاري</h3>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/profile</code></td><td class="p-3" data-ar="عرض ملفي التجاري" data-en="Show my business profile">عرض ملفي التجاري</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/v1/profile</code></td><td class="p-3" data-ar="إنشاء ملف تجاري" data-en="Create business profile">إنشاء ملف تجاري</td></tr>
                                <tr><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/profile</code></td><td class="p-3" data-ar="تحديث ملفي التجاري" data-en="Update my business profile">تحديث ملفي التجاري</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 flex flex-wrap items-center gap-1.5 px-1">
                        <span class="text-[10px] text-gray-400 font-bold">⚠️</span>
                        <code class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded text-[10px]">401 UNAUTHENTICATED</code>
                        <code class="bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded text-[10px]">404 NOT_FOUND</code>
                        <code class="bg-orange-50 text-orange-600 px-1.5 py-0.5 rounded text-[10px]">422 VALIDATION_ERROR</code>
                        <code class="bg-orange-50 text-orange-600 px-1.5 py-0.5 rounded text-[10px]">422 DUPLICATE_ENTRY</code>
                    </div>
                </section>

                {{-- Self: Favorites --}}
                <section id="self-favorites" class="mb-16">
                    <h3 class="text-2xl font-bold mb-4" data-ar="المفضلة" data-en="Favorites">المفضلة</h3>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/favorites</code></td><td class="p-3" data-ar="قائمة المفضلة" data-en="List favorites">قائمة المفضلة</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/v1/favorites</code></td><td class="p-3" data-ar="إضافة للمفضلة" data-en="Add to favorites">إضافة للمفضلة</td></tr>
                                <tr><td class="p-3"><span class="badge method-delete text-white">DEL</span></td><td class="p-3"><code>/v1/favorites/{favorite}</code></td><td class="p-3" data-ar="إزالة من المفضلة" data-en="Remove from favorites">إزالة من المفضلة</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 flex flex-wrap items-center gap-1.5 px-1">
                        <span class="text-[10px] text-gray-400 font-bold">⚠️</span>
                        <code class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded text-[10px]">401 UNAUTHENTICATED</code>
                        <code class="bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded text-[10px]">404 NOT_FOUND</code>
                        <code class="bg-orange-50 text-orange-600 px-1.5 py-0.5 rounded text-[10px]">422 DUPLICATE_ENTRY</code>
                    </div>
                </section>

                {{-- Self: Notifications --}}
                <section id="self-notifications" class="mb-16">
                    <h3 class="text-2xl font-bold mb-4" data-ar="الإشعارات" data-en="Notifications">الإشعارات</h3>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th><th class="p-3 text-right">Permission</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/notifications</code></td><td class="p-3" data-ar="قائمة الإشعارات" data-en="List notifications">قائمة الإشعارات</td><td class="p-3"><span class="perm-badge">notifications.view</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/notifications/unread-count</code></td><td class="p-3" data-ar="عدد غير المقروءة" data-en="Unread count">عدد غير المقروءة</td><td class="p-3"><span class="perm-badge">notifications.view</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/notifications/{id}/read</code></td><td class="p-3" data-ar="تعليم كمقروء" data-en="Mark as read">تعليم كمقروء</td><td class="p-3"><span class="perm-badge">notifications.update</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/notifications/read-all</code></td><td class="p-3" data-ar="تعليم الكل كمقروء" data-en="Mark all as read">تعليم الكل كمقروء</td><td class="p-3"><span class="perm-badge">notifications.update</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/notifications/preferences</code></td><td class="p-3" data-ar="تفضيلات الإشعارات" data-en="Notification prefs">تفضيلات الإشعارات</td><td class="p-3"><span class="perm-badge">notification-preferences.view</span></td></tr>
                                <tr><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/notifications/preferences</code></td><td class="p-3" data-ar="تحديث التفضيلات" data-en="Update prefs">تحديث التفضيلات</td><td class="p-3"><span class="perm-badge">notification-preferences.update</span></td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 flex flex-wrap items-center gap-1.5 px-1">
                        <span class="text-[10px] text-gray-400 font-bold">⚠️</span>
                        <code class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded text-[10px]">401 UNAUTHENTICATED</code>
                        <code class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded text-[10px]">403 PERMISSION_DENIED</code>
                        <code class="bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded text-[10px]">404 NOT_FOUND</code>
                        <code class="bg-orange-50 text-orange-600 px-1.5 py-0.5 rounded text-[10px]">422 VALIDATION_ERROR</code>
                    </div>
                </section>

                {{-- Self: Ratings --}}
                <section id="self-ratings" class="mb-16">
                    <h3 class="text-2xl font-bold mb-4" data-ar="تقييماتي" data-en="My Ratings">تقييماتي</h3>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th><th class="p-3 text-right">Permission</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/v1/ratings</code></td><td class="p-3" data-ar="إضافة تقييم" data-en="Create rating">إضافة تقييم</td><td class="p-3"><span class="perm-badge">ratings.create</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/ratings/{rating}</code></td><td class="p-3" data-ar="تعديل تقييمي" data-en="Update my rating">تعديل تقييمي</td><td class="p-3"><span class="perm-badge">ratings.update</span></td></tr>
                                <tr><td class="p-3"><span class="badge method-delete text-white">DEL</span></td><td class="p-3"><code>/v1/ratings/{rating}</code></td><td class="p-3" data-ar="حذف تقييمي" data-en="Delete my rating">حذف تقييمي</td><td class="p-3"><span class="perm-badge">ratings.delete</span></td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 flex flex-wrap items-center gap-1.5 px-1">
                        <span class="text-[10px] text-gray-400 font-bold">⚠️</span>
                        <code class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded text-[10px]">401 UNAUTHENTICATED</code>
                        <code class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded text-[10px]">403 PERMISSION_DENIED</code>
                        <code class="bg-orange-50 text-orange-600 px-1.5 py-0.5 rounded text-[10px]">422 VALIDATION_ERROR</code>
                        <code class="bg-orange-50 text-orange-600 px-1.5 py-0.5 rounded text-[10px]">422 DUPLICATE_ENTRY</code>
                    </div>
                </section>

                {{-- Self: Support Tickets --}}
                <section id="self-tickets" class="mb-16">
                    <h3 class="text-2xl font-bold mb-4" data-ar="تذاكر الدعم" data-en="Support Tickets">تذاكر الدعم</h3>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th><th class="p-3 text-right">Permission</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/support-tickets</code></td><td class="p-3" data-ar="قائمة تذاكري" data-en="List my tickets">قائمة تذاكري</td><td class="p-3"><span class="perm-badge">support-tickets.view</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/v1/support-tickets</code></td><td class="p-3" data-ar="إنشاء تذكرة" data-en="Create ticket">إنشاء تذكرة</td><td class="p-3"><span class="perm-badge">support-tickets.create</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/support-tickets/{id}</code></td><td class="p-3" data-ar="تفاصيل تذكرة" data-en="Ticket details">تفاصيل تذكرة</td><td class="p-3"><span class="perm-badge">support-tickets.view</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/v1/support-tickets/{id}/reply</code></td><td class="p-3" data-ar="رد على تذكرة" data-en="Reply to ticket">رد على تذكرة</td><td class="p-3"><span class="perm-badge">support-tickets.reply</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/support-tickets/{id}/close</code></td><td class="p-3" data-ar="إغلاق تذكرتي" data-en="Close my ticket">إغلاق تذكرتي</td><td class="p-3"><span class="perm-badge">support-tickets.close</span></td></tr>
                                <tr><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/support-tickets/{id}/reopen</code></td><td class="p-3" data-ar="إعادة فتح تذكرة" data-en="Reopen ticket">إعادة فتح تذكرة</td><td class="p-3"><span class="perm-badge">support-tickets.create</span></td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 flex flex-wrap items-center gap-1.5 px-1">
                        <span class="text-[10px] text-gray-400 font-bold">⚠️</span>
                        <code class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded text-[10px]">401 UNAUTHENTICATED</code>
                        <code class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded text-[10px]">403 PERMISSION_DENIED</code>
                        <code class="bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded text-[10px]">404 NOT_FOUND</code>
                        <code class="bg-orange-50 text-orange-600 px-1.5 py-0.5 rounded text-[10px]">422 VALIDATION_ERROR</code>
                        <code class="bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded text-[10px]">422 INVALID_STATUS</code>
                    </div>
                </section>

                {{-- Self: Invoices --}}
                <section id="self-invoices" class="mb-16">
                    <h3 class="text-2xl font-bold mb-4" data-ar="فواتيري" data-en="My Invoices">فواتيري</h3>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th><th class="p-3 text-right">Permission</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/invoices</code></td><td class="p-3" data-ar="قائمة فواتيري" data-en="List my invoices">قائمة فواتيري</td><td class="p-3"><span class="perm-badge">invoices.view</span></td></tr>
                                <tr><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/invoices/{invoice}</code></td><td class="p-3" data-ar="تفاصيل فاتورة" data-en="Invoice details">تفاصيل فاتورة</td><td class="p-3"><span class="perm-badge">invoices.view</span></td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 flex flex-wrap items-center gap-1.5 px-1">
                        <span class="text-[10px] text-gray-400 font-bold">⚠️</span>
                        <code class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded text-[10px]">401 UNAUTHENTICATED</code>
                        <code class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded text-[10px]">403 PERMISSION_DENIED</code>
                        <code class="bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded text-[10px]">404 NOT_FOUND</code>
                    </div>
                </section>

                {{-- Self: Visit Requests --}}
                <section id="self-visits" class="mb-16">
                    <h3 class="text-2xl font-bold mb-4" data-ar="طلبات الزيارة" data-en="Visit Requests">طلبات الزيارة</h3>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th><th class="p-3 text-right">Permission</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/visit-requests</code></td><td class="p-3" data-ar="قائمة طلباتي" data-en="List my requests">قائمة طلباتي</td><td class="p-3"><span class="perm-badge">visit-requests.view</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/v1/visit-requests</code></td><td class="p-3" data-ar="إنشاء طلب زيارة" data-en="Create visit request">إنشاء طلب زيارة</td><td class="p-3"><span class="perm-badge">visit-requests.create</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/visit-requests/{id}</code></td><td class="p-3" data-ar="تفاصيل طلب" data-en="Request details">تفاصيل طلب</td><td class="p-3"><span class="perm-badge">visit-requests.view</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/visit-requests/{id}</code></td><td class="p-3" data-ar="تعديل طلب" data-en="Update request">تعديل طلب</td><td class="p-3"><span class="perm-badge">visit-requests.update</span></td></tr>
                                <tr><td class="p-3"><span class="badge method-delete text-white">DEL</span></td><td class="p-3"><code>/v1/visit-requests/{id}</code></td><td class="p-3" data-ar="حذف طلب" data-en="Delete request">حذف طلب</td><td class="p-3"><span class="perm-badge">visit-requests.delete</span></td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 flex flex-wrap items-center gap-1.5 px-1">
                        <span class="text-[10px] text-gray-400 font-bold">⚠️</span>
                        <code class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded text-[10px]">401 UNAUTHENTICATED</code>
                        <code class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded text-[10px]">403 PERMISSION_DENIED</code>
                        <code class="bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded text-[10px]">404 NOT_FOUND</code>
                        <code class="bg-orange-50 text-orange-600 px-1.5 py-0.5 rounded text-[10px]">422 VALIDATION_ERROR</code>
                    </div>
                </section>

                {{-- Self: Rental Requests --}}
                <section id="self-rentals" class="mb-16">
                    <h3 class="text-2xl font-bold mb-4" data-ar="طلبات الإيجار" data-en="Rental Requests">طلبات الإيجار</h3>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-sm mb-4">
                        <span class="font-semibold text-yellow-800" data-ar="تنبيه:" data-en="Note:">تنبيه:</span>
                        <span class="text-yellow-700" data-ar="يتطلب ملف تجاري موثق (CheckVerifiedProfile)" data-en="Requires verified business profile (CheckVerifiedProfile)">يتطلب ملف تجاري موثق</span>
                    </div>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th><th class="p-3 text-right">Permission</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/rental-requests</code></td><td class="p-3" data-ar="قائمة طلباتي" data-en="List my requests">قائمة طلباتي</td><td class="p-3"><span class="perm-badge">rental-requests.view</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/v1/rental-requests</code></td><td class="p-3" data-ar="إنشاء طلب إيجار" data-en="Create rental request">إنشاء طلب إيجار</td><td class="p-3"><span class="perm-badge">rental-requests.create</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/rental-requests/{id}</code></td><td class="p-3" data-ar="تفاصيل طلب" data-en="Request details">تفاصيل طلب</td><td class="p-3"><span class="perm-badge">rental-requests.view</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/rental-requests/{id}</code></td><td class="p-3" data-ar="تعديل طلب" data-en="Update request">تعديل طلب</td><td class="p-3"><span class="perm-badge">rental-requests.update</span></td></tr>
                                <tr><td class="p-3"><span class="badge method-delete text-white">DEL</span></td><td class="p-3"><code>/v1/rental-requests/{id}</code></td><td class="p-3" data-ar="حذف طلب" data-en="Delete request">حذف طلب</td><td class="p-3"><span class="perm-badge">rental-requests.delete</span></td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 flex flex-wrap items-center gap-1.5 px-1">
                        <span class="text-[10px] text-gray-400 font-bold">⚠️</span>
                        <code class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded text-[10px]">401 UNAUTHENTICATED</code>
                        <code class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded text-[10px]">403 PROFILE_NOT_VERIFIED</code>
                        <code class="bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded text-[10px]">404 NOT_FOUND</code>
                        <code class="bg-orange-50 text-orange-600 px-1.5 py-0.5 rounded text-[10px]">422 VALIDATION_ERROR</code>
                        <code class="bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded text-[10px]">422 SPACE_UNAVAILABLE</code>
                    </div>
                </section>


                {{-- ============================================================ --}}
                {{--              OWNER-SCOPED /my/ ROUTES                        --}}
                {{-- ============================================================ --}}
                <div class="section-divider">
                    <h2 class="text-2xl font-extrabold text-emerald-700 mb-2" data-ar="🏠 بياناتي — <code>/v1/my/</code>" data-en="🏠 Owner-Scoped — <code>/v1/my/</code>">🏠 بياناتي — /v1/my/</h2>
                    <p class="text-gray-500 mb-8" data-ar="بيانات مربوطة بحسابك: مساحاتك، طلبات واردة لمساحاتك، عقودك، إلخ" data-en="Data scoped to your account: your spaces, received requests, contracts, etc.">بيانات مربوطة بحسابك</p>
                </div>

                {{-- My: Dashboard --}}
                <section id="my-dashboard" class="mb-16">
                    <h3 class="text-2xl font-bold mb-4" data-ar="لوحة التحكم الموحدة" data-en="Unified Dashboard">لوحة التحكم الموحدة</h3>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th></tr></thead>
                            <tbody>
                                <tr><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/my/dashboard</code></td><td class="p-3" data-ar="لوحة تحكم موحدة حسب أدوار المستخدم" data-en="Unified dashboard based on user roles">لوحة تحكم موحدة</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 flex flex-wrap items-center gap-1.5 px-1">
                        <span class="text-[10px] text-gray-400 font-bold">⚠️</span>
                        <code class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded text-[10px]">401 UNAUTHENTICATED</code>
                    </div>
                </section>

                {{-- My: Spaces --}}
                <section id="my-spaces" class="mb-16">
                    <h3 class="text-2xl font-bold mb-4" data-ar="مساحاتي" data-en="My Spaces">مساحاتي</h3>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th><th class="p-3 text-right">Permission</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/my/spaces</code></td><td class="p-3" data-ar="قائمة مساحاتي" data-en="List my spaces">قائمة مساحاتي</td><td class="p-3"><span class="perm-badge">spaces.view</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/v1/my/spaces</code></td><td class="p-3" data-ar="إنشاء مساحة" data-en="Create space">إنشاء مساحة</td><td class="p-3"><span class="perm-badge">spaces.create</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/my/spaces/{space}</code></td><td class="p-3" data-ar="تفاصيل مساحة" data-en="Space details">تفاصيل مساحة</td><td class="p-3"><span class="perm-badge">spaces.view</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/my/spaces/{space}</code></td><td class="p-3" data-ar="تحديث مساحة" data-en="Update space">تحديث مساحة</td><td class="p-3"><span class="perm-badge">spaces.update</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-delete text-white">DEL</span></td><td class="p-3"><code>/v1/my/spaces/{space}</code></td><td class="p-3" data-ar="حذف مساحة" data-en="Delete space">حذف مساحة</td><td class="p-3"><span class="perm-badge">spaces.delete</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/v1/my/spaces/{space}/services</code></td><td class="p-3" data-ar="إضافة خدمات للمساحة" data-en="Add services to space">إضافة خدمات</td><td class="p-3"><span class="perm-badge">spaces.update</span></td></tr>
                                <tr><td class="p-3"><span class="badge method-delete text-white">DEL</span></td><td class="p-3"><code>/v1/my/spaces/{space}/services</code></td><td class="p-3" data-ar="إزالة خدمات" data-en="Remove services">إزالة خدمات</td><td class="p-3"><span class="perm-badge">spaces.update</span></td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 flex flex-wrap items-center gap-1.5 px-1">
                        <span class="text-[10px] text-gray-400 font-bold">⚠️</span>
                        <code class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded text-[10px]">401 UNAUTHENTICATED</code>
                        <code class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded text-[10px]">403 PERMISSION_DENIED</code>
                        <code class="bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded text-[10px]">404 NOT_FOUND</code>
                        <code class="bg-orange-50 text-orange-600 px-1.5 py-0.5 rounded text-[10px]">422 VALIDATION_ERROR</code>
                    </div>
                </section>

                {{-- My: Received Visit Requests --}}
                <section id="my-received-visits" class="mb-16">
                    <h3 class="text-2xl font-bold mb-4" data-ar="زيارات واردة لمساحاتي" data-en="Received Visit Requests">زيارات واردة</h3>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th><th class="p-3 text-right">Permission</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/my/received-visit-requests</code></td><td class="p-3" data-ar="قائمة الزيارات الواردة" data-en="List received requests">القائمة</td><td class="p-3"><span class="perm-badge">visit-requests.approve</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/my/received-visit-requests/pending-count</code></td><td class="p-3" data-ar="عدد المعلقة" data-en="Pending count">عدد المعلقة</td><td class="p-3"><span class="perm-badge">visit-requests.approve</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/my/received-visit-requests/{id}</code></td><td class="p-3" data-ar="التفاصيل" data-en="Details">التفاصيل</td><td class="p-3"><span class="perm-badge">visit-requests.approve</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/my/received-visit-requests/{id}/approve</code></td><td class="p-3" data-ar="قبول" data-en="Approve">قبول</td><td class="p-3"><span class="perm-badge">visit-requests.approve</span></td></tr>
                                <tr><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/my/received-visit-requests/{id}/reject</code></td><td class="p-3" data-ar="رفض" data-en="Reject">رفض</td><td class="p-3"><span class="perm-badge">visit-requests.reject</span></td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 flex flex-wrap items-center gap-1.5 px-1">
                        <span class="text-[10px] text-gray-400 font-bold">⚠️</span>
                        <code class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded text-[10px]">401 UNAUTHENTICATED</code>
                        <code class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded text-[10px]">403 PERMISSION_DENIED</code>
                        <code class="bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded text-[10px]">404 NOT_FOUND</code>
                        <code class="bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded text-[10px]">422 INVALID_STATUS</code>
                    </div>
                </section>

                {{-- My: Received Rental Requests --}}
                <section id="my-received-rentals" class="mb-16">
                    <h3 class="text-2xl font-bold mb-4" data-ar="إيجارات واردة لمساحاتي" data-en="Received Rental Requests">إيجارات واردة</h3>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th><th class="p-3 text-right">Permission</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/my/received-rental-requests</code></td><td class="p-3" data-ar="القائمة" data-en="List">القائمة</td><td class="p-3"><span class="perm-badge">rental-requests.approve</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/my/received-rental-requests/pending-count</code></td><td class="p-3" data-ar="عدد المعلقة" data-en="Pending count">عدد المعلقة</td><td class="p-3"><span class="perm-badge">rental-requests.approve</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/my/received-rental-requests/{id}</code></td><td class="p-3" data-ar="التفاصيل" data-en="Details">التفاصيل</td><td class="p-3"><span class="perm-badge">rental-requests.approve</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/my/received-rental-requests/{id}/approve</code></td><td class="p-3" data-ar="قبول" data-en="Approve">قبول</td><td class="p-3"><span class="perm-badge">rental-requests.approve</span></td></tr>
                                <tr><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/my/received-rental-requests/{id}/reject</code></td><td class="p-3" data-ar="رفض" data-en="Reject">رفض</td><td class="p-3"><span class="perm-badge">rental-requests.reject</span></td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 flex flex-wrap items-center gap-1.5 px-1">
                        <span class="text-[10px] text-gray-400 font-bold">⚠️</span>
                        <code class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded text-[10px]">401 UNAUTHENTICATED</code>
                        <code class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded text-[10px]">403 PERMISSION_DENIED</code>
                        <code class="bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded text-[10px]">404 NOT_FOUND</code>
                        <code class="bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded text-[10px]">422 INVALID_STATUS</code>
                    </div>
                </section>

                {{-- My: Payments --}}
                <section id="my-payments" class="mb-16">
                    <h3 class="text-2xl font-bold mb-4" data-ar="مدفوعاتي" data-en="My Payments">مدفوعاتي</h3>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th><th class="p-3 text-right">Permission</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/my/payments</code></td><td class="p-3" data-ar="قائمة المدفوعات" data-en="List payments">قائمة المدفوعات</td><td class="p-3"><span class="perm-badge">payments.view</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/my/payments/summary</code></td><td class="p-3" data-ar="ملخص المدفوعات" data-en="Payment summary">ملخص المدفوعات</td><td class="p-3"><span class="perm-badge">payments.view</span></td></tr>
                                <tr><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/my/payments/{rentalRequest}</code></td><td class="p-3" data-ar="تفاصيل دفعة" data-en="Payment details">تفاصيل دفعة</td><td class="p-3"><span class="perm-badge">payments.view</span></td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 flex flex-wrap items-center gap-1.5 px-1">
                        <span class="text-[10px] text-gray-400 font-bold">⚠️</span>
                        <code class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded text-[10px]">401 UNAUTHENTICATED</code>
                        <code class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded text-[10px]">403 PERMISSION_DENIED</code>
                        <code class="bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded text-[10px]">404 NOT_FOUND</code>
                    </div>
                </section>

                {{-- My: Rental Contracts --}}
                <section id="my-rental-contracts" class="mb-16">
                    <h3 class="text-2xl font-bold mb-4" data-ar="عقود الإيجار" data-en="My Rental Contracts">عقود الإيجار</h3>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th><th class="p-3 text-right">Permission</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/my/rental-contracts</code></td><td class="p-3" data-ar="قائمة عقودي" data-en="List my contracts">قائمة عقودي</td><td class="p-3"><span class="perm-badge">rental-contracts.view</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/my/rental-contracts/{id}</code></td><td class="p-3" data-ar="تفاصيل عقد" data-en="Contract details">تفاصيل عقد</td><td class="p-3"><span class="perm-badge">rental-contracts.view</span></td></tr>
                                <tr><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/my/rental-contracts/{id}/sign</code></td><td class="p-3" data-ar="توقيع العقد" data-en="Sign contract">توقيع العقد</td><td class="p-3"><span class="perm-badge">rental-contracts.sign</span></td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 flex flex-wrap items-center gap-1.5 px-1">
                        <span class="text-[10px] text-gray-400 font-bold">⚠️</span>
                        <code class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded text-[10px]">401 UNAUTHENTICATED</code>
                        <code class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded text-[10px]">403 PERMISSION_DENIED</code>
                        <code class="bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded text-[10px]">404 NOT_FOUND</code>
                        <code class="bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded text-[10px]">422 CONTRACT_ALREADY_SIGNED</code>
                    </div>
                </section>

                {{-- My: Sponsor data --}}
                <section id="my-sponsor" class="mb-16">
                    <h3 class="text-2xl font-bold mb-4" data-ar="بيانات الرعاية" data-en="Sponsor Data">بيانات الرعاية</h3>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th><th class="p-3 text-right">Permission</th></tr></thead>
                            <tbody>
                                <tr class="border-b bg-gray-50/50"><td colspan="4" class="p-2 font-bold text-gray-600 text-xs uppercase tracking-wider" data-ar="عقود الرعاية" data-en="Sponsor Contracts">عقود الرعاية</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/my/sponsor-contracts</code></td><td class="p-3" data-ar="قائمة عقود الرعاية" data-en="List contracts">القائمة</td><td class="p-3"><span class="perm-badge">sponsor-contracts.view</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/my/sponsor-contracts/{id}</code></td><td class="p-3" data-ar="تفاصيل عقد" data-en="Details">التفاصيل</td><td class="p-3"><span class="perm-badge">sponsor-contracts.view</span></td></tr>
                                <tr class="border-b bg-gray-50/50"><td colspan="4" class="p-2 font-bold text-gray-600 text-xs uppercase tracking-wider" data-ar="مدفوعات الرعاية" data-en="Sponsor Payments">مدفوعات الرعاية</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/my/sponsor-payments</code></td><td class="p-3" data-ar="القائمة" data-en="List">القائمة</td><td class="p-3"><span class="perm-badge">sponsor-payments.view</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/my/sponsor-payments/{id}</code></td><td class="p-3" data-ar="التفاصيل" data-en="Details">التفاصيل</td><td class="p-3"><span class="perm-badge">sponsor-payments.view</span></td></tr>
                                <tr class="border-b bg-gray-50/50"><td colspan="4" class="p-2 font-bold text-gray-600 text-xs uppercase tracking-wider" data-ar="أصول الرعاية" data-en="Sponsor Assets">أصول الرعاية (CRUD)</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/my/sponsor-assets</code></td><td class="p-3" data-ar="القائمة" data-en="List">القائمة</td><td class="p-3"><span class="perm-badge">sponsor-assets.view</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/v1/my/sponsor-assets</code></td><td class="p-3" data-ar="إنشاء" data-en="Create">إنشاء</td><td class="p-3"><span class="perm-badge">sponsor-assets.create</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/my/sponsor-assets/{id}</code></td><td class="p-3" data-ar="التفاصيل" data-en="Details">التفاصيل</td><td class="p-3"><span class="perm-badge">sponsor-assets.view</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/my/sponsor-assets/{id}</code></td><td class="p-3" data-ar="تحديث" data-en="Update">تحديث</td><td class="p-3"><span class="perm-badge">sponsor-assets.update</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-delete text-white">DEL</span></td><td class="p-3"><code>/v1/my/sponsor-assets/{id}</code></td><td class="p-3" data-ar="حذف" data-en="Delete">حذف</td><td class="p-3"><span class="perm-badge">sponsor-assets.delete</span></td></tr>
                                <tr class="border-b bg-gray-50/50"><td colspan="4" class="p-2 font-bold text-gray-600 text-xs uppercase tracking-wider" data-ar="تعرض الرعاية" data-en="Sponsor Exposure (ROI)">تعرض الرعاية (ROI)</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/my/sponsor-exposure</code></td><td class="p-3" data-ar="بيانات التعرض" data-en="Exposure data">بيانات التعرض</td><td class="p-3"><span class="perm-badge">sponsor-exposure.view</span></td></tr>
                                <tr><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/my/sponsor-exposure/summary</code></td><td class="p-3" data-ar="ملخص التعرض" data-en="Exposure summary">ملخص التعرض</td><td class="p-3"><span class="perm-badge">sponsor-exposure.view</span></td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 flex flex-wrap items-center gap-1.5 px-1">
                        <span class="text-[10px] text-gray-400 font-bold">⚠️</span>
                        <code class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded text-[10px]">401 UNAUTHENTICATED</code>
                        <code class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded text-[10px]">403 PERMISSION_DENIED</code>
                        <code class="bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded text-[10px]">404 NOT_FOUND</code>
                        <code class="bg-orange-50 text-orange-600 px-1.5 py-0.5 rounded text-[10px]">422 VALIDATION_ERROR</code>
                    </div>
                </section>

                {{-- My: Activity History --}}
                <section id="my-activity" class="mb-16">
                    <h3 class="text-2xl font-bold mb-4" data-ar="نشاطاتي" data-en="My Activity">نشاطاتي</h3>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th><th class="p-3 text-right">Permission</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/my/activity</code></td><td class="p-3" data-ar="سجل نشاطاتي (مع فلترة)" data-en="My activity log (filterable)">سجل نشاطاتي</td><td class="p-3"><span class="text-gray-400 text-xs">Auth</span></td></tr>
                                <tr><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/my/activity/summary</code></td><td class="p-3" data-ar="ملخص النشاط (إحصائيات)" data-en="Activity summary (stats)">ملخص النشاط</td><td class="p-3"><span class="text-gray-400 text-xs">Auth</span></td></tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Activity Query Parameters --}}
                    <div class="mt-4 bg-gray-50 border border-gray-200 rounded-xl p-4">
                        <p class="text-sm font-bold text-gray-600 mb-2" data-ar="فلاتر الاستعلام — <code>GET /v1/my/activity</code>" data-en="Query Filters — GET /v1/my/activity">فلاتر الاستعلام — <code>GET /v1/my/activity</code></p>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-xs">
                            <code class="bg-white border rounded px-2 py-1">?action=view</code>
                            <code class="bg-white border rounded px-2 py-1">?platform=web</code>
                            <code class="bg-white border rounded px-2 py-1">?resource_type=event</code>
                            <code class="bg-white border rounded px-2 py-1">?from=2024-01-01</code>
                            <code class="bg-white border rounded px-2 py-1">?to=2024-12-31</code>
                            <code class="bg-white border rounded px-2 py-1">?per_page=20</code>
                        </div>
                    </div>

                    {{-- Summary Query --}}
                    <div class="mt-3 bg-gray-50 border border-gray-200 rounded-xl p-4">
                        <p class="text-sm font-bold text-gray-600 mb-2" data-ar="فلتر الفترة — <code>GET /v1/my/activity/summary</code>" data-en="Period Filter — GET /v1/my/activity/summary">فلتر الفترة — <code>GET /v1/my/activity/summary</code></p>
                        <div class="flex gap-2 text-xs">
                            <code class="bg-white border rounded px-2 py-1">?period=7d</code>
                            <code class="bg-white border rounded px-2 py-1">?period=30d</code>
                            <code class="bg-white border rounded px-2 py-1">?period=90d</code>
                            <code class="bg-white border rounded px-2 py-1">?period=all</code>
                        </div>
                    </div>

                    {{-- Summary Response Sample --}}
                    <div class="mt-4 code-block">
                        <div class="code-header"><span data-ar="مثال استجابة — الملخص" data-en="Response — Summary">Response — Summary</span></div>
                        <pre><code>{
  <span class="json-key">"success"</span>: <span class="json-bool">true</span>,
  <span class="json-key">"data"</span>: {
    <span class="json-key">"period"</span>: <span class="json-string">"30d"</span>,
    <span class="json-key">"total_activities"</span>: <span class="json-number">142</span>,
    <span class="json-key">"actions"</span>: { <span class="json-key">"view"</span>: <span class="json-number">85</span>, <span class="json-key">"search"</span>: <span class="json-number">32</span>, <span class="json-key">"click"</span>: <span class="json-number">25</span> },
    <span class="json-key">"platforms"</span>: { <span class="json-key">"web"</span>: <span class="json-number">90</span>, <span class="json-key">"mobile"</span>: <span class="json-number">52</span> },
    <span class="json-key">"top_viewed"</span>: [{ <span class="json-key">"resource_type"</span>: <span class="json-string">"event"</span>, <span class="json-key">"count"</span>: <span class="json-number">15</span> }],
    <span class="json-key">"recent_searches"</span>: [<span class="json-string">"معرض الرياض"</span>, <span class="json-string">"أثاث"</span>]
  }
}</code></pre>
                    </div>
                    <div class="mt-3 flex flex-wrap items-center gap-1.5 px-1">
                        <span class="text-[10px] text-gray-400 font-bold">⚠️</span>
                        <code class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded text-[10px]">401 UNAUTHENTICATED</code>
                    </div>
                </section>

                {{-- ============================================================ --}}
                {{--              MANAGEMENT /manage/ ROUTES                      --}}
                {{-- ============================================================ --}}
                <div class="section-divider">
                    <h2 class="text-2xl font-extrabold text-purple-700 mb-2" data-ar="⚙️ الإدارة — <code>/v1/manage/</code>" data-en="⚙️ Management — <code>/v1/manage/</code>">⚙️ الإدارة — /v1/manage/</h2>
                    <p class="text-gray-500 mb-4" data-ar="عمليات إدارية — التحكم عبر الصلاحيات لا الأدوار" data-en="Management operations — controlled by permissions, not roles">عمليات إدارية — التحكم عبر الصلاحيات</p>
                    <div class="bg-purple-50 border border-purple-200 rounded-xl p-4 mb-8 text-sm">
                        <p class="text-purple-700" data-ar="admin → صلاحيات CRUD كاملة | supervisor → عرض + موافقة | super-admin → يتخطى كل الفحوصات | أي دور مخصص → أعطه الصلاحيات المناسبة!" data-en="admin → full CRUD | supervisor → view + approve | super-admin → bypasses all checks | custom roles → assign appropriate permissions!">admin → CRUD كاملة | supervisor → عرض + موافقة | super-admin → يتخطى الكل</p>
                    </div>
                </div>

                {{-- Manage: Dashboard & Statistics --}}
                <section id="manage-dashboard" class="mb-16">
                    <h3 class="text-2xl font-bold mb-4" data-ar="لوحة التحكم والإحصائيات" data-en="Dashboard & Statistics">لوحة التحكم</h3>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th><th class="p-3 text-right">Permission</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/manage/dashboard</code></td><td class="p-3" data-ar="لوحة تحكم إدارية" data-en="Admin dashboard">لوحة تحكم إدارية</td><td class="p-3"><span class="perm-badge">reports.view</span></td></tr>
                                <tr><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/manage/statistics</code></td><td class="p-3" data-ar="إحصائيات مفصلة" data-en="Detailed statistics">إحصائيات مفصلة</td><td class="p-3"><span class="perm-badge">reports.view</span></td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 flex flex-wrap items-center gap-1.5 px-1">
                        <span class="text-[10px] text-gray-400 font-bold">⚠️</span>
                        <code class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded text-[10px]">401 UNAUTHENTICATED</code>
                        <code class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded text-[10px]">403 PERMISSION_DENIED</code>
                    </div>
                </section>

                {{-- Manage: Events --}}
                <section id="manage-events" class="mb-16">
                    <h3 class="text-2xl font-bold mb-4" data-ar="المعارض" data-en="Events Management">المعارض</h3>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th><th class="p-3 text-right">Permission</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/manage/events</code></td><td class="p-3" data-ar="قائمة المعارض" data-en="List events">قائمة المعارض</td><td class="p-3"><span class="perm-badge">events.view</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/v1/manage/events</code></td><td class="p-3" data-ar="إنشاء معرض" data-en="Create event">إنشاء معرض</td><td class="p-3"><span class="perm-badge">events.create</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/manage/events/{event}</code></td><td class="p-3" data-ar="تفاصيل معرض" data-en="Event details">تفاصيل معرض</td><td class="p-3"><span class="perm-badge">events.view</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/manage/events/{event}</code></td><td class="p-3" data-ar="تحديث معرض" data-en="Update event">تحديث معرض</td><td class="p-3"><span class="perm-badge">events.update</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-delete text-white">DEL</span></td><td class="p-3"><code>/v1/manage/events/{event}</code></td><td class="p-3" data-ar="حذف معرض" data-en="Delete event">حذف معرض</td><td class="p-3"><span class="perm-badge">events.delete</span></td></tr>
                                <tr class="border-b bg-gray-50/50"><td colspan="4" class="p-2 font-bold text-gray-600 text-xs uppercase tracking-wider" data-ar="موارد فرعية للمعرض" data-en="Nested under event">موارد فرعية</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/manage/events/{event}/sections</code></td><td class="p-3" data-ar="أقسام المعرض" data-en="Event sections">أقسام المعرض</td><td class="p-3"><span class="perm-badge">sections.view</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/v1/manage/events/{event}/sections</code></td><td class="p-3" data-ar="إنشاء قسم" data-en="Create section">إنشاء قسم</td><td class="p-3"><span class="perm-badge">sections.create</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/manage/events/{event}/spaces</code></td><td class="p-3" data-ar="مساحات المعرض" data-en="Event spaces">مساحات المعرض</td><td class="p-3"><span class="perm-badge">spaces.view</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/v1/manage/events/{event}/spaces</code></td><td class="p-3" data-ar="إنشاء مساحة" data-en="Create space">إنشاء مساحة</td><td class="p-3"><span class="perm-badge">spaces.create</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/manage/events/{event}/sponsor-packages</code></td><td class="p-3" data-ar="باقات الرعاية" data-en="Sponsor packages">باقات الرعاية</td><td class="p-3"><span class="perm-badge">sponsor-packages.view</span></td></tr>
                                <tr><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/v1/manage/events/{event}/sponsor-packages</code></td><td class="p-3" data-ar="إنشاء باقة" data-en="Create package">إنشاء باقة</td><td class="p-3"><span class="perm-badge">sponsor-packages.create</span></td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 flex flex-wrap items-center gap-1.5 px-1">
                        <span class="text-[10px] text-gray-400 font-bold">⚠️</span>
                        <code class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded text-[10px]">401 UNAUTHENTICATED</code>
                        <code class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded text-[10px]">403 PERMISSION_DENIED</code>
                        <code class="bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded text-[10px]">404 NOT_FOUND</code>
                        <code class="bg-orange-50 text-orange-600 px-1.5 py-0.5 rounded text-[10px]">422 VALIDATION_ERROR</code>
                        <code class="bg-orange-50 text-orange-600 px-1.5 py-0.5 rounded text-[10px]">422 DUPLICATE_ENTRY</code>
                    </div>
                </section>

                {{-- Manage: Sections & Spaces --}}
                <section id="manage-sections-spaces" class="mb-16">
                    <h3 class="text-2xl font-bold mb-4" data-ar="الأقسام والمساحات" data-en="Sections & Spaces">الأقسام والمساحات</h3>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th><th class="p-3 text-right">Permission</th></tr></thead>
                            <tbody>
                                <tr class="border-b bg-gray-50/50"><td colspan="4" class="p-2 font-bold text-gray-600 text-xs uppercase tracking-wider" data-ar="الأقسام" data-en="Sections">الأقسام</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/manage/sections/{section}</code></td><td class="p-3" data-ar="تفاصيل قسم" data-en="Section details">تفاصيل</td><td class="p-3"><span class="perm-badge">sections.view</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/manage/sections/{section}</code></td><td class="p-3" data-ar="تحديث" data-en="Update">تحديث</td><td class="p-3"><span class="perm-badge">sections.update</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-delete text-white">DEL</span></td><td class="p-3"><code>/v1/manage/sections/{section}</code></td><td class="p-3" data-ar="حذف" data-en="Delete">حذف</td><td class="p-3"><span class="perm-badge">sections.delete</span></td></tr>
                                <tr class="border-b bg-gray-50/50"><td colspan="4" class="p-2 font-bold text-gray-600 text-xs uppercase tracking-wider" data-ar="المساحات" data-en="Spaces">المساحات</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/manage/spaces/{space}</code></td><td class="p-3" data-ar="تفاصيل" data-en="Details">تفاصيل</td><td class="p-3"><span class="perm-badge">spaces.view</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/manage/spaces/{space}</code></td><td class="p-3" data-ar="تحديث" data-en="Update">تحديث</td><td class="p-3"><span class="perm-badge">spaces.update</span></td></tr>
                                <tr><td class="p-3"><span class="badge method-delete text-white">DEL</span></td><td class="p-3"><code>/v1/manage/spaces/{space}</code></td><td class="p-3" data-ar="حذف" data-en="Delete">حذف</td><td class="p-3"><span class="perm-badge">spaces.delete</span></td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 flex flex-wrap items-center gap-1.5 px-1">
                        <span class="text-[10px] text-gray-400 font-bold">⚠️</span>
                        <code class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded text-[10px]">401 UNAUTHENTICATED</code>
                        <code class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded text-[10px]">403 PERMISSION_DENIED</code>
                        <code class="bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded text-[10px]">404 NOT_FOUND</code>
                        <code class="bg-orange-50 text-orange-600 px-1.5 py-0.5 rounded text-[10px]">422 VALIDATION_ERROR</code>
                    </div>
                </section>

                {{-- Manage: Services --}}
                <section id="manage-services" class="mb-16">
                    <h3 class="text-2xl font-bold mb-4" data-ar="خدمات المعارض" data-en="Expo Services">خدمات المعارض</h3>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th><th class="p-3 text-right">Permission</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/manage/services</code></td><td class="p-3" data-ar="القائمة" data-en="List">القائمة</td><td class="p-3"><span class="perm-badge">expo-services.view</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/v1/manage/services</code></td><td class="p-3" data-ar="إنشاء" data-en="Create">إنشاء</td><td class="p-3"><span class="perm-badge">expo-services.create</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/manage/services/{service}</code></td><td class="p-3" data-ar="التفاصيل" data-en="Details">التفاصيل</td><td class="p-3"><span class="perm-badge">expo-services.view</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/manage/services/{service}</code></td><td class="p-3" data-ar="تحديث" data-en="Update">تحديث</td><td class="p-3"><span class="perm-badge">expo-services.update</span></td></tr>
                                <tr><td class="p-3"><span class="badge method-delete text-white">DEL</span></td><td class="p-3"><code>/v1/manage/services/{service}</code></td><td class="p-3" data-ar="حذف" data-en="Delete">حذف</td><td class="p-3"><span class="perm-badge">expo-services.delete</span></td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 flex flex-wrap items-center gap-1.5 px-1">
                        <span class="text-[10px] text-gray-400 font-bold">⚠️</span>
                        <code class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded text-[10px]">401 UNAUTHENTICATED</code>
                        <code class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded text-[10px]">403 PERMISSION_DENIED</code>
                        <code class="bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded text-[10px]">404 NOT_FOUND</code>
                        <code class="bg-orange-50 text-orange-600 px-1.5 py-0.5 rounded text-[10px]">422 VALIDATION_ERROR</code>
                    </div>
                </section>

                {{-- Manage: Categories, Cities, Settings --}}
                <section id="manage-lookups" class="mb-16">
                    <h3 class="text-2xl font-bold mb-4" data-ar="فئات / مدن / إعدادات" data-en="Categories / Cities / Settings">فئات / مدن / إعدادات</h3>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th><th class="p-3 text-right">Permission</th></tr></thead>
                            <tbody>
                                <tr class="border-b bg-gray-50/50"><td colspan="4" class="p-2 font-bold text-gray-600 text-xs uppercase tracking-wider" data-ar="الفئات" data-en="Categories">الفئات — CRUD</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/manage/categories</code></td><td class="p-3" data-ar="القائمة" data-en="List">القائمة</td><td class="p-3"><span class="perm-badge">categories.view</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/v1/manage/categories</code></td><td class="p-3" data-ar="إنشاء" data-en="Create">إنشاء</td><td class="p-3"><span class="perm-badge">categories.create</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/manage/categories/{category}</code></td><td class="p-3" data-ar="التفاصيل" data-en="Details">التفاصيل</td><td class="p-3"><span class="perm-badge">categories.view</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/manage/categories/{category}</code></td><td class="p-3" data-ar="تحديث" data-en="Update">تحديث</td><td class="p-3"><span class="perm-badge">categories.update</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-delete text-white">DEL</span></td><td class="p-3"><code>/v1/manage/categories/{category}</code></td><td class="p-3" data-ar="حذف" data-en="Delete">حذف</td><td class="p-3"><span class="perm-badge">categories.delete</span></td></tr>
                                <tr class="border-b bg-gray-50/50"><td colspan="4" class="p-2 font-bold text-gray-600 text-xs uppercase tracking-wider" data-ar="المدن" data-en="Cities">المدن — CRUD</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/manage/cities</code></td><td class="p-3" data-ar="القائمة" data-en="List">القائمة</td><td class="p-3"><span class="perm-badge">cities.view</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/v1/manage/cities</code></td><td class="p-3" data-ar="إنشاء" data-en="Create">إنشاء</td><td class="p-3"><span class="perm-badge">cities.create</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/manage/cities/{city}</code></td><td class="p-3" data-ar="التفاصيل" data-en="Details">التفاصيل</td><td class="p-3"><span class="perm-badge">cities.view</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/manage/cities/{city}</code></td><td class="p-3" data-ar="تحديث" data-en="Update">تحديث</td><td class="p-3"><span class="perm-badge">cities.update</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-delete text-white">DEL</span></td><td class="p-3"><code>/v1/manage/cities/{city}</code></td><td class="p-3" data-ar="حذف" data-en="Delete">حذف</td><td class="p-3"><span class="perm-badge">cities.delete</span></td></tr>
                                <tr class="border-b bg-gray-50/50"><td colspan="4" class="p-2 font-bold text-gray-600 text-xs uppercase tracking-wider" data-ar="إعدادات النظام" data-en="Settings">إعدادات النظام</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/manage/settings</code></td><td class="p-3" data-ar="جميع الإعدادات" data-en="All settings">جميع الإعدادات</td><td class="p-3"><span class="perm-badge">settings.view</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/manage/settings/{key}</code></td><td class="p-3" data-ar="إعداد واحد" data-en="Single setting">إعداد واحد</td><td class="p-3"><span class="perm-badge">settings.view</span></td></tr>
                                <tr><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/manage/settings</code></td><td class="p-3" data-ar="تحديث الإعدادات" data-en="Update settings">تحديث</td><td class="p-3"><span class="perm-badge">settings.update</span></td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 flex flex-wrap items-center gap-1.5 px-1">
                        <span class="text-[10px] text-gray-400 font-bold">⚠️</span>
                        <code class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded text-[10px]">401 UNAUTHENTICATED</code>
                        <code class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded text-[10px]">403 PERMISSION_DENIED</code>
                        <code class="bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded text-[10px]">404 NOT_FOUND</code>
                        <code class="bg-orange-50 text-orange-600 px-1.5 py-0.5 rounded text-[10px]">422 VALIDATION_ERROR</code>
                        <code class="bg-orange-50 text-orange-600 px-1.5 py-0.5 rounded text-[10px]">422 DUPLICATE_ENTRY</code>
                    </div>
                </section>

                {{-- Manage: Users & Profiles --}}
                <section id="manage-users" class="mb-16">
                    <h3 class="text-2xl font-bold mb-4" data-ar="المستخدمين والملفات التجارية" data-en="Users & Business Profiles">المستخدمين والملفات التجارية</h3>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th><th class="p-3 text-right">Permission</th></tr></thead>
                            <tbody>
                                <tr class="border-b bg-gray-50/50"><td colspan="4" class="p-2 font-bold text-gray-600 text-xs uppercase tracking-wider" data-ar="المستخدمين" data-en="Users">المستخدمين</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/manage/users</code></td><td class="p-3" data-ar="القائمة" data-en="List">القائمة</td><td class="p-3"><span class="perm-badge">profiles.view-all</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/manage/users/{profile}</code></td><td class="p-3" data-ar="التفاصيل" data-en="Details">التفاصيل</td><td class="p-3"><span class="perm-badge">profiles.view-all</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/manage/users/{profile}/approve</code></td><td class="p-3" data-ar="قبول" data-en="Approve">قبول</td><td class="p-3"><span class="perm-badge">profiles.approve</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/manage/users/{profile}/reject</code></td><td class="p-3" data-ar="رفض" data-en="Reject">رفض</td><td class="p-3"><span class="perm-badge">profiles.reject</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/manage/users/{profile}/suspend</code></td><td class="p-3" data-ar="إيقاف" data-en="Suspend">إيقاف</td><td class="p-3"><span class="perm-badge">profiles.reject</span></td></tr>
                                <tr class="border-b bg-gray-50/50"><td colspan="4" class="p-2 font-bold text-gray-600 text-xs uppercase tracking-wider" data-ar="الملفات التجارية" data-en="Business Profiles">الملفات التجارية</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/manage/profiles</code></td><td class="p-3" data-ar="القائمة" data-en="List">القائمة</td><td class="p-3"><span class="perm-badge">profiles.view-all</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/manage/profiles/{profile}</code></td><td class="p-3" data-ar="التفاصيل" data-en="Details">التفاصيل</td><td class="p-3"><span class="perm-badge">profiles.view-all</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/manage/profiles/{profile}/approve</code></td><td class="p-3" data-ar="قبول" data-en="Approve">قبول</td><td class="p-3"><span class="perm-badge">profiles.approve</span></td></tr>
                                <tr><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/manage/profiles/{profile}/reject</code></td><td class="p-3" data-ar="رفض" data-en="Reject">رفض</td><td class="p-3"><span class="perm-badge">profiles.reject</span></td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 flex flex-wrap items-center gap-1.5 px-1">
                        <span class="text-[10px] text-gray-400 font-bold">⚠️</span>
                        <code class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded text-[10px]">401 UNAUTHENTICATED</code>
                        <code class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded text-[10px]">403 PERMISSION_DENIED</code>
                        <code class="bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded text-[10px]">404 NOT_FOUND</code>
                        <code class="bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded text-[10px]">422 INVALID_STATUS</code>
                    </div>
                </section>

                {{-- Manage: Visit & Rental Requests --}}
                <section id="manage-requests" class="mb-16">
                    <h3 class="text-2xl font-bold mb-4" data-ar="طلبات الزيارة والإيجار" data-en="Visit & Rental Requests">طلبات الزيارة والإيجار</h3>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th><th class="p-3 text-right">Permission</th></tr></thead>
                            <tbody>
                                <tr class="border-b bg-gray-50/50"><td colspan="4" class="p-2 font-bold text-gray-600 text-xs uppercase tracking-wider" data-ar="طلبات الزيارة" data-en="Visit Requests">طلبات الزيارة</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/manage/visit-requests</code></td><td class="p-3" data-ar="جميع الطلبات" data-en="All requests">جميع الطلبات</td><td class="p-3"><span class="perm-badge">visit-requests.view-all</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/manage/visit-requests/{id}</code></td><td class="p-3" data-ar="التفاصيل" data-en="Details">التفاصيل</td><td class="p-3"><span class="perm-badge">visit-requests.view-all</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/manage/visit-requests/{id}/approve</code></td><td class="p-3" data-ar="قبول" data-en="Approve">قبول</td><td class="p-3"><span class="perm-badge">visit-requests.approve</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/manage/visit-requests/{id}/reject</code></td><td class="p-3" data-ar="رفض" data-en="Reject">رفض</td><td class="p-3"><span class="perm-badge">visit-requests.reject</span></td></tr>
                                <tr class="border-b bg-gray-50/50"><td colspan="4" class="p-2 font-bold text-gray-600 text-xs uppercase tracking-wider" data-ar="طلبات الإيجار" data-en="Rental Requests">طلبات الإيجار</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/manage/rental-requests</code></td><td class="p-3" data-ar="جميع الطلبات" data-en="All requests">جميع الطلبات</td><td class="p-3"><span class="perm-badge">rental-requests.view-all</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/manage/rental-requests/{id}</code></td><td class="p-3" data-ar="التفاصيل" data-en="Details">التفاصيل</td><td class="p-3"><span class="perm-badge">rental-requests.view-all</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/manage/rental-requests/{id}/approve</code></td><td class="p-3" data-ar="قبول" data-en="Approve">قبول</td><td class="p-3"><span class="perm-badge">rental-requests.approve</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/manage/rental-requests/{id}/reject</code></td><td class="p-3" data-ar="رفض" data-en="Reject">رفض</td><td class="p-3"><span class="perm-badge">rental-requests.reject</span></td></tr>
                                <tr><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/v1/manage/rental-requests/{id}/payment</code></td><td class="p-3" data-ar="تسجيل دفعة" data-en="Record payment">تسجيل دفعة</td><td class="p-3"><span class="perm-badge">rental-requests.record-payment</span></td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 flex flex-wrap items-center gap-1.5 px-1">
                        <span class="text-[10px] text-gray-400 font-bold">⚠️</span>
                        <code class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded text-[10px]">401 UNAUTHENTICATED</code>
                        <code class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded text-[10px]">403 PERMISSION_DENIED</code>
                        <code class="bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded text-[10px]">404 NOT_FOUND</code>
                        <code class="bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded text-[10px]">422 INVALID_STATUS</code>
                        <code class="bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded text-[10px]">422 SPACE_UNAVAILABLE</code>
                    </div>
                </section>

                {{-- Manage: Rental Contracts --}}
                <section id="manage-contracts" class="mb-16">
                    <h3 class="text-2xl font-bold mb-4" data-ar="عقود الإيجار" data-en="Rental Contracts">عقود الإيجار</h3>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th><th class="p-3 text-right">Permission</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/manage/rental-contracts</code></td><td class="p-3" data-ar="جميع العقود" data-en="All contracts">جميع العقود</td><td class="p-3"><span class="perm-badge">rental-contracts.view-all</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/v1/manage/rental-contracts</code></td><td class="p-3" data-ar="إنشاء عقد" data-en="Create contract">إنشاء عقد</td><td class="p-3"><span class="perm-badge">rental-contracts.create</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/manage/rental-contracts/{id}</code></td><td class="p-3" data-ar="التفاصيل" data-en="Details">التفاصيل</td><td class="p-3"><span class="perm-badge">rental-contracts.view-all</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/manage/rental-contracts/{id}</code></td><td class="p-3" data-ar="تحديث" data-en="Update">تحديث</td><td class="p-3"><span class="perm-badge">rental-contracts.update</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/manage/rental-contracts/{id}/approve</code></td><td class="p-3" data-ar="قبول" data-en="Approve">قبول</td><td class="p-3"><span class="perm-badge">rental-contracts.approve</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/manage/rental-contracts/{id}/reject</code></td><td class="p-3" data-ar="رفض" data-en="Reject">رفض</td><td class="p-3"><span class="perm-badge">rental-contracts.reject</span></td></tr>
                                <tr><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/manage/rental-contracts/{id}/terminate</code></td><td class="p-3" data-ar="إنهاء" data-en="Terminate">إنهاء</td><td class="p-3"><span class="perm-badge">rental-contracts.terminate</span></td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 flex flex-wrap items-center gap-1.5 px-1">
                        <span class="text-[10px] text-gray-400 font-bold">⚠️</span>
                        <code class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded text-[10px]">401 UNAUTHENTICATED</code>
                        <code class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded text-[10px]">403 PERMISSION_DENIED</code>
                        <code class="bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded text-[10px]">404 NOT_FOUND</code>
                        <code class="bg-orange-50 text-orange-600 px-1.5 py-0.5 rounded text-[10px]">422 VALIDATION_ERROR</code>
                        <code class="bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded text-[10px]">422 INVALID_STATUS</code>
                        <code class="bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded text-[10px]">422 CONTRACT_ALREADY_SIGNED</code>
                    </div>
                </section>

                {{-- Manage: Sponsors (full stack) --}}
                <section id="manage-sponsors" class="mb-16">
                    <h3 class="text-2xl font-bold mb-4" data-ar="الرعاة والباقات والعقود والمدفوعات" data-en="Sponsors, Packages, Contracts & Payments">الرعاة</h3>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th><th class="p-3 text-right">Permission</th></tr></thead>
                            <tbody>
                                <tr class="border-b bg-gray-50/50"><td colspan="4" class="p-2 font-bold text-gray-600 text-xs uppercase tracking-wider" data-ar="الرعاة" data-en="Sponsors">الرعاة — CRUD + approve/activate/suspend</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/manage/sponsors</code></td><td class="p-3">List</td><td class="p-3"><span class="perm-badge">sponsors.view-all</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/v1/manage/sponsors</code></td><td class="p-3">Create</td><td class="p-3"><span class="perm-badge">sponsors.create</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/manage/sponsors/{id}</code></td><td class="p-3">Show</td><td class="p-3"><span class="perm-badge">sponsors.view-all</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/manage/sponsors/{id}</code></td><td class="p-3">Update</td><td class="p-3"><span class="perm-badge">sponsors.update</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-delete text-white">DEL</span></td><td class="p-3"><code>/v1/manage/sponsors/{id}</code></td><td class="p-3">Delete</td><td class="p-3"><span class="perm-badge">sponsors.delete</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/manage/sponsors/{id}/approve</code></td><td class="p-3">Approve</td><td class="p-3"><span class="perm-badge">sponsors.approve</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/manage/sponsors/{id}/activate</code></td><td class="p-3">Activate</td><td class="p-3"><span class="perm-badge">sponsors.approve</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/manage/sponsors/{id}/suspend</code></td><td class="p-3">Suspend</td><td class="p-3"><span class="perm-badge">sponsors.reject</span></td></tr>

                                <tr class="border-b bg-gray-50/50"><td colspan="4" class="p-2 font-bold text-gray-600 text-xs uppercase tracking-wider" data-ar="باقات الرعاية" data-en="Sponsor Packages">باقات — show/update/delete</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/manage/sponsor-packages/{id}</code></td><td class="p-3">Show</td><td class="p-3"><span class="perm-badge">sponsor-packages.view</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/manage/sponsor-packages/{id}</code></td><td class="p-3">Update</td><td class="p-3"><span class="perm-badge">sponsor-packages.update</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-delete text-white">DEL</span></td><td class="p-3"><code>/v1/manage/sponsor-packages/{id}</code></td><td class="p-3">Delete</td><td class="p-3"><span class="perm-badge">sponsor-packages.delete</span></td></tr>

                                <tr class="border-b bg-gray-50/50"><td colspan="4" class="p-2 font-bold text-gray-600 text-xs uppercase tracking-wider" data-ar="عقود الرعاية" data-en="Sponsor Contracts">عقود — CRUD + approve/reject/complete</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/manage/sponsor-contracts</code></td><td class="p-3">List</td><td class="p-3"><span class="perm-badge">sponsor-contracts.view-all</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/v1/manage/sponsor-contracts</code></td><td class="p-3">Create</td><td class="p-3"><span class="perm-badge">sponsor-contracts.create</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/manage/sponsor-contracts/{id}</code></td><td class="p-3">Show</td><td class="p-3"><span class="perm-badge">sponsor-contracts.view-all</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/manage/sponsor-contracts/{id}</code></td><td class="p-3">Update</td><td class="p-3"><span class="perm-badge">sponsor-contracts.update</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/manage/sponsor-contracts/{id}/approve</code></td><td class="p-3">Approve</td><td class="p-3"><span class="perm-badge">sponsor-contracts.approve</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/manage/sponsor-contracts/{id}/reject</code></td><td class="p-3">Reject</td><td class="p-3"><span class="perm-badge">sponsor-contracts.reject</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/manage/sponsor-contracts/{id}/complete</code></td><td class="p-3">Complete</td><td class="p-3"><span class="perm-badge">sponsor-contracts.approve</span></td></tr>

                                <tr class="border-b bg-gray-50/50"><td colspan="4" class="p-2 font-bold text-gray-600 text-xs uppercase tracking-wider" data-ar="مدفوعات الرعاية" data-en="Sponsor Payments">مدفوعات — CRUD + mark-paid</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/manage/sponsor-payments</code></td><td class="p-3">List</td><td class="p-3"><span class="perm-badge">sponsor-payments.view-all</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/v1/manage/sponsor-payments</code></td><td class="p-3">Create</td><td class="p-3"><span class="perm-badge">sponsor-payments.create</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/manage/sponsor-payments/{id}</code></td><td class="p-3">Show</td><td class="p-3"><span class="perm-badge">sponsor-payments.view-all</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/manage/sponsor-payments/{id}</code></td><td class="p-3">Update</td><td class="p-3"><span class="perm-badge">sponsor-payments.create</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/manage/sponsor-payments/{id}/mark-paid</code></td><td class="p-3">Mark Paid</td><td class="p-3"><span class="perm-badge">sponsor-payments.create</span></td></tr>

                                <tr class="border-b bg-gray-50/50"><td colspan="4" class="p-2 font-bold text-gray-600 text-xs uppercase tracking-wider" data-ar="مزايا الرعاية" data-en="Sponsor Benefits">مزايا — CRUD + deliver</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/manage/sponsor-benefits</code></td><td class="p-3">List</td><td class="p-3"><span class="perm-badge">sponsor-benefits.view</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/v1/manage/sponsor-benefits</code></td><td class="p-3">Create</td><td class="p-3"><span class="perm-badge">sponsor-benefits.create</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/manage/sponsor-benefits/{id}</code></td><td class="p-3">Show</td><td class="p-3"><span class="perm-badge">sponsor-benefits.view</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/manage/sponsor-benefits/{id}</code></td><td class="p-3">Update</td><td class="p-3"><span class="perm-badge">sponsor-benefits.update</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/manage/sponsor-benefits/{id}/deliver</code></td><td class="p-3">Deliver</td><td class="p-3"><span class="perm-badge">sponsor-benefits.deliver</span></td></tr>

                                <tr class="border-b bg-gray-50/50"><td colspan="4" class="p-2 font-bold text-gray-600 text-xs uppercase tracking-wider" data-ar="أصول الرعاية" data-en="Sponsor Assets">أصول — view + approve/reject</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/manage/sponsor-assets</code></td><td class="p-3">List</td><td class="p-3"><span class="perm-badge">sponsor-assets.view</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/manage/sponsor-assets/{id}</code></td><td class="p-3">Show</td><td class="p-3"><span class="perm-badge">sponsor-assets.view</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/manage/sponsor-assets/{id}/approve</code></td><td class="p-3">Approve</td><td class="p-3"><span class="perm-badge">sponsor-assets.approve</span></td></tr>
                                <tr><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/manage/sponsor-assets/{id}/reject</code></td><td class="p-3">Reject</td><td class="p-3"><span class="perm-badge">sponsor-assets.approve</span></td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 flex flex-wrap items-center gap-1.5 px-1">
                        <span class="text-[10px] text-gray-400 font-bold">⚠️</span>
                        <code class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded text-[10px]">401 UNAUTHENTICATED</code>
                        <code class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded text-[10px]">403 PERMISSION_DENIED</code>
                        <code class="bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded text-[10px]">404 NOT_FOUND</code>
                        <code class="bg-orange-50 text-orange-600 px-1.5 py-0.5 rounded text-[10px]">422 VALIDATION_ERROR</code>
                        <code class="bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded text-[10px]">422 INVALID_STATUS</code>
                    </div>
                </section>

                {{-- Manage: Ratings & Tickets --}}
                <section id="manage-ratings-tickets" class="mb-16">
                    <h3 class="text-2xl font-bold mb-4" data-ar="التقييمات وتذاكر الدعم" data-en="Ratings & Support Tickets">التقييمات وتذاكر الدعم</h3>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th><th class="p-3 text-right">Permission</th></tr></thead>
                            <tbody>
                                <tr class="border-b bg-gray-50/50"><td colspan="4" class="p-2 font-bold text-gray-600 text-xs uppercase tracking-wider" data-ar="التقييمات" data-en="Ratings">التقييمات</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/manage/ratings</code></td><td class="p-3">List</td><td class="p-3"><span class="perm-badge">ratings.view-all</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/manage/ratings/{id}</code></td><td class="p-3">Show</td><td class="p-3"><span class="perm-badge">ratings.view-all</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/manage/ratings/{id}/approve</code></td><td class="p-3">Approve</td><td class="p-3"><span class="perm-badge">ratings.approve</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/manage/ratings/{id}/reject</code></td><td class="p-3">Reject</td><td class="p-3"><span class="perm-badge">ratings.reject</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-delete text-white">DEL</span></td><td class="p-3"><code>/v1/manage/ratings/{id}</code></td><td class="p-3">Delete</td><td class="p-3"><span class="perm-badge">ratings.delete</span></td></tr>
                                <tr class="border-b bg-gray-50/50"><td colspan="4" class="p-2 font-bold text-gray-600 text-xs uppercase tracking-wider" data-ar="تذاكر الدعم" data-en="Support Tickets">تذاكر الدعم</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/manage/support-tickets</code></td><td class="p-3">List</td><td class="p-3"><span class="perm-badge">support-tickets.view-all</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/manage/support-tickets/{id}</code></td><td class="p-3">Show</td><td class="p-3"><span class="perm-badge">support-tickets.view-all</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/manage/support-tickets/{id}/assign</code></td><td class="p-3">Assign</td><td class="p-3"><span class="perm-badge">support-tickets.assign</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/v1/manage/support-tickets/{id}/reply</code></td><td class="p-3">Reply</td><td class="p-3"><span class="perm-badge">support-tickets.reply</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/manage/support-tickets/{id}/resolve</code></td><td class="p-3">Resolve</td><td class="p-3"><span class="perm-badge">support-tickets.close</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/manage/support-tickets/{id}/close</code></td><td class="p-3">Close</td><td class="p-3"><span class="perm-badge">support-tickets.close</span></td></tr>
                                <tr><td class="p-3"><span class="badge method-delete text-white">DEL</span></td><td class="p-3"><code>/v1/manage/support-tickets/{id}</code></td><td class="p-3">Delete</td><td class="p-3"><span class="perm-badge">support-tickets.delete</span></td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 flex flex-wrap items-center gap-1.5 px-1">
                        <span class="text-[10px] text-gray-400 font-bold">⚠️</span>
                        <code class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded text-[10px]">401 UNAUTHENTICATED</code>
                        <code class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded text-[10px]">403 PERMISSION_DENIED</code>
                        <code class="bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded text-[10px]">404 NOT_FOUND</code>
                        <code class="bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded text-[10px]">422 INVALID_STATUS</code>
                    </div>
                </section>

                {{-- Manage: Invoices --}}
                <section id="manage-invoices" class="mb-16">
                    <h3 class="text-2xl font-bold mb-4" data-ar="الفواتير" data-en="Invoices">الفواتير</h3>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th><th class="p-3 text-right">Permission</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/manage/invoices</code></td><td class="p-3">List</td><td class="p-3"><span class="perm-badge">invoices.view-all</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/v1/manage/invoices</code></td><td class="p-3">Create</td><td class="p-3"><span class="perm-badge">invoices.create</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/manage/invoices/{id}</code></td><td class="p-3">Show</td><td class="p-3"><span class="perm-badge">invoices.view-all</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/manage/invoices/{id}</code></td><td class="p-3">Update</td><td class="p-3"><span class="perm-badge">invoices.update</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/manage/invoices/{id}/issue</code></td><td class="p-3" data-ar="إصدار" data-en="Issue">إصدار</td><td class="p-3"><span class="perm-badge">invoices.issue</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/manage/invoices/{id}/mark-paid</code></td><td class="p-3" data-ar="تعليم كمدفوعة" data-en="Mark Paid">Mark Paid</td><td class="p-3"><span class="perm-badge">invoices.mark-paid</span></td></tr>
                                <tr><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/manage/invoices/{id}/cancel</code></td><td class="p-3" data-ar="إلغاء" data-en="Cancel">إلغاء</td><td class="p-3"><span class="perm-badge">invoices.cancel</span></td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 flex flex-wrap items-center gap-1.5 px-1">
                        <span class="text-[10px] text-gray-400 font-bold">⚠️</span>
                        <code class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded text-[10px]">401 UNAUTHENTICATED</code>
                        <code class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded text-[10px]">403 PERMISSION_DENIED</code>
                        <code class="bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded text-[10px]">404 NOT_FOUND</code>
                        <code class="bg-orange-50 text-orange-600 px-1.5 py-0.5 rounded text-[10px]">422 VALIDATION_ERROR</code>
                        <code class="bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded text-[10px]">422 INVALID_STATUS</code>
                    </div>
                </section>

                {{-- Manage: CMS Content --}}
                <section id="manage-content" class="mb-16">
                    <h3 class="text-2xl font-bold mb-4" data-ar="المحتوى (CMS)" data-en="CMS Content">المحتوى (CMS)</h3>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th><th class="p-3 text-right">Permission</th></tr></thead>
                            <tbody>
                                <tr class="border-b bg-gray-50/50"><td colspan="4" class="p-2 font-bold text-gray-600 text-xs uppercase tracking-wider" data-ar="الصفحات" data-en="Pages">الصفحات — CRUD</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/manage/pages</code></td><td class="p-3">List</td><td class="p-3"><span class="perm-badge">pages.view</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/v1/manage/pages</code></td><td class="p-3">Create</td><td class="p-3"><span class="perm-badge">pages.create</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/manage/pages/{page}</code></td><td class="p-3">Show</td><td class="p-3"><span class="perm-badge">pages.view</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/manage/pages/{page}</code></td><td class="p-3">Update</td><td class="p-3"><span class="perm-badge">pages.update</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-delete text-white">DEL</span></td><td class="p-3"><code>/v1/manage/pages/{page}</code></td><td class="p-3">Delete</td><td class="p-3"><span class="perm-badge">pages.delete</span></td></tr>
                                <tr class="border-b bg-gray-50/50"><td colspan="4" class="p-2 font-bold text-gray-600 text-xs uppercase tracking-wider" data-ar="الأسئلة الشائعة" data-en="FAQs">الأسئلة الشائعة — CRUD</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/manage/faqs</code></td><td class="p-3">List</td><td class="p-3"><span class="perm-badge">faqs.view</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/v1/manage/faqs</code></td><td class="p-3">Create</td><td class="p-3"><span class="perm-badge">faqs.create</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/manage/faqs/{faq}</code></td><td class="p-3">Show</td><td class="p-3"><span class="perm-badge">faqs.view</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/manage/faqs/{faq}</code></td><td class="p-3">Update</td><td class="p-3"><span class="perm-badge">faqs.update</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-delete text-white">DEL</span></td><td class="p-3"><code>/v1/manage/faqs/{faq}</code></td><td class="p-3">Delete</td><td class="p-3"><span class="perm-badge">faqs.delete</span></td></tr>
                                <tr class="border-b bg-gray-50/50"><td colspan="4" class="p-2 font-bold text-gray-600 text-xs uppercase tracking-wider" data-ar="البانرات" data-en="Banners">البانرات — CRUD</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/manage/banners</code></td><td class="p-3">List</td><td class="p-3"><span class="perm-badge">banners.view</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/v1/manage/banners</code></td><td class="p-3">Create</td><td class="p-3"><span class="perm-badge">banners.create</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/manage/banners/{banner}</code></td><td class="p-3">Show</td><td class="p-3"><span class="perm-badge">banners.view</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/v1/manage/banners/{banner}</code></td><td class="p-3">Update</td><td class="p-3"><span class="perm-badge">banners.update</span></td></tr>
                                <tr><td class="p-3"><span class="badge method-delete text-white">DEL</span></td><td class="p-3"><code>/v1/manage/banners/{banner}</code></td><td class="p-3">Delete</td><td class="p-3"><span class="perm-badge">banners.delete</span></td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 flex flex-wrap items-center gap-1.5 px-1">
                        <span class="text-[10px] text-gray-400 font-bold">⚠️</span>
                        <code class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded text-[10px]">401 UNAUTHENTICATED</code>
                        <code class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded text-[10px]">403 PERMISSION_DENIED</code>
                        <code class="bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded text-[10px]">404 NOT_FOUND</code>
                        <code class="bg-orange-50 text-orange-600 px-1.5 py-0.5 rounded text-[10px]">422 VALIDATION_ERROR</code>
                        <code class="bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded text-[10px]">422 SLUG_ALREADY_EXISTS</code>
                    </div>
                </section>

                {{-- Manage: Analytics --}}
                <section id="manage-analytics" class="mb-16">
                    <h3 class="text-2xl font-bold mb-4" data-ar="التحليلات والتقارير" data-en="Analytics & Reports">📊 التحليلات</h3>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th><th class="p-3 text-right">Permission</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/manage/analytics</code></td><td class="p-3" data-ar="نظرة عامة (إجمالي نشاطات، مستخدمين، منصات)" data-en="Overview (total activities, users, platforms)">نظرة عامة</td><td class="p-3"><span class="perm-badge">reports.view</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/manage/analytics/views</code></td><td class="p-3" data-ar="تحليل المشاهدات (أكثر مشاهدة، حسب النوع)" data-en="Views analysis (top viewed, by type)">تحليل المشاهدات</td><td class="p-3"><span class="perm-badge">reports.view</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/manage/analytics/actions</code></td><td class="p-3" data-ar="سجل النشاطات (مع فلترة وصفحات)" data-en="Activity log (filtered, paginated)">سجل النشاطات</td><td class="p-3"><span class="perm-badge">reports.view</span></td></tr>
                                <tr><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/v1/manage/analytics/users</code></td><td class="p-3" data-ar="تحليل المستخدمين (أكثر نشاطاً، حسب المنصة)" data-en="User analytics (most active, by platform)">تحليل المستخدمين</td><td class="p-3"><span class="perm-badge">reports.view</span></td></tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Analytics Query Parameters --}}
                    <div class="mt-4 bg-gray-50 border border-gray-200 rounded-xl p-4">
                        <p class="text-sm font-bold text-gray-600 mb-2" data-ar="فلاتر مشتركة لجميع النقاط" data-en="Common query filters for all endpoints">فلاتر مشتركة</p>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-xs">
                            <code class="bg-white border rounded px-2 py-1">?days=30</code>
                            <code class="bg-white border rounded px-2 py-1">?platform=web</code>
                            <code class="bg-white border rounded px-2 py-1">?action=view</code>
                            <code class="bg-white border rounded px-2 py-1">?resource_type=event</code>
                            <code class="bg-white border rounded px-2 py-1">?user_id={uuid}</code>
                            <code class="bg-white border rounded px-2 py-1">?per_page=20</code>
                        </div>
                    </div>

                    {{-- Overview Response Sample --}}
                    <div class="mt-4 code-block">
                        <div class="code-header"><span data-ar="مثال — نظرة عامة" data-en="Response — Overview">Response — GET /v1/manage/analytics</span></div>
                        <pre><code>{
  <span class="json-key">"success"</span>: <span class="json-bool">true</span>,
  <span class="json-key">"data"</span>: {
    <span class="json-key">"total_activities"</span>: <span class="json-number">12450</span>,
    <span class="json-key">"total_views"</span>: <span class="json-number">8200</span>,
    <span class="json-key">"unique_users"</span>: <span class="json-number">340</span>,
    <span class="json-key">"unique_ips"</span>: <span class="json-number">520</span>,
    <span class="json-key">"actions"</span>: { <span class="json-key">"view"</span>: <span class="json-number">8200</span>, <span class="json-key">"search"</span>: <span class="json-number">2100</span>, <span class="json-key">"click"</span>: <span class="json-number">1800</span> },
    <span class="json-key">"platforms"</span>: { <span class="json-key">"web"</span>: <span class="json-number">7000</span>, <span class="json-key">"mobile"</span>: <span class="json-number">5000</span>, <span class="json-key">"api"</span>: <span class="json-number">450</span> },
    <span class="json-key">"daily_trend"</span>: [{ <span class="json-key">"date"</span>: <span class="json-string">"2024-03-15"</span>, <span class="json-key">"count"</span>: <span class="json-number">420</span> }]
  }
}</code></pre>
                    </div>
                    <div class="mt-3 flex flex-wrap items-center gap-1.5 px-1">
                        <span class="text-[10px] text-gray-400 font-bold">⚠️</span>
                        <code class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded text-[10px]">401 UNAUTHENTICATED</code>
                        <code class="bg-red-50 text-red-600 px-1.5 py-0.5 rounded text-[10px]">403 PERMISSION_DENIED</code>
                    </div>
                </section>


                {{-- ============================================================ --}}
                {{-- POSTMAN COLLECTIONS DOWNLOAD                                --}}
                {{-- ============================================================ --}}

                <section id="postman" class="mb-20 section-divider">
                    <h2 class="text-3xl font-bold mb-2" data-ar="📦 مجموعات Postman" data-en="📦 Postman Collections">📦 مجموعات Postman</h2>
                    <p class="text-gray-500 mb-6" data-ar="حمّل المجموعات مباشرة واستوردها في Postman — يمكنك تحميل المجموعة كاملة أو قسم معين فقط" data-en="Download collections and import them into Postman — download full collections or individual sections">حمّل المجموعات مباشرة واستوردها في Postman</p>

                    {{-- Download All + Environments --}}
                    <div class="flex flex-wrap gap-3 mb-8">
                        <a href="/docs/postman/all" class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white px-5 py-2.5 rounded-xl font-bold text-sm transition-colors shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span data-ar="تحميل الكل (ZIP)" data-en="Download All (ZIP)">تحميل الكل (ZIP)</span>
                        </a>
                        <a href="/docs/postman/environment/production" class="inline-flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white px-5 py-2.5 rounded-xl font-bold text-sm transition-colors shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span data-ar="بيئة Production" data-en="Production Environment">بيئة Production</span>
                        </a>
                        <a href="/docs/postman/environment/local" class="inline-flex items-center gap-2 bg-gray-500 hover:bg-gray-600 text-white px-5 py-2.5 rounded-xl font-bold text-sm transition-colors shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <span data-ar="بيئة Local" data-en="Local Environment">بيئة Local</span>
                        </a>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-8">
                        <h4 class="font-bold text-blue-800 mb-2" data-ar="كيفية الاستخدام" data-en="How to Use">كيفية الاستخدام</h4>
                        <ol class="text-sm text-blue-700 space-y-1 list-decimal list-inside">
                            <li data-ar="حمّل ملف البيئة (Production أو Local) واستورده في Postman ← Environments" data-en="Download the Environment file (Production or Local) and import it in Postman → Environments">حمّل ملف البيئة واستورده في Postman ← Environments</li>
                            <li data-ar="حمّل المجموعة المطلوبة (أو القسم فقط) واستوردها في Postman ← Collections" data-en="Download the desired Collection (or just a section) and import it in Postman → Collections">حمّل المجموعة المطلوبة واستوردها في Postman ← Collections</li>
                            <li data-ar="اختر البيئة من القائمة أعلى يمين Postman وابدأ الاختبار" data-en="Select the Environment from the dropdown in the top-right of Postman and start testing">اختر البيئة من القائمة وابدأ الاختبار</li>
                        </ol>
                    </div>

                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-8">
                        <h4 class="font-bold text-amber-800 mb-2" data-ar="متغيرات البيئة" data-en="Environment Variables">متغيرات البيئة</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm text-amber-700">
                            <div>
                                <strong>Production:</strong><br>
                                <code class="text-xs bg-amber-100 px-1 rounded">auth_url</code> = <code class="text-xs">https://auth-service-api.mahamexpo.sa/api</code><br>
                                <code class="text-xs bg-amber-100 px-1 rounded">expo_url</code> = <code class="text-xs">https://expo-service-api.mahamexpo.sa/api</code>
                            </div>
                            <div>
                                <strong>Local:</strong><br>
                                <code class="text-xs bg-amber-100 px-1 rounded">auth_url</code> = <code class="text-xs">http://localhost:8001/api</code><br>
                                <code class="text-xs bg-amber-100 px-1 rounded">expo_url</code> = <code class="text-xs">http://localhost:8002/api</code>
                            </div>
                        </div>
                    </div>

                    {{-- Collections Grid --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">

                        {{-- Public API --}}
                        <div class="bg-white rounded-xl border border-gray-200 p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-bold text-emerald-700">Public API</h4>
                                <span class="text-xs bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full">30 requests</span>
                            </div>
                            <p class="text-xs text-gray-500 mb-3" data-ar="المعارض، المساحات، الفئات، المدن، الخدمات، التقييمات" data-en="Events, Spaces, Categories, Cities, Services, Ratings">المعارض، المساحات، الفئات، المدن</p>
                            <div class="flex flex-wrap gap-1 mb-3">
                                <a href="/docs/postman/collection/public-api/health" class="text-[10px] bg-gray-100 hover:bg-emerald-100 text-gray-600 hover:text-emerald-700 px-2 py-0.5 rounded transition-colors" title="Download Health section">Health</a>
                                <a href="/docs/postman/collection/public-api/events" class="text-[10px] bg-gray-100 hover:bg-emerald-100 text-gray-600 hover:text-emerald-700 px-2 py-0.5 rounded transition-colors" title="Download Events section">Events</a>
                                <a href="/docs/postman/collection/public-api/spaces" class="text-[10px] bg-gray-100 hover:bg-emerald-100 text-gray-600 hover:text-emerald-700 px-2 py-0.5 rounded transition-colors" title="Download Spaces section">Spaces</a>
                                <a href="/docs/postman/collection/public-api/categories" class="text-[10px] bg-gray-100 hover:bg-emerald-100 text-gray-600 hover:text-emerald-700 px-2 py-0.5 rounded transition-colors" title="Download Categories section">Categories</a>
                                <a href="/docs/postman/collection/public-api/cities" class="text-[10px] bg-gray-100 hover:bg-emerald-100 text-gray-600 hover:text-emerald-700 px-2 py-0.5 rounded transition-colors" title="Download Cities section">Cities</a>
                                <a href="/docs/postman/collection/public-api/services" class="text-[10px] bg-gray-100 hover:bg-emerald-100 text-gray-600 hover:text-emerald-700 px-2 py-0.5 rounded transition-colors" title="Download Services section">Services</a>
                                <a href="/docs/postman/collection/public-api/banners" class="text-[10px] bg-gray-100 hover:bg-emerald-100 text-gray-600 hover:text-emerald-700 px-2 py-0.5 rounded transition-colors" title="Download Banners section">Banners</a>
                                <a href="/docs/postman/collection/public-api/faqs" class="text-[10px] bg-gray-100 hover:bg-emerald-100 text-gray-600 hover:text-emerald-700 px-2 py-0.5 rounded transition-colors" title="Download FAQs section">FAQs</a>
                                <a href="/docs/postman/collection/public-api/pages" class="text-[10px] bg-gray-100 hover:bg-emerald-100 text-gray-600 hover:text-emerald-700 px-2 py-0.5 rounded transition-colors" title="Download Pages section">Pages</a>
                                <a href="/docs/postman/collection/public-api/ratings-public" class="text-[10px] bg-gray-100 hover:bg-emerald-100 text-gray-600 hover:text-emerald-700 px-2 py-0.5 rounded transition-colors" title="Download Ratings section">Ratings</a>
                                <a href="/docs/postman/collection/public-api/statistics-public" class="text-[10px] bg-gray-100 hover:bg-emerald-100 text-gray-600 hover:text-emerald-700 px-2 py-0.5 rounded transition-colors" title="Download Statistics section">Statistics</a>
                                <a href="/docs/postman/collection/public-api/tracking" class="text-[10px] bg-gray-100 hover:bg-emerald-100 text-gray-600 hover:text-emerald-700 px-2 py-0.5 rounded transition-colors" title="Download Tracking section">Tracking</a>
                            </div>
                            <a href="/docs/postman/collection/public-api" class="inline-flex items-center gap-1.5 text-emerald-600 hover:text-emerald-800 text-xs font-bold transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                <span data-ar="تحميل المجموعة كاملة" data-en="Download Full Collection">تحميل المجموعة كاملة</span>
                            </a>
                        </div>

                        {{-- Admin --}}
                        <div class="bg-white rounded-xl border border-gray-200 p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-bold text-purple-700">Admin</h4>
                                <span class="text-xs bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full">118 requests</span>
                            </div>
                            <p class="text-xs text-gray-500 mb-3" data-ar="إدارة كاملة: معارض، مساحات، مستخدمين، عقود، رعاة، فواتير" data-en="Full management: events, spaces, users, contracts, sponsors, invoices">إدارة كاملة: معارض، مساحات، مستخدمين</p>
                            <div class="flex flex-wrap gap-1 mb-3">
                                <a href="/docs/postman/collection/admin/dashboard-stats" class="text-[10px] bg-gray-100 hover:bg-purple-100 text-gray-600 hover:text-purple-700 px-2 py-0.5 rounded transition-colors">Dashboard</a>
                                <a href="/docs/postman/collection/admin/events" class="text-[10px] bg-gray-100 hover:bg-purple-100 text-gray-600 hover:text-purple-700 px-2 py-0.5 rounded transition-colors">Events</a>
                                <a href="/docs/postman/collection/admin/sections" class="text-[10px] bg-gray-100 hover:bg-purple-100 text-gray-600 hover:text-purple-700 px-2 py-0.5 rounded transition-colors">Sections</a>
                                <a href="/docs/postman/collection/admin/spaces" class="text-[10px] bg-gray-100 hover:bg-purple-100 text-gray-600 hover:text-purple-700 px-2 py-0.5 rounded transition-colors">Spaces</a>
                                <a href="/docs/postman/collection/admin/categories" class="text-[10px] bg-gray-100 hover:bg-purple-100 text-gray-600 hover:text-purple-700 px-2 py-0.5 rounded transition-colors">Categories</a>
                                <a href="/docs/postman/collection/admin/services" class="text-[10px] bg-gray-100 hover:bg-purple-100 text-gray-600 hover:text-purple-700 px-2 py-0.5 rounded transition-colors">Services</a>
                                <a href="/docs/postman/collection/admin/profiles" class="text-[10px] bg-gray-100 hover:bg-purple-100 text-gray-600 hover:text-purple-700 px-2 py-0.5 rounded transition-colors">Profiles</a>
                                <a href="/docs/postman/collection/admin/visit-requests" class="text-[10px] bg-gray-100 hover:bg-purple-100 text-gray-600 hover:text-purple-700 px-2 py-0.5 rounded transition-colors">Visits</a>
                                <a href="/docs/postman/collection/admin/rental-requests" class="text-[10px] bg-gray-100 hover:bg-purple-100 text-gray-600 hover:text-purple-700 px-2 py-0.5 rounded transition-colors">Rentals</a>
                                <a href="/docs/postman/collection/admin/rental-contracts" class="text-[10px] bg-gray-100 hover:bg-purple-100 text-gray-600 hover:text-purple-700 px-2 py-0.5 rounded transition-colors">Contracts</a>
                                <a href="/docs/postman/collection/admin/sponsors" class="text-[10px] bg-gray-100 hover:bg-purple-100 text-gray-600 hover:text-purple-700 px-2 py-0.5 rounded transition-colors">Sponsors</a>
                                <a href="/docs/postman/collection/admin/invoices" class="text-[10px] bg-gray-100 hover:bg-purple-100 text-gray-600 hover:text-purple-700 px-2 py-0.5 rounded transition-colors">Invoices</a>
                                <a href="/docs/postman/collection/admin/ratings" class="text-[10px] bg-gray-100 hover:bg-purple-100 text-gray-600 hover:text-purple-700 px-2 py-0.5 rounded transition-colors">Ratings</a>
                                <a href="/docs/postman/collection/admin/support-tickets" class="text-[10px] bg-gray-100 hover:bg-purple-100 text-gray-600 hover:text-purple-700 px-2 py-0.5 rounded transition-colors">Tickets</a>
                                <a href="/docs/postman/collection/admin/analytics" class="text-[10px] bg-gray-100 hover:bg-purple-100 text-gray-600 hover:text-purple-700 px-2 py-0.5 rounded transition-colors">Analytics</a>
                            </div>
                            <a href="/docs/postman/collection/admin" class="inline-flex items-center gap-1.5 text-purple-600 hover:text-purple-800 text-xs font-bold transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                <span data-ar="تحميل المجموعة كاملة" data-en="Download Full Collection">تحميل المجموعة كاملة</span>
                            </a>
                        </div>

                        {{-- Investor --}}
                        <div class="bg-white rounded-xl border border-gray-200 p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-bold text-blue-700">Investor</h4>
                                <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">48 requests</span>
                            </div>
                            <p class="text-xs text-gray-500 mb-3" data-ar="مساحاتي، زيارات واردة، إيجارات، عقود، مدفوعات" data-en="My spaces, received visits, rentals, contracts, payments">مساحاتي، زيارات واردة، إيجارات، عقود</p>
                            <div class="flex flex-wrap gap-1 mb-3">
                                <a href="/docs/postman/collection/investor/dashboard-stats" class="text-[10px] bg-gray-100 hover:bg-blue-100 text-gray-600 hover:text-blue-700 px-2 py-0.5 rounded transition-colors">Dashboard</a>
                                <a href="/docs/postman/collection/investor/spaces" class="text-[10px] bg-gray-100 hover:bg-blue-100 text-gray-600 hover:text-blue-700 px-2 py-0.5 rounded transition-colors">Spaces</a>
                                <a href="/docs/postman/collection/investor/visit-requests" class="text-[10px] bg-gray-100 hover:bg-blue-100 text-gray-600 hover:text-blue-700 px-2 py-0.5 rounded transition-colors">Visits</a>
                                <a href="/docs/postman/collection/investor/rental-requests" class="text-[10px] bg-gray-100 hover:bg-blue-100 text-gray-600 hover:text-blue-700 px-2 py-0.5 rounded transition-colors">Rentals</a>
                                <a href="/docs/postman/collection/investor/rental-contracts" class="text-[10px] bg-gray-100 hover:bg-blue-100 text-gray-600 hover:text-blue-700 px-2 py-0.5 rounded transition-colors">Contracts</a>
                                <a href="/docs/postman/collection/investor/payments" class="text-[10px] bg-gray-100 hover:bg-blue-100 text-gray-600 hover:text-blue-700 px-2 py-0.5 rounded transition-colors">Payments</a>
                                <a href="/docs/postman/collection/investor/my-activity" class="text-[10px] bg-gray-100 hover:bg-blue-100 text-gray-600 hover:text-blue-700 px-2 py-0.5 rounded transition-colors">Activity</a>
                            </div>
                            <a href="/docs/postman/collection/investor" class="inline-flex items-center gap-1.5 text-blue-600 hover:text-blue-800 text-xs font-bold transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                <span data-ar="تحميل المجموعة كاملة" data-en="Download Full Collection">تحميل المجموعة كاملة</span>
                            </a>
                        </div>

                        {{-- Merchant --}}
                        <div class="bg-white rounded-xl border border-gray-200 p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-bold text-amber-700">Merchant</h4>
                                <span class="text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">49 requests</span>
                            </div>
                            <p class="text-xs text-gray-500 mb-3" data-ar="ملف تجاري، مفضلة، إشعارات، زيارات، إيجارات، تقييمات" data-en="Profile, favorites, notifications, visits, rentals, ratings">ملف تجاري، مفضلة، زيارات، إيجارات</p>
                            <div class="flex flex-wrap gap-1 mb-3">
                                <a href="/docs/postman/collection/merchant/profile" class="text-[10px] bg-gray-100 hover:bg-amber-100 text-gray-600 hover:text-amber-700 px-2 py-0.5 rounded transition-colors">Profile</a>
                                <a href="/docs/postman/collection/merchant/favorites" class="text-[10px] bg-gray-100 hover:bg-amber-100 text-gray-600 hover:text-amber-700 px-2 py-0.5 rounded transition-colors">Favorites</a>
                                <a href="/docs/postman/collection/merchant/notifications" class="text-[10px] bg-gray-100 hover:bg-amber-100 text-gray-600 hover:text-amber-700 px-2 py-0.5 rounded transition-colors">Notifications</a>
                                <a href="/docs/postman/collection/merchant/visit-requests" class="text-[10px] bg-gray-100 hover:bg-amber-100 text-gray-600 hover:text-amber-700 px-2 py-0.5 rounded transition-colors">Visits</a>
                                <a href="/docs/postman/collection/merchant/rental-requests" class="text-[10px] bg-gray-100 hover:bg-amber-100 text-gray-600 hover:text-amber-700 px-2 py-0.5 rounded transition-colors">Rentals</a>
                                <a href="/docs/postman/collection/merchant/ratings" class="text-[10px] bg-gray-100 hover:bg-amber-100 text-gray-600 hover:text-amber-700 px-2 py-0.5 rounded transition-colors">Ratings</a>
                                <a href="/docs/postman/collection/merchant/support-tickets" class="text-[10px] bg-gray-100 hover:bg-amber-100 text-gray-600 hover:text-amber-700 px-2 py-0.5 rounded transition-colors">Tickets</a>
                                <a href="/docs/postman/collection/merchant/invoices" class="text-[10px] bg-gray-100 hover:bg-amber-100 text-gray-600 hover:text-amber-700 px-2 py-0.5 rounded transition-colors">Invoices</a>
                                <a href="/docs/postman/collection/merchant/my-activity" class="text-[10px] bg-gray-100 hover:bg-amber-100 text-gray-600 hover:text-amber-700 px-2 py-0.5 rounded transition-colors">Activity</a>
                            </div>
                            <a href="/docs/postman/collection/merchant" class="inline-flex items-center gap-1.5 text-amber-600 hover:text-amber-800 text-xs font-bold transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                <span data-ar="تحميل المجموعة كاملة" data-en="Download Full Collection">تحميل المجموعة كاملة</span>
                            </a>
                        </div>

                        {{-- Sponsor --}}
                        <div class="bg-white rounded-xl border border-gray-200 p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-bold text-pink-700">Sponsor</h4>
                                <span class="text-xs bg-pink-100 text-pink-700 px-2 py-0.5 rounded-full">39 requests</span>
                            </div>
                            <p class="text-xs text-gray-500 mb-3" data-ar="بيانات الراعي، عقود الرعاية، حزم، أصول، مزايا" data-en="Sponsor data, contracts, packages, assets, benefits">بيانات الراعي، عقود، حزم، أصول</p>
                            <div class="flex flex-wrap gap-1 mb-3">
                                <a href="/docs/postman/collection/sponsor/profile" class="text-[10px] bg-gray-100 hover:bg-pink-100 text-gray-600 hover:text-pink-700 px-2 py-0.5 rounded transition-colors">Profile</a>
                                <a href="/docs/postman/collection/sponsor/contracts" class="text-[10px] bg-gray-100 hover:bg-pink-100 text-gray-600 hover:text-pink-700 px-2 py-0.5 rounded transition-colors">Contracts</a>
                                <a href="/docs/postman/collection/sponsor/assets" class="text-[10px] bg-gray-100 hover:bg-pink-100 text-gray-600 hover:text-pink-700 px-2 py-0.5 rounded transition-colors">Assets</a>
                                <a href="/docs/postman/collection/sponsor/payments" class="text-[10px] bg-gray-100 hover:bg-pink-100 text-gray-600 hover:text-pink-700 px-2 py-0.5 rounded transition-colors">Payments</a>
                                <a href="/docs/postman/collection/sponsor/exposure" class="text-[10px] bg-gray-100 hover:bg-pink-100 text-gray-600 hover:text-pink-700 px-2 py-0.5 rounded transition-colors">Exposure</a>
                                <a href="/docs/postman/collection/sponsor/invoices" class="text-[10px] bg-gray-100 hover:bg-pink-100 text-gray-600 hover:text-pink-700 px-2 py-0.5 rounded transition-colors">Invoices</a>
                                <a href="/docs/postman/collection/sponsor/my-activity" class="text-[10px] bg-gray-100 hover:bg-pink-100 text-gray-600 hover:text-pink-700 px-2 py-0.5 rounded transition-colors">Activity</a>
                            </div>
                            <a href="/docs/postman/collection/sponsor" class="inline-flex items-center gap-1.5 text-pink-600 hover:text-pink-800 text-xs font-bold transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                <span data-ar="تحميل المجموعة كاملة" data-en="Download Full Collection">تحميل المجموعة كاملة</span>
                            </a>
                        </div>

                        {{-- Supervisor --}}
                        <div class="bg-white rounded-xl border border-gray-200 p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-bold text-indigo-700">Supervisor</h4>
                                <span class="text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full">45 requests</span>
                            </div>
                            <p class="text-xs text-gray-500 mb-3" data-ar="عرض وموافقة: معارض، مساحات، طلبات، عقود" data-en="View & approve: events, spaces, requests, contracts">عرض وموافقة: معارض، مساحات، طلبات</p>
                            <div class="flex flex-wrap gap-1 mb-3">
                                <a href="/docs/postman/collection/supervisor/dashboard-stats" class="text-[10px] bg-gray-100 hover:bg-indigo-100 text-gray-600 hover:text-indigo-700 px-2 py-0.5 rounded transition-colors">Dashboard</a>
                                <a href="/docs/postman/collection/supervisor/events" class="text-[10px] bg-gray-100 hover:bg-indigo-100 text-gray-600 hover:text-indigo-700 px-2 py-0.5 rounded transition-colors">Events</a>
                                <a href="/docs/postman/collection/supervisor/sections" class="text-[10px] bg-gray-100 hover:bg-indigo-100 text-gray-600 hover:text-indigo-700 px-2 py-0.5 rounded transition-colors">Sections</a>
                                <a href="/docs/postman/collection/supervisor/spaces" class="text-[10px] bg-gray-100 hover:bg-indigo-100 text-gray-600 hover:text-indigo-700 px-2 py-0.5 rounded transition-colors">Spaces</a>
                                <a href="/docs/postman/collection/supervisor/visit-requests" class="text-[10px] bg-gray-100 hover:bg-indigo-100 text-gray-600 hover:text-indigo-700 px-2 py-0.5 rounded transition-colors">Visits</a>
                                <a href="/docs/postman/collection/supervisor/rental-requests" class="text-[10px] bg-gray-100 hover:bg-indigo-100 text-gray-600 hover:text-indigo-700 px-2 py-0.5 rounded transition-colors">Rentals</a>
                                <a href="/docs/postman/collection/supervisor/rental-contracts" class="text-[10px] bg-gray-100 hover:bg-indigo-100 text-gray-600 hover:text-indigo-700 px-2 py-0.5 rounded transition-colors">Contracts</a>
                                <a href="/docs/postman/collection/supervisor/sponsors" class="text-[10px] bg-gray-100 hover:bg-indigo-100 text-gray-600 hover:text-indigo-700 px-2 py-0.5 rounded transition-colors">Sponsors</a>
                                <a href="/docs/postman/collection/supervisor/support-tickets" class="text-[10px] bg-gray-100 hover:bg-indigo-100 text-gray-600 hover:text-indigo-700 px-2 py-0.5 rounded transition-colors">Tickets</a>
                            </div>
                            <a href="/docs/postman/collection/supervisor" class="inline-flex items-center gap-1.5 text-indigo-600 hover:text-indigo-800 text-xs font-bold transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                <span data-ar="تحميل المجموعة كاملة" data-en="Download Full Collection">تحميل المجموعة كاملة</span>
                            </a>
                        </div>

                        {{-- SuperAdmin --}}
                        <div class="bg-white rounded-xl border border-gray-200 p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-bold text-red-700">SuperAdmin</h4>
                                <span class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full">25 requests</span>
                            </div>
                            <p class="text-xs text-gray-500 mb-3" data-ar="صلاحيات كاملة: إعدادات النظام، إدارة عامة" data-en="Full access: system settings, general management">صلاحيات كاملة: إعدادات النظام</p>
                            <div class="flex flex-wrap gap-1 mb-3">
                                <a href="/docs/postman/collection/super-admin/dashboard-stats" class="text-[10px] bg-gray-100 hover:bg-red-100 text-gray-600 hover:text-red-700 px-2 py-0.5 rounded transition-colors">Dashboard</a>
                                <a href="/docs/postman/collection/super-admin/settings" class="text-[10px] bg-gray-100 hover:bg-red-100 text-gray-600 hover:text-red-700 px-2 py-0.5 rounded transition-colors">Settings</a>
                                <a href="/docs/postman/collection/super-admin/categories" class="text-[10px] bg-gray-100 hover:bg-red-100 text-gray-600 hover:text-red-700 px-2 py-0.5 rounded transition-colors">Categories</a>
                                <a href="/docs/postman/collection/super-admin/cities" class="text-[10px] bg-gray-100 hover:bg-red-100 text-gray-600 hover:text-red-700 px-2 py-0.5 rounded transition-colors">Cities</a>
                                <a href="/docs/postman/collection/super-admin/users" class="text-[10px] bg-gray-100 hover:bg-red-100 text-gray-600 hover:text-red-700 px-2 py-0.5 rounded transition-colors">Users</a>
                            </div>
                            <a href="/docs/postman/collection/super-admin" class="inline-flex items-center gap-1.5 text-red-600 hover:text-red-800 text-xs font-bold transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                <span data-ar="تحميل المجموعة كاملة" data-en="Download Full Collection">تحميل المجموعة كاملة</span>
                            </a>
                        </div>

                        {{-- Dashboard --}}
                        <div class="bg-white rounded-xl border border-gray-200 p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-bold text-cyan-700">Dashboard</h4>
                                <span class="text-xs bg-cyan-100 text-cyan-700 px-2 py-0.5 rounded-full">22 requests</span>
                            </div>
                            <p class="text-xs text-gray-500 mb-3" data-ar="لوحة التحكم، إحصائيات، تقارير" data-en="Dashboard, statistics, reports">لوحة التحكم، إحصائيات، تقارير</p>
                            <div class="flex flex-wrap gap-1 mb-3">
                                <a href="/docs/postman/collection/dashboard/1-auth-tsgyl-aldkhol" class="text-[10px] bg-gray-100 hover:bg-cyan-100 text-gray-600 hover:text-cyan-700 px-2 py-0.5 rounded transition-colors">Auth</a>
                                <a href="/docs/postman/collection/dashboard/2-dashboard-overview-nthr-aaam" class="text-[10px] bg-gray-100 hover:bg-cyan-100 text-gray-600 hover:text-cyan-700 px-2 py-0.5 rounded transition-colors">Overview</a>
                                <a href="/docs/postman/collection/dashboard/3-dashboard-spaces-filter-fltr-almsahat" class="text-[10px] bg-gray-100 hover:bg-cyan-100 text-gray-600 hover:text-cyan-700 px-2 py-0.5 rounded transition-colors">Spaces</a>
                                <a href="/docs/postman/collection/dashboard/4-dashboard-revenue-filter-fltr-alayradat" class="text-[10px] bg-gray-100 hover:bg-cyan-100 text-gray-600 hover:text-cyan-700 px-2 py-0.5 rounded transition-colors">Revenue</a>
                                <a href="/docs/postman/collection/dashboard/5-dashboard-combined-filters-flatr-mgtmaa" class="text-[10px] bg-gray-100 hover:bg-cyan-100 text-gray-600 hover:text-cyan-700 px-2 py-0.5 rounded transition-colors">Filters</a>
                                <a href="/docs/postman/collection/dashboard/6-user-stats-auth-service-ahsayyat-almstkhdmyn" class="text-[10px] bg-gray-100 hover:bg-cyan-100 text-gray-600 hover:text-cyan-700 px-2 py-0.5 rounded transition-colors">User Stats</a>
                                <a href="/docs/postman/collection/dashboard/7-health-checks-fhs-alsh" class="text-[10px] bg-gray-100 hover:bg-cyan-100 text-gray-600 hover:text-cyan-700 px-2 py-0.5 rounded transition-colors">Health</a>
                            </div>
                            <a href="/docs/postman/collection/dashboard" class="inline-flex items-center gap-1.5 text-cyan-600 hover:text-cyan-800 text-xs font-bold transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                <span data-ar="تحميل المجموعة كاملة" data-en="Download Full Collection">تحميل المجموعة كاملة</span>
                            </a>
                        </div>

                        {{-- Auth Service --}}
                        <div class="bg-white rounded-xl border border-gray-200 p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-bold text-gray-700">Auth Service</h4>
                                <span class="text-xs bg-gray-200 text-gray-700 px-2 py-0.5 rounded-full">51 requests</span>
                            </div>
                            <p class="text-xs text-gray-500 mb-3" data-ar="مصادقة، مستخدمين، أدوار، صلاحيات، إدارة خدمات" data-en="Auth, users, roles, permissions, service management">مصادقة، مستخدمين، أدوار، صلاحيات</p>
                            <div class="flex flex-wrap gap-1 mb-3">
                                <a href="/docs/postman/collection/auth-service/health-check" class="text-[10px] bg-gray-100 hover:bg-gray-200 text-gray-600 hover:text-gray-800 px-2 py-0.5 rounded transition-colors">Health</a>
                                <a href="/docs/postman/collection/auth-service/authentication" class="text-[10px] bg-gray-100 hover:bg-gray-200 text-gray-600 hover:text-gray-800 px-2 py-0.5 rounded transition-colors">Auth</a>
                                <a href="/docs/postman/collection/auth-service/users" class="text-[10px] bg-gray-100 hover:bg-gray-200 text-gray-600 hover:text-gray-800 px-2 py-0.5 rounded transition-colors">Users</a>
                                <a href="/docs/postman/collection/auth-service/roles" class="text-[10px] bg-gray-100 hover:bg-gray-200 text-gray-600 hover:text-gray-800 px-2 py-0.5 rounded transition-colors">Roles</a>
                                <a href="/docs/postman/collection/auth-service/permissions" class="text-[10px] bg-gray-100 hover:bg-gray-200 text-gray-600 hover:text-gray-800 px-2 py-0.5 rounded transition-colors">Permissions</a>
                                <a href="/docs/postman/collection/auth-service/services-inter-service" class="text-[10px] bg-gray-100 hover:bg-gray-200 text-gray-600 hover:text-gray-800 px-2 py-0.5 rounded transition-colors">Services</a>
                            </div>
                            <a href="/docs/postman/collection/auth-service" class="inline-flex items-center gap-1.5 text-gray-600 hover:text-gray-800 text-xs font-bold transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                <span data-ar="تحميل المجموعة كاملة" data-en="Download Full Collection">تحميل المجموعة كاملة</span>
                            </a>
                        </div>
                    </div>

                    {{-- Total counter --}}
                    <div class="text-center text-sm text-gray-400">
                        <span data-ar="المجموع: 9 مجموعات • 427 طلب • بيئتين (Production + Local)" data-en="Total: 9 Collections • 427 Requests • 2 Environments (Production + Local)">المجموع: 9 مجموعات • 427 طلب • بيئتين</span>
                    </div>
                </section>


                {{-- ============================================================ --}}
                {{-- REFERENCE --}}
                {{-- ============================================================ --}}
                <div class="section-divider">
                    <h2 class="text-2xl font-extrabold text-gray-700 mb-8" data-ar="📚 المرجع" data-en="📚 Reference">📚 المرجع</h2>
                </div>

                {{-- HTTP Status Codes --}}
                <section id="errors" class="mb-20">
                    <h2 class="text-3xl font-bold mb-4" data-ar="رموز الحالة HTTP" data-en="HTTP Status Codes">رموز الحالة HTTP</h2>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-4 text-right" data-ar="الرمز" data-en="Code">الرمز</th><th class="p-4 text-right">Name</th><th class="p-4 text-right" data-ar="الوصف" data-en="Description">الوصف</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-4"><span class="bg-green-100 text-green-700 px-2 py-1 rounded font-mono">200</span></td><td class="p-4">OK</td><td class="p-4 text-gray-600" data-ar="تمت العملية بنجاح" data-en="Success">تمت بنجاح</td></tr>
                                <tr class="border-b"><td class="p-4"><span class="bg-green-100 text-green-700 px-2 py-1 rounded font-mono">201</span></td><td class="p-4">Created</td><td class="p-4 text-gray-600" data-ar="تم إنشاء المورد" data-en="Created">تم الإنشاء</td></tr>
                                <tr class="border-b"><td class="p-4"><span class="bg-red-100 text-red-700 px-2 py-1 rounded font-mono">401</span></td><td class="p-4">Unauthorized</td><td class="p-4 text-gray-600" data-ar="غير مصرح" data-en="Not authenticated">غير مصرح</td></tr>
                                <tr class="border-b"><td class="p-4"><span class="bg-red-100 text-red-700 px-2 py-1 rounded font-mono">403</span></td><td class="p-4">Forbidden</td><td class="p-4 text-gray-600" data-ar="ليس لديك صلاحية" data-en="No permission">لا صلاحية</td></tr>
                                <tr class="border-b"><td class="p-4"><span class="bg-orange-100 text-orange-700 px-2 py-1 rounded font-mono">404</span></td><td class="p-4">Not Found</td><td class="p-4 text-gray-600" data-ar="غير موجود" data-en="Not found">غير موجود</td></tr>
                                <tr class="border-b"><td class="p-4"><span class="bg-orange-100 text-orange-700 px-2 py-1 rounded font-mono">422</span></td><td class="p-4">Unprocessable</td><td class="p-4 text-gray-600" data-ar="خطأ في التحقق" data-en="Validation error">خطأ تحقق</td></tr>
                                <tr class="border-b"><td class="p-4"><span class="bg-purple-100 text-purple-700 px-2 py-1 rounded font-mono">429</span></td><td class="p-4">Too Many</td><td class="p-4 text-gray-600" data-ar="تجاوز حد الطلبات" data-en="Rate limited">تجاوز الحد</td></tr>
                                <tr><td class="p-4"><span class="bg-red-100 text-red-700 px-2 py-1 rounded font-mono">500</span></td><td class="p-4">Server Error</td><td class="p-4 text-gray-600" data-ar="خطأ في الخادم" data-en="Server error">خطأ خادم</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- ============================================================ --}}
                {{--         COMPREHENSIVE ERROR CODES REFERENCE                  --}}
                {{-- ============================================================ --}}
                <section id="error-codes" class="mb-20">
                    <h2 class="text-3xl font-bold mb-2" data-ar="📛 جدول رموز الأخطاء الشامل" data-en="📛 Comprehensive Error Codes Reference">📛 جدول رموز الأخطاء الشامل</h2>
                    <p class="text-gray-500 mb-6" data-ar="كل مورد وأخطاؤه المحتملة — حقل error_code + حقل errors (للتحقق)" data-en="Every resource with its possible errors — error_code field + errors field (validation)">كل مورد وأخطاؤه المحتملة</p>

                    {{-- Error Response Samples --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
                        <div class="code-block">
                            <div class="code-header"><span>401 — Unauthenticated</span></div>
                            <pre><code>{
  <span class="json-key">"success"</span>: <span class="json-bool">false</span>,
  <span class="json-key">"message"</span>: <span class="json-string">"Unauthenticated."</span>,
  <span class="json-key">"error_code"</span>: <span class="json-string">"UNAUTHENTICATED"</span>
}</code></pre>
                        </div>
                        <div class="code-block">
                            <div class="code-header"><span>403 — Permission Denied</span></div>
                            <pre><code>{
  <span class="json-key">"success"</span>: <span class="json-bool">false</span>,
  <span class="json-key">"message"</span>: <span class="json-string">"You do not have permission."</span>,
  <span class="json-key">"error_code"</span>: <span class="json-string">"PERMISSION_DENIED"</span>
}</code></pre>
                        </div>
                        <div class="code-block">
                            <div class="code-header"><span>422 — Validation Error</span></div>
                            <pre><code>{
  <span class="json-key">"success"</span>: <span class="json-bool">false</span>,
  <span class="json-key">"message"</span>: <span class="json-string">"The given data was invalid."</span>,
  <span class="json-key">"error_code"</span>: <span class="json-string">"VALIDATION_ERROR"</span>,
  <span class="json-key">"errors"</span>: {
    <span class="json-key">"name"</span>: [<span class="json-string">"required"</span>],
    <span class="json-key">"email"</span>: [<span class="json-string">"taken"</span>]
  }
}</code></pre>
                        </div>
                        <div class="code-block">
                            <div class="code-header"><span>404 — Not Found</span></div>
                            <pre><code>{
  <span class="json-key">"success"</span>: <span class="json-bool">false</span>,
  <span class="json-key">"message"</span>: <span class="json-string">"Resource not found."</span>,
  <span class="json-key">"error_code"</span>: <span class="json-string">"NOT_FOUND"</span>
}</code></pre>
                        </div>
                        <div class="code-block">
                            <div class="code-header"><span>422 — Business Logic</span></div>
                            <pre><code>{
  <span class="json-key">"success"</span>: <span class="json-bool">false</span>,
  <span class="json-key">"message"</span>: <span class="json-string">"Space is unavailable."</span>,
  <span class="json-key">"error_code"</span>: <span class="json-string">"SPACE_UNAVAILABLE"</span>
}</code></pre>
                        </div>
                        <div class="code-block">
                            <div class="code-header"><span>429 — Rate Limited</span></div>
                            <pre><code>{
  <span class="json-key">"success"</span>: <span class="json-bool">false</span>,
  <span class="json-key">"message"</span>: <span class="json-string">"Too many requests."</span>,
  <span class="json-key">"error_code"</span>: <span class="json-string">"RATE_LIMITED"</span>
}</code></pre>
                        </div>
                    </div>

                    {{-- Per-Resource Error Codes Table --}}
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right" data-ar="المورد / العملية" data-en="Resource / Action">المورد / العملية</th><th class="p-3 text-right">HTTP</th><th class="p-3 text-right" data-ar="رمز الخطأ" data-en="Error Code">رمز الخطأ</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th></tr></thead>
                            <tbody>
                                {{-- Global (all endpoints) --}}
                                <tr class="bg-red-50/40"><td colspan="4" class="p-2 font-bold text-red-800 text-xs uppercase tracking-wider">🔒 Global — All Authenticated Endpoints</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs" data-ar="أي ريكوست محمي" data-en="Any protected request">أي ريكوست محمي</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">401</span></td><td class="p-3"><code class="bg-red-100 text-red-700 px-2 py-0.5 rounded text-xs font-mono">UNAUTHENTICATED</code></td><td class="p-3 text-gray-600 text-xs" data-ar="التوكن مفقود أو منتهي أو غير صالح" data-en="Token missing, expired, or invalid">التوكن مفقود أو منتهي أو غير صالح</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs" data-ar="أي ريكوست يحتاج صلاحية" data-en="Any permission-protected request">أي ريكوست يحتاج صلاحية</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">403</span></td><td class="p-3"><code class="bg-red-100 text-red-700 px-2 py-0.5 rounded text-xs font-mono">PERMISSION_DENIED</code></td><td class="p-3 text-gray-600 text-xs" data-ar="ليس لديك الصلاحية المطلوبة" data-en="Missing required permission">ليس لديك الصلاحية المطلوبة</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs" data-ar="أي ريكوست" data-en="Any request">أي ريكوست</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">429</span></td><td class="p-3"><code class="bg-purple-100 text-purple-700 px-2 py-0.5 rounded text-xs font-mono">RATE_LIMITED</code></td><td class="p-3 text-gray-600 text-xs" data-ar="تجاوزت حد الطلبات، انتظر وأعد المحاولة" data-en="Too many requests, wait and retry">تجاوزت حد الطلبات</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs" data-ar="أي ريكوست" data-en="Any request">أي ريكوست</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">500</span></td><td class="p-3"><code class="bg-gray-200 text-gray-700 px-2 py-0.5 rounded text-xs font-mono">SERVER_ERROR</code></td><td class="p-3 text-gray-600 text-xs" data-ar="خطأ داخلي في الخادم" data-en="Internal server error">خطأ داخلي</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs" data-ar="أي ريكوست يتواصل مع Auth" data-en="Any request calling Auth service">ريكوست يتواصل مع Auth</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">503</span></td><td class="p-3"><code class="bg-gray-200 text-gray-700 px-2 py-0.5 rounded text-xs font-mono">AUTH_SERVICE_UNAVAILABLE</code></td><td class="p-3 text-gray-600 text-xs" data-ar="خدمة المصادقة غير متاحة" data-en="Auth microservice is down">Auth Service غير متاح</td></tr>

                                {{-- Events --}}
                                <tr class="bg-emerald-50/40"><td colspan="4" class="p-2 font-bold text-emerald-800 text-xs uppercase tracking-wider">📅 Events — المعارض</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs">GET /events/{event}</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">404</span></td><td class="p-3"><code class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-mono">NOT_FOUND</code></td><td class="p-3 text-gray-600 text-xs" data-ar="المعرض غير موجود" data-en="Event not found">المعرض غير موجود</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs">POST /manage/events</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">422</span></td><td class="p-3"><code class="bg-orange-100 text-orange-700 px-2 py-0.5 rounded text-xs font-mono">VALIDATION_ERROR</code></td><td class="p-3 text-gray-600 text-xs" data-ar="بيانات ناقصة: name, start_date, end_date, city_id" data-en="Missing: name, start_date, end_date, city_id">name, start_date, end_date, city_id مطلوبة</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs">POST /manage/events</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">422</span></td><td class="p-3"><code class="bg-orange-100 text-orange-700 px-2 py-0.5 rounded text-xs font-mono">DUPLICATE_ENTRY</code></td><td class="p-3 text-gray-600 text-xs" data-ar="معرض بنفس الاسم موجود" data-en="Event with same name exists">اسم مكرر</td></tr>

                                {{-- Sections & Spaces --}}
                                <tr class="bg-emerald-50/40"><td colspan="4" class="p-2 font-bold text-emerald-800 text-xs uppercase tracking-wider">📐 Sections & Spaces — الأقسام والمساحات</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs">GET /{section|space}</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">404</span></td><td class="p-3"><code class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-mono">NOT_FOUND</code></td><td class="p-3 text-gray-600 text-xs" data-ar="القسم/المساحة غير موجود" data-en="Section or space not found">غير موجود</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs">POST sections|spaces</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">422</span></td><td class="p-3"><code class="bg-orange-100 text-orange-700 px-2 py-0.5 rounded text-xs font-mono">VALIDATION_ERROR</code></td><td class="p-3 text-gray-600 text-xs" data-ar="بيانات ناقصة: name, event_id, section_id, type, price" data-en="Missing data fields">name, event_id, type, price مطلوبة</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs">POST rental-requests</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">422</span></td><td class="p-3"><code class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-mono">SPACE_UNAVAILABLE</code></td><td class="p-3 text-gray-600 text-xs" data-ar="المساحة محجوزة أو معطلة أو غير متاحة" data-en="Space booked, disabled, or unavailable">محجوزة أو غير متاحة</td></tr>

                                {{-- Business Profiles --}}
                                <tr class="bg-blue-50/40"><td colspan="4" class="p-2 font-bold text-blue-800 text-xs uppercase tracking-wider">👤 Business Profiles — الملفات التجارية</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs">GET /profile</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">404</span></td><td class="p-3"><code class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-mono">NOT_FOUND</code></td><td class="p-3 text-gray-600 text-xs" data-ar="لا يوجد ملف تجاري" data-en="No business profile yet">لا يوجد ملف تجاري</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs">POST /profile</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">422</span></td><td class="p-3"><code class="bg-orange-100 text-orange-700 px-2 py-0.5 rounded text-xs font-mono">VALIDATION_ERROR</code></td><td class="p-3 text-gray-600 text-xs" data-ar="بيانات ناقصة: company_name, commercial_register" data-en="Missing: company_name, commercial_register">company_name مطلوب</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs">POST /profile</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">422</span></td><td class="p-3"><code class="bg-orange-100 text-orange-700 px-2 py-0.5 rounded text-xs font-mono">DUPLICATE_ENTRY</code></td><td class="p-3 text-gray-600 text-xs" data-ar="الملف التجاري موجود مسبقاً" data-en="Profile already exists">ملف موجود مسبقاً</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs">rental-requests/*</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">403</span></td><td class="p-3"><code class="bg-red-100 text-red-700 px-2 py-0.5 rounded text-xs font-mono">PROFILE_NOT_VERIFIED</code></td><td class="p-3 text-gray-600 text-xs" data-ar="الملف غير موثق — مطلوب لإنشاء طلبات الإيجار" data-en="Profile not verified — required for rental requests">ملف غير موثق (مطلوب للإيجار)</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs">PUT /manage/profiles/{id}/approve</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">422</span></td><td class="p-3"><code class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-mono">INVALID_STATUS</code></td><td class="p-3 text-gray-600 text-xs" data-ar="الملف بحالة لا تسمح بالقبول" data-en="Profile status doesn't allow approval">حالة غير قابلة للقبول</td></tr>

                                {{-- Visit Requests --}}
                                <tr class="bg-indigo-50/40"><td colspan="4" class="p-2 font-bold text-indigo-800 text-xs uppercase tracking-wider">🎫 Visit Requests — طلبات الزيارة</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs">GET /visit-requests/{id}</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">404</span></td><td class="p-3"><code class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-mono">NOT_FOUND</code></td><td class="p-3 text-gray-600 text-xs" data-ar="طلب الزيارة غير موجود" data-en="Visit request not found">طلب غير موجود</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs">POST /visit-requests</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">422</span></td><td class="p-3"><code class="bg-orange-100 text-orange-700 px-2 py-0.5 rounded text-xs font-mono">VALIDATION_ERROR</code></td><td class="p-3 text-gray-600 text-xs" data-ar="بيانات ناقصة: event_id, visit_date, purpose" data-en="Missing: event_id, visit_date, purpose">event_id, visit_date مطلوبة</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs">PUT /approve | /reject</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">422</span></td><td class="p-3"><code class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-mono">INVALID_STATUS</code></td><td class="p-3 text-gray-600 text-xs" data-ar="لا يمكن قبول/رفض — الحالة ليست pending" data-en="Can't approve/reject — status not pending">حالة ليست pending</td></tr>

                                {{-- Rental Requests --}}
                                <tr class="bg-amber-50/40"><td colspan="4" class="p-2 font-bold text-amber-800 text-xs uppercase tracking-wider">🏪 Rental Requests — طلبات الإيجار</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs">GET /rental-requests/{id}</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">404</span></td><td class="p-3"><code class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-mono">NOT_FOUND</code></td><td class="p-3 text-gray-600 text-xs" data-ar="طلب الإيجار غير موجود" data-en="Rental request not found">طلب غير موجود</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs">POST /rental-requests</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">403</span></td><td class="p-3"><code class="bg-red-100 text-red-700 px-2 py-0.5 rounded text-xs font-mono">PROFILE_NOT_VERIFIED</code></td><td class="p-3 text-gray-600 text-xs" data-ar="يجب توثيق الملف التجاري أولاً" data-en="Must verify business profile first">ملف غير موثق</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs">POST /rental-requests</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">422</span></td><td class="p-3"><code class="bg-orange-100 text-orange-700 px-2 py-0.5 rounded text-xs font-mono">VALIDATION_ERROR</code></td><td class="p-3 text-gray-600 text-xs" data-ar="بيانات ناقصة: space_id, event_id" data-en="Missing: space_id, event_id">space_id, event_id مطلوبة</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs">POST /rental-requests</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">422</span></td><td class="p-3"><code class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-mono">SPACE_UNAVAILABLE</code></td><td class="p-3 text-gray-600 text-xs" data-ar="المساحة محجوزة أو غير متاحة للفترة المطلوبة" data-en="Space booked or unavailable for requested period">المساحة محجوزة</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs">PUT /approve | /reject</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">422</span></td><td class="p-3"><code class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-mono">INVALID_STATUS</code></td><td class="p-3 text-gray-600 text-xs" data-ar="لا يمكن قبول/رفض — الحالة ليست pending" data-en="Can't approve/reject — status not pending">حالة ليست pending</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs">POST /payment</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">422</span></td><td class="p-3"><code class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-mono">INVALID_STATUS</code></td><td class="p-3 text-gray-600 text-xs" data-ar="الطلب غير مقبول — لا يمكن تسجيل دفعة" data-en="Request not approved — can't record payment">طلب غير مقبول</td></tr>

                                {{-- Rental Contracts --}}
                                <tr class="bg-amber-50/40"><td colspan="4" class="p-2 font-bold text-amber-800 text-xs uppercase tracking-wider">📝 Rental Contracts — عقود الإيجار</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs">GET /rental-contracts/{id}</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">404</span></td><td class="p-3"><code class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-mono">NOT_FOUND</code></td><td class="p-3 text-gray-600 text-xs" data-ar="العقد غير موجود" data-en="Contract not found">العقد غير موجود</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs">POST /manage/rental-contracts</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">422</span></td><td class="p-3"><code class="bg-orange-100 text-orange-700 px-2 py-0.5 rounded text-xs font-mono">VALIDATION_ERROR</code></td><td class="p-3 text-gray-600 text-xs" data-ar="بيانات ناقصة: rental_request_id, terms, amount" data-en="Missing: rental_request_id, terms, amount">rental_request_id مطلوب</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs">PUT /sign</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">422</span></td><td class="p-3"><code class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-mono">CONTRACT_ALREADY_SIGNED</code></td><td class="p-3 text-gray-600 text-xs" data-ar="العقد موقع مسبقاً — لا يمكن التوقيع مرة أخرى" data-en="Contract already signed — can't sign again">العقد موقع</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs">PUT /approve|reject|terminate</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">422</span></td><td class="p-3"><code class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-mono">INVALID_STATUS</code></td><td class="p-3 text-gray-600 text-xs" data-ar="العملية غير متاحة بالحالة الحالية للعقد" data-en="Action not allowed in current contract status">حالة غير قابلة</td></tr>

                                {{-- Ratings --}}
                                <tr class="bg-yellow-50/40"><td colspan="4" class="p-2 font-bold text-yellow-800 text-xs uppercase tracking-wider">⭐ Ratings — التقييمات</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs">GET /ratings/{id}</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">404</span></td><td class="p-3"><code class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-mono">NOT_FOUND</code></td><td class="p-3 text-gray-600 text-xs" data-ar="التقييم غير موجود" data-en="Rating not found">غير موجود</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs">POST /ratings</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">422</span></td><td class="p-3"><code class="bg-orange-100 text-orange-700 px-2 py-0.5 rounded text-xs font-mono">VALIDATION_ERROR</code></td><td class="p-3 text-gray-600 text-xs" data-ar="بيانات ناقصة: rateable_type, rateable_id, rating (1-5)" data-en="Missing: rateable_type, rateable_id, rating (1-5)">rateable_id, rating مطلوبة</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs">POST /ratings</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">422</span></td><td class="p-3"><code class="bg-orange-100 text-orange-700 px-2 py-0.5 rounded text-xs font-mono">DUPLICATE_ENTRY</code></td><td class="p-3 text-gray-600 text-xs" data-ar="قيّمت هذا المورد مسبقاً" data-en="Already rated this resource">مكرر</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs">PUT /manage/approve|reject</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">422</span></td><td class="p-3"><code class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-mono">INVALID_STATUS</code></td><td class="p-3 text-gray-600 text-xs" data-ar="التقييم ليس بحالة pending" data-en="Rating not in pending status">ليس pending</td></tr>

                                {{-- Support Tickets --}}
                                <tr class="bg-teal-50/40"><td colspan="4" class="p-2 font-bold text-teal-800 text-xs uppercase tracking-wider">🎟️ Support Tickets — تذاكر الدعم</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs">GET /support-tickets/{id}</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">404</span></td><td class="p-3"><code class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-mono">NOT_FOUND</code></td><td class="p-3 text-gray-600 text-xs" data-ar="التذكرة غير موجودة" data-en="Ticket not found">غير موجودة</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs">POST /support-tickets</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">422</span></td><td class="p-3"><code class="bg-orange-100 text-orange-700 px-2 py-0.5 rounded text-xs font-mono">VALIDATION_ERROR</code></td><td class="p-3 text-gray-600 text-xs" data-ar="بيانات ناقصة: subject, message, priority" data-en="Missing: subject, message, priority">subject, message مطلوبة</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs">PUT /close | /reopen | /resolve</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">422</span></td><td class="p-3"><code class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-mono">INVALID_STATUS</code></td><td class="p-3 text-gray-600 text-xs" data-ar="لا يمكن إغلاق/إعادة فتح — الحالة لا تسمح" data-en="Can't close/reopen — status doesn't allow">حالة لا تسمح</td></tr>

                                {{-- Invoices --}}
                                <tr class="bg-green-50/40"><td colspan="4" class="p-2 font-bold text-green-800 text-xs uppercase tracking-wider">🧾 Invoices — الفواتير</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs">GET /invoices/{id}</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">404</span></td><td class="p-3"><code class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-mono">NOT_FOUND</code></td><td class="p-3 text-gray-600 text-xs" data-ar="الفاتورة غير موجودة" data-en="Invoice not found">غير موجودة</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs">POST /manage/invoices</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">422</span></td><td class="p-3"><code class="bg-orange-100 text-orange-700 px-2 py-0.5 rounded text-xs font-mono">VALIDATION_ERROR</code></td><td class="p-3 text-gray-600 text-xs" data-ar="بيانات ناقصة: rental_request_id, amount, due_date" data-en="Missing: rental_request_id, amount, due_date">amount, due_date مطلوبة</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs">PUT /issue | /mark-paid | /cancel</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">422</span></td><td class="p-3"><code class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-mono">INVALID_STATUS</code></td><td class="p-3 text-gray-600 text-xs" data-ar="الفاتورة بحالة لا تسمح بهذه العملية" data-en="Invoice status doesn't allow this action">حالة لا تسمح</td></tr>

                                {{-- Favorites --}}
                                <tr class="bg-pink-50/40"><td colspan="4" class="p-2 font-bold text-pink-800 text-xs uppercase tracking-wider">❤️ Favorites — المفضلة</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs">DELETE /favorites/{id}</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">404</span></td><td class="p-3"><code class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-mono">NOT_FOUND</code></td><td class="p-3 text-gray-600 text-xs" data-ar="العنصر غير موجود في المفضلة" data-en="Item not in favorites">غير موجود</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs">POST /favorites</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">422</span></td><td class="p-3"><code class="bg-orange-100 text-orange-700 px-2 py-0.5 rounded text-xs font-mono">DUPLICATE_ENTRY</code></td><td class="p-3 text-gray-600 text-xs" data-ar="العنصر مضاف للمفضلة مسبقاً" data-en="Already in favorites">مضاف مسبقاً</td></tr>

                                {{-- Notifications --}}
                                <tr class="bg-cyan-50/40"><td colspan="4" class="p-2 font-bold text-cyan-800 text-xs uppercase tracking-wider">🔔 Notifications — الإشعارات</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs">PUT /notifications/{id}/read</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">404</span></td><td class="p-3"><code class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-mono">NOT_FOUND</code></td><td class="p-3 text-gray-600 text-xs" data-ar="الإشعار غير موجود" data-en="Notification not found">غير موجود</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs">PUT /notifications/preferences</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">422</span></td><td class="p-3"><code class="bg-orange-100 text-orange-700 px-2 py-0.5 rounded text-xs font-mono">VALIDATION_ERROR</code></td><td class="p-3 text-gray-600 text-xs" data-ar="بيانات تفضيلات غير صالحة" data-en="Invalid preferences data">بيانات غير صالحة</td></tr>

                                {{-- Sponsors --}}
                                <tr class="bg-violet-50/40"><td colspan="4" class="p-2 font-bold text-violet-800 text-xs uppercase tracking-wider">🏅 Sponsors — الرعاة</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs">GET /manage/sponsors/{id}</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">404</span></td><td class="p-3"><code class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-mono">NOT_FOUND</code></td><td class="p-3 text-gray-600 text-xs" data-ar="الراعي غير موجود" data-en="Sponsor not found">غير موجود</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs">POST /manage/sponsors</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">422</span></td><td class="p-3"><code class="bg-orange-100 text-orange-700 px-2 py-0.5 rounded text-xs font-mono">VALIDATION_ERROR</code></td><td class="p-3 text-gray-600 text-xs" data-ar="بيانات ناقصة: user_id, event_id" data-en="Missing: user_id, event_id">user_id, event_id مطلوبة</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs">PUT /approve|activate|suspend</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">422</span></td><td class="p-3"><code class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-mono">INVALID_STATUS</code></td><td class="p-3 text-gray-600 text-xs" data-ar="انتقال حالة غير مسموح" data-en="Invalid status transition">انتقال غير مسموح</td></tr>

                                {{-- Sponsor Contracts --}}
                                <tr class="bg-violet-50/40"><td colspan="4" class="p-2 font-bold text-violet-800 text-xs uppercase tracking-wider">📜 Sponsor Contracts — عقود الرعاية</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs">GET /sponsor-contracts/{id}</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">404</span></td><td class="p-3"><code class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-mono">NOT_FOUND</code></td><td class="p-3 text-gray-600 text-xs" data-ar="عقد الرعاية غير موجود" data-en="Sponsor contract not found">غير موجود</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs">POST /manage/sponsor-contracts</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">422</span></td><td class="p-3"><code class="bg-orange-100 text-orange-700 px-2 py-0.5 rounded text-xs font-mono">VALIDATION_ERROR</code></td><td class="p-3 text-gray-600 text-xs" data-ar="بيانات ناقصة: sponsor_id, package_id, amount" data-en="Missing: sponsor_id, package_id, amount">sponsor_id, package_id مطلوبة</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs">PUT /approve|reject|complete</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">422</span></td><td class="p-3"><code class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-mono">INVALID_STATUS</code></td><td class="p-3 text-gray-600 text-xs" data-ar="العقد بحالة لا تسمح بهذه العملية" data-en="Contract status doesn't allow this">حالة لا تسمح</td></tr>

                                {{-- Sponsor Payments --}}
                                <tr class="bg-violet-50/40"><td colspan="4" class="p-2 font-bold text-violet-800 text-xs uppercase tracking-wider">💰 Sponsor Payments — مدفوعات الرعاية</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs">GET /sponsor-payments/{id}</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">404</span></td><td class="p-3"><code class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-mono">NOT_FOUND</code></td><td class="p-3 text-gray-600 text-xs" data-ar="الدفعة غير موجودة" data-en="Payment not found">غير موجودة</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs">PUT /mark-paid</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">422</span></td><td class="p-3"><code class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-mono">INVALID_STATUS</code></td><td class="p-3 text-gray-600 text-xs" data-ar="الدفعة مدفوعة مسبقاً" data-en="Payment already marked as paid">مدفوعة مسبقاً</td></tr>

                                {{-- Sponsor Benefits & Assets --}}
                                <tr class="bg-violet-50/40"><td colspan="4" class="p-2 font-bold text-violet-800 text-xs uppercase tracking-wider">🎁 Sponsor Benefits & Assets — المزايا والأصول</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs">GET /sponsor-benefits/{id}</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">404</span></td><td class="p-3"><code class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-mono">NOT_FOUND</code></td><td class="p-3 text-gray-600 text-xs" data-ar="الميزة/الأصل غير موجود" data-en="Benefit/asset not found">غير موجود</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs">PUT /deliver</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">422</span></td><td class="p-3"><code class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-mono">INVALID_STATUS</code></td><td class="p-3 text-gray-600 text-xs" data-ar="الميزة مسلّمة مسبقاً" data-en="Benefit already delivered">مسلّمة مسبقاً</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs">PUT /approve|reject (assets)</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">422</span></td><td class="p-3"><code class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-mono">INVALID_STATUS</code></td><td class="p-3 text-gray-600 text-xs" data-ar="الأصل ليس بحالة pending" data-en="Asset not in pending status">ليس pending</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs">POST /my/sponsor-assets</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">422</span></td><td class="p-3"><code class="bg-orange-100 text-orange-700 px-2 py-0.5 rounded text-xs font-mono">VALIDATION_ERROR</code></td><td class="p-3 text-gray-600 text-xs" data-ar="بيانات ناقصة: type, file, sponsor_contract_id" data-en="Missing: type, file, sponsor_contract_id">type, file مطلوبة</td></tr>

                                {{-- CMS Content --}}
                                <tr class="bg-gray-100/50"><td colspan="4" class="p-2 font-bold text-gray-800 text-xs uppercase tracking-wider">📄 CMS — الصفحات والأسئلة والبانرات</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs">GET /pages/{slug} | /faqs/{id}</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">404</span></td><td class="p-3"><code class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-mono">NOT_FOUND</code></td><td class="p-3 text-gray-600 text-xs" data-ar="المحتوى غير موجود" data-en="Content not found">غير موجود</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs">POST pages|faqs|banners</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">422</span></td><td class="p-3"><code class="bg-orange-100 text-orange-700 px-2 py-0.5 rounded text-xs font-mono">VALIDATION_ERROR</code></td><td class="p-3 text-gray-600 text-xs" data-ar="بيانات ناقصة حسب النوع" data-en="Missing data depending on type">بيانات ناقصة</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs">POST /manage/pages</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">422</span></td><td class="p-3"><code class="bg-orange-100 text-orange-700 px-2 py-0.5 rounded text-xs font-mono">DUPLICATE_ENTRY</code></td><td class="p-3 text-gray-600 text-xs" data-ar="الرابط (slug) مستخدم مسبقاً" data-en="Slug already taken">slug مكرر</td></tr>

                                {{-- Categories / Cities / Settings --}}
                                <tr class="bg-gray-100/50"><td colspan="4" class="p-2 font-bold text-gray-800 text-xs uppercase tracking-wider">⚙️ Lookups — فئات / مدن / إعدادات</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs">GET /{id}</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">404</span></td><td class="p-3"><code class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-mono">NOT_FOUND</code></td><td class="p-3 text-gray-600 text-xs" data-ar="غير موجود" data-en="Not found">غير موجود</td></tr>
                                <tr class="border-b"><td class="p-3 text-gray-500 text-xs">POST categories|cities</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">422</span></td><td class="p-3"><code class="bg-orange-100 text-orange-700 px-2 py-0.5 rounded text-xs font-mono">VALIDATION_ERROR</code></td><td class="p-3 text-gray-600 text-xs" data-ar="بيانات ناقصة: name مطلوب" data-en="Missing: name required">name مطلوب</td></tr>
                                <tr><td class="p-3 text-gray-500 text-xs">POST categories|cities</td><td class="p-3"><span class="font-mono text-gray-500 text-xs">422</span></td><td class="p-3"><code class="bg-orange-100 text-orange-700 px-2 py-0.5 rounded text-xs font-mono">DUPLICATE_ENTRY</code></td><td class="p-3 text-gray-600 text-xs" data-ar="الاسم مستخدم مسبقاً" data-en="Name already taken">اسم مكرر</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- Permissions Reference --}}
                <section id="permissions-ref" class="mb-20">
                    <h2 class="text-3xl font-bold mb-6" data-ar="جدول الصلاحيات الكامل" data-en="Full Permissions Map">جدول الصلاحيات</h2>
                    <p class="text-gray-500 mb-6" data-ar="جميع الصلاحيات المستخدمة في هذه الخدمة (تُدار من Auth Service)" data-en="All permissions used by this service (managed in Auth Service)">جميع الصلاحيات المستخدمة</p>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right" data-ar="المورد" data-en="Resource">المورد</th><th class="p-3 text-right" data-ar="الصلاحيات" data-en="Permissions">الصلاحيات</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3 font-semibold">events</td><td class="p-3"><code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">view</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">create</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">update</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">delete</code></td></tr>
                                <tr class="border-b"><td class="p-3 font-semibold">sections</td><td class="p-3"><code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">view</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">create</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">update</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">delete</code></td></tr>
                                <tr class="border-b"><td class="p-3 font-semibold">spaces</td><td class="p-3"><code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">view</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">create</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">update</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">delete</code></td></tr>
                                <tr class="border-b"><td class="p-3 font-semibold">expo-services</td><td class="p-3"><code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">view</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">create</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">update</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">delete</code></td></tr>
                                <tr class="border-b"><td class="p-3 font-semibold">categories</td><td class="p-3"><code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">view</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">create</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">update</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">delete</code></td></tr>
                                <tr class="border-b"><td class="p-3 font-semibold">cities</td><td class="p-3"><code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">view</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">create</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">update</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">delete</code></td></tr>
                                <tr class="border-b"><td class="p-3 font-semibold">settings</td><td class="p-3"><code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">view</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">update</code></td></tr>
                                <tr class="border-b"><td class="p-3 font-semibold">profiles</td><td class="p-3"><code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">view-all</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">approve</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">reject</code></td></tr>
                                <tr class="border-b"><td class="p-3 font-semibold">visit-requests</td><td class="p-3"><code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">view</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">create</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">update</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">delete</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">view-all</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">approve</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">reject</code></td></tr>
                                <tr class="border-b"><td class="p-3 font-semibold">rental-requests</td><td class="p-3"><code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">view</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">create</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">update</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">delete</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">view-all</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">approve</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">reject</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">record-payment</code></td></tr>
                                <tr class="border-b"><td class="p-3 font-semibold">rental-contracts</td><td class="p-3"><code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">view</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">view-all</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">create</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">update</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">approve</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">reject</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">terminate</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">sign</code></td></tr>
                                <tr class="border-b"><td class="p-3 font-semibold">payments</td><td class="p-3"><code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">view</code></td></tr>
                                <tr class="border-b"><td class="p-3 font-semibold">sponsors</td><td class="p-3"><code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">view-all</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">create</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">update</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">delete</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">approve</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">reject</code></td></tr>
                                <tr class="border-b"><td class="p-3 font-semibold">sponsor-packages</td><td class="p-3"><code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">view</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">create</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">update</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">delete</code></td></tr>
                                <tr class="border-b"><td class="p-3 font-semibold">sponsor-contracts</td><td class="p-3"><code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">view</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">view-all</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">create</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">update</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">approve</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">reject</code></td></tr>
                                <tr class="border-b"><td class="p-3 font-semibold">sponsor-payments</td><td class="p-3"><code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">view</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">view-all</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">create</code></td></tr>
                                <tr class="border-b"><td class="p-3 font-semibold">sponsor-benefits</td><td class="p-3"><code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">view</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">create</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">update</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">deliver</code></td></tr>
                                <tr class="border-b"><td class="p-3 font-semibold">sponsor-assets</td><td class="p-3"><code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">view</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">create</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">update</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">delete</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">approve</code></td></tr>
                                <tr class="border-b"><td class="p-3 font-semibold">sponsor-exposure</td><td class="p-3"><code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">view</code></td></tr>
                                <tr class="border-b"><td class="p-3 font-semibold">ratings</td><td class="p-3"><code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">create</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">update</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">delete</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">view-all</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">approve</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">reject</code></td></tr>
                                <tr class="border-b"><td class="p-3 font-semibold">support-tickets</td><td class="p-3"><code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">view</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">create</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">view-all</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">assign</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">reply</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">close</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">delete</code></td></tr>
                                <tr class="border-b"><td class="p-3 font-semibold">invoices</td><td class="p-3"><code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">view</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">view-all</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">create</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">update</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">issue</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">mark-paid</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">cancel</code></td></tr>
                                <tr class="border-b"><td class="p-3 font-semibold">notifications</td><td class="p-3"><code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">view</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">update</code></td></tr>
                                <tr class="border-b"><td class="p-3 font-semibold">notification-preferences</td><td class="p-3"><code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">view</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">update</code></td></tr>
                                <tr class="border-b"><td class="p-3 font-semibold">reports</td><td class="p-3"><code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">view</code></td></tr>
                                <tr><td class="p-3 font-semibold">pages / faqs / banners</td><td class="p-3"><code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">view</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">create</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">update</code> <code class="bg-gray-100 px-1.5 py-0.5 rounded text-xs">delete</code></td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

            </div>
        </main>
    </div>

    <script>
        let currentLang = 'ar';

        function toggleLang() {
            currentLang = currentLang === 'ar' ? 'en' : 'ar';
            const html = document.documentElement;
            html.setAttribute('dir', currentLang === 'ar' ? 'rtl' : 'ltr');
            html.setAttribute('lang', currentLang);
            document.getElementById('langLabel').textContent = currentLang === 'ar' ? 'English' : 'العربية';

            const sidebar = document.getElementById('sidebar');
            if (currentLang === 'ar') {
                sidebar.classList.remove('left-0');
                sidebar.classList.add('right-0');
                sidebar.style.borderLeft = '1px solid #e5e7eb';
                sidebar.style.borderRight = 'none';
                document.querySelector('main').style.marginRight = '18rem';
                document.querySelector('main').style.marginLeft = '0';
            } else {
                sidebar.classList.remove('right-0');
                sidebar.classList.add('left-0');
                sidebar.style.borderRight = '1px solid #e5e7eb';
                sidebar.style.borderLeft = 'none';
                document.querySelector('main').style.marginLeft = '18rem';
                document.querySelector('main').style.marginRight = '0';
            }

            document.querySelectorAll('[data-ar][data-en]').forEach(el => {
                el.textContent = el.getAttribute('data-' + currentLang);
            });
        }

        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });

        // Active link
        const sections = document.querySelectorAll('section[id]');
        const navLinks = document.querySelectorAll('.sidebar-link');
        window.addEventListener('scroll', () => {
            let current = '';
            sections.forEach(section => {
                if (scrollY >= section.offsetTop - 120) current = section.getAttribute('id');
            });
            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === '#' + current) link.classList.add('active');
            });
        });
    </script>
</body>
</html>
