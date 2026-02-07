<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">

    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maham Auth Service - API Documentation</title>
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
        .sidebar-link.active, .sidebar-link:hover { border-color: #3B82F6; background: #eff6ff; color: #1d4ed8; }
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
                <div class="w-9 h-9 bg-blue-500 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-lg font-bold leading-tight">Maham Auth Service</h1>
                    <p class="text-xs text-gray-400 leading-tight">API Documentation</p>
                </div>
                <span class="badge bg-green-500/20 text-green-400 mr-2">v{{ config('auth-service.service_version', '1.0.0') }}</span>
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
                        <li><a href="#quick-start" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="البدء السريع" data-en="Quick Start">البدء السريع</a></li>
                        <li><a href="#headers" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="الهيدرات" data-en="Headers">الهيدرات</a></li>
                        <li><a href="#authentication" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="المصادقة" data-en="Authentication">المصادقة</a></li>
                        <li><a href="#response-format" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="صيغة الردود" data-en="Response Format">صيغة الردود</a></li>
                        <li><a href="#errors" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="الأخطاء" data-en="Errors">الأخطاء</a></li>
                        <li><a href="#rate-limiting" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="حد الطلبات" data-en="Rate Limiting">حد الطلبات</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-3" data-ar="المصادقة" data-en="Auth">المصادقة</h3>
                    <ul class="space-y-0.5">
                        <li><a href="#register" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg"><span class="badge method-post text-white ml-1">POST</span> <span data-ar="التسجيل" data-en="Register">التسجيل</span></a></li>
                        <li><a href="#login" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg"><span class="badge method-post text-white ml-1">POST</span> <span data-ar="تسجيل الدخول" data-en="Login">تسجيل الدخول</span></a></li>
                        <li><a href="#logout" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg"><span class="badge method-post text-white ml-1">POST</span> <span data-ar="تسجيل الخروج" data-en="Logout">تسجيل الخروج</span></a></li>
                        <li><a href="#me" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg"><span class="badge method-get text-white ml-1">GET</span> <span data-ar="بيانات المستخدم" data-en="Current User">بيانات المستخدم</span></a></li>
                        <li><a href="#refresh" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg"><span class="badge method-post text-white ml-1">POST</span> <span data-ar="تجديد التوكن" data-en="Refresh Token">تجديد التوكن</span></a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-3" data-ar="كلمة المرور" data-en="Password">كلمة المرور</h3>
                    <ul class="space-y-0.5">
                        <li><a href="#forgot-password" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg"><span class="badge method-post text-white ml-1">POST</span> <span data-ar="نسيت كلمة المرور" data-en="Forgot Password">نسيت كلمة المرور</span></a></li>
                        <li><a href="#reset-password" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg"><span class="badge method-post text-white ml-1">POST</span> <span data-ar="إعادة التعيين" data-en="Reset Password">إعادة التعيين</span></a></li>
                        <li><a href="#change-password" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg"><span class="badge method-post text-white ml-1">POST</span> <span data-ar="تغيير كلمة المرور" data-en="Change Password">تغيير كلمة المرور</span></a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-3" data-ar="الملف الشخصي" data-en="Profile">الملف الشخصي</h3>
                    <ul class="space-y-0.5">
                        <li><a href="#update-profile" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg"><span class="badge method-put text-white ml-1">PUT</span> <span data-ar="تحديث الملف الشخصي" data-en="Update Profile">تحديث الملف الشخصي</span></a></li>
                        <li><a href="#send-verification" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg"><span class="badge method-post text-white ml-1">POST</span> <span data-ar="إرسال كود التحقق" data-en="Send Verification">إرسال كود التحقق</span></a></li>
                        <li><a href="#verify-email" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg"><span class="badge method-post text-white ml-1">POST</span> <span data-ar="تحقق من البريد" data-en="Verify Email">تحقق من البريد</span></a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-3" data-ar="التحقق" data-en="Verification">التحقق</h3>
                    <ul class="space-y-0.5">
                        <li><a href="#verify-token" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg"><span class="badge method-post text-white ml-1">POST</span> <span data-ar="التحقق من التوكن" data-en="Verify Token">التحقق من التوكن</span></a></li>
                        <li><a href="#check-permission" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg"><span class="badge method-post text-white ml-1">POST</span> <span data-ar="فحص صلاحية" data-en="Check Permission">فحص صلاحية</span></a></li>
                        <li><a href="#check-permissions" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg"><span class="badge method-post text-white ml-1">POST</span> <span data-ar="فحص عدة صلاحيات" data-en="Check Permissions">فحص عدة صلاحيات</span></a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-3" data-ar="إدارة المستخدمين" data-en="Users">إدارة المستخدمين</h3>
                    <ul class="space-y-0.5">
                        <li><a href="#users-list" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg"><span class="badge method-get text-white ml-1">GET</span> <span data-ar="قائمة المستخدمين" data-en="List Users">قائمة المستخدمين</span></a></li>
                        <li><a href="#users-create" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg"><span class="badge method-post text-white ml-1">POST</span> <span data-ar="إنشاء مستخدم" data-en="Create User">إنشاء مستخدم</span></a></li>
                        <li><a href="#users-show" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg"><span class="badge method-get text-white ml-1">GET</span> <span data-ar="عرض مستخدم" data-en="Show User">عرض مستخدم</span></a></li>
                        <li><a href="#users-update" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg"><span class="badge method-put text-white ml-1">PUT</span> <span data-ar="تحديث مستخدم" data-en="Update User">تحديث مستخدم</span></a></li>
                        <li><a href="#users-delete" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg"><span class="badge method-delete text-white ml-1">DEL</span> <span data-ar="حذف مستخدم" data-en="Delete User">حذف مستخدم</span></a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-3" data-ar="الأدوار والصلاحيات" data-en="Roles & Permissions">الأدوار والصلاحيات</h3>
                    <ul class="space-y-0.5">
                        <li><a href="#roles" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="إدارة الأدوار" data-en="Roles Management">إدارة الأدوار</a></li>
                        <li><a href="#permissions" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="إدارة الصلاحيات" data-en="Permissions Management">إدارة الصلاحيات</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-3" data-ar="الخدمات" data-en="Services">الخدمات</h3>
                    <ul class="space-y-0.5">
                        <li><a href="#services" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="إدارة الخدمات" data-en="Services Management">إدارة الخدمات</a></li>
                        <li><a href="#s2s" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="تواصل بين الخدمات" data-en="Service-to-Service">تواصل بين الخدمات</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-3" data-ar="المرجع" data-en="Reference">المرجع</h3>
                    <ul class="space-y-0.5">
                        <li><a href="#error-codes" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="جدول رموز الأخطاء" data-en="Error Codes Table">جدول رموز الأخطاء</a></li>
                        <li><a href="#default-roles" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="الأدوار الافتراضية" data-en="Default Roles">الأدوار الافتراضية</a></li>
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
                        <h1 class="text-4xl font-extrabold mb-3" data-ar="خدمة المصادقة المركزية" data-en="Centralized Auth Service">خدمة المصادقة المركزية</h1>
                        <p class="text-lg text-gray-500" data-ar="مصادقة JWT، نظام صلاحيات RBAC، وتواصل بين الخدمات" data-en="JWT Authentication, RBAC, and Service-to-Service communication">مصادقة JWT، نظام صلاحيات RBAC، وتواصل بين الخدمات</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                        <div class="bg-white rounded-xl p-5 border border-gray-200">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mb-3">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                            </div>
                            <h3 class="font-bold mb-1" data-ar="مصادقة JWT" data-en="JWT Auth">مصادقة JWT</h3>
                            <p class="text-sm text-gray-500" data-ar="توكنات آمنة مع تجديد وحظر" data-en="Secure tokens with refresh & blacklist">توكنات آمنة مع تجديد وحظر</p>
                        </div>
                        <div class="bg-white rounded-xl p-5 border border-gray-200">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mb-3">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <h3 class="font-bold mb-1" data-ar="صلاحيات RBAC" data-en="RBAC System">صلاحيات RBAC</h3>
                            <p class="text-sm text-gray-500" data-ar="أدوار وصلاحيات مع تسلسل هرمي" data-en="Roles & permissions with hierarchy">أدوار وصلاحيات مع تسلسل هرمي</p>
                        </div>
                        <div class="bg-white rounded-xl p-5 border border-gray-200">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mb-3">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/></svg>
                            </div>
                            <h3 class="font-bold mb-1" data-ar="متعدد اللغات" data-en="Multi-Language">متعدد اللغات</h3>
                            <p class="text-sm text-gray-500" data-ar="عربي وإنجليزي عبر Accept-Language" data-en="Arabic & English via Accept-Language">عربي وإنجليزي عبر Accept-Language</p>
                        </div>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-5 mb-6">
                        <h4 class="font-bold text-blue-800 mb-2">Base URL</h4>
                        <code class="text-blue-700 text-lg">{{ url('/api') }}</code>
                    </div>

                    <div class="code-block">
                        <div class="code-header">
                            <span>Health Check</span>
                            <span class="badge method-get text-white">GET /health</span>
                        </div>
                        <pre><code>{
  <span class="json-key">"status"</span>: <span class="json-string">"ok"</span>,
  <span class="json-key">"service"</span>: <span class="json-string">"{{ config('auth-service.service_name', 'auth-service') }}"</span>,
  <span class="json-key">"version"</span>: <span class="json-string">"{{ config('auth-service.service_version', '1.0.0') }}"</span>,
  <span class="json-key">"timestamp"</span>: <span class="json-string">"{{ now()->toISOString() }}"</span>
}</code></pre>
                    </div>
                </section>

                {{-- ============================================================ --}}
                {{-- QUICK START --}}
                {{-- ============================================================ --}}
                <section id="quick-start" class="mb-20">
                    <h2 class="text-3xl font-bold mb-4" data-ar="البدء السريع" data-en="Quick Start">البدء السريع</h2>
                    <p class="text-gray-600 mb-6" data-ar="اتبع هذه الخطوات للبدء باستخدام API" data-en="Follow these steps to get started with the API">اتبع هذه الخطوات للبدء باستخدام API</p>

                    <div class="space-y-4">
                        <div class="bg-white border border-gray-200 rounded-xl p-5">
                            <div class="flex items-center gap-3 mb-3">
                                <span class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center font-bold text-sm">1</span>
                                <h4 class="font-semibold" data-ar="سجل حساب جديد" data-en="Register a new account">سجل حساب جديد</h4>
                            </div>
                            <div class="code-block"><pre><code>curl -X POST {{ url('/api') }}/auth/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"name":"Ahmed","email":"ahmed@example.com","password":"Password123","password_confirmation":"Password123"}'</code></pre></div>
                        </div>

                        <div class="bg-white border border-gray-200 rounded-xl p-5">
                            <div class="flex items-center gap-3 mb-3">
                                <span class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center font-bold text-sm">2</span>
                                <h4 class="font-semibold" data-ar="سجل الدخول واحصل على التوكن" data-en="Login and get your token">سجل الدخول واحصل على التوكن</h4>
                            </div>
                            <div class="code-block"><pre><code>curl -X POST {{ url('/api') }}/auth/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"identifier":"ahmed@example.com","password":"Password123"}'</code></pre></div>
                        </div>

                        <div class="bg-white border border-gray-200 rounded-xl p-5">
                            <div class="flex items-center gap-3 mb-3">
                                <span class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center font-bold text-sm">3</span>
                                <h4 class="font-semibold" data-ar="استخدم التوكن للطلبات المحمية" data-en="Use the token for authenticated requests">استخدم التوكن للطلبات المحمية</h4>
                            </div>
                            <div class="code-block"><pre><code>curl -X GET {{ url('/api') }}/auth/me \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"</code></pre></div>
                        </div>
                    </div>
                </section>

                {{-- ============================================================ --}}
                {{-- HEADERS --}}
                {{-- ============================================================ --}}
                <section id="headers" class="mb-20">
                    <h2 class="text-3xl font-bold mb-4" data-ar="الهيدرات المطلوبة" data-en="Required Headers">الهيدرات المطلوبة</h2>
                    <p class="text-gray-600 mb-6" data-ar="يجب إرسال هذه الهيدرات مع كل طلب" data-en="These headers must be sent with every request">يجب إرسال هذه الهيدرات مع كل طلب</p>

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
                                <tr class="border-b">
                                    <td class="p-4"><code class="bg-gray-100 px-2 py-1 rounded text-sm">Content-Type</code></td>
                                    <td class="p-4"><code class="text-sm">application/json</code></td>
                                    <td class="p-4"><span class="text-red-500 font-bold">*</span></td>
                                    <td class="p-4 text-sm text-gray-600" data-ar="نوع المحتوى المرسل" data-en="Content type of request body">نوع المحتوى المرسل</td>
                                </tr>
                                <tr class="border-b">
                                    <td class="p-4"><code class="bg-gray-100 px-2 py-1 rounded text-sm">Accept</code></td>
                                    <td class="p-4"><code class="text-sm">application/json</code></td>
                                    <td class="p-4"><span class="text-red-500 font-bold">*</span></td>
                                    <td class="p-4 text-sm text-gray-600" data-ar="نوع المحتوى المطلوب" data-en="Expected response format">نوع المحتوى المطلوب</td>
                                </tr>
                                <tr class="border-b">
                                    <td class="p-4"><code class="bg-gray-100 px-2 py-1 rounded text-sm">Authorization</code></td>
                                    <td class="p-4"><code class="text-sm">Bearer {token}</code></td>
                                    <td class="p-4 text-yellow-600" data-ar="للمحمية" data-en="Protected">للمحمية</td>
                                    <td class="p-4 text-sm text-gray-600" data-ar="توكن JWT للطلبات المحمية" data-en="JWT token for protected routes">توكن JWT للطلبات المحمية</td>
                                </tr>
                                <tr class="border-b">
                                    <td class="p-4"><code class="bg-gray-100 px-2 py-1 rounded text-sm">Accept-Language</code></td>
                                    <td class="p-4"><code class="text-sm">ar</code> | <code class="text-sm">en</code></td>
                                    <td class="p-4 text-gray-400" data-ar="اختياري" data-en="Optional">اختياري</td>
                                    <td class="p-4 text-sm text-gray-600" data-ar="لغة الردود (الافتراضي: en)" data-en="Response language (default: en)">لغة الردود (الافتراضي: en)</td>
                                </tr>
                                <tr>
                                    <td class="p-4"><code class="bg-gray-100 px-2 py-1 rounded text-sm">X-Service-Token</code></td>
                                    <td class="p-4"><code class="text-sm">{service_token}</code></td>
                                    <td class="p-4 text-purple-600" data-ar="للخدمات" data-en="Services">للخدمات</td>
                                    <td class="p-4 text-sm text-gray-600" data-ar="توكن الخدمة (S2S فقط)" data-en="Service token (S2S only)">توكن الخدمة (S2S فقط)</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="code-block">
                        <div class="code-header"><span data-ar="مثال كامل للهيدرات" data-en="Complete Headers Example">مثال كامل للهيدرات</span></div>
                        <pre><code>Content-Type: application/json
