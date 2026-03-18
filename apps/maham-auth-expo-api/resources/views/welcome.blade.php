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
        .perm-badge { font-size: 11px; background: #f3e8ff; color: #7c3aed; padding: 2px 10px; border-radius: 6px; font-family: 'JetBrains Mono', monospace; }
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
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
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
                    </ul>
                </div>

                <div>
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-3" data-ar="المصادقة" data-en="Auth Endpoints">المصادقة</h3>
                    <ul class="space-y-0.5">
                        <li><a href="#register" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg"><span class="badge method-post text-white ml-1">POST</span> <span data-ar="التسجيل" data-en="Register">التسجيل</span></a></li>
                        <li><a href="#login" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg"><span class="badge method-post text-white ml-1">POST</span> <span data-ar="تسجيل الدخول" data-en="Login">تسجيل الدخول</span></a></li>
                        <li><a href="#otp-send" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg"><span class="badge method-post text-white ml-1">POST</span> <span data-ar="إرسال OTP" data-en="Send OTP">إرسال OTP</span></a></li>
                        <li><a href="#otp-verify" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg"><span class="badge method-post text-white ml-1">POST</span> <span data-ar="تحقق OTP" data-en="Verify OTP">تحقق OTP</span></a></li>
                        <li><a href="#otp-complete" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg"><span class="badge method-post text-white ml-1">POST</span> <span data-ar="إكمال التسجيل" data-en="Complete Registration">إكمال التسجيل</span></a></li>
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
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-3" data-ar="الملف الشخصي" data-en="Profile & Email">الملف الشخصي</h3>
                    <ul class="space-y-0.5">
                        <li><a href="#update-profile" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg"><span class="badge method-put text-white ml-1">PUT</span> <span data-ar="تحديث الملف" data-en="Update Profile">تحديث الملف</span></a></li>
                        <li><a href="#send-verification" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg"><span class="badge method-post text-white ml-1">POST</span> <span data-ar="إرسال كود التحقق" data-en="Send Verification">إرسال كود التحقق</span></a></li>
                        <li><a href="#verify-email" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg"><span class="badge method-post text-white ml-1">POST</span> <span data-ar="تحقق من البريد" data-en="Verify Email">تحقق من البريد</span></a></li>
                        <li><a href="#phone-send-otp" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg"><span class="badge method-post text-white ml-1">POST</span> <span data-ar="تحقق الجوال" data-en="Phone OTP">تحقق الجوال</span></a></li>
                        <li><a href="#phone-verify-otp" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg"><span class="badge method-post text-white ml-1">POST</span> <span data-ar="تأكيد الجوال" data-en="Verify Phone">تأكيد الجوال</span></a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-3" data-ar="التحقق (S2S)" data-en="Verification (S2S)">التحقق (S2S)</h3>
                    <ul class="space-y-0.5">
                        <li><a href="#verify-token" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg"><span class="badge method-post text-white ml-1">POST</span> <span data-ar="التحقق من التوكن" data-en="Verify Token">التحقق من التوكن</span></a></li>
                        <li><a href="#check-permission" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg"><span class="badge method-post text-white ml-1">POST</span> <span data-ar="فحص صلاحية" data-en="Check Permission">فحص صلاحية</span></a></li>
                        <li><a href="#check-permissions" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg"><span class="badge method-post text-white ml-1">POST</span> <span data-ar="فحص عدة صلاحيات" data-en="Check Permissions">فحص عدة صلاحيات</span></a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-3" data-ar="إدارة OTP" data-en="OTP Management">إدارة OTP</h3>
                    <ul class="space-y-0.5">
                        <li><a href="#otp-balance" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg"><span class="badge method-get text-white ml-1">GET</span> <span data-ar="رصيد OTP" data-en="OTP Balance">رصيد OTP</span></a></li>
                        <li><a href="#otp-providers" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg"><span class="badge method-get text-white ml-1">GET</span> <span data-ar="حالة المزودين" data-en="Providers Status">حالة المزودين</span></a></li>
                        <li><a href="#otp-stats" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg"><span class="badge method-get text-white ml-1">GET</span> <span data-ar="إحصائيات OTP" data-en="OTP Stats">إحصائيات OTP</span></a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-3" data-ar="الإدارة" data-en="Management">الإدارة</h3>
                    <ul class="space-y-0.5">
                        <li><a href="#users" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="المستخدمين" data-en="Users">المستخدمين</a></li>
                        <li><a href="#roles" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="الأدوار" data-en="Roles">الأدوار</a></li>
                        <li><a href="#permissions" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="الصلاحيات" data-en="Permissions">الصلاحيات</a></li>
                        <li><a href="#services" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="الخدمات" data-en="Services">الخدمات</a></li>
                        <li><a href="#s2s" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="تواصل S2S" data-en="Service-to-Service">تواصل S2S</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-3" data-ar="المرجع" data-en="Reference">المرجع</h3>
                    <ul class="space-y-0.5">
                        <li><a href="#errors" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="رموز الحالة" data-en="Status Codes">رموز الحالة</a></li>
                        <li><a href="#error-codes" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="رموز الأخطاء" data-en="Error Codes">رموز الأخطاء</a></li>
                        <li><a href="#rate-limiting" class="sidebar-link block py-2 px-3 text-sm text-gray-600 rounded-lg" data-ar="حد الطلبات" data-en="Rate Limiting">حد الطلبات</a></li>
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
                        <div class="code-header"><span>Health Check</span><span class="badge method-get text-white">GET /health</span></div>
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
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full">
                            <thead><tr class="bg-gray-50 border-b">
                                <th class="p-4 text-right text-sm font-semibold text-gray-500" data-ar="الهيدر" data-en="Header">الهيدر</th>
                                <th class="p-4 text-right text-sm font-semibold text-gray-500" data-ar="القيمة" data-en="Value">القيمة</th>
                                <th class="p-4 text-right text-sm font-semibold text-gray-500" data-ar="مطلوب" data-en="Required">مطلوب</th>
                                <th class="p-4 text-right text-sm font-semibold text-gray-500" data-ar="الوصف" data-en="Description">الوصف</th>
                            </tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-4"><code class="bg-gray-100 px-2 py-1 rounded text-sm">Content-Type</code></td><td class="p-4"><code class="text-sm">application/json</code></td><td class="p-4"><span class="text-red-500 font-bold">✓</span></td><td class="p-4 text-sm text-gray-600" data-ar="نوع المحتوى المرسل" data-en="Content type of request body">نوع المحتوى المرسل</td></tr>
                                <tr class="border-b"><td class="p-4"><code class="bg-gray-100 px-2 py-1 rounded text-sm">Accept</code></td><td class="p-4"><code class="text-sm">application/json</code></td><td class="p-4"><span class="text-red-500 font-bold">✓</span></td><td class="p-4 text-sm text-gray-600" data-ar="نوع المحتوى المطلوب" data-en="Expected response format">نوع المحتوى المطلوب</td></tr>
                                <tr class="border-b"><td class="p-4"><code class="bg-gray-100 px-2 py-1 rounded text-sm">Authorization</code></td><td class="p-4"><code class="text-sm">Bearer {token}</code></td><td class="p-4"><span class="text-yellow-600 text-sm" data-ar="للمحمية" data-en="Protected">للمحمية</span></td><td class="p-4 text-sm text-gray-600" data-ar="توكن JWT للطلبات المحمية" data-en="JWT token for protected routes">توكن JWT للمحمية</td></tr>
                                <tr class="border-b"><td class="p-4"><code class="bg-gray-100 px-2 py-1 rounded text-sm">Accept-Language</code></td><td class="p-4"><code class="text-sm">ar</code> | <code class="text-sm">en</code></td><td class="p-4"><span class="text-gray-400 text-sm" data-ar="اختياري" data-en="Optional">اختياري</span></td><td class="p-4 text-sm text-gray-600" data-ar="لغة الردود (الافتراضي: en)" data-en="Response language (default: en)">لغة الردود</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- ============================================================ --}}
                {{-- AUTHENTICATION INFO --}}
                {{-- ============================================================ --}}
                <section id="authentication" class="mb-20">
                    <h2 class="text-3xl font-bold mb-4" data-ar="المصادقة" data-en="Authentication">المصادقة</h2>
                    <p class="text-gray-600 mb-6" data-ar="تستخدم الـ API مصادقة JWT. أرسل التوكن في Header لكل الطلبات المحمية." data-en="The API uses JWT authentication. Send the token in the Authorization header for all protected routes.">تستخدم الـ API مصادقة JWT.</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div class="bg-white border border-gray-200 rounded-xl p-5">
                            <h4 class="font-semibold mb-2" data-ar="طلبات المستخدم (JWT)" data-en="User Requests (JWT)">طلبات المستخدم (JWT)</h4>
                            <div class="code-block"><pre><code>Authorization: Bearer eyJ0eXAiOiJKV1Qi...</code></pre></div>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-xl p-5">
                            <h4 class="font-semibold mb-2" data-ar="تواصل بين الخدمات" data-en="Service-to-Service">تواصل بين الخدمات</h4>
                            <div class="code-block"><pre><code>Internal Docker Network (no auth needed)</code></pre></div>
                        </div>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-5 mb-6">
                        <h4 class="font-bold text-blue-800 mb-3" data-ar="معلومات التوكن" data-en="Token Information">معلومات التوكن</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div><span class="text-blue-600 font-semibold" data-ar="مدة الصلاحية:" data-en="Expiry:">مدة الصلاحية:</span> <span class="text-blue-800">{{ config('jwt.ttl', 60) }} <span data-ar="دقيقة" data-en="min">دقيقة</span></span></div>
                            <div><span class="text-blue-600 font-semibold" data-ar="فترة التجديد:" data-en="Refresh:">فترة التجديد:</span> <span class="text-blue-800">{{ config('jwt.refresh_ttl', 20160) }} <span data-ar="دقيقة" data-en="min">دقيقة</span></span></div>
                            <div><span class="text-blue-600 font-semibold" data-ar="الخوارزمية:" data-en="Algorithm:">الخوارزمية:</span> <span class="text-blue-800">{{ config('jwt.algo', 'HS256') }}</span></div>
                        </div>
                    </div>

                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-5">
                        <h4 class="font-bold text-yellow-800 mb-2" data-ar="تنبيهات مهمة" data-en="Important Notes">تنبيهات مهمة</h4>
                        <ul class="text-sm text-yellow-700 space-y-1">
                            <li data-ar="• عند تسجيل الخروج يتم حظر التوكن ولا يمكن استخدامه مجدداً" data-en="• Token is blacklisted on logout and cannot be reused">• عند تسجيل الخروج يتم حظر التوكن</li>
                            <li data-ar="• استخدم /auth/refresh لتجديد التوكن قبل انتهاء صلاحيته" data-en="• Use /auth/refresh to renew token before expiry">• استخدم /auth/refresh لتجديد التوكن</li>
                            <li data-ar="• التوكن القديم يصبح غير صالح بعد التجديد" data-en="• Old token becomes invalid after refresh">• التوكن القديم يصبح غير صالح بعد التجديد</li>
                        </ul>
                    </div>
                </section>

                {{-- ============================================================ --}}
                {{-- RESPONSE FORMAT --}}
                {{-- ============================================================ --}}
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
  <span class="json-key">"code"</span>: <span class="json-string">"error_code"</span>,
  <span class="json-key">"errors"</span>: { ... }
}</code></pre>
                        </div>
                    </div>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b">
                                <th class="p-4 text-right font-semibold text-gray-500" data-ar="الحقل" data-en="Field">الحقل</th>
                                <th class="p-4 text-right font-semibold text-gray-500" data-ar="النوع" data-en="Type">النوع</th>
                                <th class="p-4 text-right font-semibold text-gray-500" data-ar="الوصف" data-en="Description">الوصف</th>
                            </tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-4"><code>success</code></td><td class="p-4">boolean</td><td class="p-4 text-gray-600" data-ar="حالة العملية (true/false)" data-en="Operation status">حالة العملية</td></tr>
                                <tr class="border-b"><td class="p-4"><code>message</code></td><td class="p-4">string</td><td class="p-4 text-gray-600" data-ar="رسالة توضيحية (حسب اللغة)" data-en="Descriptive message (localized)">رسالة توضيحية</td></tr>
                                <tr class="border-b"><td class="p-4"><code>data</code></td><td class="p-4">object|array</td><td class="p-4 text-gray-600" data-ar="البيانات (للردود الناجحة)" data-en="Data payload (success only)">البيانات</td></tr>
                                <tr class="border-b"><td class="p-4"><code>code</code></td><td class="p-4">string</td><td class="p-4 text-gray-600" data-ar="رمز الخطأ للمعالجة البرمجية" data-en="Error code for programmatic use">رمز الخطأ</td></tr>
                                <tr><td class="p-4"><code>errors</code></td><td class="p-4">object</td><td class="p-4 text-gray-600" data-ar="تفاصيل أخطاء التحقق" data-en="Validation error details">أخطاء التحقق</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- ============================================================ --}}
                {{-- REGISTER --}}
                {{-- ============================================================ --}}
                <section id="register" class="mb-16">
                    <div class="endpoint-card">
                        <div class="bg-blue-50 border-b border-blue-100 p-5 flex items-center gap-3">
                            <span class="badge method-post text-white text-xs">POST</span>
                            <code class="text-gray-800 font-semibold">/auth/register</code>
                            <span class="mr-auto text-sm text-gray-500" data-ar="تسجيل مستخدم جديد" data-en="Register new user">تسجيل مستخدم جديد</span>
                            <span class="badge bg-green-100 text-green-700" data-ar="عام" data-en="Public">عام</span>
                        </div>
                        <div class="p-5 space-y-6">
                            <div class="overflow-x-auto">
                                <table class="param-table w-full text-sm">
                                    <thead><tr class="border-b bg-gray-50"><th class="p-3 text-right" data-ar="الحقل" data-en="Field">الحقل</th><th class="p-3 text-right" data-ar="النوع" data-en="Type">النوع</th><th class="p-3 text-right" data-ar="مطلوب" data-en="Required">مطلوب</th><th class="p-3 text-right" data-ar="القواعد" data-en="Rules">القواعد</th></tr></thead>
                                    <tbody>
                                        <tr class="border-b"><td class="p-3"><code>name</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500 font-bold">✓</span></td><td class="p-3">max:255</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>email</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500 font-bold">✓</span></td><td class="p-3" data-ar="بريد صالح، فريد" data-en="valid email, unique">بريد صالح، فريد</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>password</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500 font-bold">✓</span></td><td class="p-3" data-ar="8 أحرف+، كبير وصغير وأرقام" data-en="min:8, mixed case, numbers">8 أحرف+، كبير وصغير وأرقام</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>password_confirmation</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500 font-bold">✓</span></td><td class="p-3" data-ar="يطابق كلمة المرور" data-en="must match password">يطابق كلمة المرور</td></tr>
                                        <tr><td class="p-3"><code>phone</code></td><td class="p-3">string</td><td class="p-3"><span class="text-gray-400">—</span></td><td class="p-3">max:20</td></tr>
                                    </tbody>
                                </table>
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

                {{-- LOGIN --}}
                <section id="login" class="mb-16">
                    <div class="endpoint-card">
                        <div class="bg-blue-50 border-b border-blue-100 p-5 flex items-center gap-3">
                            <span class="badge method-post text-white text-xs">POST</span>
                            <code class="text-gray-800 font-semibold">/auth/login</code>
                            <span class="mr-auto text-sm text-gray-500" data-ar="تسجيل الدخول" data-en="Login">تسجيل الدخول</span>
                            <span class="badge bg-green-100 text-green-700" data-ar="عام" data-en="Public">عام</span>
                        </div>
                        <div class="p-5 space-y-6">
                            <div class="overflow-x-auto">
                                <table class="param-table w-full text-sm">
                                    <thead><tr class="border-b bg-gray-50"><th class="p-3 text-right" data-ar="الحقل" data-en="Field">الحقل</th><th class="p-3 text-right" data-ar="النوع" data-en="Type">النوع</th><th class="p-3 text-right" data-ar="مطلوب" data-en="Required">مطلوب</th><th class="p-3 text-right" data-ar="القواعد" data-en="Rules">القواعد</th></tr></thead>
                                    <tbody>
                                        <tr class="border-b"><td class="p-3"><code>identifier</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500 font-bold">✓</span></td><td class="p-3" data-ar="البريد أو رقم الجوال" data-en="Email or phone number">البريد أو رقم الجوال</td></tr>
                                        <tr><td class="p-3"><code>password</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500 font-bold">✓</span></td><td class="p-3" data-ar="كلمة المرور" data-en="Password">كلمة المرور</td></tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                <div class="code-block">
                                    <div class="code-header"><span>Request</span></div>
                                    <pre><code>{
  <span class="json-key">"identifier"</span>: <span class="json-string">"ahmed@example.com"</span>,
  <span class="json-key">"password"</span>: <span class="json-string">"Password123"</span>
}</code></pre>
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
                            </div>
                        </div>
                    </div>
                </section>

                {{-- ============================================================ --}}
                {{-- OTP LOGIN FLOW --}}
                {{-- ============================================================ --}}
                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-5 mb-8">
                    <h3 class="text-xl font-bold text-emerald-800 mb-2 flex items-center gap-2" data-ar="📱 تسجيل الدخول عبر OTP" data-en="📱 OTP Login Flow">📱 تسجيل الدخول عبر OTP</h3>
                    <p class="text-emerald-700 text-sm mb-3" data-ar="تسجيل الدخول أو إنشاء حساب جديد عبر رقم الجوال و رمز التحقق — مدعوم بمزود Authentica (SMS + WhatsApp)" data-en="Login or register via phone number and OTP — powered by Authentica provider (SMS + WhatsApp)">تسجيل الدخول أو إنشاء حساب جديد عبر رقم الجوال و رمز التحقق — مزود Authentica</p>
                    <div class="bg-white/60 rounded-lg p-4 text-sm">
                        <p class="font-bold text-emerald-800 mb-2" data-ar="التدفق الكامل:" data-en="Complete Flow:">التدفق الكامل:</p>
                        <ol class="list-decimal list-inside space-y-1 text-emerald-700">
                            <li data-ar="أرسل رقم الجوال → يصلك رمز OTP (user_type اختياري — يُكتشف تلقائياً)" data-en="Send phone → receive OTP code (user_type optional — auto-detected)">أرسل رقم الجوال → يصلك رمز OTP (user_type اختياري)</li>
                            <li data-ar="أرسل الرمز للتحقق → مستخدم موجود = توكن JWT، مستخدم جديد = registration_token" data-en="Verify OTP → existing user = JWT token, new user = registration_token">أرسل الرمز للتحقق → موجود = توكن، جديد = registration_token</li>
                            <li data-ar="(مستخدمين جدد) أكمل البيانات + حدد user_type (merchant/investor/sponsor)" data-en="(New users) Complete profile + specify user_type (merchant/investor/sponsor)">(مستخدمين جدد) أكمل البيانات + حدد نوع الحساب user_type</li>
                        </ol>
                        <div class="mt-3 p-2 bg-blue-50 border border-blue-200 rounded text-blue-700 text-xs">
                            <span class="font-bold">🔌</span>
                            <span data-ar="المزود: Authentica (authentica.sa) — يدعم SMS و WhatsApp مع خاصية الفولباك" data-en="Provider: Authentica (authentica.sa) — supports SMS & WhatsApp with fallback">المزود: Authentica — SMS + WhatsApp مع فولباك</span>
                        </div>
                        <div class="mt-2 p-2 bg-yellow-50 border border-yellow-200 rounded text-yellow-700 text-xs">
                            <span class="font-bold">⚠️</span>
                            <span data-ar="وضع الاختبار: أي رمز OTP مقبول (لا يُرسل SMS فعلي)" data-en="Test mode: any OTP code is accepted (no real SMS sent)">وضع الاختبار: أي رمز OTP مقبول (لا يُرسل SMS فعلي)</span>
                        </div>
                    </div>
                </div>

                {{-- OTP SEND --}}
                <section id="otp-send" class="mb-16">
                    <div class="endpoint-card">
                        <div class="bg-emerald-50 border-b border-emerald-100 p-5 flex items-center gap-3">
                            <span class="badge method-post text-white text-xs">POST</span>
                            <code class="text-gray-800 font-semibold">/auth/otp/send</code>
                            <span class="mr-auto text-sm text-gray-500" data-ar="إرسال رمز التحقق" data-en="Send OTP Code">إرسال رمز التحقق</span>
                            <span class="badge bg-green-100 text-green-700" data-ar="عام" data-en="Public">عام</span>
                        </div>
                        <div class="p-5 space-y-6">
                            <div class="overflow-x-auto">
                                <table class="param-table w-full text-sm">
                                    <thead><tr class="border-b bg-gray-50"><th class="p-3 text-right" data-ar="الحقل" data-en="Field">الحقل</th><th class="p-3 text-right" data-ar="النوع" data-en="Type">النوع</th><th class="p-3 text-right" data-ar="مطلوب" data-en="Required">مطلوب</th><th class="p-3 text-right" data-ar="القواعد" data-en="Rules">القواعد</th></tr></thead>
                                    <tbody>
                                        <tr class="border-b"><td class="p-3"><code>phone</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500 font-bold">✓</span></td><td class="p-3" data-ar="رقم الجوال (0501234567 أو +966501234567)" data-en="Phone (0501234567 or +966501234567)">رقم الجوال</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>user_type</code></td><td class="p-3">string</td><td class="p-3"><span class="text-gray-400">—</span></td><td class="p-3" data-ar="اختياري — يُكتشف تلقائياً للمستخدمين الموجودين (merchant, investor, sponsor, ...)" data-en="Optional — auto-detected for existing users (merchant, investor, sponsor, ...)">اختياري — يُكتشف تلقائياً</td></tr>
                                        <tr><td class="p-3"><code>channel</code></td><td class="p-3">string</td><td class="p-3"><span class="text-gray-400">—</span></td><td class="p-3" data-ar="sms أو whatsapp (افتراضي: sms)" data-en="sms or whatsapp (default: sms)">sms | whatsapp</td></tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                <div class="code-block">
                                    <div class="code-header"><span>Request</span></div>
                                    <pre><code>{
  <span class="json-key">"phone"</span>: <span class="json-string">"+966567891234"</span>
}</code></pre>
                                </div>
                                <div class="code-block">
                                    <div class="code-header"><span>Response</span><span class="badge bg-green-500/30 text-green-300">200</span></div>
                                    <pre><code>{
  <span class="json-key">"success"</span>: <span class="json-bool">true</span>,
  <span class="json-key">"message"</span>: <span class="json-string">"تم إرسال رمز التحقق"</span>,
  <span class="json-key">"data"</span>: {
    <span class="json-key">"is_new_user"</span>: <span class="json-bool">false</span>,
    <span class="json-key">"user_type"</span>: <span class="json-string">"merchant"</span>
  }
}</code></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- OTP VERIFY --}}
                <section id="otp-verify" class="mb-16">
                    <div class="endpoint-card">
                        <div class="bg-emerald-50 border-b border-emerald-100 p-5 flex items-center gap-3">
                            <span class="badge method-post text-white text-xs">POST</span>
                            <code class="text-gray-800 font-semibold">/auth/otp/verify</code>
                            <span class="mr-auto text-sm text-gray-500" data-ar="تحقق من الرمز" data-en="Verify OTP Code">تحقق من الرمز</span>
                            <span class="badge bg-green-100 text-green-700" data-ar="عام" data-en="Public">عام</span>
                        </div>
                        <div class="p-5 space-y-6">
                            <div class="overflow-x-auto">
                                <table class="param-table w-full text-sm">
                                    <thead><tr class="border-b bg-gray-50"><th class="p-3 text-right" data-ar="الحقل" data-en="Field">الحقل</th><th class="p-3 text-right" data-ar="النوع" data-en="Type">النوع</th><th class="p-3 text-right" data-ar="مطلوب" data-en="Required">مطلوب</th><th class="p-3 text-right" data-ar="القواعد" data-en="Rules">القواعد</th></tr></thead>
                                    <tbody>
                                        <tr class="border-b"><td class="p-3"><code>phone</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500 font-bold">✓</span></td><td class="p-3" data-ar="نفس الرقم المستخدم في الإرسال" data-en="Same phone used in send">نفس رقم الإرسال</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>code</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500 font-bold">✓</span></td><td class="p-3" data-ar="رمز التحقق المرسل (4-8 أرقام)" data-en="OTP code received (4-8 digits)">رمز التحقق</td></tr>
                                        <tr><td class="p-3"><code>user_type</code></td><td class="p-3">string</td><td class="p-3"><span class="text-gray-400">—</span></td><td class="p-3" data-ar="اختياري — يُسترجع من الكاش تلقائياً" data-en="Optional — auto-retrieved from cache">اختياري — يُكتشف تلقائياً</td></tr>
                                    </tbody>
                                </table>
                            </div>
                            <p class="text-sm text-gray-500 bg-gray-50 rounded-lg p-3" data-ar="إذا كان المستخدم موجود → يرجع token + بيانات المستخدم. إذا جديد → يرجع registration_token لإكمال التسجيل." data-en="If user exists → returns token + user data. If new → returns registration_token to complete registration.">إذا كان المستخدم موجود → يرجع token. إذا جديد → يرجع registration_token.</p>
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                <div class="code-block">
                                    <div class="code-header"><span>Request</span></div>
                                    <pre><code>{
  <span class="json-key">"phone"</span>: <span class="json-string">"+966567891234"</span>,
  <span class="json-key">"code"</span>: <span class="json-string">"123456"</span>
}</code></pre>
                                </div>
                                <div class="code-block">
                                    <div class="code-header"><span data-ar="رد — مستخدم موجود" data-en="Response — Existing User">رد — مستخدم موجود</span><span class="badge bg-green-500/30 text-green-300">200</span></div>
                                    <pre><code>{
  <span class="json-key">"success"</span>: <span class="json-bool">true</span>,
  <span class="json-key">"message"</span>: <span class="json-string">"تم تسجيل الدخول بنجاح"</span>,
  <span class="json-key">"data"</span>: {
    <span class="json-key">"is_new_user"</span>: <span class="json-bool">false</span>,
    <span class="json-key">"user"</span>: { ... },
    <span class="json-key">"token"</span>: <span class="json-string">"eyJ0eXAi..."</span>,
    <span class="json-key">"token_type"</span>: <span class="json-string">"bearer"</span>,
    <span class="json-key">"expires_in"</span>: <span class="json-number">3600</span>
  }
}</code></pre>
                                </div>
                            </div>
                            <div class="code-block">
                                <div class="code-header"><span data-ar="رد — مستخدم جديد" data-en="Response — New User">رد — مستخدم جديد</span><span class="badge bg-blue-500/30 text-blue-300">200</span></div>
                                <pre><code>{
  <span class="json-key">"success"</span>: <span class="json-bool">true</span>,
  <span class="json-key">"message"</span>: <span class="json-string">"تم التحقق — أكمل بياناتك للتسجيل"</span>,
  <span class="json-key">"data"</span>: {
    <span class="json-key">"is_new_user"</span>: <span class="json-bool">true</span>,
    <span class="json-key">"registration_token"</span>: <span class="json-string">"xPbaVXHRV5zXi4bBJx92Q..."</span>
  }
}</code></pre>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- OTP COMPLETE REGISTRATION --}}
                <section id="otp-complete" class="mb-16">
                    <div class="endpoint-card">
                        <div class="bg-emerald-50 border-b border-emerald-100 p-5 flex items-center gap-3">
                            <span class="badge method-post text-white text-xs">POST</span>
                            <code class="text-gray-800 font-semibold">/auth/otp/complete-registration</code>
                            <span class="mr-auto text-sm text-gray-500" data-ar="إكمال التسجيل بعد OTP" data-en="Complete Registration after OTP">إكمال التسجيل بعد OTP</span>
                            <span class="badge bg-green-100 text-green-700" data-ar="عام" data-en="Public">عام</span>
                        </div>
                        <div class="p-5 space-y-6">
                            <p class="text-sm text-gray-500 bg-gray-50 rounded-lg p-3" data-ar="هذا الإندبوينت فقط للمستخدمين الجدد — بعد الحصول على registration_token من /otp/verify. يجب تحديد نوع الحساب (user_type)." data-en="This endpoint is only for new users — after getting registration_token from /otp/verify. user_type is required.">فقط للمستخدمين الجدد بعد الحصول على registration_token — يجب تحديد نوع الحساب</p>
                            <div class="overflow-x-auto">
                                <table class="param-table w-full text-sm">
                                    <thead><tr class="border-b bg-gray-50"><th class="p-3 text-right" data-ar="الحقل" data-en="Field">الحقل</th><th class="p-3 text-right" data-ar="النوع" data-en="Type">النوع</th><th class="p-3 text-right" data-ar="مطلوب" data-en="Required">مطلوب</th><th class="p-3 text-right" data-ar="القواعد" data-en="Rules">القواعد</th></tr></thead>
                                    <tbody>
                                        <tr class="border-b"><td class="p-3"><code>registration_token</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500 font-bold">✓</span></td><td class="p-3" data-ar="التوكن من /otp/verify" data-en="Token from /otp/verify">التوكن من verify</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>name</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500 font-bold">✓</span></td><td class="p-3" data-ar="الاسم الكامل" data-en="Full name">الاسم الكامل</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>user_type</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500 font-bold">✓</span></td><td class="p-3" data-ar="نوع الحساب: merchant, investor, sponsor, user (لا يقبل admin/super-admin)" data-en="Role: merchant, investor, sponsor, user (not admin/super-admin)">merchant | investor | sponsor | user</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>email</code></td><td class="p-3">string</td><td class="p-3"><span class="text-gray-400">—</span></td><td class="p-3" data-ar="بريد إلكتروني فريد" data-en="Unique email">بريد إلكتروني</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>business_name</code></td><td class="p-3">string</td><td class="p-3"><span class="text-gray-400">—</span></td><td class="p-3" data-ar="اسم المؤسسة / الشركة" data-en="Business / Company name">اسم المؤسسة</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>business_type</code></td><td class="p-3">string</td><td class="p-3"><span class="text-gray-400">—</span></td><td class="p-3" data-ar="نوع النشاط التجاري" data-en="Business type">نوع النشاط</td></tr>
                                        <tr><td class="p-3"><code>region</code></td><td class="p-3">string</td><td class="p-3"><span class="text-gray-400">—</span></td><td class="p-3" data-ar="المنطقة" data-en="Region">المنطقة</td></tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                <div class="code-block">
                                    <div class="code-header"><span>Request</span></div>
                                    <pre><code>{
  <span class="json-key">"registration_token"</span>: <span class="json-string">"xPbaVXHRV5zXi4bBJx92Q..."</span>,
  <span class="json-key">"name"</span>: <span class="json-string">"أحمد محمد"</span>,
  <span class="json-key">"user_type"</span>: <span class="json-string">"merchant"</span>,
  <span class="json-key">"email"</span>: <span class="json-string">"ahmed@example.com"</span>,
  <span class="json-key">"business_name"</span>: <span class="json-string">"مؤسسة النجاح التجارية"</span>
}</code></pre>
                                </div>
                                <div class="code-block">
                                    <div class="code-header"><span>Response</span><span class="badge bg-green-500/30 text-green-300">201</span></div>
                                    <pre><code>{
  <span class="json-key">"success"</span>: <span class="json-bool">true</span>,
  <span class="json-key">"message"</span>: <span class="json-string">"تم التسجيل بنجاح"</span>,
  <span class="json-key">"data"</span>: {
    <span class="json-key">"user"</span>: { ... },
    <span class="json-key">"token"</span>: <span class="json-string">"eyJ0eXAi..."</span>,
    <span class="json-key">"token_type"</span>: <span class="json-string">"bearer"</span>,
    <span class="json-key">"expires_in"</span>: <span class="json-number">3600</span>
  }
}</code></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- ============================================================ --}}
                {{-- OTP MANAGEMENT (Admin) --}}
                {{-- ============================================================ --}}
                <div class="bg-purple-50 border border-purple-200 rounded-xl p-5 mb-8">
                    <h3 class="text-xl font-bold text-purple-800 mb-2 flex items-center gap-2" data-ar="📱 إدارة OTP (أدمن)" data-en="📱 OTP Management (Admin)">📱 إدارة OTP</h3>
                    <p class="text-purple-700 text-sm" data-ar="إدارة مزودي خدمة OTP والرصيد والإحصائيات — يحتاج صلاحية admin أو super-admin" data-en="Manage OTP providers, balance and stats — requires admin or super-admin role">إدارة المزودين والرصيد والإحصائيات — يحتاج أدمن</p>
                </div>

                <section id="otp-balance" class="mb-16">
                    <div class="endpoint-card">
                        <div class="bg-purple-50 border-b border-purple-100 p-5 flex items-center gap-3">
                            <span class="badge method-get text-white text-xs">GET</span>
                            <code class="text-gray-800 font-semibold">/admin/otp/balance</code>
                            <span class="mr-auto text-sm text-gray-500" data-ar="رصيد المزود النشط" data-en="Active Provider Balance">رصيد المزود النشط</span>
                            <span class="badge bg-yellow-100 text-yellow-700">Admin</span>
                        </div>
                        <div class="p-5">
                            <p class="text-sm text-gray-600 mb-4" data-ar="يرجع رصيد مزود OTP النشط حالياً (مثل Authentica)" data-en="Returns the balance of the currently active OTP provider (e.g. Authentica)">يرجع رصيد مزود OTP النشط حالياً</p>
                            <div class="code-block">
                                <div class="code-header"><span>Response</span><span class="badge bg-green-500/30 text-green-300">200</span></div>
                                <pre><code>{
  <span class="json-key">"success"</span>: <span class="json-bool">true</span>,
  <span class="json-key">"data"</span>: {
    <span class="json-key">"provider"</span>: <span class="json-string">"authentica"</span>,
    <span class="json-key">"balance"</span>: <span class="json-string">"100.00"</span>
  }
}</code></pre>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="otp-providers" class="mb-16">
                    <div class="endpoint-card">
                        <div class="bg-purple-50 border-b border-purple-100 p-5 flex items-center gap-3">
                            <span class="badge method-get text-white text-xs">GET</span>
                            <code class="text-gray-800 font-semibold">/admin/otp/providers</code>
                            <span class="mr-auto text-sm text-gray-500" data-ar="حالة المزودين" data-en="Providers Status">حالة المزودين</span>
                            <span class="badge bg-yellow-100 text-yellow-700">Admin</span>
                        </div>
                        <div class="p-5">
                            <p class="text-sm text-gray-600 mb-4" data-ar="يرجع حالة كل مزودي OTP المتاحين (Authentica + Twilio) مع حالة التهيئة والمزود النشط" data-en="Returns status of all available OTP providers (Authentica + Twilio) with configuration and active provider">يرجع حالة كل المزودين</p>
                            <div class="code-block">
                                <div class="code-header"><span>Response</span><span class="badge bg-green-500/30 text-green-300">200</span></div>
                                <pre><code>{
  <span class="json-key">"success"</span>: <span class="json-bool">true</span>,
  <span class="json-key">"data"</span>: {
    <span class="json-key">"active_provider"</span>: <span class="json-string">"authentica"</span>,
    <span class="json-key">"test_mode"</span>: <span class="json-bool">true</span>,
    <span class="json-key">"providers"</span>: {
      <span class="json-key">"authentica"</span>: { <span class="json-key">"configured"</span>: <span class="json-bool">true</span>, <span class="json-key">"active"</span>: <span class="json-bool">true</span> },
      <span class="json-key">"twilio"</span>: { <span class="json-key">"configured"</span>: <span class="json-bool">false</span>, <span class="json-key">"active"</span>: <span class="json-bool">false</span> }
    }
  }
}</code></pre>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="otp-stats" class="mb-16">
                    <div class="endpoint-card">
                        <div class="bg-purple-50 border-b border-purple-100 p-5 flex items-center gap-3">
                            <span class="badge method-get text-white text-xs">GET</span>
                            <code class="text-gray-800 font-semibold">/admin/otp/stats</code>
                            <span class="mr-auto text-sm text-gray-500" data-ar="إحصائيات OTP" data-en="OTP Statistics">إحصائيات OTP</span>
                            <span class="badge bg-yellow-100 text-yellow-700">Admin</span>
                        </div>
                        <div class="p-5">
                            <p class="text-sm text-gray-600 mb-4" data-ar="إحصائيات إرسال واستخدام OTP (اليوم / هذا الأسبوع / هذا الشهر)" data-en="OTP send and usage statistics (today / this week / this month)">إحصائيات OTP اليوم والأسبوع والشهر</p>
                            <div class="code-block">
                                <div class="code-header"><span>Response</span><span class="badge bg-green-500/30 text-green-300">200</span></div>
                                <pre><code>{
  <span class="json-key">"success"</span>: <span class="json-bool">true</span>,
  <span class="json-key">"data"</span>: {
    <span class="json-key">"today"</span>: { <span class="json-key">"otp_sent"</span>: <span class="json-number">15</span>, <span class="json-key">"otp_logins"</span>: <span class="json-number">8</span>, <span class="json-key">"otp_registrations"</span>: <span class="json-number">3</span> },
    <span class="json-key">"this_week"</span>: { ... },
    <span class="json-key">"this_month"</span>: { ... },
    <span class="json-key">"active_provider"</span>: <span class="json-string">"authentica"</span>,
    <span class="json-key">"balance"</span>: { <span class="json-key">"balance"</span>: <span class="json-string">"99.00"</span> }
  }
}</code></pre>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- LOGOUT --}}
                <section id="logout" class="mb-16">
                    <div class="endpoint-card">
                        <div class="bg-blue-50 border-b border-blue-100 p-5 flex items-center gap-3">
                            <span class="badge method-post text-white text-xs">POST</span>
                            <code class="text-gray-800 font-semibold">/auth/logout</code>
                            <span class="mr-auto text-sm text-gray-500" data-ar="تسجيل الخروج" data-en="Logout">تسجيل الخروج</span>
                            <span class="badge bg-yellow-100 text-yellow-700">Auth</span>
                        </div>
                        <div class="p-5">
                            <p class="text-sm text-gray-600 mb-4" data-ar="لا يحتاج حقول. يتم حظر التوكن الحالي." data-en="No body required. Current token will be blacklisted.">لا يحتاج حقول. يتم حظر التوكن الحالي.</p>
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

                {{-- ME --}}
                <section id="me" class="mb-16">
                    <div class="endpoint-card">
                        <div class="bg-green-50 border-b border-green-100 p-5 flex items-center gap-3">
                            <span class="badge method-get text-white text-xs">GET</span>
                            <code class="text-gray-800 font-semibold">/auth/me</code>
                            <span class="mr-auto text-sm text-gray-500" data-ar="بيانات المستخدم الحالي" data-en="Current user info">بيانات المستخدم الحالي</span>
                            <span class="badge bg-yellow-100 text-yellow-700">Auth</span>
                        </div>
                        <div class="p-5">
                            <p class="text-sm text-gray-600 mb-4" data-ar="لا يحتاج حقول. يرجع بيانات المستخدم مع الأدوار والصلاحيات." data-en="No params. Returns user data with roles & permissions.">لا يحتاج حقول.</p>
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

                {{-- REFRESH --}}
                <section id="refresh" class="mb-16">
                    <div class="endpoint-card">
                        <div class="bg-blue-50 border-b border-blue-100 p-5 flex items-center gap-3">
                            <span class="badge method-post text-white text-xs">POST</span>
                            <code class="text-gray-800 font-semibold">/auth/refresh</code>
                            <span class="mr-auto text-sm text-gray-500" data-ar="تجديد التوكن" data-en="Refresh JWT token">تجديد التوكن</span>
                            <span class="badge bg-yellow-100 text-yellow-700">Auth</span>
                        </div>
                        <div class="p-5">
                            <p class="text-sm text-gray-600 mb-4" data-ar="لا يحتاج حقول. التوكن القديم يصبح غير صالح." data-en="No body required. Old token becomes invalid.">لا يحتاج حقول.</p>
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

                {{-- FORGOT PASSWORD --}}
                <section id="forgot-password" class="mb-16">
                    <div class="endpoint-card">
                        <div class="bg-blue-50 border-b border-blue-100 p-5 flex items-center gap-3">
                            <span class="badge method-post text-white text-xs">POST</span>
                            <code class="text-gray-800 font-semibold">/auth/forgot-password</code>
                            <span class="mr-auto text-sm text-gray-500" data-ar="طلب إعادة تعيين كلمة المرور" data-en="Request password reset">طلب إعادة تعيين</span>
                            <span class="badge bg-green-100 text-green-700" data-ar="عام" data-en="Public">عام</span>
                        </div>
                        <div class="p-5 space-y-6">
                            <div class="overflow-x-auto">
                                <table class="param-table w-full text-sm">
                                    <thead><tr class="border-b bg-gray-50"><th class="p-3 text-right" data-ar="الحقل" data-en="Field">الحقل</th><th class="p-3 text-right" data-ar="النوع" data-en="Type">النوع</th><th class="p-3 text-right" data-ar="مطلوب" data-en="Required">مطلوب</th><th class="p-3 text-right" data-ar="القواعد" data-en="Rules">القواعد</th></tr></thead>
                                    <tbody>
                                        <tr><td class="p-3"><code>email</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500 font-bold">✓</span></td><td class="p-3" data-ar="بريد صالح، مسجل" data-en="valid email, exists in system">بريد صالح، مسجل</td></tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-sm">
                                <span class="font-semibold text-yellow-800" data-ar="ملاحظة:" data-en="Note:">ملاحظة:</span>
                                <span class="text-yellow-700" data-ar="التوكن يُرجع فقط في بيئة التطوير (APP_DEBUG=true)" data-en="Token only returned when APP_DEBUG=true">التوكن يُرجع فقط في بيئة التطوير</span>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- RESET PASSWORD --}}
                <section id="reset-password" class="mb-16">
                    <div class="endpoint-card">
                        <div class="bg-blue-50 border-b border-blue-100 p-5 flex items-center gap-3">
                            <span class="badge method-post text-white text-xs">POST</span>
                            <code class="text-gray-800 font-semibold">/auth/reset-password</code>
                            <span class="mr-auto text-sm text-gray-500" data-ar="إعادة تعيين كلمة المرور" data-en="Reset password with token">إعادة تعيين كلمة المرور</span>
                            <span class="badge bg-green-100 text-green-700" data-ar="عام" data-en="Public">عام</span>
                        </div>
                        <div class="p-5 space-y-6">
                            <div class="overflow-x-auto">
                                <table class="param-table w-full text-sm">
                                    <thead><tr class="border-b bg-gray-50"><th class="p-3 text-right" data-ar="الحقل" data-en="Field">الحقل</th><th class="p-3 text-right" data-ar="النوع" data-en="Type">النوع</th><th class="p-3 text-right" data-ar="مطلوب" data-en="Required">مطلوب</th><th class="p-3 text-right" data-ar="القواعد" data-en="Rules">القواعد</th></tr></thead>
                                    <tbody>
                                        <tr class="border-b"><td class="p-3"><code>email</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500 font-bold">✓</span></td><td class="p-3" data-ar="بريد صالح" data-en="valid email">بريد صالح</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>token</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500 font-bold">✓</span></td><td class="p-3" data-ar="رمز إعادة التعيين (صالح 60 دقيقة)" data-en="reset token (valid 60 min)">رمز إعادة التعيين</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>password</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500 font-bold">✓</span></td><td class="p-3" data-ar="8 أحرف+" data-en="min:8, mixed case, numbers">8 أحرف+</td></tr>
                                        <tr><td class="p-3"><code>password_confirmation</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500 font-bold">✓</span></td><td class="p-3" data-ar="يطابق كلمة المرور" data-en="must match password">يطابق كلمة المرور</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- CHANGE PASSWORD --}}
                <section id="change-password" class="mb-16">
                    <div class="endpoint-card">
                        <div class="bg-blue-50 border-b border-blue-100 p-5 flex items-center gap-3">
                            <span class="badge method-post text-white text-xs">POST</span>
                            <code class="text-gray-800 font-semibold">/auth/change-password</code>
                            <span class="mr-auto text-sm text-gray-500" data-ar="تغيير كلمة المرور" data-en="Change password (logged-in)">تغيير كلمة المرور</span>
                            <span class="badge bg-yellow-100 text-yellow-700">Auth</span>
                        </div>
                        <div class="p-5">
                            <div class="overflow-x-auto">
                                <table class="param-table w-full text-sm">
                                    <thead><tr class="border-b bg-gray-50"><th class="p-3 text-right" data-ar="الحقل" data-en="Field">الحقل</th><th class="p-3 text-right" data-ar="النوع" data-en="Type">النوع</th><th class="p-3 text-right" data-ar="مطلوب" data-en="Required">مطلوب</th><th class="p-3 text-right" data-ar="القواعد" data-en="Rules">القواعد</th></tr></thead>
                                    <tbody>
                                        <tr class="border-b"><td class="p-3"><code>current_password</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500 font-bold">✓</span></td><td class="p-3" data-ar="كلمة المرور الحالية" data-en="current password">كلمة المرور الحالية</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>password</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500 font-bold">✓</span></td><td class="p-3" data-ar="8 أحرف+، مختلفة عن الحالية" data-en="min:8, different from current">8+، مختلفة عن الحالية</td></tr>
                                        <tr><td class="p-3"><code>password_confirmation</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500 font-bold">✓</span></td><td class="p-3" data-ar="يطابق كلمة المرور" data-en="must match password">يطابق كلمة المرور</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- UPDATE PROFILE --}}
                <section id="update-profile" class="mb-16">
                    <div class="endpoint-card">
                        <div class="bg-yellow-50 border-b border-yellow-100 p-5 flex items-center gap-3">
                            <span class="badge method-put text-white text-xs">PUT</span>
                            <code class="text-gray-800 font-semibold">/auth/profile</code>
                            <span class="mr-auto text-sm text-gray-500" data-ar="تحديث الملف الشخصي" data-en="Update profile">تحديث الملف الشخصي</span>
                            <span class="badge bg-yellow-100 text-yellow-700">Auth</span>
                        </div>
                        <div class="p-5 space-y-6">
                            <div class="overflow-x-auto">
                                <table class="param-table w-full text-sm">
                                    <thead><tr class="border-b bg-gray-50"><th class="p-3 text-right" data-ar="الحقل" data-en="Field">الحقل</th><th class="p-3 text-right" data-ar="النوع" data-en="Type">النوع</th><th class="p-3 text-right" data-ar="مطلوب" data-en="Required">مطلوب</th><th class="p-3 text-right" data-ar="القواعد" data-en="Rules">القواعد</th></tr></thead>
                                    <tbody>
                                        <tr class="border-b"><td class="p-3"><code>name</code></td><td class="p-3">string</td><td class="p-3"><span class="text-gray-400">—</span></td><td class="p-3">max:255</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>email</code></td><td class="p-3">string</td><td class="p-3"><span class="text-gray-400">—</span></td><td class="p-3" data-ar="بريد صالح، فريد" data-en="valid email, unique">بريد صالح، فريد</td></tr>
                                        <tr><td class="p-3"><code>phone</code></td><td class="p-3">string</td><td class="p-3"><span class="text-gray-400">—</span></td><td class="p-3">max:20</td></tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 text-sm">
                                <span class="font-semibold text-orange-800" data-ar="تنبيه:" data-en="Warning:">تنبيه:</span>
                                <span class="text-orange-700" data-ar="عند تغيير البريد سيتم إلغاء التحقق ويجب إعادة التحقق" data-en="Changing email resets verification status">عند تغيير البريد سيتم إلغاء التحقق</span>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- SEND VERIFICATION --}}
                <section id="send-verification" class="mb-16">
                    <div class="endpoint-card">
                        <div class="bg-blue-50 border-b border-blue-100 p-5 flex items-center gap-3">
                            <span class="badge method-post text-white text-xs">POST</span>
                            <code class="text-gray-800 font-semibold">/auth/email/send-verification</code>
                            <span class="mr-auto text-sm text-gray-500" data-ar="إرسال كود التحقق" data-en="Send verification code">إرسال كود التحقق</span>
                            <span class="badge bg-yellow-100 text-yellow-700">Auth</span>
                        </div>
                        <div class="p-5">
                            <p class="text-gray-600 text-sm mb-3" data-ar="لا يحتاج حقول. يرسل كود التحقق للبريد المسجل." data-en="No params. Sends verification code to registered email.">لا يحتاج حقول.</p>
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-sm">
                                <span class="font-semibold text-yellow-800" data-ar="ملاحظة:" data-en="Note:">ملاحظة:</span>
                                <span class="text-yellow-700" data-ar="الكود يُرجع فقط في بيئة التطوير. صالح لمدة 15 دقيقة." data-en="Code only returned in debug mode. Valid for 15 minutes.">الكود يُرجع فقط في بيئة التطوير. صالح 15 دقيقة.</span>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- VERIFY EMAIL --}}
                <section id="verify-email" class="mb-16">
                    <div class="endpoint-card">
                        <div class="bg-blue-50 border-b border-blue-100 p-5 flex items-center gap-3">
                            <span class="badge method-post text-white text-xs">POST</span>
                            <code class="text-gray-800 font-semibold">/auth/email/verify</code>
                            <span class="mr-auto text-sm text-gray-500" data-ar="التحقق من البريد" data-en="Verify email address">التحقق من البريد</span>
                            <span class="badge bg-yellow-100 text-yellow-700">Auth</span>
                        </div>
                        <div class="p-5">
                            <div class="overflow-x-auto">
                                <table class="param-table w-full text-sm">
                                    <thead><tr class="border-b bg-gray-50"><th class="p-3 text-right" data-ar="الحقل" data-en="Field">الحقل</th><th class="p-3 text-right" data-ar="النوع" data-en="Type">النوع</th><th class="p-3 text-right" data-ar="مطلوب" data-en="Required">مطلوب</th><th class="p-3 text-right" data-ar="القواعد" data-en="Rules">القواعد</th></tr></thead>
                                    <tbody>
                                        <tr><td class="p-3"><code>code</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500 font-bold">✓</span></td><td class="p-3" data-ar="6 أرقام" data-en="6-digit code">6 أرقام</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- ============================================================ --}}
                {{-- PHONE VERIFICATION --}}
                {{-- ============================================================ --}}
                <section id="phone-send-otp" class="mb-16">
                    <div class="endpoint-card">
                        <div class="bg-blue-50 border-b border-blue-100 p-5 flex items-center gap-3">
                            <span class="badge method-post text-white text-xs">POST</span>
                            <code class="text-gray-800 font-semibold">/auth/phone/send-otp</code>
                            <span class="mr-auto text-sm text-gray-500" data-ar="إرسال رمز تحقق الجوال" data-en="Send Phone OTP">إرسال رمز تحقق الجوال</span>
                            <span class="badge bg-yellow-100 text-yellow-700">Auth</span>
                        </div>
                        <div class="p-5 space-y-6">
                            <p class="text-gray-600 text-sm mb-1" data-ar="يرسل رمز OTP لتوثيق رقم جوال المستخدم المسجل دخوله." data-en="Sends OTP to verify the authenticated user's phone number.">يرسل رمز OTP لتوثيق رقم جوال المستخدم المسجل دخوله.</p>
                            <div class="overflow-x-auto">
                                <table class="param-table w-full text-sm">
                                    <thead><tr class="border-b bg-gray-50"><th class="p-3 text-right" data-ar="الحقل" data-en="Field">الحقل</th><th class="p-3 text-right" data-ar="النوع" data-en="Type">النوع</th><th class="p-3 text-right" data-ar="مطلوب" data-en="Required">مطلوب</th><th class="p-3 text-right" data-ar="القواعد" data-en="Rules">القواعد</th></tr></thead>
                                    <tbody>
                                        <tr class="border-b"><td class="p-3"><code>phone</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500 font-bold">✓</span></td><td class="p-3" data-ar="رقم الجوال" data-en="Phone number">رقم الجوال</td></tr>
                                        <tr><td class="p-3"><code>channel</code></td><td class="p-3">string</td><td class="p-3"><span class="text-gray-400">—</span></td><td class="p-3" data-ar="sms أو whatsapp (افتراضي: sms)" data-en="sms or whatsapp (default: sms)">sms | whatsapp</td></tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                <div class="code-block">
                                    <div class="code-header"><span>Request</span></div>
                                    <pre><code>{
  <span class="json-key">"phone"</span>: <span class="json-string">"0501234567"</span>,
  <span class="json-key">"channel"</span>: <span class="json-string">"sms"</span>
}</code></pre>
                                </div>
                                <div class="code-block">
                                    <div class="code-header"><span>Response</span><span class="badge bg-green-500/30 text-green-300">200</span></div>
                                    <pre><code>{
  <span class="json-key">"success"</span>: <span class="json-bool">true</span>,
  <span class="json-key">"message"</span>: <span class="json-string">"تم إرسال رمز التحقق"</span>,
  <span class="json-key">"data"</span>: {
    <span class="json-key">"channel"</span>: <span class="json-string">"sms"</span>
  }
}</code></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="phone-verify-otp" class="mb-16">
                    <div class="endpoint-card">
                        <div class="bg-blue-50 border-b border-blue-100 p-5 flex items-center gap-3">
                            <span class="badge method-post text-white text-xs">POST</span>
                            <code class="text-gray-800 font-semibold">/auth/phone/verify-otp</code>
                            <span class="mr-auto text-sm text-gray-500" data-ar="تأكيد رقم الجوال" data-en="Verify Phone Number">تأكيد رقم الجوال</span>
                            <span class="badge bg-yellow-100 text-yellow-700">Auth</span>
                        </div>
                        <div class="p-5 space-y-6">
                            <div class="overflow-x-auto">
                                <table class="param-table w-full text-sm">
                                    <thead><tr class="border-b bg-gray-50"><th class="p-3 text-right" data-ar="الحقل" data-en="Field">الحقل</th><th class="p-3 text-right" data-ar="النوع" data-en="Type">النوع</th><th class="p-3 text-right" data-ar="مطلوب" data-en="Required">مطلوب</th><th class="p-3 text-right" data-ar="القواعد" data-en="Rules">القواعد</th></tr></thead>
                                    <tbody>
                                        <tr class="border-b"><td class="p-3"><code>phone</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500 font-bold">✓</span></td><td class="p-3" data-ar="نفس الرقم" data-en="Same phone">نفس الرقم</td></tr>
                                        <tr><td class="p-3"><code>code</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500 font-bold">✓</span></td><td class="p-3" data-ar="رمز التحقق" data-en="OTP code">رمز التحقق</td></tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                <div class="code-block">
                                    <div class="code-header"><span>Request</span></div>
                                    <pre><code>{
  <span class="json-key">"phone"</span>: <span class="json-string">"0501234567"</span>,
  <span class="json-key">"code"</span>: <span class="json-string">"123456"</span>
}</code></pre>
                                </div>
                                <div class="code-block">
                                    <div class="code-header"><span>Response</span><span class="badge bg-green-500/30 text-green-300">200</span></div>
                                    <pre><code>{
  <span class="json-key">"success"</span>: <span class="json-bool">true</span>,
  <span class="json-key">"message"</span>: <span class="json-string">"تم توثيق رقم الجوال بنجاح"</span>
}</code></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- ============================================================ --}}
                {{-- VERIFY TOKEN --}}
                {{-- ============================================================ --}}
                <section id="verify-token" class="mb-16">
                    <div class="endpoint-card">
                        <div class="bg-blue-50 border-b border-blue-100 p-5 flex items-center gap-3">
                            <span class="badge method-post text-white text-xs">POST</span>
                            <code class="text-gray-800 font-semibold">/verify-token</code>
                            <span class="mr-auto text-sm text-gray-500" data-ar="التحقق من صلاحية التوكن" data-en="Verify token validity">التحقق من صلاحية التوكن</span>
                            <span class="badge bg-yellow-100 text-yellow-700">Auth</span>
                        </div>
                        <div class="p-5 space-y-6">
                            <div class="overflow-x-auto">
                                <table class="param-table w-full text-sm">
                                    <thead><tr class="border-b bg-gray-50"><th class="p-3 text-right" data-ar="الحقل" data-en="Field">الحقل</th><th class="p-3 text-right" data-ar="النوع" data-en="Type">النوع</th><th class="p-3 text-right" data-ar="مطلوب" data-en="Required">مطلوب</th><th class="p-3 text-right" data-ar="القواعد" data-en="Rules">القواعد</th></tr></thead>
                                    <tbody>
                                        <tr><td class="p-3"><code>token</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500 font-bold">✓</span></td><td class="p-3" data-ar="توكن JWT المراد فحصه" data-en="JWT token to validate">توكن JWT المراد فحصه</td></tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                <div class="code-block"><div class="code-header"><span>Request</span></div><pre><code>{
  <span class="json-key">"token"</span>: <span class="json-string">"eyJ0eXAi..."</span>
}</code></pre></div>
                                <div class="code-block"><div class="code-header"><span>Response</span><span class="badge bg-green-500/30 text-green-300">200</span></div><pre><code>{
  <span class="json-key">"success"</span>: <span class="json-bool">true</span>,
  <span class="json-key">"data"</span>: {
    <span class="json-key">"valid"</span>: <span class="json-bool">true</span>,
    <span class="json-key">"user"</span>: { ... },
    <span class="json-key">"expires_at"</span>: <span class="json-string">"..."</span>
  }
}</code></pre></div>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- CHECK PERMISSION --}}
                <section id="check-permission" class="mb-16">
                    <div class="endpoint-card">
                        <div class="bg-blue-50 border-b border-blue-100 p-5 flex items-center gap-3">
                            <span class="badge method-post text-white text-xs">POST</span>
                            <code class="text-gray-800 font-semibold">/check-permission</code>
                            <span class="mr-auto text-sm text-gray-500" data-ar="فحص صلاحية واحدة" data-en="Check single permission">فحص صلاحية واحدة</span>
                            <span class="badge bg-yellow-100 text-yellow-700">Auth</span>
                        </div>
                        <div class="p-5 space-y-6">
                            <div class="overflow-x-auto">
                                <table class="param-table w-full text-sm">
                                    <thead><tr class="border-b bg-gray-50"><th class="p-3 text-right" data-ar="الحقل" data-en="Field">الحقل</th><th class="p-3 text-right" data-ar="النوع" data-en="Type">النوع</th><th class="p-3 text-right" data-ar="مطلوب" data-en="Required">مطلوب</th><th class="p-3 text-right" data-ar="القواعد" data-en="Rules">القواعد</th></tr></thead>
                                    <tbody>
                                        <tr class="border-b"><td class="p-3"><code>user_id</code></td><td class="p-3">string (UUID)</td><td class="p-3"><span class="text-red-500 font-bold">✓</span></td><td class="p-3" data-ar="UUID المستخدم" data-en="User UUID">UUID المستخدم</td></tr>
                                        <tr><td class="p-3"><code>permission</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500 font-bold">✓</span></td><td class="p-3" data-ar="اسم الصلاحية مثل users.view" data-en="Permission name e.g. users.view">مثل: users.view</td></tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                <div class="code-block"><div class="code-header"><span>Request</span></div><pre><code>{
  <span class="json-key">"user_id"</span>: <span class="json-string">"uuid-here"</span>,
  <span class="json-key">"permission"</span>: <span class="json-string">"users.view"</span>
}</code></pre></div>
                                <div class="code-block"><div class="code-header"><span>Response</span><span class="badge bg-green-500/30 text-green-300">200</span></div><pre><code>{
  <span class="json-key">"success"</span>: <span class="json-bool">true</span>,
  <span class="json-key">"data"</span>: {
    <span class="json-key">"has_permission"</span>: <span class="json-bool">true</span>,
    <span class="json-key">"user_id"</span>: <span class="json-string">"uuid"</span>,
    <span class="json-key">"permission"</span>: <span class="json-string">"users.view"</span>
  }
}</code></pre></div>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- CHECK PERMISSIONS (multiple) --}}
                <section id="check-permissions" class="mb-16">
                    <div class="endpoint-card">
                        <div class="bg-blue-50 border-b border-blue-100 p-5 flex items-center gap-3">
                            <span class="badge method-post text-white text-xs">POST</span>
                            <code class="text-gray-800 font-semibold">/check-permissions</code>
                            <span class="mr-auto text-sm text-gray-500" data-ar="فحص عدة صلاحيات" data-en="Check multiple permissions">فحص عدة صلاحيات</span>
                            <span class="badge bg-yellow-100 text-yellow-700">Auth</span>
                        </div>
                        <div class="p-5 space-y-6">
                            <div class="overflow-x-auto">
                                <table class="param-table w-full text-sm">
                                    <thead><tr class="border-b bg-gray-50"><th class="p-3 text-right" data-ar="الحقل" data-en="Field">الحقل</th><th class="p-3 text-right" data-ar="النوع" data-en="Type">النوع</th><th class="p-3 text-right" data-ar="مطلوب" data-en="Required">مطلوب</th><th class="p-3 text-right" data-ar="القواعد" data-en="Rules">القواعد</th></tr></thead>
                                    <tbody>
                                        <tr class="border-b"><td class="p-3"><code>user_id</code></td><td class="p-3">string (UUID)</td><td class="p-3"><span class="text-red-500 font-bold">✓</span></td><td class="p-3" data-ar="UUID المستخدم" data-en="User UUID">UUID المستخدم</td></tr>
                                        <tr class="border-b"><td class="p-3"><code>permissions</code></td><td class="p-3">array</td><td class="p-3"><span class="text-red-500 font-bold">✓</span></td><td class="p-3" data-ar="مصفوفة أسماء الصلاحيات" data-en="Array of permission names">مصفوفة الصلاحيات</td></tr>
                                        <tr><td class="p-3"><code>require_all</code></td><td class="p-3">boolean</td><td class="p-3"><span class="text-gray-400">—</span></td><td class="p-3" data-ar="يتطلب جميعها (الافتراضي: false)" data-en="Require all (default: false)">الافتراضي: false</td></tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                <div class="code-block"><div class="code-header"><span>Request</span></div><pre><code>{
  <span class="json-key">"user_id"</span>: <span class="json-string">"uuid-here"</span>,
  <span class="json-key">"permissions"</span>: [
    <span class="json-string">"users.view"</span>,
    <span class="json-string">"users.create"</span>
  ],
  <span class="json-key">"require_all"</span>: <span class="json-bool">false</span>
}</code></pre></div>
                                <div class="code-block"><div class="code-header"><span>Response</span><span class="badge bg-green-500/30 text-green-300">200</span></div><pre><code>{
  <span class="json-key">"success"</span>: <span class="json-bool">true</span>,
  <span class="json-key">"data"</span>: {
    <span class="json-key">"has_access"</span>: <span class="json-bool">true</span>,
    <span class="json-key">"permissions"</span>: {
      <span class="json-key">"users.view"</span>: <span class="json-bool">true</span>,
      <span class="json-key">"users.create"</span>: <span class="json-bool">false</span>
    },
    <span class="json-key">"require_all"</span>: <span class="json-bool">false</span>
  }
}</code></pre></div>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- ============================================================ --}}
                {{-- USERS MANAGEMENT --}}
                {{-- ============================================================ --}}
                <section id="users" class="mb-20">
                    <h2 class="text-3xl font-bold mb-4" data-ar="إدارة المستخدمين" data-en="Users Management">إدارة المستخدمين</h2>
                    <p class="text-gray-600 mb-6" data-ar="CRUD كامل لإدارة المستخدمين - تتطلب صلاحيات users.*" data-en="Full CRUD for user management — requires users.* permissions">CRUD كامل - تتطلب صلاحيات users.*</p>

                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200 mb-8">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right" data-ar="الطريقة" data-en="Method">الطريقة</th><th class="p-3 text-right" data-ar="المسار" data-en="Endpoint">المسار</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th><th class="p-3 text-right" data-ar="الصلاحية" data-en="Permission">الصلاحية</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/users</code></td><td class="p-3" data-ar="قائمة المستخدمين" data-en="List users (paginated)">قائمة المستخدمين</td><td class="p-3"><span class="perm-badge">users.view</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/users</code></td><td class="p-3" data-ar="إنشاء مستخدم" data-en="Create user">إنشاء مستخدم</td><td class="p-3"><span class="perm-badge">users.create</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/users/{id}</code></td><td class="p-3" data-ar="عرض مستخدم" data-en="Show user">عرض مستخدم</td><td class="p-3"><span class="perm-badge">users.view</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/users/{id}</code></td><td class="p-3" data-ar="تحديث مستخدم" data-en="Update user">تحديث مستخدم</td><td class="p-3"><span class="perm-badge">users.update</span></td></tr>
                                <tr><td class="p-3"><span class="badge method-delete text-white">DEL</span></td><td class="p-3"><code>/users/{id}</code></td><td class="p-3" data-ar="حذف مستخدم" data-en="Delete user">حذف مستخدم</td><td class="p-3"><span class="perm-badge">users.delete</span></td></tr>
                            </tbody>
                        </table>
                    </div>

                    <h4 class="font-semibold mb-3 text-sm text-gray-500 uppercase tracking-wider" data-ar="حقول إنشاء / تحديث المستخدم" data-en="Create / Update User Parameters">حقول إنشاء / تحديث المستخدم</h4>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="param-table w-full text-sm">
                            <thead><tr class="border-b bg-gray-50"><th class="p-3 text-right" data-ar="الحقل" data-en="Field">الحقل</th><th class="p-3 text-right" data-ar="النوع" data-en="Type">النوع</th><th class="p-3 text-right" data-ar="إنشاء" data-en="Create">إنشاء</th><th class="p-3 text-right" data-ar="تحديث" data-en="Update">تحديث</th><th class="p-3 text-right" data-ar="القواعد" data-en="Rules">القواعد</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><code>name</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500 font-bold">✓</span></td><td class="p-3"><span class="text-gray-400">—</span></td><td class="p-3">max:255</td></tr>
                                <tr class="border-b"><td class="p-3"><code>email</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500 font-bold">✓</span></td><td class="p-3"><span class="text-gray-400">—</span></td><td class="p-3" data-ar="بريد صالح، فريد" data-en="valid email, unique">بريد صالح، فريد</td></tr>
                                <tr class="border-b"><td class="p-3"><code>password</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500 font-bold">✓</span></td><td class="p-3"><span class="text-gray-400">—</span></td><td class="p-3" data-ar="8 أحرف+، كبير وصغير وأرقام" data-en="min:8, mixed case, numbers">8 أحرف+</td></tr>
                                <tr class="border-b"><td class="p-3"><code>password_confirmation</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500 font-bold">✓</span></td><td class="p-3"><span class="text-gray-400">—</span></td><td class="p-3" data-ar="يطابق كلمة المرور" data-en="must match password">يطابق كلمة المرور</td></tr>
                                <tr class="border-b"><td class="p-3"><code>phone</code></td><td class="p-3">string</td><td class="p-3"><span class="text-gray-400">—</span></td><td class="p-3"><span class="text-gray-400">—</span></td><td class="p-3">max:20</td></tr>
                                <tr class="border-b"><td class="p-3"><code>status</code></td><td class="p-3">string</td><td class="p-3"><span class="text-gray-400">—</span></td><td class="p-3"><span class="text-gray-400">—</span></td><td class="p-3">active | suspended | blocked</td></tr>
                                <tr><td class="p-3"><code>roles</code></td><td class="p-3">array</td><td class="p-3"><span class="text-gray-400">—</span></td><td class="p-3"><span class="text-gray-400">—</span></td><td class="p-3" data-ar="مصفوفة أسماء الأدوار" data-en="Array of role names">مصفوفة أسماء الأدوار</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- ============================================================ --}}
                {{-- ROLES --}}
                {{-- ============================================================ --}}
                <section id="roles" class="mb-20">
                    <h2 class="text-3xl font-bold mb-4" data-ar="إدارة الأدوار" data-en="Roles Management">إدارة الأدوار</h2>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200 mb-8">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th><th class="p-3 text-right">Permission</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/roles</code></td><td class="p-3" data-ar="قائمة الأدوار" data-en="List roles">قائمة الأدوار</td><td class="p-3"><span class="perm-badge">roles.view</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/roles</code></td><td class="p-3" data-ar="إنشاء دور" data-en="Create role">إنشاء دور</td><td class="p-3"><span class="perm-badge">roles.create</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/roles/{id}</code></td><td class="p-3" data-ar="عرض دور" data-en="Show role">عرض دور</td><td class="p-3"><span class="perm-badge">roles.view</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/roles/{id}</code></td><td class="p-3" data-ar="تحديث دور" data-en="Update role">تحديث دور</td><td class="p-3"><span class="perm-badge">roles.update</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-delete text-white">DEL</span></td><td class="p-3"><code>/roles/{id}</code></td><td class="p-3" data-ar="حذف دور" data-en="Delete role">حذف دور</td><td class="p-3"><span class="perm-badge">roles.delete</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/roles/{id}/permissions</code></td><td class="p-3" data-ar="مزامنة الصلاحيات (استبدال)" data-en="Sync permissions (replace all)">مزامنة الصلاحيات</td><td class="p-3"><span class="perm-badge">roles.update</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/roles/{id}/permissions/add</code></td><td class="p-3" data-ar="إضافة صلاحيات" data-en="Add permissions">إضافة صلاحيات</td><td class="p-3"><span class="perm-badge">roles.update</span></td></tr>
                                <tr><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/roles/{id}/permissions/remove</code></td><td class="p-3" data-ar="إزالة صلاحيات" data-en="Remove permissions">إزالة صلاحيات</td><td class="p-3"><span class="perm-badge">roles.update</span></td></tr>
                            </tbody>
                        </table>
                    </div>

                    <h4 class="font-semibold mb-3 text-sm text-gray-500 uppercase tracking-wider" data-ar="حقول إنشاء/تحديث الدور" data-en="Create / Update Role">حقول إنشاء/تحديث الدور</h4>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200 mb-6">
                        <table class="param-table w-full text-sm">
                            <thead><tr class="border-b bg-gray-50"><th class="p-3 text-right">Field</th><th class="p-3 text-right">Type</th><th class="p-3 text-right">Create</th><th class="p-3 text-right">Update</th><th class="p-3 text-right">Rules</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><code>name</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500 font-bold">✓</span></td><td class="p-3"><span class="text-gray-400">—</span></td><td class="p-3" data-ar="فريد، max:255" data-en="unique, max:255">فريد، max:255</td></tr>
                                <tr class="border-b"><td class="p-3"><code>display_name</code></td><td class="p-3">string</td><td class="p-3"><span class="text-gray-400">—</span></td><td class="p-3"><span class="text-gray-400">—</span></td><td class="p-3">max:255</td></tr>
                                <tr><td class="p-3"><code>description</code></td><td class="p-3">string</td><td class="p-3"><span class="text-gray-400">—</span></td><td class="p-3"><span class="text-gray-400">—</span></td><td class="p-3">max:500</td></tr>
                            </tbody>
                        </table>
                    </div>

                    <h4 class="font-semibold mb-3 text-sm text-gray-500 uppercase tracking-wider" data-ar="حقول مزامنة / إضافة / إزالة الصلاحيات" data-en="Sync / Add / Remove Permissions">حقول مزامنة / إضافة / إزالة الصلاحيات</h4>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="param-table w-full text-sm">
                            <thead><tr class="border-b bg-gray-50"><th class="p-3 text-right">Field</th><th class="p-3 text-right">Type</th><th class="p-3 text-right">Required</th><th class="p-3 text-right">Rules</th></tr></thead>
                            <tbody>
                                <tr><td class="p-3"><code>permissions</code></td><td class="p-3">array</td><td class="p-3"><span class="text-red-500 font-bold">✓</span></td><td class="p-3" data-ar="مثل: [\"users.view\", \"users.create\"]" data-en="e.g. [\"users.view\", \"users.create\"]">["users.view", "users.create"]</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- ============================================================ --}}
                {{-- PERMISSIONS --}}
                {{-- ============================================================ --}}
                <section id="permissions" class="mb-20">
                    <h2 class="text-3xl font-bold mb-4" data-ar="إدارة الصلاحيات" data-en="Permissions Management">إدارة الصلاحيات</h2>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200 mb-8">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th><th class="p-3 text-right">Permission</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/permissions</code></td><td class="p-3" data-ar="قائمة الصلاحيات" data-en="List permissions">قائمة الصلاحيات</td><td class="p-3"><span class="perm-badge">permissions.view</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/permissions</code></td><td class="p-3" data-ar="إنشاء صلاحية" data-en="Create permission">إنشاء صلاحية</td><td class="p-3"><span class="perm-badge">permissions.create</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/permissions/resource</code></td><td class="p-3" data-ar="إنشاء مجموعة لمورد (CRUD)" data-en="Create resource permissions (CRUD)">إنشاء مجموعة لمورد</td><td class="p-3"><span class="perm-badge">permissions.create</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/permissions/{id}</code></td><td class="p-3" data-ar="عرض صلاحية" data-en="Show permission">عرض صلاحية</td><td class="p-3"><span class="perm-badge">permissions.view</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/permissions/{id}</code></td><td class="p-3" data-ar="تحديث صلاحية" data-en="Update permission">تحديث صلاحية</td><td class="p-3"><span class="perm-badge">permissions.update</span></td></tr>
                                <tr><td class="p-3"><span class="badge method-delete text-white">DEL</span></td><td class="p-3"><code>/permissions/{id}</code></td><td class="p-3" data-ar="حذف صلاحية" data-en="Delete permission">حذف صلاحية</td><td class="p-3"><span class="perm-badge">permissions.delete</span></td></tr>
                            </tbody>
                        </table>
                    </div>

                    <h4 class="font-semibold mb-3 text-sm text-gray-500 uppercase tracking-wider">POST /permissions</h4>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200 mb-6">
                        <table class="param-table w-full text-sm">
                            <thead><tr class="border-b bg-gray-50"><th class="p-3 text-right">Field</th><th class="p-3 text-right">Type</th><th class="p-3 text-right">Required</th><th class="p-3 text-right">Rules</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><code>name</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500 font-bold">✓</span></td><td class="p-3" data-ar="فريد مثل: reports.export" data-en="unique e.g. reports.export">فريد، مثل: reports.export</td></tr>
                                <tr class="border-b"><td class="p-3"><code>display_name</code></td><td class="p-3">string</td><td class="p-3"><span class="text-gray-400">—</span></td><td class="p-3">max:255</td></tr>
                                <tr><td class="p-3"><code>description</code></td><td class="p-3">string</td><td class="p-3"><span class="text-gray-400">—</span></td><td class="p-3">max:500</td></tr>
                            </tbody>
                        </table>
                    </div>

                    <h4 class="font-semibold mb-3 text-sm text-gray-500 uppercase tracking-wider">POST /permissions/resource</h4>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="param-table w-full text-sm">
                            <thead><tr class="border-b bg-gray-50"><th class="p-3 text-right">Field</th><th class="p-3 text-right">Type</th><th class="p-3 text-right">Required</th><th class="p-3 text-right">Rules</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><code>resource</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500 font-bold">✓</span></td><td class="p-3" data-ar="اسم المورد → ينشئ .view .create .update .delete" data-en="Resource name → creates .view .create .update .delete">اسم المورد</td></tr>
                                <tr><td class="p-3"><code>actions</code></td><td class="p-3">array</td><td class="p-3"><span class="text-gray-400">—</span></td><td class="p-3" data-ar="الافتراضي: [view, create, update, delete]" data-en="default: [view, create, update, delete]">الافتراضي: [view,create,update,delete]</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- ============================================================ --}}
                {{-- SERVICES --}}
                {{-- ============================================================ --}}
                <section id="services" class="mb-20">
                    <h2 class="text-3xl font-bold mb-4" data-ar="إدارة الخدمات" data-en="Services Management">إدارة الخدمات</h2>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200 mb-8">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th><th class="p-3 text-right">Permission</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/services</code></td><td class="p-3" data-ar="قائمة الخدمات" data-en="List services">قائمة الخدمات</td><td class="p-3"><span class="perm-badge">services.view</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/services</code></td><td class="p-3" data-ar="إنشاء خدمة" data-en="Create service">إنشاء خدمة</td><td class="p-3"><span class="perm-badge">services.create</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-get text-white">GET</span></td><td class="p-3"><code>/services/{id}</code></td><td class="p-3" data-ar="عرض خدمة" data-en="Show service">عرض خدمة</td><td class="p-3"><span class="perm-badge">services.view</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-put text-white">PUT</span></td><td class="p-3"><code>/services/{id}</code></td><td class="p-3" data-ar="تحديث خدمة" data-en="Update service">تحديث خدمة</td><td class="p-3"><span class="perm-badge">services.update</span></td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-delete text-white">DEL</span></td><td class="p-3"><code>/services/{id}</code></td><td class="p-3" data-ar="حذف خدمة" data-en="Delete service">حذف خدمة</td><td class="p-3"><span class="perm-badge">services.delete</span></td></tr>
                                <tr><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/services/{id}/regenerate-token</code></td><td class="p-3" data-ar="إعادة توليد التوكن" data-en="Regenerate token">إعادة توليد التوكن</td><td class="p-3"><span class="perm-badge">services.update</span></td></tr>
                            </tbody>
                        </table>
                    </div>

                    <h4 class="font-semibold mb-3 text-sm text-gray-500 uppercase tracking-wider" data-ar="حقول إنشاء/تحديث الخدمة" data-en="Create / Update Service">حقول إنشاء/تحديث الخدمة</h4>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="param-table w-full text-sm">
                            <thead><tr class="border-b bg-gray-50"><th class="p-3 text-right">Field</th><th class="p-3 text-right">Type</th><th class="p-3 text-right">Create</th><th class="p-3 text-right">Update</th><th class="p-3 text-right">Rules</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><code>name</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500 font-bold">✓</span></td><td class="p-3"><span class="text-gray-400">—</span></td><td class="p-3" data-ar="فريد، max:255" data-en="unique, max:255">فريد، max:255</td></tr>
                                <tr class="border-b"><td class="p-3"><code>base_url</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500 font-bold">✓</span></td><td class="p-3"><span class="text-gray-400">—</span></td><td class="p-3" data-ar="URL صالح" data-en="valid URL">URL صالح</td></tr>
                                <tr class="border-b"><td class="p-3"><code>description</code></td><td class="p-3">string</td><td class="p-3"><span class="text-gray-400">—</span></td><td class="p-3"><span class="text-gray-400">—</span></td><td class="p-3">max:500</td></tr>
                                <tr><td class="p-3"><code>is_active</code></td><td class="p-3">boolean</td><td class="p-3"><span class="text-gray-400">—</span></td><td class="p-3"><span class="text-gray-400">—</span></td><td class="p-3" data-ar="الافتراضي: true" data-en="default: true">الافتراضي: true</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- ============================================================ --}}
                {{-- S2S --}}
                {{-- ============================================================ --}}
                <section id="s2s" class="mb-20">
                    <h2 class="text-3xl font-bold mb-4" data-ar="تواصل بين الخدمات (S2S)" data-en="Service-to-Service (S2S)">تواصل بين الخدمات (S2S)</h2>
                    <div class="bg-purple-50 border border-purple-200 rounded-xl p-5 mb-6">
                        <h4 class="font-bold text-purple-800 mb-2" data-ar="محمية عبر الشبكة الداخلية فقط" data-en="Protected via internal Docker network only">محمية عبر الشبكة الداخلية فقط</h4>
                        <code class="text-purple-600">No authentication required — internal Docker network</code>
                    </div>

                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200 mb-8">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Method</th><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/service/verify-token</code></td><td class="p-3" data-ar="التحقق من توكن مستخدم" data-en="Verify user JWT token">التحقق من توكن مستخدم</td></tr>
                                <tr class="border-b"><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/service/check-permission</code></td><td class="p-3" data-ar="فحص صلاحية مستخدم" data-en="Check user permission">فحص صلاحية مستخدم</td></tr>
                                <tr><td class="p-3"><span class="badge method-post text-white">POST</span></td><td class="p-3"><code>/service/user-info</code></td><td class="p-3" data-ar="جلب بيانات مستخدم" data-en="Get user info by ID">جلب بيانات مستخدم</td></tr>
                            </tbody>
                        </table>
                    </div>

                    <h4 class="font-semibold mb-3 text-sm text-gray-500 uppercase tracking-wider" data-ar="حقول S2S" data-en="S2S Parameters">حقول S2S</h4>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="param-table w-full text-sm">
                            <thead><tr class="border-b bg-gray-50"><th class="p-3 text-right">Endpoint</th><th class="p-3 text-right">Field</th><th class="p-3 text-right">Type</th><th class="p-3 text-right">Required</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3"><code>/service/verify-token</code></td><td class="p-3"><code>token</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500 font-bold">✓</span></td><td class="p-3" data-ar="توكن JWT" data-en="JWT token to verify">توكن JWT</td></tr>
                                <tr class="border-b"><td class="p-3" rowspan="2"><code>/service/check-permission</code></td><td class="p-3"><code>user_id</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500 font-bold">✓</span></td><td class="p-3">UUID</td></tr>
                                <tr class="border-b"><td class="p-3"><code>permission</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500 font-bold">✓</span></td><td class="p-3" data-ar="اسم الصلاحية" data-en="Permission name">اسم الصلاحية</td></tr>
                                <tr><td class="p-3"><code>/service/user-info</code></td><td class="p-3"><code>user_id</code></td><td class="p-3">string</td><td class="p-3"><span class="text-red-500 font-bold">✓</span></td><td class="p-3">UUID</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- ============================================================ --}}
                {{-- HTTP STATUS CODES --}}
                {{-- ============================================================ --}}
                <section id="errors" class="mb-20">
                    <h2 class="text-3xl font-bold mb-4" data-ar="رموز الحالة HTTP" data-en="HTTP Status Codes">رموز الحالة HTTP</h2>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-4 text-right" data-ar="الرمز" data-en="Code">الرمز</th><th class="p-4 text-right">Name</th><th class="p-4 text-right" data-ar="الوصف" data-en="Description">الوصف</th></tr></thead>
                            <tbody>
                                <tr class="border-b bg-green-50/50"><td colspan="3" class="p-2 font-bold text-green-800 text-xs uppercase tracking-wider" data-ar="نجاح" data-en="Success">نجاح</td></tr>
                                <tr class="border-b"><td class="p-4"><span class="bg-green-100 text-green-700 px-2 py-1 rounded font-mono">200</span></td><td class="p-4">OK</td><td class="p-4 text-gray-600" data-ar="تمت العملية بنجاح" data-en="Request succeeded">تمت العملية بنجاح</td></tr>
                                <tr class="border-b"><td class="p-4"><span class="bg-green-100 text-green-700 px-2 py-1 rounded font-mono">201</span></td><td class="p-4">Created</td><td class="p-4 text-gray-600" data-ar="تم إنشاء المورد" data-en="Resource created">تم إنشاء المورد</td></tr>
                                <tr class="border-b bg-yellow-50/50"><td colspan="3" class="p-2 font-bold text-yellow-800 text-xs uppercase tracking-wider" data-ar="أخطاء العميل" data-en="Client Errors">أخطاء العميل</td></tr>
                                <tr class="border-b"><td class="p-4"><span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded font-mono">400</span></td><td class="p-4">Bad Request</td><td class="p-4 text-gray-600" data-ar="خطأ في التحقق" data-en="Validation error">خطأ في التحقق</td></tr>
                                <tr class="border-b"><td class="p-4"><span class="bg-red-100 text-red-700 px-2 py-1 rounded font-mono">401</span></td><td class="p-4">Unauthorized</td><td class="p-4 text-gray-600" data-ar="غير مصرح" data-en="Not authenticated">غير مصرح</td></tr>
                                <tr class="border-b"><td class="p-4"><span class="bg-red-100 text-red-700 px-2 py-1 rounded font-mono">403</span></td><td class="p-4">Forbidden</td><td class="p-4 text-gray-600" data-ar="ليس لديك صلاحية" data-en="No permission">ليس لديك صلاحية</td></tr>
                                <tr class="border-b"><td class="p-4"><span class="bg-orange-100 text-orange-700 px-2 py-1 rounded font-mono">404</span></td><td class="p-4">Not Found</td><td class="p-4 text-gray-600" data-ar="غير موجود" data-en="Not found">غير موجود</td></tr>
                                <tr class="border-b"><td class="p-4"><span class="bg-orange-100 text-orange-700 px-2 py-1 rounded font-mono">422</span></td><td class="p-4">Unprocessable</td><td class="p-4 text-gray-600" data-ar="لا يمكن معالجتها" data-en="Cannot be processed">لا يمكن معالجتها</td></tr>
                                <tr class="border-b"><td class="p-4"><span class="bg-purple-100 text-purple-700 px-2 py-1 rounded font-mono">429</span></td><td class="p-4">Too Many</td><td class="p-4 text-gray-600" data-ar="تجاوز حد الطلبات" data-en="Rate limit exceeded">تجاوز حد الطلبات</td></tr>
                                <tr class="border-b bg-red-50/50"><td colspan="3" class="p-2 font-bold text-red-800 text-xs uppercase tracking-wider" data-ar="أخطاء الخادم" data-en="Server Errors">أخطاء الخادم</td></tr>
                                <tr><td class="p-4"><span class="bg-red-100 text-red-700 px-2 py-1 rounded font-mono">500</span></td><td class="p-4">Server Error</td><td class="p-4 text-gray-600" data-ar="خطأ في الخادم" data-en="Internal server error">خطأ في الخادم</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- ERROR CODES --}}
                <section id="error-codes" class="mb-20">
                    <h2 class="text-3xl font-bold mb-6" data-ar="جدول رموز الأخطاء" data-en="Error Codes Reference">جدول رموز الأخطاء</h2>
                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right">Error Code</th><th class="p-3 text-right">HTTP</th><th class="p-3 text-right" data-ar="الوصف" data-en="Description">الوصف</th></tr></thead>
                            <tbody>
                                <tr class="border-b bg-red-50/50"><td colspan="3" class="p-2 font-bold text-red-800 text-xs uppercase tracking-wider">Authentication</td></tr>
                                <tr class="border-b"><td class="p-3"><code>authentication_required</code></td><td class="p-3">401</td><td class="p-3" data-ar="غير مصرح" data-en="Unauthenticated">Unauthenticated</td></tr>
                                <tr class="border-b"><td class="p-3"><code>token_expired</code></td><td class="p-3">401</td><td class="p-3" data-ar="انتهت صلاحية التوكن" data-en="Token expired">Token expired</td></tr>
                                <tr class="border-b"><td class="p-3"><code>token_invalid</code></td><td class="p-3">401</td><td class="p-3" data-ar="التوكن غير صالح" data-en="Token invalid">Token invalid</td></tr>
                                <tr class="border-b"><td class="p-3"><code>token_blacklisted</code></td><td class="p-3">401</td><td class="p-3" data-ar="التوكن محظور" data-en="Token blacklisted">Token blacklisted</td></tr>
                                <tr class="border-b"><td class="p-3"><code>invalid_login_credentials</code></td><td class="p-3">401</td><td class="p-3" data-ar="بيانات دخول خاطئة" data-en="Invalid credentials">Invalid credentials</td></tr>
                                <tr class="border-b bg-orange-50/50"><td colspan="3" class="p-2 font-bold text-orange-800 text-xs uppercase tracking-wider">Authorization</td></tr>
                                <tr class="border-b"><td class="p-3"><code>permission_denied</code></td><td class="p-3">403</td><td class="p-3" data-ar="ليس لديك صلاحية" data-en="Permission denied">Permission denied</td></tr>
                                <tr class="border-b"><td class="p-3"><code>user_blocked</code></td><td class="p-3">403</td><td class="p-3" data-ar="الحساب محظور" data-en="Account blocked">Account blocked</td></tr>
                                <tr class="border-b"><td class="p-3"><code>user_suspended</code></td><td class="p-3">403</td><td class="p-3" data-ar="الحساب موقوف" data-en="Account suspended">Account suspended</td></tr>
                                <tr class="border-b bg-yellow-50/50"><td colspan="3" class="p-2 font-bold text-yellow-800 text-xs uppercase tracking-wider">Validation</td></tr>
                                <tr class="border-b"><td class="p-3"><code>validation_failed</code></td><td class="p-3">400</td><td class="p-3" data-ar="فشل التحقق" data-en="Validation failed">Validation failed</td></tr>
                                <tr class="border-b"><td class="p-3"><code>resource_not_found</code></td><td class="p-3">404</td><td class="p-3" data-ar="غير موجود" data-en="Not found">Resource not found</td></tr>
                                <tr><td class="p-3"><code>rate_limit_exceeded</code></td><td class="p-3">429</td><td class="p-3" data-ar="تجاوز حد الطلبات" data-en="Too many requests">Too many requests</td></tr>
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
                                <tr class="border-b"><td class="p-4">API Requests</td><td class="p-4">{{ config('auth-service.rate_limit.api_per_minute', 60) }}</td><td class="p-4" data-ar="في الدقيقة" data-en="per minute">في الدقيقة</td></tr>
                                <tr class="border-b"><td class="p-4">Login Attempts</td><td class="p-4">{{ config('auth-service.rate_limit.login_attempts', 5) }}</td><td class="p-4" data-ar="في الدقيقة" data-en="per minute">في الدقيقة</td></tr>
                                <tr><td class="p-4">Service-to-Service</td><td class="p-4">100</td><td class="p-4" data-ar="في الدقيقة" data-en="per minute">في الدقيقة</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- DEFAULT ROLES --}}
                <section id="default-roles" class="mb-20">
                    <h2 class="text-3xl font-bold mb-6" data-ar="الأدوار والصلاحيات الافتراضية" data-en="Default Roles & Permissions">الأدوار والصلاحيات الافتراضية</h2>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-8">
                        @php $defaultRoles = [
                            ['name' => 'super-admin', 'color' => 'red', 'ar' => 'وصول كامل', 'en' => 'Full access'],
                            ['name' => 'admin', 'color' => 'blue', 'ar' => 'إدارة كاملة', 'en' => 'Administrative'],
                            ['name' => 'supervisor', 'color' => 'indigo', 'ar' => 'مراقبة وموافقات', 'en' => 'Review & approve'],
                            ['name' => 'investor', 'color' => 'emerald', 'ar' => 'إدارة المساحات', 'en' => 'Space management'],
                            ['name' => 'merchant', 'color' => 'amber', 'ar' => 'طلبات إيجار وزيارة', 'en' => 'Rental & visit'],
                            ['name' => 'sponsor', 'color' => 'purple', 'ar' => 'إدارة الرعاية', 'en' => 'Sponsorship'],
                            ['name' => 'user', 'color' => 'gray', 'ar' => 'مستخدم عادي', 'en' => 'Basic user'],
                        ]; @endphp
                        @foreach($defaultRoles as $r)
                        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
                            <div class="flex items-center justify-center gap-2 mb-2">
                                <span class="w-3 h-3 bg-{{ $r['color'] }}-500 rounded-full"></span>
                                <h4 class="font-bold text-sm">{{ $r['name'] }}</h4>
                            </div>
                            <p class="text-xs text-gray-500" data-ar="{{ $r['ar'] }}" data-en="{{ $r['en'] }}">{{ $r['ar'] }}</p>
                        </div>
                        @endforeach
                    </div>

                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-sm">
                            <thead><tr class="bg-gray-50 border-b"><th class="p-3 text-right" data-ar="المورد" data-en="Resource">المورد</th><th class="p-3 text-right" data-ar="الصلاحيات" data-en="Permissions">الصلاحيات</th></tr></thead>
                            <tbody>
                                <tr class="border-b"><td class="p-3 font-semibold">Users</td><td class="p-3"><code class="bg-gray-100 px-2 py-0.5 rounded text-xs">users.view</code> <code class="bg-gray-100 px-2 py-0.5 rounded text-xs">users.create</code> <code class="bg-gray-100 px-2 py-0.5 rounded text-xs">users.update</code> <code class="bg-gray-100 px-2 py-0.5 rounded text-xs">users.delete</code></td></tr>
                                <tr class="border-b"><td class="p-3 font-semibold">Roles</td><td class="p-3"><code class="bg-gray-100 px-2 py-0.5 rounded text-xs">roles.view</code> <code class="bg-gray-100 px-2 py-0.5 rounded text-xs">roles.create</code> <code class="bg-gray-100 px-2 py-0.5 rounded text-xs">roles.update</code> <code class="bg-gray-100 px-2 py-0.5 rounded text-xs">roles.delete</code></td></tr>
                                <tr class="border-b"><td class="p-3 font-semibold">Permissions</td><td class="p-3"><code class="bg-gray-100 px-2 py-0.5 rounded text-xs">permissions.view</code> <code class="bg-gray-100 px-2 py-0.5 rounded text-xs">permissions.create</code> <code class="bg-gray-100 px-2 py-0.5 rounded text-xs">permissions.update</code> <code class="bg-gray-100 px-2 py-0.5 rounded text-xs">permissions.delete</code></td></tr>
                                <tr><td class="p-3 font-semibold">Services</td><td class="p-3"><code class="bg-gray-100 px-2 py-0.5 rounded text-xs">services.view</code> <code class="bg-gray-100 px-2 py-0.5 rounded text-xs">services.create</code> <code class="bg-gray-100 px-2 py-0.5 rounded text-xs">services.update</code> <code class="bg-gray-100 px-2 py-0.5 rounded text-xs">services.delete</code></td></tr>
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
