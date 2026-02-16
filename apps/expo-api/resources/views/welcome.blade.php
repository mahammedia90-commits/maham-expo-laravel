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
                    </ul>
                </div>
                <div>
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-3" data-ar="المستخدم" data-en="User">المستخدم</h3>
                    <ul class="space-y-0.5">
                        <li><a href="#profile" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="الملف التجاري" data-en="Business Profile">الملف التجاري</a></li>
                        <li><a href="#favorites" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="المفضلة" data-en="Favorites">المفضلة</a></li>
                        <li><a href="#notifications" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="الإشعارات" data-en="Notifications">الإشعارات</a></li>
                        <li><a href="#visit-requests" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="طلبات الزيارة" data-en="Visit Requests">طلبات الزيارة</a></li>
                        <li><a href="#rental-requests" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="طلبات الإيجار" data-en="Rental Requests">طلبات الإيجار</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-3" data-ar="الإدارة" data-en="Admin">الإدارة</h3>
                    <ul class="space-y-0.5">
                        <li><a href="#admin-dashboard" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg"><span class="badge method-get text-white ml-1">GET</span> <span data-ar="لوحة التحكم" data-en="Dashboard">لوحة التحكم</span></a></li>
                        <li><a href="#admin-events" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="إدارة الفعاليات" data-en="Events Management">إدارة الفعاليات</a></li>
                        <li><a href="#admin-sections" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="إدارة الأقسام" data-en="Sections">إدارة الأقسام</a></li>
                        <li><a href="#admin-spaces" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="إدارة المساحات" data-en="Spaces">إدارة المساحات</a></li>
                        <li><a href="#admin-services" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="إدارة الخدمات" data-en="Services">إدارة الخدمات</a></li>
                        <li><a href="#admin-visit-requests" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="طلبات الزيارة" data-en="Visit Requests">طلبات الزيارة</a></li>
                        <li><a href="#admin-rental-requests" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="طلبات الإيجار" data-en="Rental Requests">طلبات الإيجار</a></li>
                        <li><a href="#admin-profiles" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="الملفات التجارية" data-en="Business Profiles">الملفات التجارية</a></li>
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
                                            <tr><td class="p-3"><code>per_page</code></td><td class="p-3">integer</td><td class="p-3" data-ar="عدد النتائج (افتراضي: 15)" data-en="Results per page (default: 15)">عدد النتائج (افتراضي: 15)</td></tr>
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
                                            <tr class="border-b"><td class="p-3"><code>commercial_reg</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500">*</span></td><td class="p-3" data-ar="رقم السجل التجاري" data-en="Commercial registration">رقم السجل التجاري</td></tr>
                                            <tr class="border-b"><td class="p-3"><code>phone</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500">*</span></td><td class="p-3">max:20</td></tr>
                                            <tr class="border-b"><td class="p-3"><code>city</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500">*</span></td><td class="p-3">max:100</td></tr>
                                            <tr class="border-b"><td class="p-3"><code>address</code></td><td class="p-3">string</td><td class="p-3 text-gray-400">-</td><td class="p-3">max:500</td></tr>
                                            <tr><td class="p-3"><code>description</code></td><td class="p-3">string</td><td class="p-3 text-gray-400">-</td><td class="p-3">max:1000</td></tr>
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
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/favorites</code></td><td class="p-3" data-ar="إضافة للمفضلة" data-en="Add to favorites">إضافة للمفضلة</td><td class="p-3"><span class="badge bg-yellow-100 text-yellow-700">Auth</span></td></tr>
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
                                <tr><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/notifications/read-all</code></td><td class="p-3" data-ar="قراءة الكل" data-en="Mark all as read">قراءة الكل</td></tr>
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
                                        <tr class="border-b"><td class="p-3"><code>event_id</code></td><td class="p-3">uuid</td><td class="p-3"><span class="text-red-500">*</span></td><td class="p-3" data-ar="معرف الفعالية" data-en="Event ID">معرف الفعالية</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>visit_date</code></td><td class="p-3">date</td><td class="p-3"><span class="text-red-500">*</span></td><td class="p-3" data-ar="تاريخ الزيارة (مستقبلي)" data-en="Visit date (future)">تاريخ الزيارة (مستقبلي)</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>visitors_count</code></td><td class="p-3">integer</td><td class="p-3"><span class="text-red-500">*</span></td><td class="p-3">min:1</td></tr>
                                        <tr><td class="p-3"><code>notes</code></td><td class="p-3">string</td><td class="p-3 text-gray-400">-</td><td class="p-3">max:500</td></tr>
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
                                        <tr class="border-b"><td class="p-3"><code>space_id</code></td><td class="p-3">uuid</td><td class="p-3"><span class="text-red-500">*</span></td><td class="p-3" data-ar="معرف المساحة" data-en="Space ID">معرف المساحة</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>start_date</code></td><td class="p-3">date</td><td class="p-3"><span class="text-red-500">*</span></td><td class="p-3" data-ar="تاريخ البداية" data-en="Start date">تاريخ البداية</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>end_date</code></td><td class="p-3">date</td><td class="p-3"><span class="text-red-500">*</span></td><td class="p-3" data-ar="تاريخ النهاية (بعد البداية)" data-en="End date (after start)">تاريخ النهاية (بعد البداية)</td></tr>
                                        <tr><td class="p-3"><code>notes</code></td><td class="p-3">string</td><td class="p-3 text-gray-400">-</td><td class="p-3">max:500</td></tr>
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
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
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
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/admin/visit-requests/{visitRequest}/approve</code></td><td class="p-3" data-ar="قبول الطلب" data-en="Approve request">قبول الطلب</td></tr>
                                <tr><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/admin/visit-requests/{visitRequest}/reject</code></td><td class="p-3" data-ar="رفض الطلب" data-en="Reject request">رفض الطلب</td></tr>
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