Accept: application/json
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...
Accept-Language: ar</code></pre>
                    </div>
                </section>

                {{-- ============================================================ --}}
                {{-- AUTHENTICATION INFO --}}
                {{-- ============================================================ --}}
                <section id="authentication" class="mb-20">
                    <h2 class="text-3xl font-bold mb-4" data-ar="المصادقة" data-en="Authentication">المصادقة</h2>
                    <p class="text-gray-600 mb-6" data-ar="تستخدم الـ API مصادقة JWT. أرسل التوكن في Header لكل الطلبات المحمية." data-en="The API uses JWT authentication. Send the token in the Authorization header for all protected routes.">تستخدم الـ API مصادقة JWT. أرسل التوكن في Header لكل الطلبات المحمية.</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div class="bg-white border border-gray-200 rounded-xl p-5">
                            <h4 class="font-semibold mb-2" data-ar="طلبات المستخدم (JWT)" data-en="User Requests (JWT)">طلبات المستخدم (JWT)</h4>
                            <div class="code-block"><pre><code>Authorization: Bearer eyJ0eXAiOiJKV1Qi...</code></pre></div>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-xl p-5">
                            <h4 class="font-semibold mb-2" data-ar="تواصل بين الخدمات" data-en="Service-to-Service">تواصل بين الخدمات</h4>
                            <div class="code-block"><pre><code>X-Service-Token: your-service-token</code></pre></div>
                        </div>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-5 mb-6">
                        <h4 class="font-bold text-blue-800 mb-3" data-ar="معلومات التوكن" data-en="Token Information">معلومات التوكن</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <span class="text-blue-600 font-semibold" data-ar="مدة الصلاحية:" data-en="Expiry Time:">مدة الصلاحية:</span>
                                <span class="text-blue-800">{{ config('jwt.ttl', 60) }} <span data-ar="دقيقة" data-en="minutes">دقيقة</span></span>
                            </div>
                            <div>
                                <span class="text-blue-600 font-semibold" data-ar="فترة التجديد:" data-en="Refresh Window:">فترة التجديد:</span>
                                <span class="text-blue-800">{{ config('jwt.refresh_ttl', 20160) }} <span data-ar="دقيقة" data-en="minutes">دقيقة</span></span>
                            </div>
                            <div>
                                <span class="text-blue-600 font-semibold" data-ar="الخوارزمية:" data-en="Algorithm:">الخوارزمية:</span>
                                <span class="text-blue-800">{{ config('jwt.algo', 'HS256') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-5">
                        <h4 class="font-bold text-yellow-800 mb-2" data-ar="تنبيهات مهمة" data-en="Important Notes">تنبيهات مهمة</h4>
                        <ul class="text-sm text-yellow-700 space-y-1">
                            <li data-ar="• عند تسجيل الخروج يتم حظر التوكن ولا يمكن استخدامه مجدداً" data-en="• Token is blacklisted on logout and cannot be reused">• عند تسجيل الخروج يتم حظر التوكن ولا يمكن استخدامه مجدداً</li>
                            <li data-ar="• استخدم /auth/refresh لتجديد التوكن قبل انتهاء صلاحيته" data-en="• Use /auth/refresh to renew token before expiry">• استخدم /auth/refresh لتجديد التوكن قبل انتهاء صلاحيته</li>
                            <li data-ar="• التوكن القديم يصبح غير صالح بعد التجديد" data-en="• Old token becomes invalid after refresh">• التوكن القديم يصبح غير صالح بعد التجديد</li>
                        </ul>
                    </div>
                </section>

                {{-- ============================================================ --}}
                {{-- RESPONSE FORMAT --}}
                {{-- ============================================================ --}}
                <section id="response-format" class="mb-20">
                    <h2 class="text-3xl font-bold mb-4" data-ar="صيغة الردود" data-en="Response Format">صيغة الردود</h2>
                    <p class="text-gray-600 mb-6" data-ar="جميع الردود تأتي بصيغة JSON موحدة" data-en="All responses follow a consistent JSON format">جميع الردود تأتي بصيغة JSON موحدة</p>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
                        <div class="code-block">
                            <div class="code-header"><span data-ar="رد ناجح" data-en="Success Response">رد ناجح</span><span class="badge bg-green-500/30 text-green-300">2xx</span></div>
                            <pre><code>{
  <span class="json-key">"success"</span>: <span class="json-bool">true</span>,
  <span class="json-key">"message"</span>: <span class="json-string">"Operation successful"</span>,
  <span class="json-key">"data"</span>: {
    <span class="json-key">"..."</span>: <span class="json-string">"..."</span>
  }
}</code></pre>
                        </div>
                        <div class="code-block">
                            <div class="code-header"><span data-ar="رد خطأ" data-en="Error Response">رد خطأ</span><span class="badge bg-red-500/30 text-red-300">4xx/5xx</span></div>
                            <pre><code>{
  <span class="json-key">"success"</span>: <span class="json-bool">false</span>,
  <span class="json-key">"message"</span>: <span class="json-string">"Error message"</span>,
  <span class="json-key">"code"</span>: <span class="json-string">"error_code"</span>,
  <span class="json-key">"errors"</span>: { ... }
}</code></pre>
                        </div>
                    </div>

                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 border-b">
                                    <th class="p-4 text-right font-semibold text-gray-500" data-ar="الحقل" data-en="Field">الحقل</th>
                                    <th class="p-4 text-right font-semibold text-gray-500" data-ar="النوع" data-en="Type">النوع</th>
                                    <th class="p-4 text-right font-semibold text-gray-500" data-ar="الوصف" data-en="Description">الوصف</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b">
                                    <td class="p-4"><code>success</code></td>
                                    <td class="p-4">boolean</td>
                                    <td class="p-4 text-gray-600" data-ar="حالة العملية (true/false)" data-en="Operation status (true/false)">حالة العملية (true/false)</td>
                                </tr>
                                <tr class="border-b">
                                    <td class="p-4"><code>message</code></td>
                                    <td class="p-4">string</td>
                                    <td class="p-4 text-gray-600" data-ar="رسالة توضيحية (حسب اللغة)" data-en="Descriptive message (based on language)">رسالة توضيحية (حسب اللغة)</td>
                                </tr>
                                <tr class="border-b">
                                    <td class="p-4"><code>data</code></td>
                                    <td class="p-4">object|array</td>
                                    <td class="p-4 text-gray-600" data-ar="البيانات المطلوبة (للردود الناجحة)" data-en="Requested data (for success responses)">البيانات المطلوبة (للردود الناجحة)</td>
                                </tr>
                                <tr class="border-b">
                                    <td class="p-4"><code>code</code></td>
                                    <td class="p-4">string</td>
                                    <td class="p-4 text-gray-600" data-ar="رمز الخطأ للمعالجة البرمجية" data-en="Error code for programmatic handling">رمز الخطأ للمعالجة البرمجية</td>
                                </tr>
                                <tr>
                                    <td class="p-4"><code>errors</code></td>
                                    <td class="p-4">object</td>
                                    <td class="p-4 text-gray-600" data-ar="تفاصيل أخطاء التحقق (للحقول)" data-en="Validation errors details (for fields)">تفاصيل أخطاء التحقق (للحقول)</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- ============================================================ --}}
                {{-- REGISTER --}}
                {{-- ============================================================ --}}
                <section id="register" class="mb-20">
                    <div class="endpoint-card">
                        <div class="bg-blue-50 border-b border-blue-100 p-5 flex items-center gap-3">
                            <span class="badge method-post text-white text-xs">POST</span>
                            <code class="text-gray-800 font-semibold">/auth/register</code>
                            <span class="mr-auto text-sm text-gray-500" data-ar="تسجيل مستخدم جديد" data-en="Register new user">تسجيل مستخدم جديد</span>
                            <span class="badge bg-green-100 text-green-700" data-ar="عام" data-en="Public">عام</span>
                        </div>
                        <div class="p-5 space-y-6">
                            <div>
                                <h4 class="font-semibold mb-3 text-sm text-gray-500 uppercase tracking-wider" data-ar="الحقول" data-en="Parameters">الحقول</h4>
                                <div class="overflow-x-auto">
                                    <table class="param-table w-full text-sm">
                                        <thead><tr class="border-b bg-gray-50"><th class="p-3 text-right" data-ar="الحقل" data-en="Field">الحقل</th><th class="p-3 text-right" data-ar="النوع" data-en="Type">النوع</th><th class="p-3 text-right" data-ar="مطلوب" data-en="Required">مطلوب</th><th class="p-3 text-right" data-ar="القواعد" data-en="Rules">القواعد</th></tr></thead>
                                        <tbody>
                                            <tr class="border-b"><td class="p-3"><code>name</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500">*</span></td><td class="p-3">max:255</td></tr>
                                            <tr class="border-b"><td class="p-3"><code>email</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500">*</span></td><td class="p-3" data-ar="بريد صالح، فريد" data-en="valid email, unique">بريد صالح، فريد</td></tr>
                                            <tr class="border-b"><td class="p-3"><code>password</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500">*</span></td><td class="p-3" data-ar="8 أحرف+، كبير وصغير وأرقام" data-en="min:8, mixed case, numbers">8 أحرف+، كبير وصغير وأرقام</td></tr>
                                            <tr class="border-b"><td class="p-3"><code>password_confirmation</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500">*</span></td><td class="p-3" data-ar="يطابق كلمة المرور" data-en="must match password">يطابق كلمة المرور</td></tr>
                                            <tr><td class="p-3"><code>phone</code></td><td class="p-3">string</td><td class="p-3 text-gray-400">-</td><td class="p-3">max:20</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                <div class="code-block">
                                    <div class="code-header"><span>Request</span></div>
                                    <pre><code>{
  <span class="json-key">"name"</span>: <span class="json-string">"Ahmed"</span>,
  <span class="json-key">"email"</span>: <span class="json-string">"ahmed@example.com"</span>,
  <span class="json-key">"password"</span>: <span class="json-string">"Password123"</span>,
  <span class="json-key">"password_confirmation"</span>: <span class="json-string">"Password123"</span>,
  <span class="json-key">"phone"</span>: <span class="json-string">"0501234567"</span>
}</code></pre>
                                </div>
                                <div class="code-block">
                                    <div class="code-header"><span>Response</span><span class="badge bg-green-500/30 text-green-300">201</span></div>
                                    <pre><code>{
  <span class="json-key">"success"</span>: <span class="json-bool">true</span>,
  <span class="json-key">"message"</span>: <span class="json-string">"Registration successful"</span>,
  <span class="json-key">"data"</span>: {
    <span class="json-key">"user"</span>: { ... },
    <span class="json-key">"token"</span>: <span class="json-string">"eyJ0eXAi..."</span>
  }
}</code></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- ============================================================ --}}
                {{-- LOGIN --}}
                {{-- ============================================================ --}}
                <section id="login" class="mb-20">
                    <div class="endpoint-card">
                        <div class="bg-blue-50 border-b border-blue-100 p-5 flex items-center gap-3">
                            <span class="badge method-post text-white text-xs">POST</span>
                            <code class="text-gray-800 font-semibold">/auth/login</code>
                            <span class="mr-auto text-sm text-gray-500" data-ar="تسجيل الدخول" data-en="Login">تسجيل الدخول</span>
                            <span class="badge bg-green-100 text-green-700" data-ar="عام" data-en="Public">عام</span>
                        </div>
                        <div class="p-5 space-y-6">
                            <div>
                                <h4 class="font-semibold mb-3 text-sm text-gray-500 uppercase tracking-wider" data-ar="الحقول" data-en="Parameters">الحقول</h4>
                                <div class="overflow-x-auto">
                                    <table class="param-table w-full text-sm">
                                        <thead><tr class="border-b bg-gray-50"><th class="p-3 text-right" data-ar="الحقل" data-en="Field">الحقل</th><th class="p-3 text-right" data-ar="النوع" data-en="Type">النوع</th><th class="p-3 text-right" data-ar="مطلوب" data-en="Required">مطلوب</th><th class="p-3 text-right" data-ar="القواعد" data-en="Rules">القواعد</th></tr></thead>
                                        <tbody>
                                            <tr class="border-b"><td class="p-3"><code>identifier</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500">*</span></td><td class="p-3" data-ar="البريد الإلكتروني أو رقم الجوال" data-en="Email or phone number">البريد الإلكتروني أو رقم الجوال</td></tr>
                                            <tr class="border-b"><td class="p-3"><code>password</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500">*</span></td><td class="p-3" data-ar="كلمة المرور" data-en="Password">كلمة المرور</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                <div class="code-block">
                                    <div class="code-header"><span data-ar="طلب بالإيميل" data-en="Request with Email">طلب بالإيميل</span></div>
                                    <pre><code>{
  <span class="json-key">"identifier"</span>: <span class="json-string">"ahmed@example.com"</span>,
  <span class="json-key">"password"</span>: <span class="json-string">"Password123"</span>
}</code></pre>
                                </div>
                                <div class="code-block">
                                    <div class="code-header"><span data-ar="طلب برقم الجوال" data-en="Request with Phone">طلب برقم الجوال</span></div>
                                    <pre><code>{
  <span class="json-key">"identifier"</span>: <span class="json-string">"0501234567"</span>,
  <span class="json-key">"password"</span>: <span class="json-string">"Password123"</span>
}</code></pre>
                                </div>
                            </div>
                            <div class="code-block">
                                <div class="code-header"><span>Response</span><span class="badge bg-green-500/30 text-green-300">200</span></div>
                                <pre><code>{
  <span class="json-key">"success"</span>: <span class="json-bool">true</span>,
  <span class="json-key">"message"</span>: <span class="json-string">"Login successful"</span>,
  <span class="json-key">"data"</span>: {
    <span class="json-key">"user"</span>: { ... },
    <span class="json-key">"token"</span>: <span class="json-string">"eyJ0eXAi..."</span>,
    <span class="json-key">"token_type"</span>: <span class="json-string">"bearer"</span>,
    <span class="json-key">"expires_in"</span>: <span class="json-number">3600</span>
  }
}</code></pre>
                            </div>
                            <div class="code-block">
                                <div class="code-header"><span data-ar="رد الخطأ" data-en="Error Response">رد الخطأ</span><span class="badge bg-red-500/30 text-red-300">401</span></div>
                                <pre><code>{
  <span class="json-key">"success"</span>: <span class="json-bool">false</span>,
  <span class="json-key">"code"</span>: <span class="json-string">"invalid_login_credentials"</span>,
  <span class="json-key">"message"</span>: <span class="json-string">"Invalid credentials"</span>
}</code></pre>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- ============================================================ --}}
                {{-- LOGOUT --}}
                {{-- ============================================================ --}}
                <section id="logout" class="mb-20">
                    <div class="endpoint-card">
                        <div class="bg-blue-50 border-b border-blue-100 p-5 flex items-center gap-3">
                            <span class="badge method-post text-white text-xs">POST</span>
                            <code class="text-gray-800 font-semibold">/auth/logout</code>
                            <span class="mr-auto text-sm text-gray-500" data-ar="تسجيل الخروج" data-en="Logout">تسجيل الخروج</span>
                            <span class="badge bg-yellow-100 text-yellow-700">Auth</span>
                        </div>
                        <div class="p-5">
                            <div class="code-block">
                                <div class="code-header"><span>Response</span><span class="badge bg-green-500/30 text-green-300">200</span></div>
                                <pre><code>{
  <span class="json-key">"success"</span>: <span class="json-bool">true</span>,
  <span class="json-key">"message"</span>: <span class="json-string">"Logout successful"</span>
}</code></pre>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- ============================================================ --}}
                {{-- ME --}}
                {{-- ============================================================ --}}
                <section id="me" class="mb-20">
                    <div class="endpoint-card">
                        <div class="bg-green-50 border-b border-green-100 p-5 flex items-center gap-3">
                            <span class="badge method-get text-white text-xs">GET</span>
                            <code class="text-gray-800 font-semibold">/auth/me</code>
                            <span class="mr-auto text-sm text-gray-500" data-ar="بيانات المستخدم الحالي" data-en="Current user info">بيانات المستخدم الحالي</span>
                            <span class="badge bg-yellow-100 text-yellow-700">Auth</span>
                        </div>
                        <div class="p-5">
                            <div class="code-block">
                                <div class="code-header"><span>Response</span><span class="badge bg-green-500/30 text-green-300">200</span></div>
                                <pre><code>{
  <span class="json-key">"success"</span>: <span class="json-bool">true</span>,
  <span class="json-key">"data"</span>: {
    <span class="json-key">"id"</span>: <span class="json-string">"550e8400-e29b-..."</span>,
    <span class="json-key">"name"</span>: <span class="json-string">"Ahmed"</span>,
    <span class="json-key">"email"</span>: <span class="json-string">"ahmed@example.com"</span>,
    <span class="json-key">"phone"</span>: <span class="json-string">"0501234567"</span>,
    <span class="json-key">"status"</span>: <span class="json-string">"active"</span>,
    <span class="json-key">"roles"</span>: [<span class="json-string">"user"</span>],
    <span class="json-key">"permissions"</span>: [<span class="json-string">"users.view"</span>]
  }
}</code></pre>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- ============================================================ --}}
                {{-- REFRESH --}}
                {{-- ============================================================ --}}
                <section id="refresh" class="mb-20">
                    <div class="endpoint-card">
                        <div class="bg-blue-50 border-b border-blue-100 p-5 flex items-center gap-3">
                            <span class="badge method-post text-white text-xs">POST</span>
                            <code class="text-gray-800 font-semibold">/auth/refresh</code>
                            <span class="mr-auto text-sm text-gray-500" data-ar="تجديد التوكن" data-en="Refresh JWT token">تجديد التوكن</span>
                            <span class="badge bg-yellow-100 text-yellow-700">Auth</span>
                        </div>
                        <div class="p-5">
                            <div class="code-block">
                                <div class="code-header"><span>Response</span><span class="badge bg-green-500/30 text-green-300">200</span></div>
                                <pre><code>{
  <span class="json-key">"success"</span>: <span class="json-bool">true</span>,
  <span class="json-key">"data"</span>: {
    <span class="json-key">"token"</span>: <span class="json-string">"eyJ0eXAi..."</span>,
    <span class="json-key">"token_type"</span>: <span class="json-string">"bearer"</span>,
    <span class="json-key">"expires_in"</span>: <span class="json-number">{{ (config('jwt.ttl', 60) * 60) }}</span>
  }
}</code></pre>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- ============================================================ --}}
                {{-- FORGOT PASSWORD --}}
                {{-- ============================================================ --}}
                <section id="forgot-password" class="mb-20">
                    <div class="endpoint-card">
                        <div class="bg-blue-50 border-b border-blue-100 p-5 flex items-center gap-3">
                            <span class="badge method-post text-white text-xs">POST</span>
                            <code class="text-gray-800 font-semibold">/auth/forgot-password</code>
                            <span class="mr-auto text-sm text-gray-500" data-ar="طلب إعادة تعيين كلمة المرور" data-en="Request password reset">طلب إعادة تعيين كلمة المرور</span>
                            <span class="badge bg-green-100 text-green-700" data-ar="عام" data-en="Public">عام</span>
                        </div>
                        <div class="p-5 space-y-6">
                            <div>
                                <h4 class="font-semibold mb-3 text-sm text-gray-500 uppercase tracking-wider" data-ar="الحقول" data-en="Parameters">الحقول</h4>
                                <div class="overflow-x-auto">
                                    <table class="param-table w-full text-sm">
                                        <thead><tr class="border-b bg-gray-50"><th class="p-3 text-right" data-ar="الحقل" data-en="Field">الحقل</th><th class="p-3 text-right" data-ar="النوع" data-en="Type">النوع</th><th class="p-3 text-right" data-ar="مطلوب" data-en="Required">مطلوب</th><th class="p-3 text-right" data-ar="القواعد" data-en="Rules">القواعد</th></tr></thead>
                                        <tbody>
                                            <tr><td class="p-3"><code>email</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500">*</span></td><td class="p-3" data-ar="بريد صالح، مسجل" data-en="valid email, exists">بريد صالح، مسجل</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                <div class="code-block">
                                    <div class="code-header"><span>Request</span></div>
                                    <pre><code>{
  <span class="json-key">"email"</span>: <span class="json-string">"ahmed@example.com"</span>
}</code></pre>
                                </div>
                                <div class="code-block">
                                    <div class="code-header"><span>Response</span><span class="badge bg-green-500/30 text-green-300">200</span></div>
                                    <pre><code>{
  <span class="json-key">"success"</span>: <span class="json-bool">true</span>,
  <span class="json-key">"message"</span>: <span class="json-string">"Password reset link sent"</span>,
  <span class="json-key">"data"</span>: {
    <span class="json-key">"token"</span>: <span class="json-string">"abc123..."</span>
  }
}</code></pre>
                                </div>
                            </div>
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-sm">
                                <span class="font-semibold text-yellow-800" data-ar="ملاحظة:" data-en="Note:">ملاحظة:</span>
                                <span class="text-yellow-700" data-ar="التوكن يُرجع فقط في بيئة التطوير (APP_DEBUG=true)" data-en="Token is only returned in debug mode (APP_DEBUG=true)">التوكن يُرجع فقط في بيئة التطوير (APP_DEBUG=true)</span>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- ============================================================ --}}
                {{-- RESET PASSWORD --}}
                {{-- ============================================================ --}}
                <section id="reset-password" class="mb-20">
                    <div class="endpoint-card">
                        <div class="bg-blue-50 border-b border-blue-100 p-5 flex items-center gap-3">
                            <span class="badge method-post text-white text-xs">POST</span>
                            <code class="text-gray-800 font-semibold">/auth/reset-password</code>
                            <span class="mr-auto text-sm text-gray-500" data-ar="إعادة تعيين كلمة المرور" data-en="Reset password">إعادة تعيين كلمة المرور</span>
                            <span class="badge bg-green-100 text-green-700" data-ar="عام" data-en="Public">عام</span>
                        </div>
                        <div class="p-5 space-y-6">
                            <div>
                                <h4 class="font-semibold mb-3 text-sm text-gray-500 uppercase tracking-wider" data-ar="الحقول" data-en="Parameters">الحقول</h4>
                                <div class="overflow-x-auto">
                                    <table class="param-table w-full text-sm">
                                        <thead><tr class="border-b bg-gray-50"><th class="p-3 text-right" data-ar="الحقل" data-en="Field">الحقل</th><th class="p-3 text-right" data-ar="النوع" data-en="Type">النوع</th><th class="p-3 text-right" data-ar="مطلوب" data-en="Required">مطلوب</th><th class="p-3 text-right" data-ar="القواعد" data-en="Rules">القواعد</th></tr></thead>
                                        <tbody>
                                            <tr class="border-b"><td class="p-3"><code>email</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500">*</span></td><td class="p-3" data-ar="بريد صالح، مسجل" data-en="valid email, exists">بريد صالح، مسجل</td></tr>
                                            <tr class="border-b"><td class="p-3"><code>token</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500">*</span></td><td class="p-3" data-ar="رمز إعادة التعيين" data-en="reset token">رمز إعادة التعيين</td></tr>
                                            <tr class="border-b"><td class="p-3"><code>password</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500">*</span></td><td class="p-3" data-ar="8 أحرف+، كبير وصغير وأرقام" data-en="min:8, mixed case, numbers">8 أحرف+، كبير وصغير وأرقام</td></tr>
                                            <tr><td class="p-3"><code>password_confirmation</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500">*</span></td><td class="p-3" data-ar="يطابق كلمة المرور" data-en="must match password">يطابق كلمة المرور</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                <div class="code-block">
                                    <div class="code-header"><span>Request</span></div>
                                    <pre><code>{
  <span class="json-key">"email"</span>: <span class="json-string">"ahmed@example.com"</span>,
  <span class="json-key">"token"</span>: <span class="json-string">"abc123..."</span>,
  <span class="json-key">"password"</span>: <span class="json-string">"NewPassword123"</span>,
  <span class="json-key">"password_confirmation"</span>: <span class="json-string">"NewPassword123"</span>
}</code></pre>
                                </div>
                                <div class="code-block">
                                    <div class="code-header"><span>Response</span><span class="badge bg-green-500/30 text-green-300">200</span></div>
                                    <pre><code>{
  <span class="json-key">"success"</span>: <span class="json-bool">true</span>,
  <span class="json-key">"message"</span>: <span class="json-string">"Password reset successfully"</span>
}</code></pre>
                                </div>
                            </div>
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm">
                                <span class="font-semibold text-blue-800" data-ar="التوكن صالح لمدة:" data-en="Token valid for:">التوكن صالح لمدة:</span>
                                <span class="text-blue-700" data-ar="60 دقيقة (ساعة واحدة)" data-en="60 minutes (1 hour)">60 دقيقة (ساعة واحدة)</span>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- ============================================================ --}}
                {{-- CHANGE PASSWORD --}}
                {{-- ============================================================ --}}
                <section id="change-password" class="mb-20">
                    <div class="endpoint-card">
                        <div class="bg-blue-50 border-b border-blue-100 p-5 flex items-center gap-3">
                            <span class="badge method-post text-white text-xs">POST</span>
                            <code class="text-gray-800 font-semibold">/auth/change-password</code>
                            <span class="mr-auto text-sm text-gray-500" data-ar="تغيير كلمة المرور" data-en="Change password">تغيير كلمة المرور</span>
                            <span class="badge bg-yellow-100 text-yellow-700">Auth</span>
                        </div>
                        <div class="p-5 space-y-6">
                            <div>
                                <h4 class="font-semibold mb-3 text-sm text-gray-500 uppercase tracking-wider" data-ar="الحقول" data-en="Parameters">الحقول</h4>
                                <div class="overflow-x-auto">
                                    <table class="param-table w-full text-sm">
                                        <thead><tr class="border-b bg-gray-50"><th class="p-3 text-right" data-ar="الحقل" data-en="Field">الحقل</th><th class="p-3 text-right" data-ar="النوع" data-en="Type">النوع</th><th class="p-3 text-right" data-ar="مطلوب" data-en="Required">مطلوب</th><th class="p-3 text-right" data-ar="القواعد" data-en="Rules">القواعد</th></tr></thead>
                                        <tbody>
                                            <tr class="border-b"><td class="p-3"><code>current_password</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500">*</span></td><td class="p-3" data-ar="كلمة المرور الحالية" data-en="current password">كلمة المرور الحالية</td></tr>
                                            <tr class="border-b"><td class="p-3"><code>password</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500">*</span></td><td class="p-3" data-ar="8 أحرف+، مختلفة عن الحالية" data-en="min:8, different from current">8 أحرف+، مختلفة عن الحالية</td></tr>
                                            <tr><td class="p-3"><code>password_confirmation</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500">*</span></td><td class="p-3" data-ar="يطابق كلمة المرور" data-en="must match password">يطابق كلمة المرور</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                <div class="code-block">
                                    <div class="code-header"><span>Request</span></div>
                                    <pre><code>{
  <span class="json-key">"current_password"</span>: <span class="json-string">"OldPassword123"</span>,
  <span class="json-key">"password"</span>: <span class="json-string">"NewPassword123"</span>,
  <span class="json-key">"password_confirmation"</span>: <span class="json-string">"NewPassword123"</span>
}</code></pre>
                                </div>
                                <div class="code-block">
                                    <div class="code-header"><span>Response</span><span class="badge bg-green-500/30 text-green-300">200</span></div>
                                    <pre><code>{
  <span class="json-key">"success"</span>: <span class="json-bool">true</span>,
  <span class="json-key">"message"</span>: <span class="json-string">"Password changed successfully"</span>
}</code></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- ============================================================ --}}
                {{-- UPDATE PROFILE --}}
                {{-- ============================================================ --}}
                <section id="update-profile" class="mb-20">
                    <div class="endpoint-card">
                        <div class="bg-yellow-50 border-b border-yellow-100 p-5 flex items-center gap-3">
                            <span class="badge method-put text-white text-xs">PUT</span>
                            <code class="text-gray-800 font-semibold">/auth/profile</code>
                            <span class="mr-auto text-sm text-gray-500" data-ar="تحديث الملف الشخصي" data-en="Update profile">تحديث الملف الشخصي</span>
                            <span class="badge bg-yellow-100 text-yellow-700">Auth</span>
                        </div>
                        <div class="p-5 space-y-6">
                            <div>
                                <h4 class="font-semibold mb-3 text-sm text-gray-500 uppercase tracking-wider" data-ar="الحقول" data-en="Parameters">الحقول</h4>
                                <div class="overflow-x-auto">
                                    <table class="param-table w-full text-sm">
                                        <thead><tr class="border-b bg-gray-50"><th class="p-3 text-right" data-ar="الحقل" data-en="Field">الحقل</th><th class="p-3 text-right" data-ar="النوع" data-en="Type">النوع</th><th class="p-3 text-right" data-ar="مطلوب" data-en="Required">مطلوب</th><th class="p-3 text-right" data-ar="القواعد" data-en="Rules">القواعد</th></tr></thead>
                                        <tbody>
                                            <tr class="border-b"><td class="p-3"><code>name</code></td><td class="p-3">string</td><td class="p-3 text-gray-400">-</td><td class="p-3">max:255</td></tr>
                                            <tr class="border-b"><td class="p-3"><code>email</code></td><td class="p-3">string</td><td class="p-3 text-gray-400">-</td><td class="p-3" data-ar="بريد صالح، فريد" data-en="valid email, unique">بريد صالح، فريد</td></tr>
                                            <tr><td class="p-3"><code>phone</code></td><td class="p-3">string</td><td class="p-3 text-gray-400">-</td><td class="p-3">max:20</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                <div class="code-block">
                                    <div class="code-header"><span>Request</span></div>
                                    <pre><code>{
  <span class="json-key">"name"</span>: <span class="json-string">"Ahmed Ali"</span>,
  <span class="json-key">"phone"</span>: <span class="json-string">"0509876543"</span>
}</code></pre>
                                </div>
                                <div class="code-block">
                                    <div class="code-header"><span>Response</span><span class="badge bg-green-500/30 text-green-300">200</span></div>
                                    <pre><code>{
  <span class="json-key">"success"</span>: <span class="json-bool">true</span>,
  <span class="json-key">"message"</span>: <span class="json-string">"Profile updated"</span>,
  <span class="json-key">"data"</span>: {
    <span class="json-key">"user"</span>: { ... }
  }
}</code></pre>
                                </div>
                            </div>
                            <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 text-sm">
                                <span class="font-semibold text-orange-800" data-ar="تنبيه:" data-en="Warning:">تنبيه:</span>
                                <span class="text-orange-700" data-ar="عند تغيير البريد الإلكتروني سيتم إلغاء التحقق ويجب إعادة التحقق" data-en="Changing email will reset verification status">عند تغيير البريد الإلكتروني سيتم إلغاء التحقق ويجب إعادة التحقق</span>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- ============================================================ --}}
                {{-- SEND EMAIL VERIFICATION --}}
                {{-- ============================================================ --}}
                <section id="send-verification" class="mb-20">
                    <div class="endpoint-card">
                        <div class="bg-blue-50 border-b border-blue-100 p-5 flex items-center gap-3">
                            <span class="badge method-post text-white text-xs">POST</span>
                            <code class="text-gray-800 font-semibold">/auth/email/send-verification</code>
                            <span class="mr-auto text-sm text-gray-500" data-ar="إرسال كود التحقق" data-en="Send verification code">إرسال كود التحقق</span>
                            <span class="badge bg-yellow-100 text-yellow-700">Auth</span>
                        </div>
                        <div class="p-5 space-y-6">
                            <p class="text-gray-600" data-ar="لا يحتاج إلى أي حقول - يرسل كود التحقق للبريد المسجل" data-en="No parameters needed - sends verification code to registered email">لا يحتاج إلى أي حقول - يرسل كود التحقق للبريد المسجل</p>
                            <div class="code-block">
                                <div class="code-header"><span>Response</span><span class="badge bg-green-500/30 text-green-300">200</span></div>
                                <pre><code>{
  <span class="json-key">"success"</span>: <span class="json-bool">true</span>,
  <span class="json-key">"message"</span>: <span class="json-string">"Verification code sent"</span>,
  <span class="json-key">"data"</span>: {
    <span class="json-key">"code"</span>: <span class="json-string">"123456"</span>
  }
}</code></pre>
                            </div>
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-sm">
                                <span class="font-semibold text-yellow-800" data-ar="ملاحظة:" data-en="Note:">ملاحظة:</span>
                                <span class="text-yellow-700" data-ar="الكود يُرجع فقط في بيئة التطوير. الكود صالح لمدة 15 دقيقة." data-en="Code is only returned in debug mode. Code valid for 15 minutes.">الكود يُرجع فقط في بيئة التطوير. الكود صالح لمدة 15 دقيقة.</span>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- ============================================================ --}}
                {{-- VERIFY EMAIL --}}
                {{-- ============================================================ --}}
                <section id="verify-email" class="mb-20">
                    <div class="endpoint-card">
                        <div class="bg-blue-50 border-b border-blue-100 p-5 flex items-center gap-3">
                            <span class="badge method-post text-white text-xs">POST</span>
                            <code class="text-gray-800 font-semibold">/auth/email/verify</code>
                            <span class="mr-auto text-sm text-gray-500" data-ar="التحقق من البريد الإلكتروني" data-en="Verify email">التحقق من البريد الإلكتروني</span>
                            <span class="badge bg-yellow-100 text-yellow-700">Auth</span>
                        </div>
                        <div class="p-5 space-y-6">
                            <div>
                                <h4 class="font-semibold mb-3 text-sm text-gray-500 uppercase tracking-wider" data-ar="الحقول" data-en="Parameters">الحقول</h4>
                                <div class="overflow-x-auto">
                                    <table class="param-table w-full text-sm">
                                        <thead><tr class="border-b bg-gray-50"><th class="p-3 text-right" data-ar="الحقل" data-en="Field">الحقل</th><th class="p-3 text-right" data-ar="النوع" data-en="Type">النوع</th><th class="p-3 text-right" data-ar="مطلوب" data-en="Required">مطلوب</th><th class="p-3 text-right" data-ar="القواعد" data-en="Rules">القواعد</th></tr></thead>
                                        <tbody>
                                            <tr><td class="p-3"><code>code</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500">*</span></td><td class="p-3" data-ar="6 أرقام" data-en="6 digits">6 أرقام</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                <div class="code-block">
                                    <div class="code-header"><span>Request</span></div>
                                    <pre><code>{
  <span class="json-key">"code"</span>: <span class="json-string">"123456"</span>
}</code></pre>
                                </div>
                                <div class="code-block">
                                    <div class="code-header"><span>Response</span><span class="badge bg-green-500/30 text-green-300">200</span></div>
                                    <pre><code>{
  <span class="json-key">"success"</span>: <span class="json-bool">true</span>,
  <span class="json-key">"message"</span>: <span class="json-string">"Email verified successfully"</span>
}</code></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- ============================================================ --}}
                {{-- VERIFY TOKEN --}}
                {{-- ============================================================ --}}
                <section id="verify-token" class="mb-20">
                    <div class="endpoint-card">
                        <div class="bg-blue-50 border-b border-blue-100 p-5 flex items-center gap-3">
                            <span class="badge method-post text-white text-xs">POST</span>
                            <code class="text-gray-800 font-semibold">/verify-token</code>
                            <span class="mr-auto text-sm text-gray-500" data-ar="التحقق من صلاحية التوكن" data-en="Verify token validity">التحقق من صلاحية التوكن</span>
                            <span class="badge bg-yellow-100 text-yellow-700">Auth</span>
                        </div>
                        <div class="p-5 space-y-4">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                <div class="code-block">
                                    <div class="code-header"><span>Request</span></div>
                                    <pre><code>{
  <span class="json-key">"token"</span>: <span class="json-string">"eyJ0eXAi..."</span>
}</code></pre>
                                </div>
                                <div class="code-block">
                                    <div class="code-header"><span>Response</span><span class="badge bg-green-500/30 text-green-300">200</span></div>
                                    <pre><code>{
  <span class="json-key">"success"</span>: <span class="json-bool">true</span>,
  <span class="json-key">"data"</span>: {
    <span class="json-key">"valid"</span>: <span class="json-bool">true</span>,
    <span class="json-key">"user"</span>: { ... },
    <span class="json-key">"expires_at"</span>: <span class="json-string">"..."</span>
  }
}</code></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- ============================================================ --}}
                {{-- CHECK PERMISSION --}}
                {{-- ============================================================ --}}
                <section id="check-permission" class="mb-20">
                    <div class="endpoint-card">
                        <div class="bg-blue-50 border-b border-blue-100 p-5 flex items-center gap-3">
                            <span class="badge method-post text-white text-xs">POST</span>
                            <code class="text-gray-800 font-semibold">/check-permission</code>
                            <span class="mr-auto text-sm text-gray-500" data-ar="فحص صلاحية مستخدم" data-en="Check user permission">فحص صلاحية مستخدم</span>
                            <span class="badge bg-yellow-100 text-yellow-700">Auth</span>
                        </div>
                        <div class="p-5 space-y-4">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                <div class="code-block">
                                    <div class="code-header"><span>Request</span></div>
                                    <pre><code>{
  <span class="json-key">"user_id"</span>: <span class="json-string">"uuid-here"</span>,
  <span class="json-key">"permission"</span>: <span class="json-string">"users.view"</span>
}</code></pre>
                                </div>
                                <div class="code-block">
                                    <div class="code-header"><span>Response</span><span class="badge bg-green-500/30 text-green-300">200</span></div>
                                    <pre><code>{
  <span class="json-key">"success"</span>: <span class="json-bool">true</span>,
  <span class="json-key">"data"</span>: {
    <span class="json-key">"has_permission"</span>: <span class="json-bool">true</span>,
    <span class="json-key">"user_id"</span>: <span class="json-string">"uuid"</span>,
    <span class="json-key">"permission"</span>: <span class="json-string">"users.view"</span>
  }
}</code></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- ============================================================ --}}
                {{-- CHECK PERMISSIONS --}}
                {{-- ============================================================ --}}
                <section id="check-permissions" class="mb-20">
                    <div class="endpoint-card">
                        <div class="bg-blue-50 border-b border-blue-100 p-5 flex items-center gap-3">
                            <span class="badge method-post text-white text-xs">POST</span>
                            <code class="text-gray-800 font-semibold">/check-permissions</code>
                            <span class="mr-auto text-sm text-gray-500" data-ar="فحص عدة صلاحيات" data-en="Check multiple permissions">فحص عدة صلاحيات</span>
                            <span class="badge bg-yellow-100 text-yellow-700">Auth</span>
                        </div>
                        <div class="p-5 space-y-4">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                <div class="code-block">
                                    <div class="code-header"><span>Request</span></div>
                                    <pre><code>{
  <span class="json-key">"user_id"</span>: <span class="json-string">"uuid-here"</span>,
  <span class="json-key">"permissions"</span>: [
    <span class="json-string">"users.view"</span>,
    <span class="json-string">"users.create"</span>
  ],
  <span class="json-key">"require_all"</span>: <span class="json-bool">false</span>
}</code></pre>
                                </div>
                                <div class="code-block">
                                    <div class="code-header"><span>Response</span><span class="badge bg-green-500/30 text-green-300">200</span></div>
                                    <pre><code>{
  <span class="json-key">"success"</span>: <span class="json-bool">true</span>,
  <span class="json-key">"data"</span>: {
    <span class="json-key">"has_access"</span>: <span class="json-bool">true</span>,
    <span class="json-key">"permissions"</span>: {
      <span class="json-key">"users.view"</span>: <span class="json-bool">true</span>,
      <span class="json-key">"users.create"</span>: <span class="json-bool">false</span>
    },
    <span class="json-key">"require_all"</span>: <span class="json-bool">false</span>
  }
}</code></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- ============================================================ --}}
                {{-- USERS CRUD --}}
                {{-- ============================================================ --}}
                <section id="users-list" class="mb-10">
                    <h2 class="text-3xl font-bold mb-6" data-ar="إدارة المستخدمين" data-en="Users Management">إدارة المستخدمين</h2>
                </section>

                @php
                $userEndpoints = [
                    ['id' => 'users-list', 'method' => 'GET', 'path' => '/users', 'ar' => 'قائمة المستخدمين', 'en' => 'List all users', 'perm' => 'users.view', 'color' => 'green'],
                    ['id' => 'users-create', 'method' => 'POST', 'path' => '/users', 'ar' => 'إنشاء مستخدم', 'en' => 'Create user', 'perm' => 'users.create', 'color' => 'blue'],
                    ['id' => 'users-show', 'method' => 'GET', 'path' => '/users/{id}', 'ar' => 'عرض مستخدم', 'en' => 'Show user', 'perm' => 'users.view', 'color' => 'green'],
                    ['id' => 'users-update', 'method' => 'PUT', 'path' => '/users/{id}', 'ar' => 'تحديث مستخدم', 'en' => 'Update user', 'perm' => 'users.update', 'color' => 'yellow'],
                    ['id' => 'users-delete', 'method' => 'DELETE', 'path' => '/users/{id}', 'ar' => 'حذف مستخدم', 'en' => 'Delete user', 'perm' => 'users.delete', 'color' => 'red'],
                ];
                @endphp

                @foreach($userEndpoints as $ep)
                <section id="{{ $ep['id'] }}" class="mb-6">
                    <div class="endpoint-card">
                        <div class="bg-{{ $ep['color'] }}-50 border-b border-{{ $ep['color'] }}-100 p-4 flex items-center gap-3">
                            <span class="badge method-{{ strtolower($ep['method'] === 'DELETE' ? 'delete' : strtolower($ep['method'])) }} text-white text-xs">{{ $ep['method'] }}</span>
                            <code class="text-gray-800 font-semibold">{{ $ep['path'] }}</code>
                            <span class="mr-auto text-sm text-gray-500" data-ar="{{ $ep['ar'] }}" data-en="{{ $ep['en'] }}">{{ $ep['ar'] }}</span>
                            <span class="badge bg-purple-100 text-purple-700">{{ $ep['perm'] }}</span>
                        </div>
                    </div>
                </section>
                @endforeach

                {{-- ============================================================ --}}
                {{-- ROLES --}}
                {{-- ============================================================ --}}
                <section id="roles" class="mb-20">
                    <h2 class="text-3xl font-bold mb-6" data-ar="إدارة الأدوار" data-en="Roles Management">إدارة الأدوار</h2>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right" data-ar="الطريقة" data-en="Method">الطريقة</th><th class="p-3 text-right" data-ar="المسار" data-en="Endpoint">المسار</th><th class="p-3 text-right" data-ar="الصلاحية" data-en="Permission">الصلاحية</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/roles</code></td><td class="p-3"><code>roles.view</code></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/roles</code></td><td class="p-3"><code>roles.create</code></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/roles/{id}</code></td><td class="p-3"><code>roles.view</code></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/roles/{id}</code></td><td class="p-3"><code>roles.update</code></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-delete text-white">DEL</span></td><td class="p-3"><code>/roles/{id}</code></td><td class="p-3"><code>roles.delete</code></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/roles/{id}/permissions</code></td><td class="p-3"><code>roles.update</code></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/roles/{id}/permissions/add</code></td><td class="p-3"><code>roles.update</code></td></tr>
                                <tr><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/roles/{id}/permissions/remove</code></td><td class="p-3"><code>roles.update</code></td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- ============================================================ --}}
                {{-- PERMISSIONS --}}
                {{-- ============================================================ --}}
                <section id="permissions" class="mb-20">
                    <h2 class="text-3xl font-bold mb-6" data-ar="إدارة الصلاحيات" data-en="Permissions Management">إدارة الصلاحيات</h2>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right" data-ar="الطريقة" data-en="Method">الطريقة</th><th class="p-3 text-right" data-ar="المسار" data-en="Endpoint">المسار</th><th class="p-3 text-right" data-ar="الصلاحية" data-en="Permission">الصلاحية</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/permissions</code></td><td class="p-3"><code>permissions.view</code></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/permissions</code></td><td class="p-3"><code>permissions.create</code></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/permissions/resource</code></td><td class="p-3"><code>permissions.create</code></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/permissions/{id}</code></td><td class="p-3"><code>permissions.view</code></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/permissions/{id}</code></td><td class="p-3"><code>permissions.update</code></td></tr>
                                <tr><td class="p-3"><span class="badge method-delete text-white">DEL</span></td><td class="p-3"><code>/permissions/{id}</code></td><td class="p-3"><code>permissions.delete</code></td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- ============================================================ --}}
                {{-- SERVICES --}}
                {{-- ============================================================ --}}
                <section id="services" class="mb-20">
                    <h2 class="text-3xl font-bold mb-6" data-ar="إدارة الخدمات" data-en="Services Management">إدارة الخدمات</h2>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right" data-ar="الطريقة" data-en="Method">الطريقة</th><th class="p-3 text-right" data-ar="المسار" data-en="Endpoint">المسار</th><th class="p-3 text-right" data-ar="الصلاحية" data-en="Permission">الصلاحية</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/services</code></td><td class="p-3"><code>services.view</code></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/services</code></td><td class="p-3"><code>services.create</code></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/services/{id}</code></td><td class="p-3"><code>services.view</code></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/services/{id}</code></td><td class="p-3"><code>services.update</code></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-delete text-white">DEL</span></td><td class="p-3"><code>/services/{id}</code></td><td class="p-3"><code>services.delete</code></td></tr>
                                <tr><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/services/{id}/regenerate-token</code></td><td class="p-3"><code>services.update</code></td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- ============================================================ --}}
                {{-- S2S --}}
                {{-- ============================================================ --}}
                <section id="s2s" class="mb-20">
                    <h2 class="text-3xl font-bold mb-4" data-ar="تواصل بين الخدمات" data-en="Service-to-Service">تواصل بين الخدمات</h2>
                    <div class="bg-purple-50 border border-purple-200 rounded-xl p-5 mb-6">
                        <h4 class="font-bold text-purple-800 mb-2" data-ar="يتطلب هيدر X-Service-Token بدلاً من JWT" data-en="Requires X-Service-Token header instead of JWT">يتطلب هيدر X-Service-Token بدلاً من JWT</h4>
                        <code class="text-purple-600">X-Service-Token: your-service-token</code>
                    </div>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right" data-ar="الطريقة" data-en="Method">الطريقة</th><th class="p-3 text-right" data-ar="المسار" data-en="Endpoint">المسار</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/service/verify-token</code></td><td class="p-3" data-ar="التحقق من توكن مستخدم" data-en="Verify user token">التحقق من توكن مستخدم</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/service/check-permission</code></td><td class="p-3" data-ar="فحص صلاحية مستخدم" data-en="Check user permission">فحص صلاحية مستخدم</td></tr>
                                <tr><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/service/user-info</code></td><td class="p-3" data-ar="جلب بيانات مستخدم" data-en="Get user info">جلب بيانات مستخدم</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- ============================================================ --}}
                {{-- HTTP STATUS CODES --}}
                {{-- ============================================================ --}}
                <section id="errors" class="mb-20">
                    <h2 class="text-3xl font-bold mb-4" data-ar="رموز الحالة HTTP" data-en="HTTP Status Codes">رموز الحالة HTTP</h2>
                    <p class="text-gray-600 mb-6" data-ar="رموز HTTP المستخدمة في الردود" data-en="HTTP status codes used in responses">رموز HTTP المستخدمة في الردود</p>

                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200 mb-8">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 border-b">
                                    <th class="p-4 text-right font-semibold text-gray-500" data-ar="الرمز" data-en="Code">الرمز</th>
                                    <th class="p-4 text-right font-semibold text-gray-500" data-ar="الاسم" data-en="Name">الاسم</th>
                                    <th class="p-4 text-right font-semibold text-gray-500" data-ar="الوصف" data-en="Description">الوصف</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b bg-green-50/50"><td colspan="3" class="p-2 font-bold text-green-800 text-xs uppercase tracking-wider" data-ar="نجاح" data-en="Success">نجاح</td></tr>
                                <tr class="border-b">
                                    <td class="p-4"><span class="bg-green-100 text-green-700 px-2 py-1 rounded font-mono">200</span></td>
                                    <td class="p-4 font-medium">OK</td>
                                    <td class="p-4 text-gray-600" data-ar="تمت العملية بنجاح" data-en="Request succeeded">تمت العملية بنجاح</td>
                                </tr>
                                <tr class="border-b">
                                    <td class="p-4"><span class="bg-green-100 text-green-700 px-2 py-1 rounded font-mono">201</span></td>
                                    <td class="p-4 font-medium">Created</td>
                                    <td class="p-4 text-gray-600" data-ar="تم إنشاء المورد بنجاح" data-en="Resource created successfully">تم إنشاء المورد بنجاح</td>
                                </tr>
                                <tr class="border-b bg-yellow-50/50"><td colspan="3" class="p-2 font-bold text-yellow-800 text-xs uppercase tracking-wider" data-ar="أخطاء العميل" data-en="Client Errors">أخطاء العميل</td></tr>
                                <tr class="border-b">
                                    <td class="p-4"><span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded font-mono">400</span></td>
                                    <td class="p-4 font-medium">Bad Request</td>
                                    <td class="p-4 text-gray-600" data-ar="خطأ في التحقق من البيانات" data-en="Validation error">خطأ في التحقق من البيانات</td>
                                </tr>
                                <tr class="border-b">
                                    <td class="p-4"><span class="bg-red-100 text-red-700 px-2 py-1 rounded font-mono">401</span></td>
                                    <td class="p-4 font-medium">Unauthorized</td>
                                    <td class="p-4 text-gray-600" data-ar="غير مصرح - التوكن مفقود أو منتهي" data-en="Not authenticated - token missing or expired">غير مصرح - التوكن مفقود أو منتهي</td>
                                </tr>
                                <tr class="border-b">
                                    <td class="p-4"><span class="bg-red-100 text-red-700 px-2 py-1 rounded font-mono">403</span></td>
                                    <td class="p-4 font-medium">Forbidden</td>
                                    <td class="p-4 text-gray-600" data-ar="ليس لديك صلاحية لهذا الإجراء" data-en="No permission for this action">ليس لديك صلاحية لهذا الإجراء</td>
                                </tr>
                                <tr class="border-b">
                                    <td class="p-4"><span class="bg-orange-100 text-orange-700 px-2 py-1 rounded font-mono">404</span></td>
                                    <td class="p-4 font-medium">Not Found</td>
                                    <td class="p-4 text-gray-600" data-ar="المورد غير موجود" data-en="Resource not found">المورد غير موجود</td>
                                </tr>
                                <tr class="border-b">
                                    <td class="p-4"><span class="bg-orange-100 text-orange-700 px-2 py-1 rounded font-mono">422</span></td>
                                    <td class="p-4 font-medium">Unprocessable Entity</td>
                                    <td class="p-4 text-gray-600" data-ar="البيانات صحيحة لكن لا يمكن معالجتها" data-en="Valid data but cannot be processed">البيانات صحيحة لكن لا يمكن معالجتها</td>
                                </tr>
                                <tr class="border-b">
                                    <td class="p-4"><span class="bg-purple-100 text-purple-700 px-2 py-1 rounded font-mono">429</span></td>
                                    <td class="p-4 font-medium">Too Many Requests</td>
                                    <td class="p-4 text-gray-600" data-ar="تم تجاوز حد الطلبات" data-en="Rate limit exceeded">تم تجاوز حد الطلبات</td>
                                </tr>
                                <tr class="border-b bg-red-50/50"><td colspan="3" class="p-2 font-bold text-red-800 text-xs uppercase tracking-wider" data-ar="أخطاء الخادم" data-en="Server Errors">أخطاء الخادم</td></tr>
                                <tr>
                                    <td class="p-4"><span class="bg-red-100 text-red-700 px-2 py-1 rounded font-mono">500</span></td>
                                    <td class="p-4 font-medium">Internal Server Error</td>
                                    <td class="p-4 text-gray-600" data-ar="خطأ في الخادم" data-en="Server error">خطأ في الخادم</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <h3 class="text-xl font-bold mb-4" data-ar="صيغة رد الخطأ" data-en="Error Response Format">صيغة رد الخطأ</h3>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <div class="code-block">
                            <div class="code-header"><span data-ar="خطأ مصادقة" data-en="Authentication Error">خطأ مصادقة</span><span class="badge bg-red-500/30 text-red-300">401</span></div>
                            <pre><code>{
  <span class="json-key">"success"</span>: <span class="json-bool">false</span>,
  <span class="json-key">"code"</span>: <span class="json-string">"token_expired"</span>,
  <span class="json-key">"message"</span>: <span class="json-string">"انتهت صلاحية التوكن"</span>
}</code></pre>
                        </div>
                        <div class="code-block">
                            <div class="code-header"><span data-ar="خطأ تحقق" data-en="Validation Error">خطأ تحقق</span><span class="badge bg-yellow-500/30 text-yellow-300">400</span></div>
                            <pre><code>{
  <span class="json-key">"success"</span>: <span class="json-bool">false</span>,
  <span class="json-key">"message"</span>: <span class="json-string">"فشل التحقق من البيانات"</span>,
  <span class="json-key">"errors"</span>: {
    <span class="json-key">"email"</span>: [<span class="json-string">"البريد مطلوب"</span>],
    <span class="json-key">"password"</span>: [<span class="json-string">"كلمة المرور قصيرة"</span>]
  }
}</code></pre>
                        </div>
                    </div>
                </section>

                <section id="error-codes" class="mb-20">
                    <h2 class="text-3xl font-bold mb-6" data-ar="جدول رموز الأخطاء" data-en="Error Codes Reference">جدول رموز الأخطاء</h2>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Error Code</th><th class="p-3 text-right">HTTP</th><th class="p-3 text-right" data-ar="الوصف (عربي)" data-en="Description (AR)">الوصف (عربي)</th><th class="p-3 text-right" data-ar="الوصف (إنجليزي)" data-en="Description (EN)">الوصف (إنجليزي)</th></tr></thead>
                            <tbody>
                                <tr class="border-b bg-red-50/50"><td colspan="4" class="p-2 font-bold text-red-800 text-xs uppercase tracking-wider">Authentication</td></tr>
                                <tr class="border-b"><td class="p-3"><code>authentication_required</code></td><td class="p-3">401</td><td class="p-3">غير مصرح لك بالوصول</td><td class="p-3">Unauthenticated</td></tr>
                                <tr class="border-b"><td class="p-3"><code>token_expired</code></td><td class="p-3">401</td><td class="p-3">انتهت صلاحية التوكن</td><td class="p-3">Token has expired</td></tr>
                                <tr class="border-b"><td class="p-3"><code>token_invalid</code></td><td class="p-3">401</td><td class="p-3">التوكن غير صالح</td><td class="p-3">Token is invalid</td></tr>
                                <tr class="border-b"><td class="p-3"><code>token_blacklisted</code></td><td class="p-3">401</td><td class="p-3">التوكن محظور</td><td class="p-3">Token has been blacklisted</td></tr>
                                <tr class="border-b"><td class="p-3"><code>invalid_login_credentials</code></td><td class="p-3">401</td><td class="p-3">بيانات الدخول غير صحيحة</td><td class="p-3">Invalid credentials</td></tr>
                                <tr class="border-b bg-orange-50/50"><td colspan="4" class="p-2 font-bold text-orange-800 text-xs uppercase tracking-wider">Authorization</td></tr>
                                <tr class="border-b"><td class="p-3"><code>permission_denied</code></td><td class="p-3">403</td><td class="p-3">ليس لديك صلاحية</td><td class="p-3">Permission denied</td></tr>
                                <tr class="border-b"><td class="p-3"><code>user_blocked</code></td><td class="p-3">403</td><td class="p-3">الحساب محظور</td><td class="p-3">Account is blocked</td></tr>
                                <tr class="border-b"><td class="p-3"><code>user_suspended</code></td><td class="p-3">403</td><td class="p-3">الحساب موقوف</td><td class="p-3">Account is suspended</td></tr>
                                <tr class="border-b"><td class="p-3"><code>user_disabled</code></td><td class="p-3">403</td><td class="p-3">الحساب غير مفعل</td><td class="p-3">Account is disabled</td></tr>
                                <tr class="border-b bg-yellow-50/50"><td colspan="4" class="p-2 font-bold text-yellow-800 text-xs uppercase tracking-wider">Validation</td></tr>
                                <tr class="border-b"><td class="p-3"><code>validation_failed</code></td><td class="p-3">400</td><td class="p-3">فشل التحقق من البيانات</td><td class="p-3">Validation failed</td></tr>
                                <tr class="border-b"><td class="p-3"><code>resource_not_found</code></td><td class="p-3">404</td><td class="p-3">المورد غير موجود</td><td class="p-3">Resource not found</td></tr>
                                <tr class="border-b"><td class="p-3"><code>rate_limit_exceeded</code></td><td class="p-3">429</td><td class="p-3">تم تجاوز حد الطلبات</td><td class="p-3">Too many requests</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- ============================================================ --}}
                {{-- RATE LIMITING --}}
                {{-- ============================================================ --}}
                <section id="rate-limiting" class="mb-20">
                    <h2 class="text-3xl font-bold mb-4" data-ar="حد الطلبات" data-en="Rate Limiting">حد الطلبات</h2>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-4 text-right" data-ar="النوع" data-en="Type">النوع</th><th class="p-4 text-right" data-ar="الحد" data-en="Limit">الحد</th><th class="p-4 text-right" data-ar="الفترة" data-en="Window">الفترة</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-4">API Requests</td><td class="p-4">{{ config('auth-service.rate_limit.api_per_minute', 60) }}</td><td class="p-4" data-ar="دقيقة" data-en="minute">دقيقة</td></tr>
                                <tr class="border-b"><td class="p-4">Login Attempts</td><td class="p-4">{{ config('auth-service.rate_limit.login_attempts', 5) }}</td><td class="p-4" data-ar="دقيقة" data-en="minute">دقيقة</td></tr>
                                <tr><td class="p-4">Service-to-Service</td><td class="p-4">100</td><td class="p-4" data-ar="دقيقة" data-en="minute">دقيقة</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- ============================================================ --}}
                {{-- DEFAULT ROLES --}}
                {{-- ============================================================ --}}
                <section id="default-roles" class="mb-20">
                    <h2 class="text-3xl font-bold mb-6" data-ar="الأدوار والصلاحيات الافتراضية" data-en="Default Roles & Permissions">الأدوار والصلاحيات الافتراضية</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                        <div class="bg-white rounded-xl border border-gray-200 p-5">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="w-3 h-3 bg-red-500 rounded-full"></span>
                                <h4 class="font-bold">super-admin</h4>
                            </div>
                            <p class="text-sm text-gray-500" data-ar="وصول كامل - جميع الصلاحيات" data-en="Full access - all permissions">وصول كامل - جميع الصلاحيات</p>
                        </div>
                        <div class="bg-white rounded-xl border border-gray-200 p-5">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="w-3 h-3 bg-blue-500 rounded-full"></span>
                                <h4 class="font-bold">admin</h4>
                            </div>
                            <p class="text-sm text-gray-500" data-ar="وصول إداري" data-en="Administrative access">وصول إداري</p>
                        </div>
                        <div class="bg-white rounded-xl border border-gray-200 p-5">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                                <h4 class="font-bold">user</h4>
                            </div>
                            <p class="text-sm text-gray-500" data-ar="مستخدم عادي" data-en="Regular user">مستخدم عادي</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right" data-ar="المورد" data-en="Resource">المورد</th><th class="p-3 text-right" data-ar="الصلاحيات" data-en="Permissions">الصلاحيات</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3 font-semibold" data-ar="المستخدمين" data-en="Users">المستخدمين</td><td class="p-3 space-x-1 space-x-reverse"><code class="bg-gray-100 px-2 py-0.5 rounded text-xs">users.view</code> <code class="bg-gray-100 px-2 py-0.5 rounded text-xs">users.create</code> <code class="bg-gray-100 px-2 py-0.5 rounded text-xs">users.update</code> <code class="bg-gray-100 px-2 py-0.5 rounded text-xs">users.delete</code></td></tr>
                                <tr class="border-b"><td class="p-3 font-semibold" data-ar="الأدوار" data-en="Roles">الأدوار</td><td class="p-3 space-x-1 space-x-reverse"><code class="bg-gray-100 px-2 py-0.5 rounded text-xs">roles.view</code> <code class="bg-gray-100 px-2 py-0.5 rounded text-xs">roles.create</code> <code class="bg-gray-100 px-2 py-0.5 rounded text-xs">roles.update</code> <code class="bg-gray-100 px-2 py-0.5 rounded text-xs">roles.delete</code></td></tr>
                                <tr class="border-b"><td class="p-3 font-semibold" data-ar="الصلاحيات" data-en="Permissions">الصلاحيات</td><td class="p-3 space-x-1 space-x-reverse"><code class="bg-gray-100 px-2 py-0.5 rounded text-xs">permissions.view</code> <code class="bg-gray-100 px-2 py-0.5 rounded text-xs">permissions.create</code> <code class="bg-gray-100 px-2 py-0.5 rounded text-xs">permissions.update</code> <code class="bg-gray-100 px-2 py-0.5 rounded text-xs">permissions.delete</code></td></tr>
                                <tr><td class="p-3 font-semibold" data-ar="الخدمات" data-en="Services">الخدمات</td><td class="p-3 space-x-1 space-x-reverse"><code class="bg-gray-100 px-2 py-0.5 rounded text-xs">services.view</code> <code class="bg-gray-100 px-2 py-0.5 rounded text-xs">services.create</code> <code class="bg-gray-100 px-2 py-0.5 rounded text-xs">services.update</code> <code class="bg-gray-100 px-2 py-0.5 rounded text-xs">services.delete</code></td></tr>
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
