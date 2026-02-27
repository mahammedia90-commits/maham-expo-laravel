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
        .sidebar-link.active, .sidebar-link:hover { border-color: #10B981; background: #ecfdf5; color: #047857; }
        .param-table th { font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em; }
        .badge { font-size: 10px; padding: 2px 8px; border-radius: 9999px; font-weight: 600; letter-spacing: 0.05em; }
        .copy-btn { cursor: pointer; opacity: 0.6; transition: opacity 0.15s; }
        .copy-btn:hover { opacity: 1; }
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
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-lg font-bold leading-tight">Maham Expo API</h1>
                    <p class="text-xs text-gray-400 leading-tight">API Documentation</p>
                </div>
                <span class="badge bg-emerald-500/20 text-emerald-400 border border-emerald-500/20 mr-2">v{{ config('app.version', '1.0.0') }}</span>
            </div>
            <div class="flex items-center gap-4">
                <button onclick="toggleLang()" id="langBtn" class="flex items-center gap-2 bg-gray-800 hover:bg-gray-700 px-3 py-1.5 rounded-lg text-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/></svg>
                    <span id="langLabel">English</span>
                </button>
                <a href="{{ url('/api/health') }}" target="_blank" class="text-sm text-gray-400 hover:text-emerald-400 transition flex items-center gap-1">
                    <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
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
                        <li><a href="#quick-start" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="البدء السريع" data-en="Quick Start">البدء السريع</a></li>
                        <li><a href="#headers" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="الهيدرات" data-en="Headers">الهيدرات</a></li>
                        <li><a href="#authentication" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="المصادقة" data-en="Authentication">المصادقة</a></li>
                        <li><a href="#response-format" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="صيغة الردود" data-en="Response Format">صيغة الردود</a></li>
                        <li><a href="#errors" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="الأخطاء" data-en="Errors">الأخطاء</a></li>
                        <li><a href="#rate-limiting" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="حد الطلبات" data-en="Rate Limiting">حد الطلبات</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-3" data-ar="العامة" data-en="Public">العامة</h3>
                    <ul class="space-y-0.5">
                        <li><a href="#categories" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg"><span class="badge method-get text-white ml-1">GET</span> <span data-ar="التصنيفات" data-en="Categories">التصنيفات</span></a></li>
                        <li><a href="#cities" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg"><span class="badge method-get text-white ml-1">GET</span> <span data-ar="المدن" data-en="Cities">المدن</span></a></li>
                        <li><a href="#events" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg"><span class="badge method-get text-white ml-1">GET</span> <span data-ar="الفعاليات" data-en="Events">الفعاليات</span></a></li>
                        <li><a href="#spaces" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg"><span class="badge method-get text-white ml-1">GET</span> <span data-ar="المساحات" data-en="Spaces">المساحات</span></a></li>
                        <li><a href="#services-public" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg"><span class="badge method-get text-white ml-1">GET</span> <span data-ar="الخدمات" data-en="Services">الخدمات</span></a></li>
                        <li><a href="#statistics" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg"><span class="badge method-get text-white ml-1">GET</span> <span data-ar="الإحصائيات" data-en="Statistics">الإحصائيات</span></a></li>
                        <li><a href="#sponsors-public" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="الرعاة" data-en="Sponsors">الرعاة</a></li>
                        <li><a href="#ratings-public" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="التقييمات" data-en="Ratings">التقييمات</a></li>
                        <li><a href="#cms-public" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="صفحات و أسئلة وبانرات" data-en="Pages, FAQs & Banners">صفحات وأسئلة وبانرات</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-3" data-ar="المستخدم" data-en="User">المستخدم</h3>
                    <ul class="space-y-0.5">
                        <li><a href="#profile" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="الملف التجاري" data-en="Business Profile">الملف التجاري</a></li>
                        <li><a href="#favorites" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="المفضلة" data-en="Favorites">المفضلة</a></li>
                        <li><a href="#notifications" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="الإشعارات" data-en="Notifications">الإشعارات</a></li>
                        <li><a href="#ratings-user" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="التقييمات" data-en="Ratings">التقييمات</a></li>
                        <li><a href="#support-tickets" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="تذاكر الدعم" data-en="Support Tickets">تذاكر الدعم</a></li>
                        <li><a href="#invoices-user" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="الفواتير" data-en="Invoices">الفواتير</a></li>
                        <li><a href="#visit-requests" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="طلبات الزيارة" data-en="Visit Requests">طلبات الزيارة</a></li>
                        <li><a href="#rental-requests" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="طلبات الإيجار" data-en="Rental Requests">طلبات الإيجار</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-3" data-ar="الإدارة" data-en="Admin">الإدارة</h3>
                    <ul class="space-y-0.5">
                        <li><a href="#admin-dashboard" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg"><span class="badge method-get text-white ml-1">GET</span> <span data-ar="لوحة التحكم" data-en="Dashboard">لوحة التحكم</span></a></li>
                        <li><a href="#admin-events" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="إدارة الفعاليات" data-en="Events">إدارة الفعاليات</a></li>
                        <li><a href="#admin-sections" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="الأقسام" data-en="Sections">الأقسام</a></li>
                        <li><a href="#admin-spaces" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="المساحات" data-en="Spaces">المساحات</a></li>
                        <li><a href="#admin-services" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="الخدمات" data-en="Services">الخدمات</a></li>
                        <li><a href="#admin-visit-requests" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="طلبات الزيارة" data-en="Visit Requests">طلبات الزيارة</a></li>
                        <li><a href="#admin-rental-requests" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="طلبات الإيجار" data-en="Rental Requests">طلبات الإيجار</a></li>
                        <li><a href="#admin-profiles" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="الملفات التجارية" data-en="Profiles">الملفات التجارية</a></li>
                        <li><a href="#admin-sponsors" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="الرعاة" data-en="Sponsors">الرعاة</a></li>
                        <li><a href="#admin-ratings" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="التقييمات" data-en="Ratings">التقييمات</a></li>
                        <li><a href="#admin-tickets" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="التذاكر" data-en="Tickets">التذاكر</a></li>
                        <li><a href="#admin-contracts" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="العقود والفواتير" data-en="Contracts & Invoices">العقود والفواتير</a></li>
                        <li><a href="#admin-cms" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="إدارة المحتوى" data-en="CMS">إدارة المحتوى</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-3" data-ar="الأدوار" data-en="Roles">الأدوار</h3>
                    <ul class="space-y-0.5">
                        <li><a href="#supervisor-api" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="المشرف" data-en="Supervisor">المشرف</a></li>
                        <li><a href="#superadmin-api" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="المدير العام" data-en="Super Admin">المدير العام</a></li>
                        <li><a href="#investor-api" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="المستثمر" data-en="Investor">المستثمر</a></li>
                        <li><a href="#sponsor-api" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="الراعي" data-en="Sponsor">الراعي</a></li>
                        <li><a href="#merchant-api" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="التاجر" data-en="Merchant">التاجر</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-3" data-ar="المرجع" data-en="Reference">المرجع</h3>
                    <ul class="space-y-0.5">
                        <li><a href="#error-codes" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="رموز الأخطاء" data-en="Error Codes">رموز الأخطاء</a></li>
                        <li><a href="#request-statuses" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="حالات الطلبات" data-en="Request Statuses">حالات الطلبات</a></li>
                    </ul>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 mr-72 p-8 min-h-screen">
            <div class="max-w-4xl mx-auto">

                {{-- INTRODUCTION --}}
                <section id="introduction" class="mb-20">
                    <div class="mb-8">
                        <h1 class="text-4xl font-extrabold mb-3" data-ar="منصة المعارض والفعاليات" data-en="Exhibitions & Events Platform">منصة المعارض والفعاليات</h1>
                        <p class="text-lg text-gray-500" data-ar="إدارة المعارض، المساحات، طلبات الإيجار والزيارات" data-en="Events management, spaces, rental & visit requests">إدارة المعارض، المساحات، طلبات الإيجار والزيارات</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                        <div class="bg-white rounded-xl p-5 border border-gray-200">
                            <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center mb-3">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <h3 class="font-bold mb-1" data-ar="إدارة المعارض" data-en="Events Management">إدارة المعارض</h3>
                            <p class="text-sm text-gray-500" data-ar="فعاليات، أقسام ومساحات" data-en="Events, sections & spaces">فعاليات، أقسام ومساحات</p>
                        </div>
                        <div class="bg-white rounded-xl p-5 border border-gray-200">
                            <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center mb-3">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </div>
                            <h3 class="font-bold mb-1" data-ar="نظام الإيجار" data-en="Rental System">نظام الإيجار</h3>
                            <p class="text-sm text-gray-500" data-ar="طلبات إيجار ومدفوعات" data-en="Rental requests & payments">طلبات إيجار ومدفوعات</p>
                        </div>
                        <div class="bg-white rounded-xl p-5 border border-gray-200">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mb-3">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/></svg>
                            </div>
                            <h3 class="font-bold mb-1" data-ar="متعدد اللغات" data-en="Multi-Language">متعدد اللغات</h3>
                            <p class="text-sm text-gray-500" data-ar="عربي وإنجليزي عبر Accept-Language" data-en="Arabic & English via Accept-Language">عربي وإنجليزي عبر Accept-Language</p>
                        </div>
                    </div>
                    <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-5 mb-6">
                        <h4 class="font-bold text-emerald-800 mb-2">Base URL</h4>
                        <code class="text-emerald-700 text-lg">{{ url('/api') }}</code>
                    </div>
                    <div class="code-block">
                        <div class="code-header">
                            <span>Health Check</span>
                            <span class="badge method-get text-white">GET /health</span>
                        </div>
                        <pre><code>{
  <span class="json-key">"status"</span>: <span class="json-string">"ok"</span>,
  <span class="json-key">"service"</span>: <span class="json-string">"{{ config('app.name', 'expo-api') }}"</span>,
  <span class="json-key">"version"</span>: <span class="json-string">"{{ config('app.version', '1.0.0') }}"</span>,
  <span class="json-key">"timestamp"</span>: <span class="json-string">"{{ now()->toISOString() }}"</span>
}</code></pre>
                    </div>
                </section>

                {{-- QUICK START --}}
                <section id="quick-start" class="mb-20">
                    <h2 class="text-3xl font-bold mb-4" data-ar="البدء السريع" data-en="Quick Start">البدء السريع</h2>
                    <p class="text-gray-600 mb-6" data-ar="اتبع هذه الخطوات للبدء باستخدام API" data-en="Follow these steps to get started with the API">اتبع هذه الخطوات للبدء باستخدام API</p>
                    <div class="space-y-4">
                        <div class="bg-white border border-gray-200 rounded-xl p-5">
                            <div class="flex items-center gap-3 mb-3">
                                <span class="w-8 h-8 bg-emerald-500 text-white rounded-full flex items-center justify-center font-bold text-sm">1</span>
                                <h4 class="font-semibold" data-ar="سجل دخول عبر Auth Service" data-en="Login via Auth Service">سجل دخول عبر Auth Service</h4>
                            </div>
                            <div class="code-block"><pre><code>curl -X POST http://localhost:8001/api/auth/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"identifier":"admin@auth-service.local","password":"password","service_name":"expo-app"}'</code></pre></div>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-xl p-5">
                            <div class="flex items-center gap-3 mb-3">
                                <span class="w-8 h-8 bg-emerald-500 text-white rounded-full flex items-center justify-center font-bold text-sm">2</span>
                                <h4 class="font-semibold" data-ar="تصفح الفعاليات" data-en="Browse Events">تصفح الفعاليات</h4>
                            </div>
                            <div class="code-block"><pre><code>curl {{ url('/api') }}/events \
  -H "Accept: application/json"</code></pre></div>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-xl p-5">
                            <div class="flex items-center gap-3 mb-3">
                                <span class="w-8 h-8 bg-emerald-500 text-white rounded-full flex items-center justify-center font-bold text-sm">3</span>
                                <h4 class="font-semibold" data-ar="أنشئ طلب زيارة (مصادق)" data-en="Create Visit Request (authenticated)">أنشئ طلب زيارة (مصادق)</h4>
                            </div>
                            <div class="code-block"><pre><code>curl -X POST {{ url('/api') }}/visit-requests \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -d '{"event_id":"event-uuid","visit_date":"2025-06-15","visitors_count":3}'</code></pre></div>
                        </div>
                    </div>
                </section>

                {{-- HEADERS --}}
                <section id="headers" class="mb-20">
                    <h2 class="text-3xl font-bold mb-4" data-ar="الهيدرات المطلوبة" data-en="Required Headers">الهيدرات المطلوبة</h2>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200 mb-6">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50 border-b">
                                    <th class="p-4 text-right text-sm font-semibold text-gray-500" data-ar="الهيدر" data-en="Header">الهيدر</th>
                                    <th class="p-4 text-right text-sm font-semibold text-gray-500" data-ar="القيمة" data-en="Value">القيمة</th>
                                    <th class="p-4 text-right text-sm font-semibold text-gray-500" data-ar="مطلوب" data-en="Required">مطلوب</th>
                                    <th class="p-4 text-right text-sm font-semibold text-gray-500" data-ar="الوصف" data-en="Description">الوصف</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b"><td class="p-4"><code class="bg-gray-100 px-2 py-1 rounded text-sm">Content-Type</code></td><td class="p-4"><code class="text-sm">application/json</code></td><td class="p-4"><span class="text-red-500 font-bold">*</span></td><td class="p-4 text-sm text-gray-600" data-ar="نوع المحتوى المرسل" data-en="Content type of request body">نوع المحتوى المرسل</td></tr>
                                <tr class="border-b"><td class="p-4"><code class="bg-gray-100 px-2 py-1 rounded text-sm">Accept</code></td><td class="p-4"><code class="text-sm">application/json</code></td><td class="p-4"><span class="text-red-500 font-bold">*</span></td><td class="p-4 text-sm text-gray-600" data-ar="نوع المحتوى المطلوب" data-en="Expected response format">نوع المحتوى المطلوب</td></tr>
                                <tr class="border-b"><td class="p-4"><code class="bg-gray-100 px-2 py-1 rounded text-sm">Authorization</code></td><td class="p-4"><code class="text-sm">Bearer {token}</code></td><td class="p-4 text-yellow-600" data-ar="للمحمية" data-en="Protected">للمحمية</td><td class="p-4 text-sm text-gray-600" data-ar="توكن JWT من Auth Service" data-en="JWT token from Auth Service">توكن JWT من Auth Service</td></tr>
                                <tr><td class="p-4"><code class="bg-gray-100 px-2 py-1 rounded text-sm">Accept-Language</code></td><td class="p-4"><code class="text-sm">ar</code> | <code class="text-sm">en</code></td><td class="p-4 text-gray-400" data-ar="اختياري" data-en="Optional">اختياري</td><td class="p-4 text-sm text-gray-600" data-ar="لغة الردود (الافتراضي: en)" data-en="Response language (default: en)">لغة الردود (الافتراضي: en)</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- AUTHENTICATION --}}
                <section id="authentication" class="mb-20">
                    <h2 class="text-3xl font-bold mb-4" data-ar="المصادقة" data-en="Authentication">المصادقة</h2>
                    <p class="text-gray-600 mb-6" data-ar="Expo API تعتمد على Auth Service للمصادقة. يتم التحقق من التوكن عبر تواصل S2S." data-en="Expo API relies on Auth Service for authentication. Token is verified via S2S communication.">Expo API تعتمد على Auth Service للمصادقة. يتم التحقق من التوكن عبر تواصل S2S.</p>
                    <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-5 mb-6">
                        <h4 class="font-bold text-emerald-800 mb-2" data-ar="كيف تعمل المصادقة" data-en="How Authentication Works">كيف تعمل المصادقة</h4>
                        <ol class="text-sm text-emerald-700 space-y-1 list-decimal list-inside">
                            <li data-ar="سجل دخول عبر Auth Service واحصل على JWT Token" data-en="Login via Auth Service to get JWT Token">سجل دخول عبر Auth Service واحصل على JWT Token</li>
                            <li data-ar="أرسل التوكن مع كل طلب في Header: Authorization: Bearer {token}" data-en="Send token with every request in Header: Authorization: Bearer {token}">أرسل التوكن مع كل طلب في Header: Authorization: Bearer {token}</li>
                            <li data-ar="Expo API يتحقق من التوكن عبر Auth Service تلقائياً" data-en="Expo API verifies token via Auth Service automatically">Expo API يتحقق من التوكن عبر Auth Service تلقائياً</li>
                        </ol>
                    </div>
                    <div class="code-block">
                        <div class="code-header"><span data-ar="مثال الهيدر" data-en="Header Example">مثال الهيدر</span></div>
                        <pre><code>Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...</code></pre>
                    </div>
                </section>

                {{-- RESPONSE FORMAT --}}
                <section id="response-format" class="mb-20">
                    <h2 class="text-3xl font-bold mb-4" data-ar="صيغة الردود" data-en="Response Format">صيغة الردود</h2>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
                        <div class="code-block">
                            <div class="code-header"><span data-ar="رد ناجح" data-en="Success Response">رد ناجح</span><span class="badge bg-green-500/30 text-green-300">2xx</span></div>
                            <pre><code>{
  <span class="json-key">"success"</span>: <span class="json-bool">true</span>,
  <span class="json-key">"message"</span>: <span class="json-string">"Operation successful"</span>,
  <span class="json-key">"data"</span>: { ... }
}</code></pre>
                        </div>
                        <div class="code-block">
                            <div class="code-header"><span data-ar="رد خطأ" data-en="Error Response">رد خطأ</span><span class="badge bg-red-500/30 text-red-300">4xx/5xx</span></div>
                            <pre><code>{
  <span class="json-key">"success"</span>: <span class="json-bool">false</span>,
  <span class="json-key">"message"</span>: <span class="json-string">"Error message"</span>,
  <span class="json-key">"errors"</span>: { ... }
}</code></pre>
                        </div>
                    </div>
                </section>

                {{-- ==================== PUBLIC ENDPOINTS ==================== --}}

                {{-- CATEGORIES --}}
                <section id="categories" class="mb-20">
                    <h2 class="text-3xl font-bold mb-6" data-ar="التصنيفات" data-en="Categories">التصنيفات</h2>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right" data-ar="الطريقة" data-en="Method">الطريقة</th><th class="p-3 text-right" data-ar="المسار" data-en="Endpoint">المسار</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th><th class="p-3 text-right" data-ar="الحماية" data-en="Auth">الحماية</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/categories</code></td><td class="p-3" data-ar="قائمة التصنيفات" data-en="List categories">قائمة التصنيفات</td><td class="p-3"><span class="badge bg-green-100 text-green-700">Public</span></td></tr>
                                <tr><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/categories/{category}</code></td><td class="p-3" data-ar="تفاصيل تصنيف" data-en="Show category">تفاصيل تصنيف</td><td class="p-3"><span class="badge bg-green-100 text-green-700">Public</span></td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- CITIES --}}
                <section id="cities" class="mb-20">
                    <h2 class="text-3xl font-bold mb-6" data-ar="المدن" data-en="Cities">المدن</h2>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right" data-ar="الطريقة" data-en="Method">الطريقة</th><th class="p-3 text-right" data-ar="المسار" data-en="Endpoint">المسار</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th><th class="p-3 text-right" data-ar="الحماية" data-en="Auth">الحماية</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/cities</code></td><td class="p-3" data-ar="قائمة المدن" data-en="List cities">قائمة المدن</td><td class="p-3"><span class="badge bg-green-100 text-green-700">Public</span></td></tr>
                                <tr><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/cities/{city}</code></td><td class="p-3" data-ar="تفاصيل مدينة" data-en="Show city">تفاصيل مدينة</td><td class="p-3"><span class="badge bg-green-100 text-green-700">Public</span></td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- EVENTS --}}
                <section id="events" class="mb-20">
                    <h2 class="text-3xl font-bold mb-6" data-ar="الفعاليات" data-en="Events">الفعاليات</h2>

                    <div class="endpoint-card mb-6">
                        <div class="bg-emerald-50 border-b border-emerald-100 p-5 flex items-center gap-3">
                            <span class="badge method-get text-white text-xs">GET</span>
                            <code class="text-gray-800 font-semibold">/events</code>
                            <span class="mr-auto text-sm text-gray-500" data-ar="قائمة الفعاليات مع فلترة" data-en="List events with filters">قائمة الفعاليات مع فلترة</span>
                            <span class="badge bg-green-100 text-green-700">Public</span>
                        </div>
                        <div class="p-5 space-y-6">
                            <div>
                                <h4 class="font-semibold mb-3 text-sm text-gray-500 uppercase tracking-wider" data-ar="Query Parameters" data-en="Query Parameters">Query Parameters</h4>
                                <div class="overflow-x-auto">
                                    <table class="param-table w-full text-sm">
                                        <thead><tr class="border-b bg-gray-50"><th class="p-3 text-right" data-ar="الحقل" data-en="Field">الحقل</th><th class="p-3 text-right" data-ar="النوع" data-en="Type">النوع</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th></tr></thead>
                                        <tbody>
                                            <tr class="border-b"><td class="p-3"><code>search</code></td><td class="p-3">string</td><td class="p-3" data-ar="البحث بالاسم" data-en="Search by name">البحث بالاسم</td></tr>
                                            <tr class="border-b"><td class="p-3"><code>category_id</code></td><td class="p-3">uuid</td><td class="p-3" data-ar="فلتر حسب التصنيف" data-en="Filter by category">فلتر حسب التصنيف</td></tr>
                                            <tr class="border-b"><td class="p-3"><code>city_id</code></td><td class="p-3">uuid</td><td class="p-3" data-ar="فلتر حسب المدينة" data-en="Filter by city">فلتر حسب المدينة</td></tr>
                                            <tr class="border-b"><td class="p-3"><code>status</code></td><td class="p-3">string</td><td class="p-3" data-ar="فلتر حسب الحالة" data-en="Filter by status">فلتر حسب الحالة</td></tr>
                                            <tr class="border-b"><td class="p-3"><code>featured</code></td><td class="p-3">boolean</td><td class="p-3" data-ar="الفعاليات المميزة فقط" data-en="Featured events only">الفعاليات المميزة فقط</td></tr>
                                            <tr class="border-b"><td class="p-3"><code>start_date</code></td><td class="p-3">date</td><td class="p-3" data-ar="فلتر تاريخ البداية (من)" data-en="Filter start date (from)">فلتر تاريخ البداية (من)</td></tr>
                                            <tr class="border-b"><td class="p-3"><code>end_date</code></td><td class="p-3">date</td><td class="p-3" data-ar="فلتر تاريخ النهاية (إلى)" data-en="Filter end date (to)">فلتر تاريخ النهاية (إلى)</td></tr>
                                            <tr class="border-b"><td class="p-3"><code>rental_duration</code></td><td class="p-3">string</td><td class="p-3" data-ar="فلتر مدة الإيجار" data-en="Filter by rental duration">فلتر مدة الإيجار</td></tr>
                                            <tr class="border-b"><td class="p-3"><code>min_price</code></td><td class="p-3">numeric</td><td class="p-3" data-ar="الحد الأدنى للسعر" data-en="Minimum price">الحد الأدنى للسعر</td></tr>
                                            <tr class="border-b"><td class="p-3"><code>max_price</code></td><td class="p-3">numeric</td><td class="p-3" data-ar="الحد الأعلى للسعر" data-en="Maximum price">الحد الأعلى للسعر</td></tr>
                                            <tr class="border-b"><td class="p-3"><code>min_area</code></td><td class="p-3">numeric</td><td class="p-3" data-ar="الحد الأدنى للمساحة (م²)" data-en="Minimum area (sqm)">الحد الأدنى للمساحة (م²)</td></tr>
                                            <tr class="border-b"><td class="p-3"><code>max_area</code></td><td class="p-3">numeric</td><td class="p-3" data-ar="الحد الأعلى للمساحة (م²)" data-en="Maximum area (sqm)">الحد الأعلى للمساحة (م²)</td></tr>
                                            <tr class="border-b"><td class="p-3"><code>sort</code></td><td class="p-3">string</td><td class="p-3" data-ar="ترتيب النتائج" data-en="Sort results">ترتيب النتائج</td></tr>
                                            <tr><td class="p-3"><code>per_page</code></td><td class="p-3">integer</td><td class="p-3" data-ar="عدد النتائج (افتراضي: 15، أقصى: 50)" data-en="Results per page (default: 15, max: 50)">عدد النتائج (افتراضي: 15، أقصى: 50)</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="code-block">
                                <div class="code-header"><span>Response</span><span class="badge bg-green-500/30 text-green-300">200</span></div>
                                <pre><code>{
  <span class="json-key">"success"</span>: <span class="json-bool">true</span>,
  <span class="json-key">"data"</span>: {
    <span class="json-key">"data"</span>: [
      { <span class="json-key">"id"</span>: <span class="json-string">"uuid"</span>, <span class="json-key">"name"</span>: <span class="json-string">"معرض الرياض"</span>, ... }
    ],
    <span class="json-key">"meta"</span>: { <span class="json-key">"current_page"</span>: <span class="json-number">1</span>, <span class="json-key">"last_page"</span>: <span class="json-number">5</span>, <span class="json-key">"total"</span>: <span class="json-number">50</span> }
  }
}</code></pre>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/events/featured</code></td><td class="p-3" data-ar="الفعاليات المميزة" data-en="Featured events">الفعاليات المميزة</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/events/{event}</code></td><td class="p-3" data-ar="تفاصيل فعالية" data-en="Event details">تفاصيل فعالية</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/events/{event}/spaces</code></td><td class="p-3" data-ar="مساحات الفعالية" data-en="Event spaces">مساحات الفعالية</td></tr>
                                <tr><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/events/{event}/sections</code></td><td class="p-3" data-ar="أقسام الفعالية" data-en="Event sections">أقسام الفعالية</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- SPACES --}}
                <section id="spaces" class="mb-20">
                    <h2 class="text-3xl font-bold mb-6" data-ar="المساحات" data-en="Spaces">المساحات</h2>
                    <div class="endpoint-card">
                        <div class="bg-emerald-50 border-b border-emerald-100 p-4 flex items-center gap-3">
                            <span class="badge method-get text-white text-xs">GET</span>
                            <code class="text-gray-800 font-semibold">/spaces/{space}</code>
                            <span class="mr-auto text-sm text-gray-500" data-ar="تفاصيل مساحة" data-en="Space details">تفاصيل مساحة</span>
                            <span class="badge bg-green-100 text-green-700">Public</span>
                        </div>
                    </div>
                </section>

                {{-- SERVICES PUBLIC --}}
                <section id="services-public" class="mb-20">
                    <h2 class="text-3xl font-bold mb-6" data-ar="الخدمات" data-en="Services">الخدمات</h2>
                    <div class="endpoint-card">
                        <div class="bg-emerald-50 border-b border-emerald-100 p-4 flex items-center gap-3">
                            <span class="badge method-get text-white text-xs">GET</span>
                            <code class="text-gray-800 font-semibold">/services</code>
                            <span class="mr-auto text-sm text-gray-500" data-ar="قائمة الخدمات" data-en="List services">قائمة الخدمات</span>
                            <span class="badge bg-green-100 text-green-700">Public</span>
                        </div>
                    </div>
                </section>

                {{-- STATISTICS --}}
                <section id="statistics" class="mb-20">
                    <h2 class="text-3xl font-bold mb-6" data-ar="الإحصائيات" data-en="Statistics">الإحصائيات</h2>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th><th class="p-3 text-right">Auth</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/statistics</code></td><td class="p-3" data-ar="إحصائيات المنصة الشاملة" data-en="Platform overview statistics">إحصائيات المنصة الشاملة</td><td class="p-3"><span class="badge bg-green-100 text-green-700">Public</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/statistics/events</code></td><td class="p-3" data-ar="إحصائيات الفعاليات" data-en="Events statistics">إحصائيات الفعاليات</td><td class="p-3"><span class="badge bg-green-100 text-green-700">Public</span></td></tr>
                                <tr><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/statistics/spaces</code></td><td class="p-3" data-ar="إحصائيات المساحات" data-en="Spaces statistics">إحصائيات المساحات</td><td class="p-3"><span class="badge bg-green-100 text-green-700">Public</span></td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- SPONSORS PUBLIC --}}
                <section id="sponsors-public" class="mb-20">
                    <h2 class="text-3xl font-bold mb-6" data-ar="الرعاة" data-en="Sponsors">الرعاة</h2>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th><th class="p-3 text-right">Auth</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/events/{event}/sponsors</code></td><td class="p-3" data-ar="رعاة فعالية" data-en="Event sponsors">رعاة فعالية</td><td class="p-3"><span class="badge bg-green-100 text-green-700">Public</span></td></tr>
                                <tr><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/events/{event}/sponsor-packages</code></td><td class="p-3" data-ar="باقات الرعاية" data-en="Sponsor packages">باقات الرعاية</td><td class="p-3"><span class="badge bg-green-100 text-green-700">Public</span></td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- RATINGS PUBLIC --}}
                <section id="ratings-public" class="mb-20">
                    <h2 class="text-3xl font-bold mb-6" data-ar="التقييمات" data-en="Ratings">التقييمات</h2>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th><th class="p-3 text-right">Auth</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/ratings</code></td><td class="p-3" data-ar="قائمة التقييمات المعتمدة" data-en="List approved ratings">قائمة التقييمات المعتمدة</td><td class="p-3"><span class="badge bg-green-100 text-green-700">Public</span></td></tr>
                                <tr><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/ratings/summary</code></td><td class="p-3" data-ar="ملخص التقييمات والمتوسط" data-en="Ratings summary & average">ملخص التقييمات والمتوسط</td><td class="p-3"><span class="badge bg-green-100 text-green-700">Public</span></td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- PAGES, FAQS, BANNERS PUBLIC --}}
                <section id="cms-public" class="mb-20">
                    <h2 class="text-3xl font-bold mb-6" data-ar="صفحات وأسئلة وبانرات" data-en="Pages, FAQs & Banners">صفحات وأسئلة وبانرات</h2>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/pages</code></td><td class="p-3" data-ar="الصفحات المنشورة" data-en="Published pages">الصفحات المنشورة</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/pages/{slug}</code></td><td class="p-3" data-ar="محتوى صفحة" data-en="Page content by slug">محتوى صفحة</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/faqs</code></td><td class="p-3" data-ar="الأسئلة الشائعة" data-en="Frequently asked questions">الأسئلة الشائعة</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/faqs/categories</code></td><td class="p-3" data-ar="تصنيفات الأسئلة" data-en="FAQ categories">تصنيفات الأسئلة</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/faqs/{faq}</code></td><td class="p-3" data-ar="تفاصيل سؤال" data-en="FAQ details">تفاصيل سؤال</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/faqs/{faq}/helpful</code></td><td class="p-3" data-ar="تقييم الإجابة (مفيدة؟)" data-en="Rate answer helpfulness">تقييم الإجابة (مفيدة؟)</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/banners</code></td><td class="p-3" data-ar="البانرات النشطة" data-en="Active banners">البانرات النشطة</td></tr>
                                <tr><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/banners/{banner}/click</code></td><td class="p-3" data-ar="تسجيل نقرة على بانر" data-en="Track banner click">تسجيل نقرة على بانر</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- ==================== USER ENDPOINTS ==================== --}}

                {{-- BUSINESS PROFILE --}}
                <section id="profile" class="mb-20">
                    <h2 class="text-3xl font-bold mb-6" data-ar="الملف التجاري" data-en="Business Profile">الملف التجاري</h2>

                    <div class="endpoint-card mb-6">
                        <div class="bg-blue-50 border-b border-blue-100 p-5 flex items-center gap-3">
                            <span class="badge method-post text-white text-xs">POST</span>
                            <code class="text-gray-800 font-semibold">/profile</code>
                            <span class="mr-auto text-sm text-gray-500" data-ar="إنشاء ملف تجاري" data-en="Create business profile">إنشاء ملف تجاري</span>
                            <span class="badge bg-yellow-100 text-yellow-700">Auth</span>
                        </div>
                        <div class="p-5 space-y-6">
                            <div>
                                <h4 class="font-semibold mb-3 text-sm text-gray-500 uppercase tracking-wider" data-ar="الحقول" data-en="Parameters">الحقول</h4>
                                <div class="overflow-x-auto">
                                    <table class="param-table w-full text-sm">
                                        <thead><tr class="border-b bg-gray-50"><th class="p-3 text-right">Field</th><th class="p-3 text-right">Type</th><th class="p-3 text-right">Required</th><th class="p-3 text-right">Rules</th></tr></thead>
                                        <tbody>
                                            <tr class="border-b"><td class="p-3"><code>company_name</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500">*</span></td><td class="p-3">max:255</td></tr>
                                            <tr class="border-b"><td class="p-3"><code>company_name_ar</code></td><td class="p-3">string</td><td class="p-3 text-gray-400">-</td><td class="p-3">max:255</td></tr>
                                            <tr class="border-b"><td class="p-3"><code>business_type</code></td><td class="p-3">enum</td><td class="p-3"><span class="text-red-500">*</span></td><td class="p-3" data-ar="نوع النشاط التجاري" data-en="Business type enum">نوع النشاط التجاري</td></tr>
                                            <tr class="border-b"><td class="p-3"><code>contact_phone</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500">*</span></td><td class="p-3">max:20</td></tr>
                                            <tr class="border-b"><td class="p-3"><code>contact_email</code></td><td class="p-3">string</td><td class="p-3 text-gray-400">-</td><td class="p-3">email, max:255</td></tr>
                                            <tr class="border-b"><td class="p-3"><code>commercial_registration_number</code></td><td class="p-3">string</td><td class="p-3 text-gray-400">-</td><td class="p-3" data-ar="رقم السجل التجاري، max:50" data-en="Commercial reg number, max:50">رقم السجل التجاري، max:50</td></tr>
                                            <tr class="border-b"><td class="p-3"><code>commercial_registration_image</code></td><td class="p-3">file</td><td class="p-3 text-gray-400">-</td><td class="p-3">image (jpeg,png,jpg,webp,pdf), max:5MB</td></tr>
                                            <tr class="border-b"><td class="p-3"><code>national_id_number</code></td><td class="p-3">string</td><td class="p-3 text-gray-400">-</td><td class="p-3">max:20</td></tr>
                                            <tr class="border-b"><td class="p-3"><code>national_id_image</code></td><td class="p-3">file</td><td class="p-3 text-gray-400">-</td><td class="p-3">image (jpeg,png,jpg,webp,pdf), max:5MB</td></tr>
                                            <tr class="border-b"><td class="p-3"><code>company_logo</code></td><td class="p-3">file</td><td class="p-3 text-gray-400">-</td><td class="p-3">image (jpeg,png,jpg,webp), max:2MB</td></tr>
                                            <tr class="border-b"><td class="p-3"><code>avatar</code></td><td class="p-3">file</td><td class="p-3 text-gray-400">-</td><td class="p-3">image (jpeg,png,jpg,webp), max:2MB</td></tr>
                                            <tr class="border-b"><td class="p-3"><code>company_address</code></td><td class="p-3">string</td><td class="p-3 text-gray-400">-</td><td class="p-3">max:500</td></tr>
                                            <tr class="border-b"><td class="p-3"><code>company_address_ar</code></td><td class="p-3">string</td><td class="p-3 text-gray-400">-</td><td class="p-3">max:500</td></tr>
                                            <tr><td class="p-3"><code>website</code></td><td class="p-3">string</td><td class="p-3 text-gray-400">-</td><td class="p-3">url, max:255</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="code-block">
                                <div class="code-header"><span>Response</span><span class="badge bg-green-500/30 text-green-300">201</span></div>
                                <pre><code>{
  <span class="json-key">"success"</span>: <span class="json-bool">true</span>,
  <span class="json-key">"message"</span>: <span class="json-string">"Business profile created"</span>,
  <span class="json-key">"data"</span>: {
    <span class="json-key">"id"</span>: <span class="json-string">"uuid"</span>,
    <span class="json-key">"company_name"</span>: <span class="json-string">"شركة المعارض"</span>,
    <span class="json-key">"status"</span>: <span class="json-string">"pending"</span>
  }
}</code></pre>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/profile</code></td><td class="p-3" data-ar="عرض الملف التجاري" data-en="Show profile">عرض الملف التجاري</td></tr>
                                <tr><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/profile</code></td><td class="p-3" data-ar="تحديث الملف التجاري" data-en="Update profile">تحديث الملف التجاري</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- FAVORITES --}}
                <section id="favorites" class="mb-20">
                    <h2 class="text-3xl font-bold mb-6" data-ar="المفضلة" data-en="Favorites">المفضلة</h2>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th><th class="p-3 text-right">Auth</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/favorites</code></td><td class="p-3" data-ar="قائمة المفضلة" data-en="List favorites">قائمة المفضلة</td><td class="p-3"><span class="badge bg-yellow-100 text-yellow-700">Auth</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/favorites</code></td><td class="p-3" data-ar="إضافة للمفضلة (type: event|space, id: uuid)" data-en="Add to favorites (type: event|space, id: uuid)">إضافة للمفضلة (type: event|space, id: uuid)</td><td class="p-3"><span class="badge bg-yellow-100 text-yellow-700">Auth</span></td></tr>
                                <tr><td class="p-3"><span class="badge method-delete text-white">DEL</span></td><td class="p-3"><code>/favorites/{favorite}</code></td><td class="p-3" data-ar="إزالة من المفضلة" data-en="Remove from favorites">إزالة من المفضلة</td><td class="p-3"><span class="badge bg-yellow-100 text-yellow-700">Auth</span></td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- NOTIFICATIONS --}}
                <section id="notifications" class="mb-20">
                    <h2 class="text-3xl font-bold mb-6" data-ar="الإشعارات" data-en="Notifications">الإشعارات</h2>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/notifications</code></td><td class="p-3" data-ar="قائمة الإشعارات" data-en="List notifications">قائمة الإشعارات</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/notifications/unread-count</code></td><td class="p-3" data-ar="عدد غير المقروءة" data-en="Unread count">عدد غير المقروءة</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/notifications/{notification}/read</code></td><td class="p-3" data-ar="تحديد كمقروء" data-en="Mark as read">تحديد كمقروء</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/notifications/read-all</code></td><td class="p-3" data-ar="قراءة الكل" data-en="Mark all as read">قراءة الكل</td></tr>
                                <tr class="border-b bg-gray-50/50"><td colspan="4" class="p-2 font-bold text-gray-600 text-xs uppercase" data-ar="تفضيلات الإشعارات" data-en="Notification Preferences">تفضيلات الإشعارات</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/notifications/preferences</code></td><td class="p-3" data-ar="عرض تفضيلات الإشعارات" data-en="Get notification preferences">عرض تفضيلات الإشعارات</td></tr>
                                <tr><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/notifications/preferences</code></td><td class="p-3" data-ar="تحديث تفضيلات الإشعارات" data-en="Update notification preferences">تحديث تفضيلات الإشعارات</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- RATINGS USER --}}
                <section id="ratings-user" class="mb-20">
                    <h2 class="text-3xl font-bold mb-6" data-ar="التقييمات (مستخدم)" data-en="Ratings (User)">التقييمات (مستخدم)</h2>
                    <div class="endpoint-card mb-6">
                        <div class="bg-blue-50 border-b border-blue-100 p-5 flex items-center gap-3">
                            <span class="badge method-post text-white text-xs">POST</span>
                            <code class="text-gray-800 font-semibold">/ratings</code>
                            <span class="mr-auto text-sm text-gray-500" data-ar="إنشاء تقييم" data-en="Create rating">إنشاء تقييم</span>
                            <span class="badge bg-yellow-100 text-yellow-700">Auth</span>
                        </div>
                        <div class="p-5">
                            <div class="overflow-x-auto">
                                <table class="param-table w-full text-sm">
                                    <thead><tr class="border-b bg-gray-50"><th class="p-3 text-right">Field</th><th class="p-3 text-right">Type</th><th class="p-3 text-right">Required</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th></tr></thead>
                                    <tbody>
                                        <tr class="border-b"><td class="p-3"><code>rateable_type</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500">*</span></td><td class="p-3" data-ar="نوع العنصر (event, space)" data-en="Item type">نوع العنصر (event, space)</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>rateable_id</code></td><td class="p-3">uuid</td><td class="p-3"><span class="text-red-500">*</span></td><td class="p-3" data-ar="معرف العنصر" data-en="Item ID">معرف العنصر</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>score</code></td><td class="p-3">integer</td><td class="p-3"><span class="text-red-500">*</span></td><td class="p-3">1-5</td></tr>
                                        <tr><td class="p-3"><code>comment</code></td><td class="p-3">string</td><td class="p-3 text-gray-400">-</td><td class="p-3">max:1000</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/ratings/{rating}</code></td><td class="p-3" data-ar="تعديل تقييم" data-en="Update rating">تعديل تقييم</td></tr>
                                <tr><td class="p-3"><span class="badge method-delete text-white">DEL</span></td><td class="p-3"><code>/ratings/{rating}</code></td><td class="p-3" data-ar="حذف تقييم" data-en="Delete rating">حذف تقييم</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- SUPPORT TICKETS --}}
                <section id="support-tickets" class="mb-20">
                    <h2 class="text-3xl font-bold mb-6" data-ar="تذاكر الدعم" data-en="Support Tickets">تذاكر الدعم</h2>
                    <div class="endpoint-card mb-6">
                        <div class="bg-blue-50 border-b border-blue-100 p-5 flex items-center gap-3">
                            <span class="badge method-post text-white text-xs">POST</span>
                            <code class="text-gray-800 font-semibold">/support-tickets</code>
                            <span class="mr-auto text-sm text-gray-500" data-ar="إنشاء تذكرة دعم" data-en="Create support ticket">إنشاء تذكرة دعم</span>
                            <span class="badge bg-yellow-100 text-yellow-700">Auth</span>
                        </div>
                        <div class="p-5">
                            <div class="overflow-x-auto">
                                <table class="param-table w-full text-sm">
                                    <thead><tr class="border-b bg-gray-50"><th class="p-3 text-right">Field</th><th class="p-3 text-right">Type</th><th class="p-3 text-right">Required</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th></tr></thead>
                                    <tbody>
                                        <tr class="border-b"><td class="p-3"><code>subject</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500">*</span></td><td class="p-3">max:255</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>message</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500">*</span></td><td class="p-3">max:5000</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>priority</code></td><td class="p-3">string</td><td class="p-3 text-gray-400">-</td><td class="p-3">low, medium, high (default: medium)</td></tr>
                                        <tr><td class="p-3"><code>category</code></td><td class="p-3">string</td><td class="p-3 text-gray-400">-</td><td class="p-3" data-ar="تصنيف التذكرة" data-en="Ticket category">تصنيف التذكرة</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/support-tickets</code></td><td class="p-3" data-ar="قائمة تذاكري" data-en="My tickets">قائمة تذاكري</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/support-tickets/{id}</code></td><td class="p-3" data-ar="تفاصيل تذكرة" data-en="Ticket details">تفاصيل تذكرة</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/support-tickets/{id}/reply</code></td><td class="p-3" data-ar="الرد على تذكرة" data-en="Reply to ticket">الرد على تذكرة</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/support-tickets/{id}/close</code></td><td class="p-3" data-ar="إغلاق تذكرة" data-en="Close ticket">إغلاق تذكرة</td></tr>
                                <tr><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/support-tickets/{id}/reopen</code></td><td class="p-3" data-ar="إعادة فتح تذكرة" data-en="Reopen ticket">إعادة فتح تذكرة</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- INVOICES USER --}}
                <section id="invoices-user" class="mb-20">
                    <h2 class="text-3xl font-bold mb-6" data-ar="الفواتير" data-en="Invoices">الفواتير</h2>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th><th class="p-3 text-right">Auth</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/invoices</code></td><td class="p-3" data-ar="قائمة فواتيري" data-en="My invoices">قائمة فواتيري</td><td class="p-3"><span class="badge bg-yellow-100 text-yellow-700">Auth</span></td></tr>
                                <tr><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/invoices/{invoice}</code></td><td class="p-3" data-ar="تفاصيل فاتورة" data-en="Invoice details">تفاصيل فاتورة</td><td class="p-3"><span class="badge bg-yellow-100 text-yellow-700">Auth</span></td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- VISIT REQUESTS --}}
                <section id="visit-requests" class="mb-20">
                    <h2 class="text-3xl font-bold mb-6" data-ar="طلبات الزيارة" data-en="Visit Requests">طلبات الزيارة</h2>

                    <div class="endpoint-card mb-6">
                        <div class="bg-blue-50 border-b border-blue-100 p-5 flex items-center gap-3">
                            <span class="badge method-post text-white text-xs">POST</span>
                            <code class="text-gray-800 font-semibold">/visit-requests</code>
                            <span class="mr-auto text-sm text-gray-500" data-ar="إنشاء طلب زيارة" data-en="Create visit request">إنشاء طلب زيارة</span>
                            <span class="badge bg-yellow-100 text-yellow-700">Auth</span>
                        </div>
                        <div class="p-5 space-y-6">
                            <div class="overflow-x-auto">
                                <table class="param-table w-full text-sm">
                                    <thead><tr class="border-b bg-gray-50"><th class="p-3 text-right">Field</th><th class="p-3 text-right">Type</th><th class="p-3 text-right">Required</th><th class="p-3 text-right">Rules</th></tr></thead>
                                    <tbody>
                                        <tr class="border-b"><td class="p-3"><code>event_id</code></td><td class="p-3">uuid</td><td class="p-3"><span class="text-red-500">*</span></td><td class="p-3" data-ar="معرف الفعالية (موجود في جدول events)" data-en="Event ID (must exist in events)">معرف الفعالية (موجود في جدول events)</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>visit_date</code></td><td class="p-3">date</td><td class="p-3"><span class="text-red-500">*</span></td><td class="p-3" data-ar="تاريخ الزيارة (اليوم أو مستقبلي، ضمن تواريخ الفعالية)" data-en="Visit date (today or future, within event dates)">تاريخ الزيارة (اليوم أو مستقبلي، ضمن تواريخ الفعالية)</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>visit_time</code></td><td class="p-3">string</td><td class="p-3 text-gray-400">-</td><td class="p-3" data-ar="وقت الزيارة بصيغة H:i (مثال: 14:30)" data-en="Visit time format H:i (e.g. 14:30)">وقت الزيارة بصيغة H:i (مثال: 14:30)</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>visitors_count</code></td><td class="p-3">integer</td><td class="p-3"><span class="text-red-500">*</span></td><td class="p-3" data-ar="min:1، max:10 (افتراضي)" data-en="min:1, max:10 (default)">min:1، max:10 (افتراضي)</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>notes</code></td><td class="p-3">string</td><td class="p-3 text-gray-400">-</td><td class="p-3">max:1000</td></tr>
                                        <tr><td class="p-3"><code>contact_phone</code></td><td class="p-3">string</td><td class="p-3 text-gray-400">-</td><td class="p-3">max:20</td></tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                <div class="code-block">
                                    <div class="code-header"><span>Request</span></div>
                                    <pre><code>{
  <span class="json-key">"event_id"</span>: <span class="json-string">"event-uuid"</span>,
  <span class="json-key">"visit_date"</span>: <span class="json-string">"2025-06-15"</span>,
  <span class="json-key">"visitors_count"</span>: <span class="json-number">3</span>,
  <span class="json-key">"notes"</span>: <span class="json-string">"نرغب في زيارة المعرض"</span>
}</code></pre>
                                </div>
                                <div class="code-block">
                                    <div class="code-header"><span>Response</span><span class="badge bg-green-500/30 text-green-300">201</span></div>
                                    <pre><code>{
  <span class="json-key">"success"</span>: <span class="json-bool">true</span>,
  <span class="json-key">"message"</span>: <span class="json-string">"Visit request created"</span>,
  <span class="json-key">"data"</span>: {
    <span class="json-key">"id"</span>: <span class="json-string">"uuid"</span>,
    <span class="json-key">"status"</span>: <span class="json-string">"pending"</span>
  }
}</code></pre>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/visit-requests</code></td><td class="p-3" data-ar="قائمة طلبات الزيارة" data-en="List visit requests">قائمة طلبات الزيارة</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/visit-requests/{visitRequest}</code></td><td class="p-3" data-ar="تفاصيل طلب" data-en="Show request">تفاصيل طلب</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/visit-requests/{visitRequest}</code></td><td class="p-3" data-ar="تحديث طلب" data-en="Update request">تحديث طلب</td></tr>
                                <tr><td class="p-3"><span class="badge method-delete text-white">DEL</span></td><td class="p-3"><code>/visit-requests/{visitRequest}</code></td><td class="p-3" data-ar="حذف طلب" data-en="Delete request">حذف طلب</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- RENTAL REQUESTS --}}
                <section id="rental-requests" class="mb-20">
                    <h2 class="text-3xl font-bold mb-4" data-ar="طلبات الإيجار" data-en="Rental Requests">طلبات الإيجار</h2>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-5 mb-6">
                        <h4 class="font-bold text-yellow-800" data-ar="يتطلب ملف تجاري موثق" data-en="Requires verified business profile">يتطلب ملف تجاري موثق</h4>
                        <p class="text-sm text-yellow-700" data-ar="يجب أن يكون الملف التجاري بحالة approved قبل إنشاء طلب إيجار" data-en="Business profile must be approved before creating rental request">يجب أن يكون الملف التجاري بحالة approved قبل إنشاء طلب إيجار</p>
                    </div>

                    <div class="endpoint-card mb-6">
                        <div class="bg-blue-50 border-b border-blue-100 p-5 flex items-center gap-3">
                            <span class="badge method-post text-white text-xs">POST</span>
                            <code class="text-gray-800 font-semibold">/rental-requests</code>
                            <span class="mr-auto text-sm text-gray-500" data-ar="إنشاء طلب إيجار" data-en="Create rental request">إنشاء طلب إيجار</span>
                            <span class="badge bg-yellow-100 text-yellow-700">Auth</span>
                            <span class="badge bg-purple-100 text-purple-700">Verified</span>
                        </div>
                        <div class="p-5 space-y-6">
                            <div class="overflow-x-auto">
                                <table class="param-table w-full text-sm">
                                    <thead><tr class="border-b bg-gray-50"><th class="p-3 text-right">Field</th><th class="p-3 text-right">Type</th><th class="p-3 text-right">Required</th><th class="p-3 text-right">Rules</th></tr></thead>
                                    <tbody>
                                        <tr class="border-b"><td class="p-3"><code>space_id</code></td><td class="p-3">uuid</td><td class="p-3"><span class="text-red-500">*</span></td><td class="p-3" data-ar="معرف المساحة (موجود في جدول spaces)" data-en="Space ID (must exist in spaces)">معرف المساحة (موجود في جدول spaces)</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>start_date</code></td><td class="p-3">date</td><td class="p-3"><span class="text-red-500">*</span></td><td class="p-3" data-ar="تاريخ البداية (اليوم أو مستقبلي، ضمن تواريخ الفعالية)" data-en="Start date (today or future, within event dates)">تاريخ البداية (اليوم أو مستقبلي، ضمن تواريخ الفعالية)</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>end_date</code></td><td class="p-3">date</td><td class="p-3"><span class="text-red-500">*</span></td><td class="p-3" data-ar="تاريخ النهاية (بعد أو يساوي البداية)" data-en="End date (>= start_date)">تاريخ النهاية (بعد أو يساوي البداية)</td></tr>
                                        <tr><td class="p-3"><code>notes</code></td><td class="p-3">string</td><td class="p-3 text-gray-400">-</td><td class="p-3">max:1000</td></tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                <div class="code-block">
                                    <div class="code-header"><span>Request</span></div>
                                    <pre><code>{
  <span class="json-key">"space_id"</span>: <span class="json-string">"space-uuid"</span>,
  <span class="json-key">"start_date"</span>: <span class="json-string">"2025-06-01"</span>,
  <span class="json-key">"end_date"</span>: <span class="json-string">"2025-06-30"</span>,
  <span class="json-key">"notes"</span>: <span class="json-string">"نريد استئجار المساحة"</span>
}</code></pre>
                                </div>
                                <div class="code-block">
                                    <div class="code-header"><span>Response</span><span class="badge bg-green-500/30 text-green-300">201</span></div>
                                    <pre><code>{
  <span class="json-key">"success"</span>: <span class="json-bool">true</span>,
  <span class="json-key">"message"</span>: <span class="json-string">"Rental request created"</span>,
  <span class="json-key">"data"</span>: {
    <span class="json-key">"id"</span>: <span class="json-string">"uuid"</span>,
    <span class="json-key">"status"</span>: <span class="json-string">"pending"</span>
  }
}</code></pre>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/rental-requests</code></td><td class="p-3" data-ar="قائمة طلبات الإيجار" data-en="List rental requests">قائمة طلبات الإيجار</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/rental-requests/{rentalRequest}</code></td><td class="p-3" data-ar="تفاصيل طلب" data-en="Show request">تفاصيل طلب</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/rental-requests/{rentalRequest}</code></td><td class="p-3" data-ar="تحديث طلب" data-en="Update request">تحديث طلب</td></tr>
                                <tr><td class="p-3"><span class="badge method-delete text-white">DEL</span></td><td class="p-3"><code>/rental-requests/{rentalRequest}</code></td><td class="p-3" data-ar="حذف طلب" data-en="Delete request">حذف طلب</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- ==================== ADMIN ENDPOINTS ==================== --}}

                {{-- ADMIN DASHBOARD --}}
                <section id="admin-dashboard" class="mb-20">
                    <h2 class="text-3xl font-bold mb-4" data-ar="لوحة التحكم" data-en="Admin Dashboard">لوحة التحكم</h2>
                    <div class="bg-red-50 border border-red-200 rounded-xl p-5 mb-6">
                        <span class="font-semibold text-red-800" data-ar="يتطلب دور:" data-en="Requires role:">يتطلب دور:</span>
                        <code class="text-red-700">admin</code> | <code class="text-red-700">super-admin</code>
                    </div>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200 mb-6">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/admin/dashboard</code></td><td class="p-3" data-ar="إحصائيات لوحة التحكم" data-en="Dashboard statistics">إحصائيات لوحة التحكم</td></tr>
                                <tr><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/admin/statistics</code></td><td class="p-3" data-ar="إحصائيات تفصيلية" data-en="Detailed statistics">إحصائيات تفصيلية</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="endpoint-card">
                        <div class="bg-emerald-50 border-b border-emerald-100 p-5 flex items-center gap-3">
                            <span class="badge method-get text-white text-xs">GET</span>
                            <code class="text-gray-800 font-semibold">/admin/dashboard</code>
                            <span class="mr-auto text-sm text-gray-500" data-ar="إحصائيات لوحة التحكم" data-en="Dashboard statistics">إحصائيات لوحة التحكم</span>
                            <span class="badge bg-red-100 text-red-700">Admin</span>
                        </div>
                        <div class="p-5 space-y-6">
                            <div class="overflow-x-auto">
                                <table class="param-table w-full text-sm">
                                    <thead><tr class="border-b bg-gray-50"><th class="p-3 text-right">Query Param</th><th class="p-3 text-right">Type</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th></tr></thead>
                                    <tbody>
                                        <tr class="border-b"><td class="p-3"><code>spaces_period</code></td><td class="p-3">string</td><td class="p-3">all|today|week|month|year</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>revenue_period</code></td><td class="p-3">string</td><td class="p-3">all|today|week|month|year</td></tr>
                                        <tr><td class="p-3"><code>event_id</code></td><td class="p-3">uuid</td><td class="p-3" data-ar="فلتر حسب الفعالية" data-en="Filter by event">فلتر حسب الفعالية</td></tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="code-block">
                                <div class="code-header"><span>Response</span><span class="badge bg-green-500/30 text-green-300">200</span></div>
                                <pre><code>{
  <span class="json-key">"data"</span>: {
    <span class="json-key">"overview"</span>: {
      <span class="json-key">"total_revenue"</span>: <span class="json-number">450000</span>,
      <span class="json-key">"total_spaces"</span>: <span class="json-number">122</span>,
      <span class="json-key">"total_visit_requests"</span>: <span class="json-number">4</span>,
      <span class="json-key">"total_rental_requests"</span>: <span class="json-number">3</span>
    },
    <span class="json-key">"spaces"</span>: { <span class="json-key">"by_status"</span>: { ... } },
    <span class="json-key">"revenue"</span>: { <span class="json-key">"by_payment_status"</span>: { ... } }
  }
}</code></pre>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- ADMIN EVENTS --}}
                <section id="admin-events" class="mb-20">
                    <h2 class="text-3xl font-bold mb-6" data-ar="إدارة الفعاليات" data-en="Events Management">إدارة الفعاليات</h2>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200 mb-6">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/admin/events</code></td><td class="p-3" data-ar="قائمة الفعاليات" data-en="List events">قائمة الفعاليات</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/admin/events</code></td><td class="p-3" data-ar="إنشاء فعالية" data-en="Create event">إنشاء فعالية</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/admin/events/{event}</code></td><td class="p-3" data-ar="تفاصيل فعالية" data-en="Show event">تفاصيل فعالية</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/admin/events/{event}</code></td><td class="p-3" data-ar="تحديث فعالية" data-en="Update event">تحديث فعالية</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-delete text-white">DEL</span></td><td class="p-3"><code>/admin/events/{event}</code></td><td class="p-3" data-ar="حذف فعالية" data-en="Delete event">حذف فعالية</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/admin/events/{event}/sections</code></td><td class="p-3" data-ar="أقسام الفعالية" data-en="Event sections">أقسام الفعالية</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/admin/events/{event}/sections</code></td><td class="p-3" data-ar="إنشاء قسم" data-en="Create section">إنشاء قسم</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/admin/events/{event}/spaces</code></td><td class="p-3" data-ar="مساحات الفعالية" data-en="Event spaces">مساحات الفعالية</td></tr>
                                <tr><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/admin/events/{event}/spaces</code></td><td class="p-3" data-ar="إنشاء مساحة" data-en="Create space">إنشاء مساحة</td></tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Event Create Fields --}}
                    <div class="endpoint-card mb-6">
                        <div class="bg-blue-50 border-b border-blue-100 p-5 flex items-center gap-3">
                            <span class="badge method-post text-white text-xs">POST</span>
                            <code class="text-gray-800 font-semibold">/admin/events</code>
                            <span class="mr-auto text-sm text-gray-500" data-ar="حقول إنشاء فعالية" data-en="Create event fields">حقول إنشاء فعالية</span>
                        </div>
                        <div class="p-5">
                            <div class="overflow-x-auto">
                                <table class="param-table w-full text-sm">
                                    <thead><tr class="border-b bg-gray-50"><th class="p-3 text-right">Field</th><th class="p-3 text-right">Type</th><th class="p-3 text-right">Required</th><th class="p-3 text-right">Rules</th></tr></thead>
                                    <tbody>
                                        <tr class="border-b"><td class="p-3"><code>name</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500">*</span></td><td class="p-3">max:255</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>name_ar</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500">*</span></td><td class="p-3">max:255</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>description</code></td><td class="p-3">string</td><td class="p-3 text-gray-400">-</td><td class="p-3">max:5000</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>description_ar</code></td><td class="p-3">string</td><td class="p-3 text-gray-400">-</td><td class="p-3">max:5000</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>category_id</code></td><td class="p-3">uuid</td><td class="p-3"><span class="text-red-500">*</span></td><td class="p-3" data-ar="موجود في categories" data-en="exists in categories">exists in categories</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>city_id</code></td><td class="p-3">uuid</td><td class="p-3"><span class="text-red-500">*</span></td><td class="p-3" data-ar="موجود في cities" data-en="exists in cities">exists in cities</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>address</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500">*</span></td><td class="p-3">max:500</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>address_ar</code></td><td class="p-3">string</td><td class="p-3 text-gray-400">-</td><td class="p-3">max:500</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>latitude</code></td><td class="p-3">numeric</td><td class="p-3 text-gray-400">-</td><td class="p-3">-90 to 90</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>longitude</code></td><td class="p-3">numeric</td><td class="p-3 text-gray-400">-</td><td class="p-3">-180 to 180</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>start_date</code></td><td class="p-3">date</td><td class="p-3"><span class="text-red-500">*</span></td><td class="p-3">Y-m-d</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>end_date</code></td><td class="p-3">date</td><td class="p-3"><span class="text-red-500">*</span></td><td class="p-3">>= start_date</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>opening_time</code></td><td class="p-3">string</td><td class="p-3 text-gray-400">-</td><td class="p-3">H:i</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>closing_time</code></td><td class="p-3">string</td><td class="p-3 text-gray-400">-</td><td class="p-3">H:i</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>images[]</code></td><td class="p-3">file[]</td><td class="p-3 text-gray-400">-</td><td class="p-3">max:10, jpeg/png/jpg/webp, 5MB each</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>images_360[]</code></td><td class="p-3">file[]</td><td class="p-3 text-gray-400">-</td><td class="p-3">max:5, jpeg/png/jpg/webp, 10MB each</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>features[]</code></td><td class="p-3">array</td><td class="p-3 text-gray-400">-</td><td class="p-3">max:50 items</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>features_ar[]</code></td><td class="p-3">array</td><td class="p-3 text-gray-400">-</td><td class="p-3">max:50 items</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>organizer_name</code></td><td class="p-3">string</td><td class="p-3 text-gray-400">-</td><td class="p-3">max:255</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>organizer_phone</code></td><td class="p-3">string</td><td class="p-3 text-gray-400">-</td><td class="p-3">max:20</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>organizer_email</code></td><td class="p-3">string</td><td class="p-3 text-gray-400">-</td><td class="p-3">email, max:255</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>website</code></td><td class="p-3">string</td><td class="p-3 text-gray-400">-</td><td class="p-3">url, max:255</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>status</code></td><td class="p-3">enum</td><td class="p-3 text-gray-400">-</td><td class="p-3">EventStatus</td></tr>
                                        <tr><td class="p-3"><code>is_featured</code></td><td class="p-3">boolean</td><td class="p-3 text-gray-400">-</td><td class="p-3">true/false</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Section Create Fields --}}
                    <div class="endpoint-card mb-6">
                        <div class="bg-blue-50 border-b border-blue-100 p-5 flex items-center gap-3">
                            <span class="badge method-post text-white text-xs">POST</span>
                            <code class="text-gray-800 font-semibold">/admin/events/{event}/sections</code>
                            <span class="mr-auto text-sm text-gray-500" data-ar="حقول إنشاء قسم" data-en="Create section fields">حقول إنشاء قسم</span>
                        </div>
                        <div class="p-5">
                            <div class="overflow-x-auto">
                                <table class="param-table w-full text-sm">
                                    <thead><tr class="border-b bg-gray-50"><th class="p-3 text-right">Field</th><th class="p-3 text-right">Type</th><th class="p-3 text-right">Required</th><th class="p-3 text-right">Rules</th></tr></thead>
                                    <tbody>
                                        <tr class="border-b"><td class="p-3"><code>name</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500">*</span></td><td class="p-3">max:255</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>name_ar</code></td><td class="p-3">string</td><td class="p-3 text-gray-400">-</td><td class="p-3">max:255</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>description</code></td><td class="p-3">string</td><td class="p-3 text-gray-400">-</td><td class="p-3">max:2000</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>description_ar</code></td><td class="p-3">string</td><td class="p-3 text-gray-400">-</td><td class="p-3">max:2000</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>icon</code></td><td class="p-3">string</td><td class="p-3 text-gray-400">-</td><td class="p-3">max:100</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>is_active</code></td><td class="p-3">boolean</td><td class="p-3 text-gray-400">-</td><td class="p-3">true/false</td></tr>
                                        <tr><td class="p-3"><code>sort_order</code></td><td class="p-3">integer</td><td class="p-3 text-gray-400">-</td><td class="p-3">min:0</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Space Create Fields --}}
                    <div class="endpoint-card">
                        <div class="bg-blue-50 border-b border-blue-100 p-5 flex items-center gap-3">
                            <span class="badge method-post text-white text-xs">POST</span>
                            <code class="text-gray-800 font-semibold">/admin/events/{event}/spaces</code>
                            <span class="mr-auto text-sm text-gray-500" data-ar="حقول إنشاء مساحة" data-en="Create space fields">حقول إنشاء مساحة</span>
                        </div>
                        <div class="p-5">
                            <div class="overflow-x-auto">
                                <table class="param-table w-full text-sm">
                                    <thead><tr class="border-b bg-gray-50"><th class="p-3 text-right">Field</th><th class="p-3 text-right">Type</th><th class="p-3 text-right">Required</th><th class="p-3 text-right">Rules</th></tr></thead>
                                    <tbody>
                                        <tr class="border-b"><td class="p-3"><code>name</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500">*</span></td><td class="p-3">max:255</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>name_ar</code></td><td class="p-3">string</td><td class="p-3 text-gray-400">-</td><td class="p-3">max:255</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>description</code></td><td class="p-3">string</td><td class="p-3 text-gray-400">-</td><td class="p-3">max:2000</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>description_ar</code></td><td class="p-3">string</td><td class="p-3 text-gray-400">-</td><td class="p-3">max:2000</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>location_code</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500">*</span></td><td class="p-3" data-ar="max:20، فريد لكل فعالية" data-en="max:20, unique per event">max:20، فريد لكل فعالية</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>area_sqm</code></td><td class="p-3">numeric</td><td class="p-3"><span class="text-red-500">*</span></td><td class="p-3">min:1, max:999999</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>price_per_day</code></td><td class="p-3">numeric</td><td class="p-3 text-gray-400">-</td><td class="p-3">min:0</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>price_total</code></td><td class="p-3">numeric</td><td class="p-3"><span class="text-red-500">*</span></td><td class="p-3">min:0</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>section_id</code></td><td class="p-3">uuid</td><td class="p-3 text-gray-400">-</td><td class="p-3" data-ar="قسم من نفس الفعالية" data-en="Section from same event">قسم من نفس الفعالية</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>space_type</code></td><td class="p-3">enum</td><td class="p-3 text-gray-400">-</td><td class="p-3">SpaceType</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>payment_system</code></td><td class="p-3">enum</td><td class="p-3 text-gray-400">-</td><td class="p-3">PaymentSystem</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>rental_duration</code></td><td class="p-3">enum</td><td class="p-3 text-gray-400">-</td><td class="p-3">RentalDuration</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>floor_number</code></td><td class="p-3">integer</td><td class="p-3 text-gray-400">-</td><td class="p-3">-10 to 200</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>images[]</code></td><td class="p-3">file[]</td><td class="p-3 text-gray-400">-</td><td class="p-3">max:10, jpeg/png/jpg/webp, 5MB</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>images_360[]</code></td><td class="p-3">file[]</td><td class="p-3 text-gray-400">-</td><td class="p-3">max:5, 10MB each</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>amenities[]</code></td><td class="p-3">array</td><td class="p-3 text-gray-400">-</td><td class="p-3">max:50 items</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>amenities_ar[]</code></td><td class="p-3">array</td><td class="p-3 text-gray-400">-</td><td class="p-3">max:50 items</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>latitude</code></td><td class="p-3">numeric</td><td class="p-3 text-gray-400">-</td><td class="p-3">-90 to 90</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>longitude</code></td><td class="p-3">numeric</td><td class="p-3 text-gray-400">-</td><td class="p-3">-180 to 180</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>address</code></td><td class="p-3">string</td><td class="p-3 text-gray-400">-</td><td class="p-3">max:500</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>address_ar</code></td><td class="p-3">string</td><td class="p-3 text-gray-400">-</td><td class="p-3">max:500</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>services[]</code></td><td class="p-3">uuid[]</td><td class="p-3 text-gray-400">-</td><td class="p-3" data-ar="max:20، معرفات خدمات" data-en="max:20, service UUIDs">max:20، معرفات خدمات</td></tr>
                                        <tr><td class="p-3"><code>status</code></td><td class="p-3">enum</td><td class="p-3 text-gray-400">-</td><td class="p-3">SpaceStatus</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- ADMIN SECTIONS --}}
                <section id="admin-sections" class="mb-20">
                    <h2 class="text-3xl font-bold mb-6" data-ar="إدارة الأقسام" data-en="Sections Management">إدارة الأقسام</h2>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/admin/sections/{section}</code></td><td class="p-3" data-ar="تفاصيل قسم" data-en="Show section">تفاصيل قسم</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/admin/sections/{section}</code></td><td class="p-3" data-ar="تحديث قسم" data-en="Update section">تحديث قسم</td></tr>
                                <tr><td class="p-3"><span class="badge method-delete text-white">DEL</span></td><td class="p-3"><code>/admin/sections/{section}</code></td><td class="p-3" data-ar="حذف قسم" data-en="Delete section">حذف قسم</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- ADMIN SPACES --}}
                <section id="admin-spaces" class="mb-20">
                    <h2 class="text-3xl font-bold mb-6" data-ar="إدارة المساحات" data-en="Spaces Management">إدارة المساحات</h2>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/admin/spaces/{space}</code></td><td class="p-3" data-ar="تفاصيل مساحة" data-en="Show space">تفاصيل مساحة</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/admin/spaces/{space}</code></td><td class="p-3" data-ar="تحديث مساحة" data-en="Update space">تحديث مساحة</td></tr>
                                <tr><td class="p-3"><span class="badge method-delete text-white">DEL</span></td><td class="p-3"><code>/admin/spaces/{space}</code></td><td class="p-3" data-ar="حذف مساحة" data-en="Delete space">حذف مساحة</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- ADMIN SERVICES --}}
                <section id="admin-services" class="mb-20">
                    <h2 class="text-3xl font-bold mb-6" data-ar="إدارة الخدمات" data-en="Services Management">إدارة الخدمات</h2>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/admin/services</code></td><td class="p-3" data-ar="قائمة الخدمات" data-en="List services">قائمة الخدمات</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/admin/services</code></td><td class="p-3" data-ar="إنشاء خدمة" data-en="Create service">إنشاء خدمة</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/admin/services/{service}</code></td><td class="p-3" data-ar="تفاصيل خدمة" data-en="Show service">تفاصيل خدمة</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/admin/services/{service}</code></td><td class="p-3" data-ar="تحديث خدمة" data-en="Update service">تحديث خدمة</td></tr>
                                <tr><td class="p-3"><span class="badge method-delete text-white">DEL</span></td><td class="p-3"><code>/admin/services/{service}</code></td><td class="p-3" data-ar="حذف خدمة" data-en="Delete service">حذف خدمة</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="endpoint-card">
                        <div class="bg-blue-50 border-b border-blue-100 p-5 flex items-center gap-3">
                            <span class="badge method-post text-white text-xs">POST</span>
                            <code class="text-gray-800 font-semibold">/admin/services</code>
                            <span class="mr-auto text-sm text-gray-500" data-ar="حقول إنشاء خدمة" data-en="Create service fields">حقول إنشاء خدمة</span>
                        </div>
                        <div class="p-5">
                            <div class="overflow-x-auto">
                                <table class="param-table w-full text-sm">
                                    <thead><tr class="border-b bg-gray-50"><th class="p-3 text-right">Field</th><th class="p-3 text-right">Type</th><th class="p-3 text-right">Required</th><th class="p-3 text-right">Rules</th></tr></thead>
                                    <tbody>
                                        <tr class="border-b"><td class="p-3"><code>name</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500">*</span></td><td class="p-3">max:255</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>name_ar</code></td><td class="p-3">string</td><td class="p-3 text-gray-400">-</td><td class="p-3">max:255</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>description</code></td><td class="p-3">string</td><td class="p-3 text-gray-400">-</td><td class="p-3">max:2000</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>description_ar</code></td><td class="p-3">string</td><td class="p-3 text-gray-400">-</td><td class="p-3">max:2000</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>icon</code></td><td class="p-3">string</td><td class="p-3 text-gray-400">-</td><td class="p-3">max:100</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>is_active</code></td><td class="p-3">boolean</td><td class="p-3 text-gray-400">-</td><td class="p-3">true/false</td></tr>
                                        <tr><td class="p-3"><code>sort_order</code></td><td class="p-3">integer</td><td class="p-3 text-gray-400">-</td><td class="p-3">min:0</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- ADMIN VISIT REQUESTS --}}
                <section id="admin-visit-requests" class="mb-20">
                    <h2 class="text-3xl font-bold mb-6" data-ar="إدارة طلبات الزيارة" data-en="Visit Requests Management">إدارة طلبات الزيارة</h2>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/admin/visit-requests</code></td><td class="p-3" data-ar="قائمة جميع الطلبات" data-en="List all requests">قائمة جميع الطلبات</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/admin/visit-requests/{visitRequest}</code></td><td class="p-3" data-ar="تفاصيل طلب" data-en="Show request">تفاصيل طلب</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/admin/visit-requests/{visitRequest}/approve</code></td><td class="p-3" data-ar="قبول الطلب (notes: اختياري)" data-en="Approve (notes: optional)">قبول الطلب (notes: اختياري)</td></tr>
                                <tr><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/admin/visit-requests/{visitRequest}/reject</code></td><td class="p-3" data-ar="رفض الطلب (reason: مطلوب، max:1000)" data-en="Reject (reason: required, max:1000)">رفض الطلب (reason: مطلوب، max:1000)</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- ADMIN RENTAL REQUESTS --}}
                <section id="admin-rental-requests" class="mb-20">
                    <h2 class="text-3xl font-bold mb-6" data-ar="إدارة طلبات الإيجار" data-en="Rental Requests Management">إدارة طلبات الإيجار</h2>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/admin/rental-requests</code></td><td class="p-3" data-ar="قائمة جميع الطلبات" data-en="List all requests">قائمة جميع الطلبات</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/admin/rental-requests/{rentalRequest}</code></td><td class="p-3" data-ar="تفاصيل طلب" data-en="Show request">تفاصيل طلب</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/admin/rental-requests/{rentalRequest}/approve</code></td><td class="p-3" data-ar="قبول الطلب" data-en="Approve request">قبول الطلب</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/admin/rental-requests/{rentalRequest}/reject</code></td><td class="p-3" data-ar="رفض الطلب" data-en="Reject request">رفض الطلب</td></tr>
                                <tr><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/admin/rental-requests/{rentalRequest}/payment</code></td><td class="p-3" data-ar="تسجيل دفعة" data-en="Record payment">تسجيل دفعة</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- ADMIN BUSINESS PROFILES --}}
                <section id="admin-profiles" class="mb-20">
                    <h2 class="text-3xl font-bold mb-6" data-ar="إدارة الملفات التجارية" data-en="Business Profiles Management">إدارة الملفات التجارية</h2>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/admin/profiles</code></td><td class="p-3" data-ar="قائمة الملفات التجارية" data-en="List profiles">قائمة الملفات التجارية</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/admin/profiles/{profile}</code></td><td class="p-3" data-ar="تفاصيل ملف تجاري" data-en="Show profile">تفاصيل ملف تجاري</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/admin/profiles/{profile}/approve</code></td><td class="p-3" data-ar="قبول الملف" data-en="Approve profile">قبول الملف</td></tr>
                                <tr><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/admin/profiles/{profile}/reject</code></td><td class="p-3" data-ar="رفض الملف" data-en="Reject profile">رفض الملف</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- ADMIN SPONSORS --}}
                <section id="admin-sponsors" class="mb-20">
                    <h2 class="text-3xl font-bold mb-6" data-ar="إدارة الرعاة" data-en="Sponsors Management">إدارة الرعاة</h2>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/admin/sponsors</code></td><td class="p-3" data-ar="قائمة الرعاة" data-en="List sponsors">قائمة الرعاة</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/admin/sponsors</code></td><td class="p-3" data-ar="إنشاء راعي" data-en="Create sponsor">إنشاء راعي</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/admin/sponsors/{sponsor}</code></td><td class="p-3" data-ar="تفاصيل راعي" data-en="Show sponsor">تفاصيل راعي</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/admin/sponsors/{sponsor}</code></td><td class="p-3" data-ar="تحديث راعي" data-en="Update sponsor">تحديث راعي</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-delete text-white">DEL</span></td><td class="p-3"><code>/admin/sponsors/{sponsor}</code></td><td class="p-3" data-ar="حذف راعي" data-en="Delete sponsor">حذف راعي</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/admin/sponsors/{sponsor}/approve</code></td><td class="p-3" data-ar="قبول راعي" data-en="Approve">قبول راعي</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/admin/sponsors/{sponsor}/activate</code></td><td class="p-3" data-ar="تفعيل راعي" data-en="Activate">تفعيل راعي</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/admin/sponsors/{sponsor}/suspend</code></td><td class="p-3" data-ar="تعليق راعي" data-en="Suspend">تعليق راعي</td></tr>
                                <tr class="border-b bg-gray-50/50"><td colspan="3" class="p-2 font-bold text-gray-600 text-xs uppercase" data-ar="باقات الرعاية" data-en="Sponsor Packages">باقات الرعاية</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/admin/events/{event}/sponsor-packages</code></td><td class="p-3" data-ar="باقات الفعالية" data-en="Event packages">باقات الفعالية</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/admin/events/{event}/sponsor-packages</code></td><td class="p-3" data-ar="إنشاء باقة" data-en="Create package">إنشاء باقة</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/admin/sponsor-packages/{id}</code></td><td class="p-3" data-ar="تفاصيل باقة" data-en="Show package">تفاصيل باقة</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/admin/sponsor-packages/{id}</code></td><td class="p-3" data-ar="تحديث باقة" data-en="Update package">تحديث باقة</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-delete text-white">DEL</span></td><td class="p-3"><code>/admin/sponsor-packages/{id}</code></td><td class="p-3" data-ar="حذف باقة" data-en="Delete package">حذف باقة</td></tr>
                                <tr class="border-b bg-gray-50/50"><td colspan="3" class="p-2 font-bold text-gray-600 text-xs uppercase" data-ar="عقود ومدفوعات ومزايا وملفات" data-en="Contracts, Payments, Benefits & Assets">عقود ومدفوعات ومزايا وملفات</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/admin/sponsor-contracts</code></td><td class="p-3" data-ar="قائمة العقود" data-en="List contracts">قائمة العقود</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/admin/sponsor-contracts</code></td><td class="p-3" data-ar="إنشاء عقد" data-en="Create contract">إنشاء عقد</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/admin/sponsor-contracts/{id}/approve</code></td><td class="p-3" data-ar="قبول عقد" data-en="Approve contract">قبول عقد</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/admin/sponsor-contracts/{id}/reject</code></td><td class="p-3" data-ar="رفض عقد" data-en="Reject contract">رفض عقد</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/admin/sponsor-contracts/{id}/complete</code></td><td class="p-3" data-ar="إكمال عقد" data-en="Complete contract">إكمال عقد</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/admin/sponsor-contracts/{id}</code></td><td class="p-3" data-ar="تفاصيل عقد" data-en="Show contract">تفاصيل عقد</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/admin/sponsor-contracts/{id}</code></td><td class="p-3" data-ar="تحديث عقد" data-en="Update contract">تحديث عقد</td></tr>
                                <tr class="border-b bg-gray-50/50"><td colspan="3" class="p-2 font-bold text-gray-600 text-xs uppercase" data-ar="مدفوعات الرعاة" data-en="Sponsor Payments">مدفوعات الرعاة</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/admin/sponsor-payments</code></td><td class="p-3" data-ar="قائمة المدفوعات" data-en="List payments">قائمة المدفوعات</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/admin/sponsor-payments</code></td><td class="p-3" data-ar="إنشاء دفعة" data-en="Create payment">إنشاء دفعة</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/admin/sponsor-payments/{id}</code></td><td class="p-3" data-ar="تفاصيل دفعة" data-en="Show payment">تفاصيل دفعة</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/admin/sponsor-payments/{id}</code></td><td class="p-3" data-ar="تحديث دفعة" data-en="Update payment">تحديث دفعة</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/admin/sponsor-payments/{id}/mark-paid</code></td><td class="p-3" data-ar="تأكيد الدفع" data-en="Mark as paid">تأكيد الدفع</td></tr>
                                <tr class="border-b bg-gray-50/50"><td colspan="3" class="p-2 font-bold text-gray-600 text-xs uppercase" data-ar="مزايا الرعاة" data-en="Sponsor Benefits">مزايا الرعاة</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/admin/sponsor-benefits</code></td><td class="p-3" data-ar="قائمة المزايا" data-en="List benefits">قائمة المزايا</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/admin/sponsor-benefits</code></td><td class="p-3" data-ar="إنشاء ميزة" data-en="Create benefit">إنشاء ميزة</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/admin/sponsor-benefits/{id}</code></td><td class="p-3" data-ar="تفاصيل ميزة" data-en="Show benefit">تفاصيل ميزة</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/admin/sponsor-benefits/{id}</code></td><td class="p-3" data-ar="تحديث ميزة" data-en="Update benefit">تحديث ميزة</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/admin/sponsor-benefits/{id}/deliver</code></td><td class="p-3" data-ar="تأكيد التسليم" data-en="Mark delivered">تأكيد التسليم</td></tr>
                                <tr class="border-b bg-gray-50/50"><td colspan="3" class="p-2 font-bold text-gray-600 text-xs uppercase" data-ar="ملفات الرعاة" data-en="Sponsor Assets">ملفات الرعاة</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/admin/sponsor-assets</code></td><td class="p-3" data-ar="قائمة الملفات" data-en="List assets">قائمة الملفات</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/admin/sponsor-assets/{id}</code></td><td class="p-3" data-ar="تفاصيل ملف" data-en="Show asset">تفاصيل ملف</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/admin/sponsor-assets/{id}/approve</code></td><td class="p-3" data-ar="قبول ملف راعي" data-en="Approve asset">قبول ملف راعي</td></tr>
                                <tr><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/admin/sponsor-assets/{id}/reject</code></td><td class="p-3" data-ar="رفض ملف راعي" data-en="Reject asset">رفض ملف راعي</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- ADMIN RATINGS --}}
                <section id="admin-ratings" class="mb-20">
                    <h2 class="text-3xl font-bold mb-6" data-ar="إدارة التقييمات" data-en="Ratings Management">إدارة التقييمات</h2>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/admin/ratings</code></td><td class="p-3" data-ar="قائمة جميع التقييمات" data-en="List all ratings">جميع التقييمات</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/admin/ratings/{rating}</code></td><td class="p-3" data-ar="تفاصيل تقييم" data-en="Show rating">تفاصيل تقييم</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/admin/ratings/{rating}/approve</code></td><td class="p-3" data-ar="قبول تقييم" data-en="Approve rating">قبول تقييم</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/admin/ratings/{rating}/reject</code></td><td class="p-3" data-ar="رفض تقييم" data-en="Reject rating">رفض تقييم</td></tr>
                                <tr><td class="p-3"><span class="badge method-delete text-white">DEL</span></td><td class="p-3"><code>/admin/ratings/{rating}</code></td><td class="p-3" data-ar="حذف تقييم" data-en="Delete rating">حذف تقييم</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- ADMIN SUPPORT TICKETS --}}
                <section id="admin-tickets" class="mb-20">
                    <h2 class="text-3xl font-bold mb-6" data-ar="إدارة التذاكر" data-en="Tickets Management">إدارة التذاكر</h2>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/admin/support-tickets</code></td><td class="p-3" data-ar="جميع التذاكر" data-en="All tickets">جميع التذاكر</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/admin/support-tickets/{id}</code></td><td class="p-3" data-ar="تفاصيل تذكرة" data-en="Ticket details">تفاصيل تذكرة</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/admin/support-tickets/{id}/assign</code></td><td class="p-3" data-ar="تعيين موظف" data-en="Assign agent">تعيين موظف</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/admin/support-tickets/{id}/reply</code></td><td class="p-3" data-ar="الرد" data-en="Reply">الرد</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/admin/support-tickets/{id}/resolve</code></td><td class="p-3" data-ar="حل التذكرة" data-en="Resolve">حل التذكرة</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/admin/support-tickets/{id}/close</code></td><td class="p-3" data-ar="إغلاق" data-en="Close">إغلاق</td></tr>
                                <tr><td class="p-3"><span class="badge method-delete text-white">DEL</span></td><td class="p-3"><code>/admin/support-tickets/{id}</code></td><td class="p-3" data-ar="حذف" data-en="Delete">حذف</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- ADMIN RENTAL CONTRACTS & INVOICES --}}
                <section id="admin-contracts" class="mb-20">
                    <h2 class="text-3xl font-bold mb-6" data-ar="العقود والفواتير" data-en="Contracts & Invoices">العقود والفواتير</h2>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200 mb-6">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th></tr></thead>
                            <tbody>
                                <tr class="border-b bg-gray-50/50"><td colspan="3" class="p-2 font-bold text-gray-600 text-xs uppercase" data-ar="عقود الإيجار" data-en="Rental Contracts">عقود الإيجار</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/admin/rental-contracts</code></td><td class="p-3" data-ar="قائمة العقود" data-en="List contracts">قائمة العقود</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/admin/rental-contracts</code></td><td class="p-3" data-ar="إنشاء عقد" data-en="Create contract">إنشاء عقد</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/admin/rental-contracts/{id}</code></td><td class="p-3" data-ar="تفاصيل عقد" data-en="Show contract">تفاصيل عقد</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/admin/rental-contracts/{id}</code></td><td class="p-3" data-ar="تحديث عقد" data-en="Update contract">تحديث عقد</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/admin/rental-contracts/{id}/approve</code></td><td class="p-3" data-ar="قبول عقد" data-en="Approve">قبول عقد</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/admin/rental-contracts/{id}/reject</code></td><td class="p-3" data-ar="رفض عقد" data-en="Reject">رفض عقد</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/admin/rental-contracts/{id}/terminate</code></td><td class="p-3" data-ar="إنهاء عقد" data-en="Terminate">إنهاء عقد</td></tr>
                                <tr class="border-b bg-gray-50/50"><td colspan="3" class="p-2 font-bold text-gray-600 text-xs uppercase" data-ar="الفواتير" data-en="Invoices">الفواتير</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/admin/invoices</code></td><td class="p-3" data-ar="قائمة الفواتير" data-en="List invoices">قائمة الفواتير</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/admin/invoices</code></td><td class="p-3" data-ar="إنشاء فاتورة" data-en="Create invoice">إنشاء فاتورة</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/admin/invoices/{invoice}</code></td><td class="p-3" data-ar="تفاصيل فاتورة" data-en="Show invoice">تفاصيل فاتورة</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/admin/invoices/{invoice}</code></td><td class="p-3" data-ar="تحديث فاتورة" data-en="Update invoice">تحديث فاتورة</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/admin/invoices/{invoice}/issue</code></td><td class="p-3" data-ar="إصدار فاتورة" data-en="Issue invoice">إصدار فاتورة</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/admin/invoices/{invoice}/mark-paid</code></td><td class="p-3" data-ar="تأكيد الدفع" data-en="Mark paid">تأكيد الدفع</td></tr>
                                <tr><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/admin/invoices/{invoice}/cancel</code></td><td class="p-3" data-ar="إلغاء فاتورة" data-en="Cancel invoice">إلغاء فاتورة</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- ADMIN CMS --}}
                <section id="admin-cms" class="mb-20">
                    <h2 class="text-3xl font-bold mb-6" data-ar="إدارة المحتوى" data-en="Content Management">إدارة المحتوى</h2>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th></tr></thead>
                            <tbody>
                                <tr class="border-b bg-gray-50/50"><td colspan="3" class="p-2 font-bold text-gray-600 text-xs uppercase" data-ar="الصفحات" data-en="Pages">الصفحات</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/admin/pages</code></td><td class="p-3" data-ar="القائمة" data-en="List">القائمة</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/admin/pages</code></td><td class="p-3" data-ar="إنشاء" data-en="Create">إنشاء</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/admin/pages/{page}</code></td><td class="p-3" data-ar="تفاصيل" data-en="Show">تفاصيل</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/admin/pages/{page}</code></td><td class="p-3" data-ar="تحديث" data-en="Update">تحديث</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-delete text-white">DEL</span></td><td class="p-3"><code>/admin/pages/{page}</code></td><td class="p-3" data-ar="حذف" data-en="Delete">حذف</td></tr>
                                <tr class="border-b bg-gray-50/50"><td colspan="3" class="p-2 font-bold text-gray-600 text-xs uppercase" data-ar="الأسئلة الشائعة" data-en="FAQs">الأسئلة الشائعة</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/admin/faqs</code></td><td class="p-3" data-ar="القائمة" data-en="List">القائمة</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/admin/faqs</code></td><td class="p-3" data-ar="إنشاء" data-en="Create">إنشاء</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/admin/faqs/{faq}</code></td><td class="p-3" data-ar="تفاصيل" data-en="Show">تفاصيل</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/admin/faqs/{faq}</code></td><td class="p-3" data-ar="تحديث" data-en="Update">تحديث</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-delete text-white">DEL</span></td><td class="p-3"><code>/admin/faqs/{faq}</code></td><td class="p-3" data-ar="حذف" data-en="Delete">حذف</td></tr>
                                <tr class="border-b bg-gray-50/50"><td colspan="3" class="p-2 font-bold text-gray-600 text-xs uppercase" data-ar="البانرات" data-en="Banners">البانرات</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/admin/banners</code></td><td class="p-3" data-ar="القائمة" data-en="List">القائمة</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/admin/banners</code></td><td class="p-3" data-ar="إنشاء" data-en="Create">إنشاء</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/admin/banners/{banner}</code></td><td class="p-3" data-ar="تفاصيل" data-en="Show">تفاصيل</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/admin/banners/{banner}</code></td><td class="p-3" data-ar="تحديث" data-en="Update">تحديث</td></tr>
                                <tr><td class="p-3"><span class="badge method-delete text-white">DEL</span></td><td class="p-3"><code>/admin/banners/{banner}</code></td><td class="p-3" data-ar="حذف" data-en="Delete">حذف</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- ==================== ROLE-BASED ENDPOINTS ==================== --}}

                {{-- SUPERVISOR --}}
                <section id="supervisor-api" class="mb-20">
                    <h2 class="text-3xl font-bold mb-4" data-ar="واجهة المشرف" data-en="Supervisor API">واجهة المشرف</h2>
                    <div class="bg-orange-50 border border-orange-200 rounded-xl p-5 mb-6">
                        <p class="text-sm text-orange-800" data-ar="يتطلب دور supervisor - صلاحيات استعراض + إدارة الطلبات" data-en="Requires supervisor role - read access + request management">يتطلب دور <code class="bg-orange-100 px-1 rounded">supervisor</code> — صلاحيات استعراض + إدارة الطلبات</p>
                    </div>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th></tr></thead>
                            <tbody>
                                <tr class="border-b bg-orange-50/50"><td colspan="3" class="p-2 font-bold text-orange-800 text-xs uppercase">Dashboard & Statistics</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/supervisor/dashboard</code></td><td class="p-3" data-ar="لوحة التحكم" data-en="Dashboard">لوحة التحكم</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/supervisor/statistics</code></td><td class="p-3" data-ar="الإحصائيات" data-en="Statistics">الإحصائيات</td></tr>
                                <tr class="border-b bg-orange-50/50"><td colspan="3" class="p-2 font-bold text-orange-800 text-xs uppercase" data-ar="الفعاليات والمساحات (قراءة فقط)" data-en="Events & Spaces (Read-Only)">الفعاليات والمساحات (قراءة فقط)</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/supervisor/events</code></td><td class="p-3" data-ar="قائمة الفعاليات" data-en="List events">قائمة الفعاليات</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/supervisor/events/{event}</code></td><td class="p-3" data-ar="تفاصيل فعالية" data-en="Show event">تفاصيل فعالية</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/supervisor/events/{event}/sections</code></td><td class="p-3" data-ar="أقسام الفعالية" data-en="Event sections">أقسام الفعالية</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/supervisor/events/{event}/spaces</code></td><td class="p-3" data-ar="مساحات الفعالية" data-en="Event spaces">مساحات الفعالية</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/supervisor/sections/{section}</code></td><td class="p-3" data-ar="تفاصيل قسم" data-en="Show section">تفاصيل قسم</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/supervisor/spaces/{space}</code></td><td class="p-3" data-ar="تفاصيل مساحة" data-en="Show space">تفاصيل مساحة</td></tr>
                                <tr class="border-b bg-orange-50/50"><td colspan="3" class="p-2 font-bold text-orange-800 text-xs uppercase" data-ar="الخدمات (قراءة فقط)" data-en="Services (Read-Only)">الخدمات (قراءة فقط)</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/supervisor/services</code></td><td class="p-3" data-ar="قائمة الخدمات" data-en="List services">قائمة الخدمات</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/supervisor/services/{service}</code></td><td class="p-3" data-ar="تفاصيل خدمة" data-en="Show service">تفاصيل خدمة</td></tr>
                                <tr class="border-b bg-orange-50/50"><td colspan="3" class="p-2 font-bold text-orange-800 text-xs uppercase" data-ar="إدارة الطلبات" data-en="Request Management">إدارة الطلبات (Approve/Reject)</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/supervisor/visit-requests</code></td><td class="p-3" data-ar="طلبات الزيارة" data-en="Visit requests">طلبات الزيارة</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/supervisor/visit-requests/{id}</code></td><td class="p-3" data-ar="تفاصيل طلب زيارة" data-en="Show visit request">تفاصيل طلب زيارة</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/supervisor/visit-requests/{id}/approve</code></td><td class="p-3" data-ar="قبول" data-en="Approve">قبول</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/supervisor/visit-requests/{id}/reject</code></td><td class="p-3" data-ar="رفض" data-en="Reject">رفض</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/supervisor/rental-requests</code></td><td class="p-3" data-ar="طلبات الإيجار" data-en="Rental requests">طلبات الإيجار</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/supervisor/rental-requests/{id}</code></td><td class="p-3" data-ar="تفاصيل طلب إيجار" data-en="Show rental request">تفاصيل طلب إيجار</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/supervisor/rental-requests/{id}/approve</code></td><td class="p-3" data-ar="قبول" data-en="Approve">قبول</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/supervisor/rental-requests/{id}/reject</code></td><td class="p-3" data-ar="رفض" data-en="Reject">رفض</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/supervisor/rental-requests/{id}/payment</code></td><td class="p-3" data-ar="تسجيل دفعة" data-en="Record payment">تسجيل دفعة</td></tr>
                                <tr class="border-b bg-orange-50/50"><td colspan="3" class="p-2 font-bold text-orange-800 text-xs uppercase" data-ar="الملفات التجارية" data-en="Business Profiles">الملفات التجارية</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/supervisor/profiles</code></td><td class="p-3" data-ar="قائمة الملفات التجارية" data-en="List profiles">قائمة الملفات التجارية</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/supervisor/profiles/{id}</code></td><td class="p-3" data-ar="تفاصيل ملف تجاري" data-en="Show profile">تفاصيل ملف تجاري</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/supervisor/profiles/{id}/approve</code></td><td class="p-3" data-ar="قبول" data-en="Approve">قبول</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/supervisor/profiles/{id}/reject</code></td><td class="p-3" data-ar="رفض" data-en="Reject">رفض</td></tr>
                                <tr class="border-b bg-orange-50/50"><td colspan="3" class="p-2 font-bold text-orange-800 text-xs uppercase" data-ar="الرعاة" data-en="Sponsors">الرعاة (قراءة فقط)</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/supervisor/sponsors</code></td><td class="p-3" data-ar="قائمة الرعاة" data-en="List sponsors">قائمة الرعاة</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/supervisor/sponsors/{id}</code></td><td class="p-3" data-ar="تفاصيل راعي" data-en="Show sponsor">تفاصيل راعي</td></tr>
                                <tr class="border-b bg-orange-50/50"><td colspan="3" class="p-2 font-bold text-orange-800 text-xs uppercase" data-ar="عقود الرعاة" data-en="Sponsor Contracts">عقود الرعاة</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/supervisor/sponsor-contracts</code></td><td class="p-3" data-ar="قائمة العقود" data-en="List contracts">قائمة العقود</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/supervisor/sponsor-contracts/{id}</code></td><td class="p-3" data-ar="تفاصيل عقد" data-en="Show contract">تفاصيل عقد</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/supervisor/sponsor-contracts/{id}/approve</code></td><td class="p-3" data-ar="قبول" data-en="Approve">قبول</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/supervisor/sponsor-contracts/{id}/reject</code></td><td class="p-3" data-ar="رفض" data-en="Reject">رفض</td></tr>
                                <tr class="border-b bg-orange-50/50"><td colspan="3" class="p-2 font-bold text-orange-800 text-xs uppercase" data-ar="تذاكر الدعم" data-en="Support Tickets">تذاكر الدعم</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/supervisor/support-tickets</code></td><td class="p-3" data-ar="قائمة التذاكر" data-en="List tickets">قائمة التذاكر</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/supervisor/support-tickets/{id}</code></td><td class="p-3" data-ar="تفاصيل تذكرة" data-en="Show ticket">تفاصيل تذكرة</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/supervisor/support-tickets/{id}/reply</code></td><td class="p-3" data-ar="الرد" data-en="Reply">الرد</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/supervisor/support-tickets/{id}/resolve</code></td><td class="p-3" data-ar="حل التذكرة" data-en="Resolve">حل التذكرة</td></tr>
                                <tr class="border-b bg-orange-50/50"><td colspan="3" class="p-2 font-bold text-orange-800 text-xs uppercase" data-ar="عقود الإيجار" data-en="Rental Contracts">عقود الإيجار</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/supervisor/rental-contracts</code></td><td class="p-3" data-ar="قائمة العقود" data-en="List contracts">قائمة العقود</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/supervisor/rental-contracts/{id}</code></td><td class="p-3" data-ar="تفاصيل عقد" data-en="Show contract">تفاصيل عقد</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/supervisor/rental-contracts/{id}/approve</code></td><td class="p-3" data-ar="قبول" data-en="Approve">قبول</td></tr>
                                <tr><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/supervisor/rental-contracts/{id}/reject</code></td><td class="p-3" data-ar="رفض" data-en="Reject">رفض</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- SUPER ADMIN --}}
                <section id="superadmin-api" class="mb-20">
                    <h2 class="text-3xl font-bold mb-4" data-ar="واجهة المدير العام" data-en="Super Admin API">واجهة المدير العام</h2>
                    <div class="bg-red-50 border border-red-200 rounded-xl p-5 mb-6">
                        <p class="text-sm text-red-800" data-ar="يتطلب دور super-admin — أعلى مستوى صلاحيات" data-en="Requires super-admin role — highest privilege level">يتطلب دور <code class="bg-red-100 px-1 rounded">super-admin</code> — أعلى مستوى صلاحيات</p>
                    </div>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/super-admin/dashboard</code></td><td class="p-3" data-ar="لوحة التحكم" data-en="Dashboard">لوحة التحكم</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/super-admin/statistics</code></td><td class="p-3" data-ar="الإحصائيات" data-en="Statistics">الإحصائيات</td></tr>
                                <tr class="border-b bg-gray-50/50"><td colspan="3" class="p-2 font-bold text-gray-600 text-xs uppercase" data-ar="التصنيفات والمدن" data-en="Categories & Cities">التصنيفات والمدن (CRUD)</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/super-admin/categories</code></td><td class="p-3" data-ar="قائمة التصنيفات" data-en="List categories">التصنيفات</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/super-admin/categories</code></td><td class="p-3" data-ar="إنشاء تصنيف" data-en="Create category">إنشاء تصنيف</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/super-admin/categories/{id}</code></td><td class="p-3" data-ar="تفاصيل تصنيف" data-en="Show category">تفاصيل تصنيف</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/super-admin/categories/{id}</code></td><td class="p-3" data-ar="تحديث" data-en="Update">تحديث</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-delete text-white">DEL</span></td><td class="p-3"><code>/super-admin/categories/{id}</code></td><td class="p-3" data-ar="حذف" data-en="Delete">حذف</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/super-admin/cities</code></td><td class="p-3" data-ar="المدن" data-en="Cities">المدن</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/super-admin/cities</code></td><td class="p-3" data-ar="إنشاء مدينة" data-en="Create city">إنشاء مدينة</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/super-admin/cities/{id}</code></td><td class="p-3" data-ar="تفاصيل مدينة" data-en="Show city">تفاصيل مدينة</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/super-admin/cities/{id}</code></td><td class="p-3" data-ar="تحديث" data-en="Update">تحديث</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-delete text-white">DEL</span></td><td class="p-3"><code>/super-admin/cities/{id}</code></td><td class="p-3" data-ar="حذف" data-en="Delete">حذف</td></tr>
                                <tr class="border-b bg-gray-50/50"><td colspan="3" class="p-2 font-bold text-gray-600 text-xs uppercase" data-ar="المستخدمون" data-en="Users">المستخدمون</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/super-admin/users</code></td><td class="p-3" data-ar="قائمة المستخدمين" data-en="List users">المستخدمون</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/super-admin/users/{id}</code></td><td class="p-3" data-ar="تفاصيل مستخدم" data-en="Show user">تفاصيل مستخدم</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/super-admin/users/{id}/approve</code></td><td class="p-3" data-ar="قبول" data-en="Approve">قبول</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/super-admin/users/{id}/reject</code></td><td class="p-3" data-ar="رفض" data-en="Reject">رفض</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/super-admin/users/{id}/suspend</code></td><td class="p-3" data-ar="تعليق" data-en="Suspend">تعليق</td></tr>
                                <tr class="border-b bg-gray-50/50"><td colspan="3" class="p-2 font-bold text-gray-600 text-xs uppercase" data-ar="الإعدادات" data-en="Settings">الإعدادات</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/super-admin/settings</code></td><td class="p-3" data-ar="قائمة الإعدادات" data-en="List settings">قائمة الإعدادات</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/super-admin/settings/{key}</code></td><td class="p-3" data-ar="قيمة إعداد" data-en="Get setting value">قيمة إعداد</td></tr>
                                <tr><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/super-admin/settings</code></td><td class="p-3" data-ar="تحديث الإعدادات" data-en="Update settings">تحديث الإعدادات</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- INVESTOR --}}
                <section id="investor-api" class="mb-20">
                    <h2 class="text-3xl font-bold mb-4" data-ar="واجهة المستثمر" data-en="Investor API">واجهة المستثمر</h2>
                    <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-5 mb-6">
                        <p class="text-sm text-indigo-800" data-ar="يتطلب دور investor — إدارة المساحات والطلبات والمدفوعات" data-en="Requires investor role — manage spaces, requests & payments">يتطلب دور <code class="bg-indigo-100 px-1 rounded">investor</code> — إدارة المساحات والطلبات والمدفوعات</p>
                    </div>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/investor/dashboard</code></td><td class="p-3" data-ar="لوحة التحكم" data-en="Dashboard">لوحة التحكم</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/investor/statistics</code></td><td class="p-3" data-ar="الإحصائيات" data-en="Statistics">الإحصائيات</td></tr>
                                <tr class="border-b bg-gray-50/50"><td colspan="3" class="p-2 font-bold text-gray-600 text-xs uppercase" data-ar="المساحات" data-en="Spaces">المساحات (CRUD)</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/investor/spaces</code></td><td class="p-3" data-ar="مساحاتي" data-en="My spaces">مساحاتي</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/investor/spaces</code></td><td class="p-3" data-ar="إنشاء مساحة" data-en="Create space">إنشاء مساحة</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/investor/spaces/{id}</code></td><td class="p-3" data-ar="تفاصيل مساحة" data-en="Show space">تفاصيل مساحة</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/investor/spaces/{id}</code></td><td class="p-3" data-ar="تحديث" data-en="Update">تحديث</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-delete text-white">DEL</span></td><td class="p-3"><code>/investor/spaces/{id}</code></td><td class="p-3" data-ar="حذف" data-en="Delete">حذف</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/investor/spaces/{id}/services</code></td><td class="p-3" data-ar="إضافة خدمات" data-en="Add services">إضافة خدمات</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-delete text-white">DEL</span></td><td class="p-3"><code>/investor/spaces/{id}/services</code></td><td class="p-3" data-ar="إزالة خدمات" data-en="Remove services">إزالة خدمات</td></tr>
                                <tr class="border-b bg-gray-50/50"><td colspan="3" class="p-2 font-bold text-gray-600 text-xs uppercase" data-ar="طلبات الإيجار" data-en="Rental Requests">طلبات الإيجار</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/investor/rental-requests</code></td><td class="p-3" data-ar="طلبات الإيجار" data-en="Rental requests">طلبات الإيجار</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/investor/rental-requests/pending-count</code></td><td class="p-3" data-ar="عدد الطلبات المعلقة" data-en="Pending count">عدد الطلبات المعلقة</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/investor/rental-requests/{id}</code></td><td class="p-3" data-ar="تفاصيل طلب" data-en="Show request">تفاصيل طلب</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/investor/rental-requests/{id}/approve</code></td><td class="p-3" data-ar="قبول" data-en="Approve">قبول</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/investor/rental-requests/{id}/reject</code></td><td class="p-3" data-ar="رفض" data-en="Reject">رفض</td></tr>
                                <tr class="border-b bg-gray-50/50"><td colspan="3" class="p-2 font-bold text-gray-600 text-xs uppercase" data-ar="طلبات الزيارة" data-en="Visit Requests">طلبات الزيارة</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/investor/visit-requests</code></td><td class="p-3" data-ar="طلبات الزيارة" data-en="Visit requests">طلبات الزيارة</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/investor/visit-requests/pending-count</code></td><td class="p-3" data-ar="عدد الطلبات المعلقة" data-en="Pending count">عدد الطلبات المعلقة</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/investor/visit-requests/{id}</code></td><td class="p-3" data-ar="تفاصيل طلب" data-en="Show request">تفاصيل طلب</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/investor/visit-requests/{id}/approve</code></td><td class="p-3" data-ar="قبول" data-en="Approve">قبول</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/investor/visit-requests/{id}/reject</code></td><td class="p-3" data-ar="رفض" data-en="Reject">رفض</td></tr>
                                <tr class="border-b bg-gray-50/50"><td colspan="3" class="p-2 font-bold text-gray-600 text-xs uppercase" data-ar="المدفوعات" data-en="Payments">المدفوعات</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/investor/payments</code></td><td class="p-3" data-ar="سجل المدفوعات" data-en="Payments">سجل المدفوعات</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/investor/payments/summary</code></td><td class="p-3" data-ar="ملخص الإيرادات" data-en="Revenue summary">ملخص الإيرادات</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/investor/payments/{rentalRequest}</code></td><td class="p-3" data-ar="تفاصيل مدفوعات طلب" data-en="Payment details for request">تفاصيل مدفوعات طلب</td></tr>
                                <tr class="border-b bg-gray-50/50"><td colspan="3" class="p-2 font-bold text-gray-600 text-xs uppercase" data-ar="عقود الإيجار" data-en="Rental Contracts">عقود الإيجار</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/investor/rental-contracts</code></td><td class="p-3" data-ar="قائمة العقود" data-en="Contracts">العقود</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/investor/rental-contracts/{id}</code></td><td class="p-3" data-ar="تفاصيل عقد" data-en="Show contract">تفاصيل عقد</td></tr>
                                <tr><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/investor/rental-contracts/{id}/sign</code></td><td class="p-3" data-ar="توقيع العقد" data-en="Sign contract">توقيع العقد</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- SPONSOR SELF-SERVICE --}}
                <section id="sponsor-api" class="mb-20">
                    <h2 class="text-3xl font-bold mb-4" data-ar="واجهة الراعي" data-en="Sponsor API">واجهة الراعي</h2>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-5 mb-6">
                        <p class="text-sm text-yellow-800" data-ar="يتطلب دور sponsor — خدمة ذاتية للرعاة" data-en="Requires sponsor role — self-service for sponsors">يتطلب دور <code class="bg-yellow-100 px-1 rounded">sponsor</code> — خدمة ذاتية للرعاة</p>
                    </div>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/sponsor/dashboard</code></td><td class="p-3" data-ar="لوحة التحكم" data-en="Dashboard">لوحة التحكم</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/sponsor/statistics</code></td><td class="p-3" data-ar="الإحصائيات" data-en="Statistics">الإحصائيات</td></tr>
                                <tr class="border-b bg-gray-50/50"><td colspan="3" class="p-2 font-bold text-gray-600 text-xs uppercase" data-ar="العقود" data-en="Contracts">العقود</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/sponsor/contracts</code></td><td class="p-3" data-ar="قائمة العقود" data-en="List contracts">العقود</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/sponsor/contracts/{id}</code></td><td class="p-3" data-ar="تفاصيل عقد" data-en="Show contract">تفاصيل عقد</td></tr>
                                <tr class="border-b bg-gray-50/50"><td colspan="3" class="p-2 font-bold text-gray-600 text-xs uppercase" data-ar="المدفوعات" data-en="Payments">المدفوعات</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/sponsor/payments</code></td><td class="p-3" data-ar="قائمة المدفوعات" data-en="List payments">المدفوعات</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/sponsor/payments/{id}</code></td><td class="p-3" data-ar="تفاصيل دفعة" data-en="Show payment">تفاصيل دفعة</td></tr>
                                <tr class="border-b bg-gray-50/50"><td colspan="3" class="p-2 font-bold text-gray-600 text-xs uppercase" data-ar="الملفات" data-en="Assets">الملفات (CRUD)</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/sponsor/assets</code></td><td class="p-3" data-ar="قائمة الملفات" data-en="List assets">الملفات</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/sponsor/assets</code></td><td class="p-3" data-ar="رفع ملف" data-en="Upload asset">رفع ملف</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/sponsor/assets/{id}</code></td><td class="p-3" data-ar="تفاصيل ملف" data-en="Show asset">تفاصيل ملف</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/sponsor/assets/{id}</code></td><td class="p-3" data-ar="تحديث ملف" data-en="Update asset">تحديث</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-delete text-white">DEL</span></td><td class="p-3"><code>/sponsor/assets/{id}</code></td><td class="p-3" data-ar="حذف ملف" data-en="Delete asset">حذف</td></tr>
                                <tr class="border-b bg-gray-50/50"><td colspan="3" class="p-2 font-bold text-gray-600 text-xs uppercase" data-ar="التعرض والتقارير" data-en="Exposure & Reports">التعرض والتقارير</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/sponsor/exposure</code></td><td class="p-3" data-ar="تقرير التعرض / ROI" data-en="Exposure / ROI report">تقرير التعرض / ROI</td></tr>
                                <tr><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/sponsor/exposure/summary</code></td><td class="p-3" data-ar="ملخص التعرض" data-en="Exposure summary">ملخص التعرض</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- MERCHANT --}}
                <section id="merchant-api" class="mb-20">
                    <h2 class="text-3xl font-bold mb-4" data-ar="واجهة التاجر" data-en="Merchant API">واجهة التاجر</h2>
                    <div class="bg-sky-50 border border-sky-200 rounded-xl p-5 mb-6">
                        <p class="text-sm text-sky-800" data-ar="يتطلب دور merchant — تصفح وطلب إيجار مساحات" data-en="Requires merchant role — browse and request space rentals">يتطلب دور <code class="bg-sky-100 px-1 rounded">merchant</code> — تصفح وطلب إيجار مساحات (يتطلب ملف تجاري موثق)</p>
                    </div>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/merchant/dashboard</code></td><td class="p-3" data-ar="لوحة التحكم" data-en="Dashboard">لوحة التحكم</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/merchant/statistics</code></td><td class="p-3" data-ar="الإحصائيات" data-en="Statistics">الإحصائيات</td></tr>
                                <tr class="border-b bg-gray-50/50"><td colspan="3" class="p-2 font-bold text-gray-600 text-xs uppercase" data-ar="تصفح الفعاليات والمساحات والخدمات" data-en="Browse Events, Spaces & Services">تصفح الفعاليات والمساحات والخدمات</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/merchant/events</code></td><td class="p-3" data-ar="قائمة الفعاليات" data-en="List events">قائمة الفعاليات</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/merchant/events/{event}</code></td><td class="p-3" data-ar="تفاصيل فعالية" data-en="Show event">تفاصيل فعالية</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/merchant/events/{event}/sections</code></td><td class="p-3" data-ar="أقسام الفعالية" data-en="Event sections">أقسام الفعالية</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/merchant/events/{event}/spaces</code></td><td class="p-3" data-ar="مساحات الفعالية" data-en="Event spaces">مساحات الفعالية</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/merchant/spaces</code></td><td class="p-3" data-ar="تصفح المساحات" data-en="Browse spaces">تصفح المساحات</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/merchant/spaces/{space}</code></td><td class="p-3" data-ar="تفاصيل مساحة" data-en="Show space">تفاصيل مساحة</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/merchant/services</code></td><td class="p-3" data-ar="قائمة الخدمات" data-en="List services">قائمة الخدمات</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/merchant/services/{service}</code></td><td class="p-3" data-ar="تفاصيل خدمة" data-en="Show service">تفاصيل خدمة</td></tr>
                                <tr class="border-b bg-gray-50/50"><td colspan="3" class="p-2 font-bold text-gray-600 text-xs uppercase" data-ar="طلبات الزيارة" data-en="Visit Requests">طلبات الزيارة (CRUD)</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/merchant/visit-requests</code></td><td class="p-3" data-ar="قائمة طلبات الزيارة" data-en="List visit requests">قائمة طلبات الزيارة</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/merchant/visit-requests</code></td><td class="p-3" data-ar="طلب زيارة جديد" data-en="New visit request">طلب زيارة جديد</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/merchant/visit-requests/{id}</code></td><td class="p-3" data-ar="تفاصيل طلب زيارة" data-en="Show visit request">تفاصيل طلب زيارة</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/merchant/visit-requests/{id}</code></td><td class="p-3" data-ar="تحديث طلب" data-en="Update request">تحديث طلب</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-delete text-white">DEL</span></td><td class="p-3"><code>/merchant/visit-requests/{id}</code></td><td class="p-3" data-ar="حذف طلب" data-en="Delete request">حذف طلب</td></tr>
                                <tr class="border-b bg-gray-50/50"><td colspan="3" class="p-2 font-bold text-gray-600 text-xs uppercase" data-ar="طلبات الإيجار" data-en="Rental Requests">طلبات الإيجار (CRUD)</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/merchant/rental-requests</code></td><td class="p-3" data-ar="قائمة طلبات الإيجار" data-en="List rental requests">قائمة طلبات الإيجار</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/merchant/rental-requests</code></td><td class="p-3" data-ar="طلب إيجار جديد" data-en="New rental request">طلب إيجار جديد</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/merchant/rental-requests/{id}</code></td><td class="p-3" data-ar="تفاصيل طلب إيجار" data-en="Show rental request">تفاصيل طلب إيجار</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/merchant/rental-requests/{id}</code></td><td class="p-3" data-ar="تحديث" data-en="Update">تحديث</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-delete text-white">DEL</span></td><td class="p-3"><code>/merchant/rental-requests/{id}</code></td><td class="p-3" data-ar="إلغاء" data-en="Cancel">إلغاء</td></tr>
                                <tr class="border-b bg-gray-50/50"><td colspan="3" class="p-2 font-bold text-gray-600 text-xs uppercase" data-ar="عقود الإيجار" data-en="Rental Contracts">عقود الإيجار</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/merchant/rental-contracts</code></td><td class="p-3" data-ar="قائمة العقود" data-en="List contracts">العقود</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/merchant/rental-contracts/{id}</code></td><td class="p-3" data-ar="تفاصيل عقد" data-en="Show contract">تفاصيل عقد</td></tr>
                                <tr><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/merchant/rental-contracts/{id}/sign</code></td><td class="p-3" data-ar="توقيع العقد" data-en="Sign contract">توقيع العقد</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- ==================== REFERENCE ==================== --}}

                {{-- ERRORS --}}
                <section id="errors" class="mb-20">
                    <h2 class="text-3xl font-bold mb-4" data-ar="رموز الحالة HTTP" data-en="HTTP Status Codes">رموز الحالة HTTP</h2>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200 mb-8">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-4 text-right" data-ar="الرمز" data-en="Code">الرمز</th><th class="p-4 text-right" data-ar="الاسم" data-en="Name">الاسم</th><th class="p-4 text-right" data-ar="الوصف" data-en="Description">الوصف</th></tr></thead>
                            <tbody>
                                <tr class="border-b bg-green-50/50"><td colspan="3" class="p-2 font-bold text-green-800 text-xs uppercase tracking-wider" data-ar="نجاح" data-en="Success">نجاح</td></tr>
                                <tr class="border-b"><td class="p-4"><span class="bg-green-100 text-green-700 px-2 py-1 rounded font-mono">200</span></td><td class="p-4">OK</td><td class="p-4 text-gray-600" data-ar="تمت العملية بنجاح" data-en="Request succeeded">تمت العملية بنجاح</td></tr>
                                <tr class="border-b"><td class="p-4"><span class="bg-green-100 text-green-700 px-2 py-1 rounded font-mono">201</span></td><td class="p-4">Created</td><td class="p-4 text-gray-600" data-ar="تم إنشاء المورد بنجاح" data-en="Resource created">تم إنشاء المورد بنجاح</td></tr>
                                <tr class="border-b bg-yellow-50/50"><td colspan="3" class="p-2 font-bold text-yellow-800 text-xs uppercase tracking-wider" data-ar="أخطاء العميل" data-en="Client Errors">أخطاء العميل</td></tr>
                                <tr class="border-b"><td class="p-4"><span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded font-mono">400</span></td><td class="p-4">Bad Request</td><td class="p-4 text-gray-600" data-ar="خطأ في التحقق" data-en="Validation error">خطأ في التحقق</td></tr>
                                <tr class="border-b"><td class="p-4"><span class="bg-red-100 text-red-700 px-2 py-1 rounded font-mono">401</span></td><td class="p-4">Unauthorized</td><td class="p-4 text-gray-600" data-ar="غير مصرح - التوكن مفقود أو منتهي" data-en="Not authenticated">غير مصرح - التوكن مفقود أو منتهي</td></tr>
                                <tr class="border-b"><td class="p-4"><span class="bg-red-100 text-red-700 px-2 py-1 rounded font-mono">403</span></td><td class="p-4">Forbidden</td><td class="p-4 text-gray-600" data-ar="ليس لديك صلاحية" data-en="No permission">ليس لديك صلاحية</td></tr>
                                <tr class="border-b"><td class="p-4"><span class="bg-orange-100 text-orange-700 px-2 py-1 rounded font-mono">404</span></td><td class="p-4">Not Found</td><td class="p-4 text-gray-600" data-ar="المورد غير موجود" data-en="Resource not found">المورد غير موجود</td></tr>
                                <tr class="border-b"><td class="p-4"><span class="bg-orange-100 text-orange-700 px-2 py-1 rounded font-mono">422</span></td><td class="p-4">Unprocessable</td><td class="p-4 text-gray-600" data-ar="لا يمكن معالجة البيانات" data-en="Cannot process">لا يمكن معالجة البيانات</td></tr>
                                <tr class="border-b"><td class="p-4"><span class="bg-purple-100 text-purple-700 px-2 py-1 rounded font-mono">429</span></td><td class="p-4">Too Many Requests</td><td class="p-4 text-gray-600" data-ar="تم تجاوز حد الطلبات" data-en="Rate limit exceeded">تم تجاوز حد الطلبات</td></tr>
                                <tr class="border-b bg-red-50/50"><td colspan="3" class="p-2 font-bold text-red-800 text-xs uppercase tracking-wider" data-ar="أخطاء الخادم" data-en="Server Errors">أخطاء الخادم</td></tr>
                                <tr><td class="p-4"><span class="bg-red-100 text-red-700 px-2 py-1 rounded font-mono">500</span></td><td class="p-4">Server Error</td><td class="p-4 text-gray-600" data-ar="خطأ في الخادم" data-en="Server error">خطأ في الخادم</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- ERROR CODES --}}
                <section id="error-codes" class="mb-20">
                    <h2 class="text-3xl font-bold mb-6" data-ar="رموز الأخطاء" data-en="Error Codes Reference">رموز الأخطاء</h2>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Error Code</th><th class="p-3 text-right">HTTP</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th></tr></thead>
                            <tbody>
                                <tr class="border-b bg-red-50/50"><td colspan="3" class="p-2 font-bold text-red-800 text-xs uppercase tracking-wider">Authentication</td></tr>
                                <tr class="border-b"><td class="p-3"><code>authentication_required</code></td><td class="p-3">401</td><td class="p-3" data-ar="غير مصرح لك بالوصول" data-en="Unauthenticated">غير مصرح لك بالوصول</td></tr>
                                <tr class="border-b"><td class="p-3"><code>token_expired</code></td><td class="p-3">401</td><td class="p-3" data-ar="انتهت صلاحية التوكن" data-en="Token expired">انتهت صلاحية التوكن</td></tr>
                                <tr class="border-b"><td class="p-3"><code>token_invalid</code></td><td class="p-3">401</td><td class="p-3" data-ar="التوكن غير صالح" data-en="Token invalid">التوكن غير صالح</td></tr>
                                <tr class="border-b bg-orange-50/50"><td colspan="3" class="p-2 font-bold text-orange-800 text-xs uppercase tracking-wider">Authorization</td></tr>
                                <tr class="border-b"><td class="p-3"><code>permission_denied</code></td><td class="p-3">403</td><td class="p-3" data-ar="ليس لديك صلاحية" data-en="Permission denied">ليس لديك صلاحية</td></tr>
                                <tr class="border-b"><td class="p-3"><code>profile_not_verified</code></td><td class="p-3">403</td><td class="p-3" data-ar="الملف التجاري غير موثق" data-en="Profile not verified">الملف التجاري غير موثق</td></tr>
                                <tr class="border-b bg-yellow-50/50"><td colspan="3" class="p-2 font-bold text-yellow-800 text-xs uppercase tracking-wider">Validation</td></tr>
                                <tr class="border-b"><td class="p-3"><code>validation_failed</code></td><td class="p-3">400</td><td class="p-3" data-ar="فشل التحقق من البيانات" data-en="Validation failed">فشل التحقق من البيانات</td></tr>
                                <tr class="border-b"><td class="p-3"><code>resource_not_found</code></td><td class="p-3">404</td><td class="p-3" data-ar="المورد غير موجود" data-en="Resource not found">المورد غير موجود</td></tr>
                                <tr class="border-b"><td class="p-3"><code>profile_already_exists</code></td><td class="p-3">422</td><td class="p-3" data-ar="الملف التجاري موجود مسبقاً" data-en="Profile already exists">الملف التجاري موجود مسبقاً</td></tr>
                                <tr class="border-b"><td class="p-3"><code>space_not_available</code></td><td class="p-3">422</td><td class="p-3" data-ar="المساحة غير متاحة" data-en="Space not available">المساحة غير متاحة</td></tr>
                                <tr class="border-b"><td class="p-3"><code>event_not_active</code></td><td class="p-3">422</td><td class="p-3" data-ar="الفعالية غير نشطة" data-en="Event not active">الفعالية غير نشطة</td></tr>
                                <tr><td class="p-3"><code>rate_limit_exceeded</code></td><td class="p-3">429</td><td class="p-3" data-ar="تم تجاوز حد الطلبات" data-en="Too many requests">تم تجاوز حد الطلبات</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- RATE LIMITING --}}
                <section id="rate-limiting" class="mb-20">
                    <h2 class="text-3xl font-bold mb-4" data-ar="حد الطلبات" data-en="Rate Limiting">حد الطلبات</h2>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-4 text-right" data-ar="النوع" data-en="Type">النوع</th><th class="p-4 text-right" data-ar="الحد" data-en="Limit">الحد</th><th class="p-4 text-right" data-ar="الفترة" data-en="Window">الفترة</th></tr></thead>
                            <tbody>
                                <tr><td class="p-4">API Requests</td><td class="p-4">60</td><td class="p-4" data-ar="دقيقة" data-en="minute">دقيقة</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- REQUEST STATUSES --}}
                <section id="request-statuses" class="mb-20">
                    <h2 class="text-3xl font-bold mb-6" data-ar="حالات الطلبات" data-en="Request Statuses">حالات الطلبات</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-white rounded-xl border border-gray-200 p-5">
                            <h4 class="font-bold mb-3" data-ar="طلبات الزيارة" data-en="Visit Requests">طلبات الزيارة</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex items-center gap-2"><span class="w-2 h-2 bg-yellow-400 rounded-full"></span><code>pending</code> - <span data-ar="قيد المراجعة" data-en="Pending review">قيد المراجعة</span></div>
                                <div class="flex items-center gap-2"><span class="w-2 h-2 bg-green-400 rounded-full"></span><code>approved</code> - <span data-ar="مقبول" data-en="Approved">مقبول</span></div>
                                <div class="flex items-center gap-2"><span class="w-2 h-2 bg-red-400 rounded-full"></span><code>rejected</code> - <span data-ar="مرفوض" data-en="Rejected">مرفوض</span></div>
                                <div class="flex items-center gap-2"><span class="w-2 h-2 bg-gray-400 rounded-full"></span><code>cancelled</code> - <span data-ar="ملغي" data-en="Cancelled">ملغي</span></div>
                            </div>
                        </div>
                        <div class="bg-white rounded-xl border border-gray-200 p-5">
                            <h4 class="font-bold mb-3" data-ar="طلبات الإيجار" data-en="Rental Requests">طلبات الإيجار</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex items-center gap-2"><span class="w-2 h-2 bg-yellow-400 rounded-full"></span><code>pending</code> - <span data-ar="قيد المراجعة" data-en="Pending">قيد المراجعة</span></div>
                                <div class="flex items-center gap-2"><span class="w-2 h-2 bg-green-400 rounded-full"></span><code>approved</code> - <span data-ar="مقبول" data-en="Approved">مقبول</span></div>
                                <div class="flex items-center gap-2"><span class="w-2 h-2 bg-red-400 rounded-full"></span><code>rejected</code> - <span data-ar="مرفوض" data-en="Rejected">مرفوض</span></div>
                                <div class="flex items-center gap-2"><span class="w-2 h-2 bg-emerald-400 rounded-full"></span><code>paid</code> - <span data-ar="مدفوع بالكامل" data-en="Fully paid">مدفوع بالكامل</span></div>
                                <div class="flex items-center gap-2"><span class="w-2 h-2 bg-blue-400 rounded-full"></span><code>partially_paid</code> - <span data-ar="مدفوع جزئياً" data-en="Partially paid">مدفوع جزئياً</span></div>
                            </div>
                        </div>
                        <div class="bg-white rounded-xl border border-gray-200 p-5">
                            <h4 class="font-bold mb-3" data-ar="الملفات التجارية" data-en="Business Profiles">الملفات التجارية</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex items-center gap-2"><span class="w-2 h-2 bg-yellow-400 rounded-full"></span><code>pending</code> - <span data-ar="قيد المراجعة" data-en="Pending">قيد المراجعة</span></div>
                                <div class="flex items-center gap-2"><span class="w-2 h-2 bg-green-400 rounded-full"></span><code>approved</code> - <span data-ar="موثق" data-en="Approved">موثق</span></div>
                                <div class="flex items-center gap-2"><span class="w-2 h-2 bg-red-400 rounded-full"></span><code>rejected</code> - <span data-ar="مرفوض" data-en="Rejected">مرفوض</span></div>
                            </div>
                        </div>
                        <div class="bg-white rounded-xl border border-gray-200 p-5">
                            <h4 class="font-bold mb-3" data-ar="المساحات" data-en="Spaces">المساحات</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex items-center gap-2"><span class="w-2 h-2 bg-green-400 rounded-full"></span><code>available</code> - <span data-ar="متاحة" data-en="Available">متاحة</span></div>
                                <div class="flex items-center gap-2"><span class="w-2 h-2 bg-yellow-400 rounded-full"></span><code>reserved</code> - <span data-ar="محجوزة" data-en="Reserved">محجوزة</span></div>
                                <div class="flex items-center gap-2"><span class="w-2 h-2 bg-blue-400 rounded-full"></span><code>rented</code> - <span data-ar="مؤجرة" data-en="Rented">مؤجرة</span></div>
                                <div class="flex items-center gap-2"><span class="w-2 h-2 bg-gray-400 rounded-full"></span><code>maintenance</code> - <span data-ar="صيانة" data-en="Maintenance">صيانة</span></div>
                            </div>
                        </div>
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

        // Active link highlighting
        const sections = document.querySelectorAll('section[id]');
        const navLinks = document.querySelectorAll('.sidebar-link');

        window.addEventListener('scroll', () => {
            let current = '';
            sections.forEach(section => {
                if (scrollY >= section.offsetTop - 120) {
                    current = section.getAttribute('id');
                }
            });
            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === '#' + current) {
                    link.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>
