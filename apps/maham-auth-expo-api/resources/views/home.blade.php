<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maham Auth Service - API Documentation</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
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
        code, .font-mono { font-family: 'JetBrains Mono', monospace; }
        .gradient-bg { background: linear-gradient(145deg, #020617 0%, #0f172a 40%, #1e1b4b 70%, #0f172a 100%); }
        .card-hover { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .card-hover:hover { transform: translateY(-3px); box-shadow: 0 20px 40px rgba(0,0,0,0.3); border-color: rgba(99, 102, 241, 0.3); }
        .pulse-dot { animation: pulse-dot 2s ease-in-out infinite; }
        @keyframes pulse-dot { 0%, 100% { opacity: 1; transform: scale(1); } 50% { opacity: 0.4; transform: scale(0.8); } }
        .glow-blue { box-shadow: 0 0 30px rgba(99, 102, 241, 0.4), 0 0 60px rgba(99, 102, 241, 0.1); }
        .glow-line { background: linear-gradient(90deg, transparent, rgba(99, 102, 241, 0.3), transparent); height: 1px; }
        .code-block { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); }
        .shimmer { background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.03) 50%, transparent 100%); background-size: 200% 100%; animation: shimmer 3s ease-in-out infinite; }
        @keyframes shimmer { 0% { background-position: -200% 0; } 100% { background-position: 200% 0; } }
        .nav-link { position: relative; }
        .nav-link::after { content: ''; position: absolute; bottom: -2px; right: 0; width: 0; height: 2px; background: #6366f1; transition: width 0.3s ease; }
        .nav-link:hover::after, .nav-link.active::after { width: 100%; }
        .endpoint-row { transition: all 0.2s ease; }
        .endpoint-row:hover { background: rgba(255,255,255,0.03); }
        .method-badge { font-size: 10px; letter-spacing: 0.5px; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        .glass { background: rgba(255,255,255,0.03); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); }
        .tab-btn.active { background: rgba(99, 102, 241, 0.2); color: #a5b4fc; border-color: rgba(99, 102, 241, 0.4); }
        .section-anchor { scroll-margin-top: 80px; }
        pre code .key { color: #c4b5fd; }
        pre code .str { color: #86efac; }
        pre code .num { color: #fbbf24; }
        pre code .comment { color: #64748b; }
        pre code .cmd { color: #fbbf24; }
        pre code .url { color: #67e8f9; }
        pre code .flag { color: #c4b5fd; }
    </style>
</head>
<body class="gradient-bg min-h-screen text-white antialiased">

    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 border-b border-white/5 glass">
        <div class="max-w-7xl mx-auto px-6 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-indigo-500 rounded-xl flex items-center justify-center glow-blue">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-sm font-bold tracking-tight">Maham Auth</h1>
                    <p class="text-[10px] text-gray-500">API Documentation</p>
                </div>
            </div>
            <div class="hidden md:flex items-center gap-6 text-sm">
                <a href="#features" class="nav-link text-gray-400 hover:text-white transition">الميزات</a>
                <a href="#auth-api" class="nav-link text-gray-400 hover:text-white transition">المصادقة</a>
                <a href="#users-api" class="nav-link text-gray-400 hover:text-white transition">المستخدمين</a>
                <a href="#roles-api" class="nav-link text-gray-400 hover:text-white transition">الأدوار</a>
                <a href="#services-api" class="nav-link text-gray-400 hover:text-white transition">الخدمات</a>
                <a href="#quickstart" class="nav-link text-gray-400 hover:text-white transition">البدء السريع</a>
                <a href="#env-vars" class="nav-link text-gray-400 hover:text-white transition">المتغيرات</a>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-[10px] bg-indigo-500/20 text-indigo-400 px-2.5 py-1 rounded-full font-semibold border border-indigo-500/20">v{{ config('auth-service.service_version', '1.0.0') }}</span>
                <span id="healthStatus" class="text-[10px] bg-gray-800/80 text-gray-500 px-2.5 py-1 rounded-full flex items-center gap-1.5 border border-white/5">
                    <span class="w-1.5 h-1.5 bg-gray-600 rounded-full"></span>
                    checking...
                </span>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section class="pt-28 pb-16 relative overflow-hidden">
        <div class="absolute inset-0 opacity-30">
            <div class="absolute top-20 left-1/4 w-72 h-72 bg-indigo-500/20 rounded-full blur-[120px]"></div>
            <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-purple-500/10 rounded-full blur-[150px]"></div>
        </div>
        <div class="max-w-7xl mx-auto px-6 text-center relative z-10">
            <div class="inline-flex items-center gap-2 bg-white/5 border border-white/10 rounded-full px-4 py-1.5 mb-8">
                <span class="w-2 h-2 bg-emerald-400 rounded-full pulse-dot"></span>
                <span class="text-xs text-gray-400">نظام المصادقة يعمل بنجاح</span>
            </div>
            <h2 class="text-5xl md:text-6xl font-black mb-6 leading-tight">
                <span class="bg-gradient-to-l from-indigo-400 via-purple-400 to-pink-400 bg-clip-text text-transparent">خدمة المصادقة</span>
                <br>
                <span class="text-white/90">المركزية</span>
            </h2>
            <p class="text-gray-400 text-lg max-w-2xl mx-auto mb-10 leading-relaxed">
                نظام مصادقة وإدارة صلاحيات متكامل يعتمد على <span class="text-indigo-400 font-semibold">JWT</span> مع دعم الأدوار والخدمات المتعددة وتواصل آمن بين الـ Microservices
            </p>
            <div class="flex items-center justify-center gap-4 flex-wrap">
                <a href="#auth-api" class="bg-indigo-500 hover:bg-indigo-600 text-white px-7 py-3 rounded-xl font-bold transition-all hover:shadow-lg hover:shadow-indigo-500/25 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    تصفح الـ API
                </a>
                <a href="/api/health" target="_blank" class="bg-white/5 hover:bg-white/10 text-white px-7 py-3 rounded-xl font-bold transition-all border border-white/10 hover:border-white/20 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Health Check
                </a>
                <a href="https://expo-service-api.mahamexpo.sa/docs" class="bg-white/5 hover:bg-white/10 text-white px-7 py-3 rounded-xl font-bold transition-all border border-white/10 hover:border-white/20 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
                    Expo API
                </a>
                <a href="https://dashboard.mahamexpo.sa" class="bg-white/5 hover:bg-white/10 text-white px-7 py-3 rounded-xl font-bold transition-all border border-white/10 hover:border-white/20 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6z"/></svg>
                    لوحة التحكم
                </a>
            </div>
        </div>
    </section>

    <div class="glow-line max-w-4xl mx-auto"></div>

    <!-- Stats -->
    <section class="max-w-7xl mx-auto px-6 py-12">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="glass border border-white/5 rounded-2xl p-5 text-center">
                <div class="text-3xl font-black text-indigo-400 mb-1">JWT</div>
                <div class="text-xs text-gray-500">Token Authentication</div>
            </div>
            <div class="glass border border-white/5 rounded-2xl p-5 text-center">
                <div class="text-3xl font-black text-emerald-400 mb-1">RBAC</div>
                <div class="text-xs text-gray-500">Role-Based Access</div>
            </div>
            <div class="glass border border-white/5 rounded-2xl p-5 text-center">
                <div class="text-3xl font-black text-purple-400 mb-1">S2S</div>
                <div class="text-xs text-gray-500">Service Communication</div>
            </div>
            <div class="glass border border-white/5 rounded-2xl p-5 text-center">
                <div class="text-3xl font-black text-amber-400 mb-1">Redis</div>
                <div class="text-xs text-gray-500">Caching Layer</div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section id="features" class="section-anchor max-w-7xl mx-auto px-6 pb-16">
        <div class="flex items-center gap-3 mb-8">
            <div class="w-1 h-8 bg-indigo-500 rounded-full"></div>
            <h3 class="text-2xl font-bold">الميزات الرئيسية</h3>
        </div>
        <div class="grid md:grid-cols-3 gap-4">
            @php
                $features = [
                    ['JWT Authentication', 'مصادقة بتوكنات JWT مع دعم التجديد والإلغاء. صلاحية التوكن ' . config('auth-service.jwt.ttl', 60) . ' دقيقة.', 'indigo', 'M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z'],
                    ['Roles & Permissions', 'نظام أدوار وصلاحيات متعدد المستويات مع دعم الرفض الصريح والصلاحيات المؤقتة.', 'emerald', 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
                    ['Service-to-Service', 'تواصل آمن بين الخدمات عبر توكنات مخصصة مع التحقق من IP وتسجيل الأنشطة.', 'purple', 'M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                    ['Service Roles', 'تحديد أدوار لكل خدمة للتحكم بمن يقدر يسجل دخول. إضافة وتعديل الأدوار ديناميكياً.', 'amber', 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10'],
                    ['Audit Trail', 'تسجيل شامل لجميع الأحداث: تسجيل دخول، تغييرات الأدوار، تعديلات الصلاحيات مع تتبع IP.', 'rose', 'M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'],
                    ['Redis Caching', 'كاش ذكي للصلاحيات والأدوار لأداء سريع مع TTL قابل للتخصيص وتنظيف تلقائي.', 'cyan', 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4'],
                ];
            @endphp
            @foreach($features as $f)
            <div class="glass border border-white/5 rounded-2xl p-6 card-hover group">
                <div class="w-11 h-11 bg-{{ $f[2] }}-500/10 rounded-xl flex items-center justify-center mb-4 group-hover:bg-{{ $f[2] }}-500/20 transition">
                    <svg class="w-5 h-5 text-{{ $f[2] }}-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $f[3] }}"/></svg>
                </div>
                <h4 class="font-bold mb-2 text-white/90">{{ $f[0] }}</h4>
                <p class="text-sm text-gray-500 leading-relaxed">{{ $f[1] }}</p>
            </div>
            @endforeach
        </div>
    </section>

    <div class="glow-line max-w-4xl mx-auto"></div>

    <!-- ==================== API DOCUMENTATION ==================== -->

    <!-- Auth Endpoints -->
    <section id="auth-api" class="section-anchor max-w-7xl mx-auto px-6 py-16">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-1 h-8 bg-blue-500 rounded-full"></div>
            <h3 class="text-2xl font-bold">المصادقة</h3>
            <span class="text-xs bg-blue-500/10 text-blue-400 px-2.5 py-1 rounded-full">Authentication</span>
        </div>
        <p class="text-gray-500 text-sm mb-8 mr-4">تسجيل الدخول، التسجيل، إدارة الجلسات والتوكنات</p>

        <!-- POST /api/v1/auth/register -->
        <div class="mb-8 glass border border-white/5 rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-white/5 flex items-center gap-3">
                <span class="method-badge font-bold px-2.5 py-1 rounded-md bg-blue-500/20 text-blue-400 font-mono">POST</span>
                <code class="text-sm text-gray-300 font-mono" dir="ltr">/api/v1/auth/register</code>
                <span class="text-xs text-gray-600 mr-auto">تسجيل مستخدم جديد</span>
                <span class="text-[10px] px-2 py-0.5 rounded-full bg-green-500/10 text-green-400 font-semibold">public</span>
            </div>
            <div class="grid md:grid-cols-2 divide-y md:divide-y-0 md:divide-x md:divide-x-reverse divide-white/5">
                <div class="p-6">
                    <h5 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Request Body</h5>
                    <div class="space-y-3">
                        <div class="flex items-start gap-2">
                            <code class="text-xs bg-white/5 px-2 py-0.5 rounded text-indigo-300 font-mono">name</code>
                            <span class="text-[10px] text-rose-400 font-bold">required</span>
                            <span class="text-xs text-gray-500">string - اسم المستخدم</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <code class="text-xs bg-white/5 px-2 py-0.5 rounded text-indigo-300 font-mono">email</code>
                            <span class="text-[10px] text-rose-400 font-bold">required</span>
                            <span class="text-xs text-gray-500">email - البريد الإلكتروني (فريد)</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <code class="text-xs bg-white/5 px-2 py-0.5 rounded text-indigo-300 font-mono">password</code>
                            <span class="text-[10px] text-rose-400 font-bold">required</span>
                            <span class="text-xs text-gray-500">string - كلمة المرور (min:8، أحرف كبيرة وصغيرة وأرقام)</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <code class="text-xs bg-white/5 px-2 py-0.5 rounded text-indigo-300 font-mono">password_confirmation</code>
                            <span class="text-[10px] text-rose-400 font-bold">required</span>
                            <span class="text-xs text-gray-500">string - تأكيد كلمة المرور</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <code class="text-xs bg-white/5 px-2 py-0.5 rounded text-indigo-300 font-mono">phone</code>
                            <span class="text-[10px] text-rose-400 font-bold">required</span>
                            <span class="text-xs text-gray-500">string - رقم الهاتف (فريد)</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <code class="text-xs bg-white/5 px-2 py-0.5 rounded text-indigo-300 font-mono">roles</code>
                            <span class="text-[10px] text-gray-500 font-bold">optional</span>
                            <span class="text-xs text-gray-500">array - أسماء الأدوار</span>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <h5 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Response <span class="text-emerald-400">201</span></h5>
                    <div class="code-block rounded-xl p-4 text-xs overflow-x-auto" dir="ltr">
<pre class="text-gray-300"><code>{
  <span class="key">"success"</span>: <span class="text-emerald-400">true</span>,
  <span class="key">"message"</span>: <span class="str">"تم التسجيل بنجاح"</span>,
  <span class="key">"data"</span>: {
    <span class="key">"user"</span>: {
      <span class="key">"id"</span>: <span class="str">"uuid"</span>,
      <span class="key">"name"</span>: <span class="str">"Ahmed"</span>,
      <span class="key">"email"</span>: <span class="str">"ahmed@example.com"</span>,
      <span class="key">"roles"</span>: [<span class="str">"user"</span>]
    },
    <span class="key">"token"</span>: <span class="str">"eyJ0eXAiOiJKV1Qi..."</span>
  }
}</code></pre>
                    </div>
                </div>
            </div>
        </div>

        <!-- POST /api/v1/auth/login -->
        <div class="mb-8 glass border border-white/5 rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-white/5 flex items-center gap-3">
                <span class="method-badge font-bold px-2.5 py-1 rounded-md bg-blue-500/20 text-blue-400 font-mono">POST</span>
                <code class="text-sm text-gray-300 font-mono" dir="ltr">/api/v1/auth/login</code>
                <span class="text-xs text-gray-600 mr-auto">تسجيل الدخول</span>
                <span class="text-[10px] px-2 py-0.5 rounded-full bg-green-500/10 text-green-400 font-semibold">public</span>
            </div>
            <div class="grid md:grid-cols-2 divide-y md:divide-y-0 md:divide-x md:divide-x-reverse divide-white/5">
                <div class="p-6">
                    <h5 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Request Body</h5>
                    <div class="space-y-3">
                        <div class="flex items-start gap-2">
                            <code class="text-xs bg-white/5 px-2 py-0.5 rounded text-indigo-300 font-mono">identifier</code>
                            <span class="text-[10px] text-rose-400 font-bold">required</span>
                            <span class="text-xs text-gray-500">string - البريد أو رقم الهاتف</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <code class="text-xs bg-white/5 px-2 py-0.5 rounded text-indigo-300 font-mono">password</code>
                            <span class="text-[10px] text-rose-400 font-bold">required</span>
                            <span class="text-xs text-gray-500">string - كلمة المرور</span>
                        </div>
                    </div>
                    <div class="mt-4 p-3 bg-amber-500/5 border border-amber-500/10 rounded-lg">
                        <p class="text-xs text-amber-400/80"><span class="font-bold">ملاحظة:</span> إذا الخدمة عندها أدوار محددة، لازم يكون عند المستخدم واحد من هالأدوار عشان يقدر يسجل دخول.</p>
                    </div>
                </div>
                <div class="p-6">
                    <h5 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Response <span class="text-emerald-400">200</span></h5>
                    <div class="code-block rounded-xl p-4 text-xs overflow-x-auto" dir="ltr">
<pre class="text-gray-300"><code>{
  <span class="key">"success"</span>: <span class="text-emerald-400">true</span>,
  <span class="key">"message"</span>: <span class="str">"تم تسجيل الدخول بنجاح"</span>,
  <span class="key">"data"</span>: {
    <span class="key">"user"</span>: {
      <span class="key">"id"</span>: <span class="str">"uuid"</span>,
      <span class="key">"name"</span>: <span class="str">"Ahmed"</span>,
      <span class="key">"email"</span>: <span class="str">"ahmed@example.com"</span>,
      <span class="key">"roles"</span>: [<span class="str">"admin"</span>],
      <span class="key">"permissions"</span>: [...]
    },
    <span class="key">"token"</span>: <span class="str">"eyJ0eXAiOiJKV1Qi..."</span>
  }
}</code></pre>
                    </div>
                </div>
            </div>
        </div>

        <!-- GET /api/v1/auth/me + POST /api/v1/auth/refresh + POST /api/v1/auth/logout -->

        <!-- OTP Login Flow -->
        <div class="mb-8 glass border border-indigo-500/10 rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-white/5 bg-emerald-500/5">
                <h4 class="font-bold text-emerald-400 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    تسجيل الدخول بالجوال + رمز OTP
                </h4>
                <p class="text-xs text-gray-500 mt-1">الطريقة الرئيسية لتسجيل الدخول عبر التطبيق — SMS أو WhatsApp</p>
            </div>
            <div class="p-6">
                <div class="grid md:grid-cols-3 gap-4 mb-6">
                    <!-- Step 1: Send OTP -->
                    <div class="glass border border-white/5 rounded-xl p-4">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="w-6 h-6 bg-emerald-500/20 rounded-full flex items-center justify-center text-xs text-emerald-400 font-bold">1</span>
                            <span class="method-badge font-bold px-2 py-0.5 rounded bg-blue-500/20 text-blue-400 font-mono">POST</span>
                        </div>
                        <code class="text-xs text-gray-300 font-mono block mb-2" dir="ltr">/api/v1/auth/otp/send</code>
                        <div class="text-xs text-gray-500 space-y-1">
                            <div><code class="text-indigo-300/70">phone</code> <span class="text-rose-400 text-[10px] font-bold">required</span></div>
                            <div><code class="text-indigo-300/70">channel</code> <span class="text-gray-600 text-[10px]">sms | whatsapp</span></div>
                        </div>
                        <div class="mt-3 text-[10px] text-gray-600">يرسل رمز مكوّن من 6 أرقام</div>
                    </div>
                    <!-- Step 2: Verify OTP -->
                    <div class="glass border border-white/5 rounded-xl p-4">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="w-6 h-6 bg-emerald-500/20 rounded-full flex items-center justify-center text-xs text-emerald-400 font-bold">2</span>
                            <span class="method-badge font-bold px-2 py-0.5 rounded bg-blue-500/20 text-blue-400 font-mono">POST</span>
                        </div>
                        <code class="text-xs text-gray-300 font-mono block mb-2" dir="ltr">/api/v1/auth/otp/verify</code>
                        <div class="text-xs text-gray-500 space-y-1">
                            <div><code class="text-indigo-300/70">phone</code> <span class="text-rose-400 text-[10px] font-bold">required</span></div>
                            <div><code class="text-indigo-300/70">code</code> <span class="text-rose-400 text-[10px] font-bold">required</span> <span class="text-gray-600">6 أرقام</span></div>
                        </div>
                        <div class="mt-3 text-[10px] text-gray-600">→ مستخدم موجود: JWT Token</div>
                        <div class="text-[10px] text-gray-600">→ مستخدم جديد: registration_token</div>
                    </div>
                    <!-- Step 3: Complete Registration -->
                    <div class="glass border border-white/5 rounded-xl p-4">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="w-6 h-6 bg-amber-500/20 rounded-full flex items-center justify-center text-xs text-amber-400 font-bold">3</span>
                            <span class="method-badge font-bold px-2 py-0.5 rounded bg-blue-500/20 text-blue-400 font-mono">POST</span>
                        </div>
                        <code class="text-xs text-gray-300 font-mono block mb-2" dir="ltr">/api/v1/auth/otp/complete-registration</code>
                        <div class="text-xs text-gray-500 space-y-1">
                            <div><code class="text-indigo-300/70">registration_token</code> <span class="text-rose-400 text-[10px] font-bold">required</span></div>
                            <div><code class="text-indigo-300/70">name</code> <span class="text-rose-400 text-[10px] font-bold">required</span></div>
                            <div><code class="text-indigo-300/70">email</code> <span class="text-gray-600 text-[10px]">optional</span></div>
                        </div>
                        <div class="mt-3 text-[10px] text-amber-400/70">فقط للمستخدمين الجدد</div>
                    </div>
                </div>
                <div class="p-3 bg-amber-500/5 border border-amber-500/10 rounded-lg">
                    <p class="text-xs text-amber-400/80"><span class="font-bold">⚡ وضع الاختبار:</span> عند تفعيل <code class="text-amber-300">sms_test_mode</code> من إعدادات المنصة، الرمز دائماً <code class="text-emerald-300 font-bold">123456</code> — بدون إرسال SMS فعلي</p>
                </div>
            </div>
        </div>

        <div class="grid md:grid-cols-3 gap-4 mb-8">
            <div class="glass border border-white/5 rounded-2xl p-5">
                <div class="flex items-center gap-2 mb-3">
                    <span class="method-badge font-bold px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-400 font-mono">GET</span>
                    <code class="text-xs text-gray-400 font-mono" dir="ltr">/api/v1/auth/me</code>
                </div>
                <span class="text-[10px] px-2 py-0.5 rounded-full bg-blue-500/10 text-blue-400 font-semibold">auth</span>
                <p class="text-xs text-gray-500 mt-3">جلب بيانات المستخدم الحالي مع الأدوار والصلاحيات الكاملة</p>
                <div class="mt-3 text-[10px] text-gray-600">
                    <span class="text-gray-500">Header:</span> Authorization: Bearer {token}
                </div>
            </div>
            <div class="glass border border-white/5 rounded-2xl p-5">
                <div class="flex items-center gap-2 mb-3">
                    <span class="method-badge font-bold px-2 py-0.5 rounded bg-blue-500/20 text-blue-400 font-mono">POST</span>
                    <code class="text-xs text-gray-400 font-mono" dir="ltr">/api/v1/auth/refresh</code>
                </div>
                <span class="text-[10px] px-2 py-0.5 rounded-full bg-blue-500/10 text-blue-400 font-semibold">auth</span>
                <p class="text-xs text-gray-500 mt-3">تجديد التوكن المنتهي. يرجع توكن جديد مع مدة الصلاحية</p>
                <div class="mt-3 text-[10px] text-gray-600">
                    Response: <code class="text-gray-400">{ token, token_type, expires_in }</code>
                </div>
            </div>
            <div class="glass border border-white/5 rounded-2xl p-5">
                <div class="flex items-center gap-2 mb-3">
                    <span class="method-badge font-bold px-2 py-0.5 rounded bg-blue-500/20 text-blue-400 font-mono">POST</span>
                    <code class="text-xs text-gray-400 font-mono" dir="ltr">/api/v1/auth/logout</code>
                </div>
                <span class="text-[10px] px-2 py-0.5 rounded-full bg-blue-500/10 text-blue-400 font-semibold">auth</span>
                <p class="text-xs text-gray-500 mt-3">تسجيل خروج المستخدم وإلغاء التوكن الحالي نهائياً</p>
                <div class="mt-3 text-[10px] text-gray-600">
                    <span class="text-gray-500">Header:</span> Authorization: Bearer {token}
                </div>
            </div>
        </div>

        <!-- Password & Profile -->
        <div class="grid md:grid-cols-2 gap-4 mb-8">
            <div class="glass border border-white/5 rounded-2xl p-5">
                <div class="flex items-center gap-2 mb-3">
                    <span class="method-badge font-bold px-2 py-0.5 rounded bg-blue-500/20 text-blue-400 font-mono">POST</span>
                    <code class="text-xs text-gray-400 font-mono" dir="ltr">/api/v1/auth/change-password</code>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-blue-500/10 text-blue-400 font-semibold mr-auto">auth</span>
                </div>
                <p class="text-xs text-gray-500 mb-3">تغيير كلمة المرور</p>
                <div class="space-y-1.5 text-xs text-gray-600">
                    <div><code class="text-indigo-300/70">current_password</code> - كلمة المرور الحالية</div>
                    <div><code class="text-indigo-300/70">password</code> - كلمة المرور الجديدة</div>
                    <div><code class="text-indigo-300/70">password_confirmation</code> - التأكيد</div>
                </div>
            </div>
            <div class="glass border border-white/5 rounded-2xl p-5">
                <div class="flex items-center gap-2 mb-3">
                    <span class="method-badge font-bold px-2 py-0.5 rounded bg-amber-500/20 text-amber-400 font-mono">PUT</span>
                    <code class="text-xs text-gray-400 font-mono" dir="ltr">/api/v1/auth/profile</code>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-blue-500/10 text-blue-400 font-semibold mr-auto">auth</span>
                </div>
                <p class="text-xs text-gray-500 mb-3">تحديث الملف الشخصي</p>
                <div class="space-y-1.5 text-xs text-gray-600">
                    <div><code class="text-indigo-300/70">name</code> - الاسم</div>
                    <div><code class="text-indigo-300/70">phone</code> - رقم الهاتف</div>
                </div>
            </div>
        </div>

        <!-- Forgot/Reset Password -->
        <div class="grid md:grid-cols-2 gap-4">
            <div class="glass border border-white/5 rounded-2xl p-5">
                <div class="flex items-center gap-2 mb-3">
                    <span class="method-badge font-bold px-2 py-0.5 rounded bg-blue-500/20 text-blue-400 font-mono">POST</span>
                    <code class="text-xs text-gray-400 font-mono" dir="ltr">/api/v1/auth/forgot-password</code>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-green-500/10 text-green-400 font-semibold mr-auto">public</span>
                </div>
                <p class="text-xs text-gray-500 mb-2">إرسال رابط استعادة كلمة المرور</p>
                <div class="text-xs text-gray-600"><code class="text-indigo-300/70">email</code> - البريد الإلكتروني</div>
            </div>
            <div class="glass border border-white/5 rounded-2xl p-5">
                <div class="flex items-center gap-2 mb-3">
                    <span class="method-badge font-bold px-2 py-0.5 rounded bg-blue-500/20 text-blue-400 font-mono">POST</span>
                    <code class="text-xs text-gray-400 font-mono" dir="ltr">/api/v1/auth/reset-password</code>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-green-500/10 text-green-400 font-semibold mr-auto">public</span>
                </div>
                <p class="text-xs text-gray-500 mb-2">إعادة تعيين كلمة المرور</p>
                <div class="space-y-1 text-xs text-gray-600">
                    <div><code class="text-indigo-300/70">token</code> + <code class="text-indigo-300/70">email</code> + <code class="text-indigo-300/70">password</code></div>
                </div>
            </div>
        </div>

        <!-- Email Verification -->
        <div class="grid md:grid-cols-2 gap-4 mt-4">
            <div class="glass border border-white/5 rounded-2xl p-5">
                <div class="flex items-center gap-2 mb-3">
                    <span class="method-badge font-bold px-2 py-0.5 rounded bg-blue-500/20 text-blue-400 font-mono">POST</span>
                    <code class="text-xs text-gray-400 font-mono" dir="ltr">/api/v1/auth/email/send-verification</code>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-blue-500/10 text-blue-400 font-semibold mr-auto">auth</span>
                </div>
                <p class="text-xs text-gray-500 mb-2">إرسال رمز التحقق من البريد الإلكتروني</p>
                <div class="text-xs text-gray-600">بدون حقول إدخال - يرسل رمز التحقق للبريد المسجل</div>
            </div>
            <div class="glass border border-white/5 rounded-2xl p-5">
                <div class="flex items-center gap-2 mb-3">
                    <span class="method-badge font-bold px-2 py-0.5 rounded bg-blue-500/20 text-blue-400 font-mono">POST</span>
                    <code class="text-xs text-gray-400 font-mono" dir="ltr">/api/v1/auth/email/verify</code>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-blue-500/10 text-blue-400 font-semibold mr-auto">auth</span>
                </div>
                <p class="text-xs text-gray-500 mb-2">التحقق من البريد الإلكتروني</p>
                <div class="space-y-1 text-xs text-gray-600">
                    <div><code class="text-indigo-300/70">code</code> <span class="text-rose-400 text-[10px] font-bold">required</span> - رمز التحقق المرسل للبريد</div>
                </div>
            </div>
        </div>

        <!-- Admin Stats -->
        <div class="mt-4">
            <div class="glass border border-white/5 rounded-2xl p-5">
                <div class="flex items-center gap-2 mb-3">
                    <span class="method-badge font-bold px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-400 font-mono">GET</span>
                    <code class="text-xs text-gray-400 font-mono" dir="ltr">/api/v1/admin/stats/users</code>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-blue-500/10 text-blue-400 font-semibold">auth</span>
                    <span class="text-[9px] px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-400/70 font-mono mr-auto">users.view</span>
                </div>
                <p class="text-xs text-gray-500">إحصائيات المستخدمين للوحة تحكم الأدمن</p>
            </div>
        </div>
    </section>

    <div class="glow-line max-w-4xl mx-auto"></div>

    <!-- Users API -->
    <section id="users-api" class="section-anchor max-w-7xl mx-auto px-6 py-16">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-1 h-8 bg-emerald-500 rounded-full"></div>
            <h3 class="text-2xl font-bold">إدارة المستخدمين</h3>
            <span class="text-xs bg-emerald-500/10 text-emerald-400 px-2.5 py-1 rounded-full">Users Management</span>
        </div>
        <p class="text-gray-500 text-sm mb-8 mr-4">عرض، إنشاء، تعديل وحذف المستخدمين مع إدارة الأدوار والصلاحيات</p>

        <div class="glass border border-white/5 rounded-2xl overflow-hidden">
            <div class="divide-y divide-white/5">
                @php
                    $userEndpoints = [
                        ['GET', '/api/v1/users', 'قائمة المستخدمين', 'users.view', 'يدعم الفلترة والترتيب والبحث مع pagination'],
                        ['POST', '/api/v1/users', 'إنشاء مستخدم', 'users.create', 'name, email, password, phone, roles'],
                        ['GET', '/api/v1/users/{id}', 'تفاصيل مستخدم', 'users.view', 'بيانات المستخدم الكاملة مع الأدوار والصلاحيات'],
                        ['PUT', '/api/v1/users/{id}', 'تحديث مستخدم', 'users.update', 'name, email, phone, status'],
                        ['DELETE', '/api/v1/users/{id}', 'حذف مستخدم', 'users.delete', 'حذف نهائي للمستخدم'],
                        ['POST', '/api/v1/users/{id}/roles', 'تعيين أدوار', 'roles.update', 'roles: ["admin", "editor"]'],
                        ['POST', '/api/v1/users/{id}/permissions', 'تعيين صلاحيات', 'permissions.update', 'permissions: ["users.view", "events.create"]'],
                        ['GET', '/api/v1/users/{id}/permissions', 'عرض صلاحيات', 'permissions.view', 'جميع الصلاحيات المباشرة والموروثة'],
                    ];
                @endphp
                @foreach($userEndpoints as $ep)
                <div class="endpoint-row flex items-center px-6 py-3.5 gap-3">
                    <span class="method-badge font-bold px-2 py-0.5 rounded font-mono min-w-[56px] text-center {{ $ep[0] === 'GET' ? 'bg-emerald-500/20 text-emerald-400' : ($ep[0] === 'POST' ? 'bg-blue-500/20 text-blue-400' : ($ep[0] === 'PUT' ? 'bg-amber-500/20 text-amber-400' : 'bg-rose-500/20 text-rose-400')) }}">{{ $ep[0] }}</span>
                    <code class="text-xs text-gray-300 font-mono whitespace-nowrap" dir="ltr">{{ $ep[1] }}</code>
                    <span class="text-xs text-gray-600">{{ $ep[2] }}</span>
                    <span class="mr-auto"></span>
                    <span class="text-[9px] px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-400/70 font-mono whitespace-nowrap">{{ $ep[3] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <div class="glow-line max-w-4xl mx-auto"></div>

    <!-- Roles & Permissions API -->
    <section id="roles-api" class="section-anchor max-w-7xl mx-auto px-6 py-16">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-1 h-8 bg-purple-500 rounded-full"></div>
            <h3 class="text-2xl font-bold">الأدوار والصلاحيات</h3>
            <span class="text-xs bg-purple-500/10 text-purple-400 px-2.5 py-1 rounded-full">Roles & Permissions</span>
        </div>
        <p class="text-gray-500 text-sm mb-8 mr-4">نظام RBAC كامل لإدارة الأدوار والصلاحيات مع ربط متعدد المستويات</p>

        <div class="grid md:grid-cols-2 gap-6">
            <!-- Roles -->
            <div class="glass border border-white/5 rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-white/5">
                    <h4 class="font-bold text-purple-400 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        Roles
                    </h4>
                </div>
                <div class="divide-y divide-white/5">
                    @php
                        $roleEps = [
                            ['GET', '/api/v1/roles', 'القائمة'],
                            ['POST', '/api/v1/roles', 'إنشاء دور'],
                            ['GET', '/api/v1/roles/{id}', 'التفاصيل'],
                            ['PUT', '/api/v1/roles/{id}', 'تحديث'],
                            ['DELETE', '/api/v1/roles/{id}', 'حذف'],
                            ['POST', '/api/v1/roles/{id}/permissions', 'مزامنة الصلاحيات'],
                            ['POST', '/api/v1/roles/{id}/permissions/add', 'إضافة صلاحيات'],
                            ['POST', '/api/v1/roles/{id}/permissions/remove', 'إزالة صلاحيات'],
                        ];
                    @endphp
                    @foreach($roleEps as $ep)
                    <div class="endpoint-row flex items-center px-5 py-2.5 gap-2">
                        <span class="method-badge font-bold px-1.5 py-0.5 rounded font-mono min-w-[44px] text-center {{ $ep[0] === 'GET' ? 'bg-emerald-500/15 text-emerald-400' : ($ep[0] === 'POST' ? 'bg-blue-500/15 text-blue-400' : ($ep[0] === 'PUT' ? 'bg-amber-500/15 text-amber-400' : 'bg-rose-500/15 text-rose-400')) }}">{{ $ep[0] }}</span>
                        <code class="text-[11px] text-gray-400 font-mono" dir="ltr">{{ $ep[1] }}</code>
                        <span class="text-[11px] text-gray-600 mr-auto">{{ $ep[2] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Permissions -->
            <div class="glass border border-white/5 rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-white/5">
                    <h4 class="font-bold text-amber-400 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                        Permissions
                    </h4>
                </div>
                <div class="divide-y divide-white/5">
                    @php
                        $permEps = [
                            ['GET', '/api/v1/permissions', 'القائمة'],
                            ['POST', '/api/v1/permissions', 'إنشاء صلاحية'],
                            ['POST', '/api/v1/permissions/resource', 'إنشاء CRUD لمورد'],
                            ['GET', '/api/v1/permissions/{id}', 'التفاصيل'],
                            ['PUT', '/api/v1/permissions/{id}', 'تحديث'],
                            ['DELETE', '/api/v1/permissions/{id}', 'حذف'],
                        ];
                    @endphp
                    @foreach($permEps as $ep)
                    <div class="endpoint-row flex items-center px-5 py-2.5 gap-2">
                        <span class="method-badge font-bold px-1.5 py-0.5 rounded font-mono min-w-[44px] text-center {{ $ep[0] === 'GET' ? 'bg-emerald-500/15 text-emerald-400' : ($ep[0] === 'POST' ? 'bg-blue-500/15 text-blue-400' : ($ep[0] === 'PUT' ? 'bg-amber-500/15 text-amber-400' : 'bg-rose-500/15 text-rose-400')) }}">{{ $ep[0] }}</span>
                        <code class="text-[11px] text-gray-400 font-mono" dir="ltr">{{ $ep[1] }}</code>
                        <span class="text-[11px] text-gray-600 mr-auto">{{ $ep[2] }}</span>
                    </div>
                    @endforeach
                </div>
                <div class="p-4 bg-white/[0.02]">
                    <p class="text-[10px] text-gray-600"><span class="text-amber-400/60 font-bold">POST /resource</span> - ينشئ صلاحيات CRUD كاملة (view, create, update, delete) لمورد واحد</p>
                </div>
            </div>
        </div>
    </section>

    <div class="glow-line max-w-4xl mx-auto"></div>

    <!-- Services API -->
    <section id="services-api" class="section-anchor max-w-7xl mx-auto px-6 py-16">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-1 h-8 bg-cyan-500 rounded-full"></div>
            <h3 class="text-2xl font-bold">إدارة الخدمات</h3>
            <span class="text-xs bg-cyan-500/10 text-cyan-400 px-2.5 py-1 rounded-full">Services & S2S</span>
        </div>
        <p class="text-gray-500 text-sm mb-8 mr-4">تسجيل وإدارة الـ Microservices مع التواصل الآمن عبر Service Tokens</p>

        <!-- POST /api/services (Create) -->
        <div class="mb-8 glass border border-white/5 rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-white/5 flex items-center gap-3">
                <span class="method-badge font-bold px-2.5 py-1 rounded-md bg-blue-500/20 text-blue-400 font-mono">POST</span>
                <code class="text-sm text-gray-300 font-mono" dir="ltr">/api/v1/services</code>
                <span class="text-xs text-gray-600 mr-auto">تسجيل خدمة جديدة</span>
                <span class="text-[10px] px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-400 font-semibold">services.create</span>
            </div>
            <div class="grid md:grid-cols-2 divide-y md:divide-y-0 md:divide-x md:divide-x-reverse divide-white/5">
                <div class="p-6">
                    <h5 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Request Body</h5>
                    <div class="space-y-3">
                        <div class="flex items-start gap-2">
                            <code class="text-xs bg-white/5 px-2 py-0.5 rounded text-cyan-300 font-mono">name</code>
                            <span class="text-[10px] text-rose-400 font-bold">required</span>
                            <span class="text-xs text-gray-500">string - اسم الخدمة (فريد)</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <code class="text-xs bg-white/5 px-2 py-0.5 rounded text-cyan-300 font-mono">display_name</code>
                            <span class="text-[10px] text-rose-400 font-bold">required</span>
                            <span class="text-xs text-gray-500">string - الاسم المعروض</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <code class="text-xs bg-white/5 px-2 py-0.5 rounded text-cyan-300 font-mono">description</code>
                            <span class="text-[10px] text-gray-500 font-bold">optional</span>
                            <span class="text-xs text-gray-500">string - الوصف</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <code class="text-xs bg-white/5 px-2 py-0.5 rounded text-cyan-300 font-mono">allowed_ips</code>
                            <span class="text-[10px] text-gray-500 font-bold">optional</span>
                            <span class="text-xs text-gray-500">array - IPs المسموح بها</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <code class="text-xs bg-white/5 px-2 py-0.5 rounded text-cyan-300 font-mono">webhook_url</code>
                            <span class="text-[10px] text-gray-500 font-bold">optional</span>
                            <span class="text-xs text-gray-500">url - رابط الـ Webhook</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <code class="text-xs bg-white/5 px-2 py-0.5 rounded text-cyan-300 font-mono">roles</code>
                            <span class="text-[10px] text-gray-500 font-bold">optional</span>
                            <span class="text-xs text-gray-500">array - أدوار الخدمة (تحدد من يقدر يسجل دخول)</span>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <h5 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Response <span class="text-emerald-400">201</span></h5>
                    <div class="code-block rounded-xl p-4 text-xs overflow-x-auto" dir="ltr">
<pre class="text-gray-300"><code>{
  <span class="key">"success"</span>: <span class="text-emerald-400">true</span>,
  <span class="key">"message"</span>: <span class="str">"تم تسجيل الخدمة بنجاح"</span>,
  <span class="key">"data"</span>: {
    <span class="key">"id"</span>: <span class="str">"uuid"</span>,
    <span class="key">"name"</span>: <span class="str">"expo-app"</span>,
    <span class="key">"token"</span>: <span class="str">"svc_xxx..."</span>,
    <span class="key">"secret"</span>: <span class="str">"sec_xxx..."</span>,
    <span class="key">"roles"</span>: [
      { <span class="key">"name"</span>: <span class="str">"admin"</span> }
    ]
  },
  <span class="key">"warning"</span>: <span class="str">"احفظ التوكن والسر!"</span>
}</code></pre>
                    </div>
                    <div class="mt-3 p-3 bg-rose-500/5 border border-rose-500/10 rounded-lg">
                        <p class="text-[10px] text-rose-400/80"><span class="font-bold">تحذير:</span> التوكن والسر يظهروا مرة وحدة فقط! لازم تحفظهم فوراً.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Service CRUD endpoints list -->
        <div class="glass border border-white/5 rounded-2xl overflow-hidden mb-8">
            <div class="px-6 py-3 border-b border-white/5 bg-white/[0.02]">
                <h5 class="text-xs font-bold text-gray-400">إدارة الخدمات - CRUD</h5>
            </div>
            <div class="divide-y divide-white/5">
                @php
                    $svcEps = [
                        ['GET', '/api/v1/services', 'قائمة الخدمات', 'services.view'],
                        ['GET', '/api/v1/services/{id}', 'تفاصيل خدمة', 'services.view'],
                        ['PUT', '/api/v1/services/{id}', 'تحديث خدمة (+ roles sync)', 'services.update'],
                        ['DELETE', '/api/v1/services/{id}', 'حذف خدمة', 'services.delete'],
                        ['POST', '/api/v1/services/{id}/regenerate-token', 'تجديد التوكن', 'services.update'],
                        ['GET', '/api/v1/services/{id}/roles', 'عرض أدوار الخدمة', 'services.view'],
                        ['POST', '/api/v1/services/{id}/roles', 'إضافة أدوار للخدمة', 'services.update'],
                        ['PUT', '/api/v1/services/{id}/roles', 'مزامنة الأدوار', 'services.update'],
                        ['DELETE', '/api/v1/services/{id}/roles', 'إزالة أدوار', 'services.update'],
                    ];
                @endphp
                @foreach($svcEps as $ep)
                <div class="endpoint-row flex items-center px-6 py-3 gap-3">
                    <span class="method-badge font-bold px-2 py-0.5 rounded font-mono min-w-[56px] text-center {{ $ep[0] === 'GET' ? 'bg-emerald-500/20 text-emerald-400' : ($ep[0] === 'POST' ? 'bg-blue-500/20 text-blue-400' : ($ep[0] === 'PUT' ? 'bg-amber-500/20 text-amber-400' : 'bg-rose-500/20 text-rose-400')) }}">{{ $ep[0] }}</span>
                    <code class="text-xs text-gray-300 font-mono whitespace-nowrap" dir="ltr">{{ $ep[1] }}</code>
                    <span class="text-xs text-gray-600">{{ $ep[2] }}</span>
                    <span class="mr-auto"></span>
                    <span class="text-[9px] px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-400/70 font-mono">{{ $ep[3] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- S2S Endpoints -->
        <div class="glass border border-white/5 rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-white/5 flex items-center gap-3">
                <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <h4 class="font-bold text-cyan-400">Service-to-Service (S2S)</h4>
                <span class="text-xs text-gray-600">محمية عبر الشبكة الداخلية فقط</span>
            </div>
            <div class="grid md:grid-cols-3 divide-y md:divide-y-0 md:divide-x md:divide-x-reverse divide-white/5">
                <div class="p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="method-badge font-bold px-2 py-0.5 rounded bg-blue-500/20 text-blue-400 font-mono">POST</span>
                        <code class="text-[11px] text-gray-400 font-mono" dir="ltr">/api/v1/service/verify-token</code>
                    </div>
                    <p class="text-xs text-gray-500 mb-2">التحقق من توكن المستخدم</p>
                    <div class="text-[10px] text-gray-600 space-y-1">
                        <div>Body: <code class="text-cyan-300/60">{ "token": "jwt..." }</code></div>
                        <div>Returns: user data + roles</div>
                    </div>
                </div>
                <div class="p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="method-badge font-bold px-2 py-0.5 rounded bg-blue-500/20 text-blue-400 font-mono">POST</span>
                        <code class="text-[11px] text-gray-400 font-mono" dir="ltr">/api/v1/service/check-permission</code>
                    </div>
                    <p class="text-xs text-gray-500 mb-2">التحقق من صلاحية المستخدم</p>
                    <div class="text-[10px] text-gray-600 space-y-1">
                        <div>Body: <code class="text-cyan-300/60">{ "token", "permission" }</code></div>
                        <div>Returns: has_permission boolean</div>
                    </div>
                </div>
                <div class="p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="method-badge font-bold px-2 py-0.5 rounded bg-blue-500/20 text-blue-400 font-mono">POST</span>
                        <code class="text-[11px] text-gray-400 font-mono" dir="ltr">/api/v1/service/user-info</code>
                    </div>
                    <p class="text-xs text-gray-500 mb-2">جلب معلومات المستخدم</p>
                    <div class="text-[10px] text-gray-600 space-y-1">
                        <div>Body: <code class="text-cyan-300/60">{ "token": "jwt..." }</code></div>
                        <div>Returns: full user info</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="glow-line max-w-4xl mx-auto"></div>

    <!-- Quick Start -->
    <section id="quickstart" class="section-anchor max-w-7xl mx-auto px-6 py-16">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-1 h-8 bg-amber-500 rounded-full"></div>
            <h3 class="text-2xl font-bold">البدء السريع</h3>
            <span class="text-xs bg-amber-500/10 text-amber-400 px-2.5 py-1 rounded-full">Quick Start</span>
        </div>
        <p class="text-gray-500 text-sm mb-8 mr-4">أمثلة سريعة للبدء بإستخدام الـ API</p>

        <!-- Tabs -->
        <div class="flex gap-2 mb-6 overflow-x-auto scrollbar-hide">
            <button onclick="showTab('login')" class="tab-btn active text-xs px-4 py-2 rounded-lg border border-white/10 text-gray-400 hover:text-white transition whitespace-nowrap" id="tab-login">تسجيل الدخول</button>
            <button onclick="showTab('register')" class="tab-btn text-xs px-4 py-2 rounded-lg border border-white/10 text-gray-400 hover:text-white transition whitespace-nowrap" id="tab-register">إنشاء حساب</button>
            <button onclick="showTab('s2s')" class="tab-btn text-xs px-4 py-2 rounded-lg border border-white/10 text-gray-400 hover:text-white transition whitespace-nowrap" id="tab-s2s">S2S Verify</button>
            <button onclick="showTab('service')" class="tab-btn text-xs px-4 py-2 rounded-lg border border-white/10 text-gray-400 hover:text-white transition whitespace-nowrap" id="tab-service">إنشاء خدمة</button>
        </div>

        <!-- Login Tab -->
        <div id="content-login" class="tab-content">
            <div class="code-block border border-white/5 rounded-2xl overflow-hidden">
                <div class="px-5 py-3 border-b border-white/5 flex items-center justify-between bg-white/[0.02]">
                    <span class="text-xs text-gray-500">Login with Service Name</span>
                    <button onclick="copyCode('login')" class="text-xs text-gray-500 hover:text-white transition flex items-center gap-1 copy-btn" data-target="login">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        Copy
                    </button>
                </div>
                <pre class="p-5 text-sm overflow-x-auto" dir="ltr"><code class="text-gray-300" id="code-login"><span class="cmd">curl</span> <span class="flag">-X</span> POST <span class="url">https://auth-service-api.mahamexpo.sa/api/v1/auth/login</span> \
  <span class="flag">-H</span> <span class="str">"Content-Type: application/json"</span> \
  <span class="flag">-d</span> <span class="str">'{
    "identifier": "admin@auth-service.local",
    "password": "password",
    "service_name": "expo-app"
  }'</span></code></pre>
            </div>
        </div>

        <!-- Register Tab -->
        <div id="content-register" class="tab-content hidden">
            <div class="code-block border border-white/5 rounded-2xl overflow-hidden">
                <div class="px-5 py-3 border-b border-white/5 flex items-center justify-between bg-white/[0.02]">
                    <span class="text-xs text-gray-500">Register New User</span>
                    <button onclick="copyCode('register')" class="text-xs text-gray-500 hover:text-white transition flex items-center gap-1 copy-btn" data-target="register">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        Copy
                    </button>
                </div>
                <pre class="p-5 text-sm overflow-x-auto" dir="ltr"><code class="text-gray-300" id="code-register"><span class="cmd">curl</span> <span class="flag">-X</span> POST <span class="url">https://auth-service-api.mahamexpo.sa/api/v1/auth/register</span> \
  <span class="flag">-H</span> <span class="str">"Content-Type: application/json"</span> \
  <span class="flag">-d</span> <span class="str">'{
    "name": "Ahmed Ali",
    "email": "ahmed@example.com",
    "password": "Password123",
    "password_confirmation": "Password123",
    "phone": "0501234567"
  }'</span></code></pre>
            </div>
        </div>

        <!-- S2S Tab -->
        <div id="content-s2s" class="tab-content hidden">
            <div class="code-block border border-white/5 rounded-2xl overflow-hidden">
                <div class="px-5 py-3 border-b border-white/5 flex items-center justify-between bg-white/[0.02]">
                    <span class="text-xs text-gray-500">Service-to-Service Token Verification</span>
                    <button onclick="copyCode('s2s')" class="text-xs text-gray-500 hover:text-white transition flex items-center gap-1 copy-btn" data-target="s2s">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        Copy
                    </button>
                </div>
                <pre class="p-5 text-sm overflow-x-auto" dir="ltr"><code class="text-gray-300" id="code-s2s"><span class="cmd">curl</span> <span class="flag">-X</span> POST <span class="url">https://auth-service-api.mahamexpo.sa/api/v1/service/verify-token</span> \
  <span class="flag">-H</span> <span class="str">"Content-Type: application/json"</span> \
  <span class="flag">-d</span> <span class="str">'{
    "token": "user-jwt-token-here"
  }'</span></code></pre>
            </div>
        </div>

        <!-- Service Tab -->
        <div id="content-service" class="tab-content hidden">
            <div class="code-block border border-white/5 rounded-2xl overflow-hidden">
                <div class="px-5 py-3 border-b border-white/5 flex items-center justify-between bg-white/[0.02]">
                    <span class="text-xs text-gray-500">Create Service with Roles</span>
                    <button onclick="copyCode('service')" class="text-xs text-gray-500 hover:text-white transition flex items-center gap-1 copy-btn" data-target="service">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        Copy
                    </button>
                </div>
                <pre class="p-5 text-sm overflow-x-auto" dir="ltr"><code class="text-gray-300" id="code-service"><span class="cmd">curl</span> <span class="flag">-X</span> POST <span class="url">https://auth-service-api.mahamexpo.sa/api/v1/services</span> \
  <span class="flag">-H</span> <span class="str">"Content-Type: application/json"</span> \
  <span class="flag">-H</span> <span class="str">"Authorization: Bearer admin-jwt-token"</span> \
  <span class="flag">-d</span> <span class="str">'{
    "name": "expo-app",
    "display_name": "Expo Application",
    "description": "Exhibition management service",
    "roles": ["admin", "organizer"],
    "webhook_url": "http://expo-api:8000/webhook"
  }'</span></code></pre>
            </div>
        </div>
    </section>

    <div class="glow-line max-w-4xl mx-auto"></div>

    <!-- Architecture -->
    <section class="max-w-7xl mx-auto px-6 py-16">
        <div class="flex items-center gap-3 mb-8">
            <div class="w-1 h-8 bg-rose-500 rounded-full"></div>
            <h3 class="text-2xl font-bold">البنية التقنية</h3>
            <span class="text-xs bg-rose-500/10 text-rose-400 px-2.5 py-1 rounded-full">Architecture</span>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            <!-- Auth Flow -->
            <div class="glass border border-white/5 rounded-2xl p-6">
                <h4 class="font-bold text-indigo-400 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    مسار المصادقة
                </h4>
                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <span class="w-6 h-6 bg-indigo-500/20 rounded-full flex items-center justify-center text-xs text-indigo-400 font-bold shrink-0 mt-0.5">1</span>
                        <div>
                            <div class="text-sm font-semibold text-white/80">تسجيل الدخول</div>
                            <div class="text-xs text-gray-500">إرسال email/phone + password + service_name (اختياري)</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="w-6 h-6 bg-indigo-500/20 rounded-full flex items-center justify-center text-xs text-indigo-400 font-bold shrink-0 mt-0.5">2</span>
                        <div>
                            <div class="text-sm font-semibold text-white/80">التحقق من الأدوار</div>
                            <div class="text-xs text-gray-500">إذا الخدمة عندها أدوار ← يتم التحقق إن المستخدم عنده الأدوار المطلوبة</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="w-6 h-6 bg-indigo-500/20 rounded-full flex items-center justify-center text-xs text-indigo-400 font-bold shrink-0 mt-0.5">3</span>
                        <div>
                            <div class="text-sm font-semibold text-white/80">إصدار JWT</div>
                            <div class="text-xs text-gray-500">توكن يحتوي بيانات المستخدم + الأدوار + الصلاحيات</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="w-6 h-6 bg-indigo-500/20 rounded-full flex items-center justify-center text-xs text-indigo-400 font-bold shrink-0 mt-0.5">4</span>
                        <div>
                            <div class="text-sm font-semibold text-white/80">استخدام التوكن</div>
                            <div class="text-xs text-gray-500">إرسال التوكن في Header: <code class="text-indigo-300/60">Authorization: Bearer {token}</code></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- S2S Flow -->
            <div class="glass border border-white/5 rounded-2xl p-6">
                <h4 class="font-bold text-cyan-400 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    تواصل الخدمات (S2S)
                </h4>
                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <span class="w-6 h-6 bg-cyan-500/20 rounded-full flex items-center justify-center text-xs text-cyan-400 font-bold shrink-0 mt-0.5">1</span>
                        <div>
                            <div class="text-sm font-semibold text-white/80">تسجيل الخدمة</div>
                            <div class="text-xs text-gray-500">إنشاء خدمة والحصول على Service Token + Secret</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="w-6 h-6 bg-cyan-500/20 rounded-full flex items-center justify-center text-xs text-cyan-400 font-bold shrink-0 mt-0.5">2</span>
                        <div>
                            <div class="text-sm font-semibold text-white/80">إرسال الطلب</div>
                            <div class="text-xs text-gray-500">الطلبات تمر عبر الشبكة الداخلية بدون مصادقة</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="w-6 h-6 bg-cyan-500/20 rounded-full flex items-center justify-center text-xs text-cyan-400 font-bold shrink-0 mt-0.5">3</span>
                        <div>
                            <div class="text-sm font-semibold text-white/80">التحقق من IP</div>
                            <div class="text-xs text-gray-500">إذا الخدمة عندها allowed_ips ← يتم التحقق من IP المرسل</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="w-6 h-6 bg-cyan-500/20 rounded-full flex items-center justify-center text-xs text-cyan-400 font-bold shrink-0 mt-0.5">4</span>
                        <div>
                            <div class="text-sm font-semibold text-white/80">الرد الآمن</div>
                            <div class="text-xs text-gray-500">بيانات المستخدم المطلوبة مع الأدوار والصلاحيات</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="glow-line max-w-4xl mx-auto"></div>

    <!-- Docker Services --> 
    {{-- <section class="max-w-7xl mx-auto px-6 py-16">
        <div class="flex items-center gap-3 mb-8">
            <div class="w-1 h-8 bg-sky-500 rounded-full"></div>
            <h3 class="text-2xl font-bold">Docker Services</h3>
        </div>
        <div class="grid md:grid-cols-3 gap-4">
            <div class="glass border border-white/5 rounded-2xl p-5 card-hover">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-9 h-9 bg-indigo-500/15 rounded-xl flex items-center justify-center">
                        <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <div>
                        <div class="font-bold text-sm">Auth Service</div>
                        <div class="text-[10px] text-gray-500">PHP 8.3 + JWT + Redis</div>
                    </div>
                </div>
                <div class="space-y-2 text-xs">
                    <div class="flex justify-between text-gray-500"><span>API</span><a href="https://auth-service-api.mahamexpo.sa" class="font-mono text-indigo-400 hover:underline">auth-service-api.mahamexpo.sa</a></div>
                    <div class="flex justify-between text-gray-500"><span>MySQL</span><span class="font-mono text-gray-400">internal (auth-mysql:3306)</span></div>
                    <div class="flex justify-between text-gray-500"><span>Redis</span><span class="font-mono text-gray-400">internal (auth-redis:6379)</span></div>
                </div>
            </div>
            <div class="glass border border-white/5 rounded-2xl p-5 card-hover">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-9 h-9 bg-emerald-500/15 rounded-xl flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div>
                        <div class="font-bold text-sm">Expo API</div>
                        <div class="text-[10px] text-gray-500">Events + Spaces + Rentals</div>
                    </div>
                </div>
                <div class="space-y-2 text-xs">
                    <div class="flex justify-between text-gray-500"><span>API</span><a href="https://expo-service-api.mahamexpo.sa" class="font-mono text-emerald-400 hover:underline">expo-service-api.mahamexpo.sa</a></div>
                    <div class="flex justify-between text-gray-500"><span>MySQL</span><span class="font-mono text-gray-400">internal (mysql:3306)</span></div>
                    <div class="flex justify-between text-gray-500"><span>Redis</span><span class="font-mono text-gray-400">internal (redis:6379)</span></div>
                </div>
            </div>
            <div class="glass border border-white/5 rounded-2xl p-5 card-hover">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-9 h-9 bg-orange-500/15 rounded-xl flex items-center justify-center">
                        <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
                    </div>
                    <div>
                        <div class="font-bold text-sm">phpMyAdmin</div>
                        <div class="text-[10px] text-gray-500">Database Management</div>
                    </div>
                </div>
                <div class="space-y-2 text-xs">
                    <div class="flex justify-between text-gray-500"><span>Interface</span><a href="https://dashboard.mahamexpo.sa" class="font-mono text-orange-400 hover:underline">dashboard.mahamexpo.sa</a></div>
                    <div class="flex justify-between text-gray-500"><span>Auth DB</span><span class="font-mono text-gray-400">auth-mysql:3306</span></div>
                    <div class="flex justify-between text-gray-500"><span>Expo DB</span><span class="font-mono text-gray-400">expo-mysql:3306</span></div>
                </div>
            </div>
        </div>
    </section> --}}

    <div class="glow-line max-w-4xl mx-auto"></div>

    <!-- Environment Variables -->
    <section id="env-vars" class="section-anchor max-w-7xl mx-auto px-6 py-16">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-1 h-8 bg-teal-500 rounded-full"></div>
            <h3 class="text-2xl font-bold">متغيرات البيئة</h3>
            <span class="text-xs bg-teal-500/10 text-teal-400 px-2.5 py-1 rounded-full">Environment Variables</span>
        </div>
        <p class="text-gray-500 text-sm mb-8 mr-4">جميع المتغيرات المطلوبة والاختيارية لتشغيل خدمة المصادقة</p>

        <!-- Required Variables -->
        <div class="mb-8">
            <div class="flex items-center gap-2 mb-4">
                <span class="w-2 h-2 bg-rose-400 rounded-full"></span>
                <h4 class="font-bold text-rose-400">Required - مطلوبة</h4>
                <span class="text-[10px] text-rose-400/60 bg-rose-500/10 px-2 py-0.5 rounded-full">يجب تعيينها</span>
            </div>
            <div class="glass border border-rose-500/10 rounded-2xl overflow-hidden">
                <div class="divide-y divide-white/5">
                    @php
                        $requiredVars = [
                            ['APP_KEY', 'base64:xxxxx', 'مفتاح التشفير الرئيسي للتطبيق', 'php artisan key:generate --show'],
                            ['DB_PASSWORD', '-', 'كلمة سر قاعدة البيانات MySQL', ''],
                            ['JWT_SECRET', '-', 'المفتاح السري لتوقيع JWT tokens', 'openssl rand -hex 32'],
                        ];
                    @endphp
                    @foreach($requiredVars as $v)
                    <div class="endpoint-row flex items-center px-6 py-3.5 gap-3">
                        <code class="text-xs bg-rose-500/10 px-2.5 py-1 rounded text-rose-300 font-mono min-w-[160px]">{{ $v[0] }}</code>
                        <span class="text-xs text-gray-500 flex-1">{{ $v[2] }}</span>
                        @if($v[3])
                        <code class="text-[10px] text-gray-600 font-mono bg-white/5 px-2 py-0.5 rounded hidden md:inline" dir="ltr">{{ $v[3] }}</code>
                        @endif
                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-rose-500/20 text-rose-400 font-bold">required</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Optional Variables -->
        <div class="mb-8">
            <div class="flex items-center gap-2 mb-4">
                <span class="w-2 h-2 bg-gray-400 rounded-full"></span>
                <h4 class="font-bold text-gray-400">Optional - اختيارية</h4>
                <span class="text-[10px] text-gray-500 bg-white/5 px-2 py-0.5 rounded-full">لها قيم افتراضية</span>
            </div>

            <!-- App & DB -->
            <div class="glass border border-white/5 rounded-2xl overflow-hidden mb-4">
                <div class="px-6 py-3 border-b border-white/5 bg-white/[0.02]">
                    <h5 class="text-xs font-bold text-gray-400">Application & Database</h5>
                </div>
                <div class="divide-y divide-white/5">
                    @php
                        $appVars = [
                            ['APP_NAME', 'Maham Auth Service', 'اسم التطبيق'],
                            ['APP_ENV', 'production', 'بيئة التشغيل'],
                            ['APP_DEBUG', 'false', 'وضع التصحيح'],
                            ['APP_URL', 'https://auth-service-api.mahamexpo.sa', 'رابط التطبيق الرئيسي'],
                            ['APP_LOCALE', 'ar', 'اللغة الافتراضية'],
                            ['DB_CONNECTION', 'mysql', 'نوع قاعدة البيانات'],
                            ['DB_HOST', 'auth-mysql', 'مضيف قاعدة البيانات'],
                            ['DB_PORT', '3306', 'منفذ قاعدة البيانات'],
                            ['DB_DATABASE', 'auth_service', 'اسم قاعدة البيانات'],
                            ['DB_USERNAME', 'auth_user', 'مستخدم قاعدة البيانات'],
                        ];
                    @endphp
                    @foreach($appVars as $v)
                    <div class="endpoint-row flex items-center px-6 py-2.5 gap-3">
                        <code class="text-xs bg-white/5 px-2.5 py-0.5 rounded text-indigo-300/70 font-mono min-w-[160px]">{{ $v[0] }}</code>
                        <code class="text-[10px] text-gray-600 font-mono">{{ $v[1] }}</code>
                        <span class="text-xs text-gray-600 flex-1 text-left" dir="rtl">{{ $v[2] }}</span>
                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-white/5 text-gray-500">optional</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Redis & Cache & Queue -->
            <div class="glass border border-white/5 rounded-2xl overflow-hidden mb-4">
                <div class="px-6 py-3 border-b border-white/5 bg-white/[0.02]">
                    <h5 class="text-xs font-bold text-gray-400">Redis & Cache & Queue</h5>
                </div>
                <div class="divide-y divide-white/5">
                    @php
                        $redisVars = [
                            ['REDIS_HOST', 'auth-redis', 'مضيف Redis'],
                            ['REDIS_PORT', '6379', 'منفذ Redis'],
                            ['REDIS_PASSWORD', '(empty)', 'كلمة سر Redis'],
                            ['CACHE_STORE', 'redis', 'محرك التخزين المؤقت'],
                            ['CACHE_PREFIX', 'auth_', 'بادئة مفاتيح الكاش'],
                            ['QUEUE_CONNECTION', 'redis', 'محرك قائمة المهام'],
                            ['SESSION_DRIVER', 'redis', 'محرك الجلسات'],
                            ['SESSION_LIFETIME', '120', 'عمر الجلسة (دقائق)'],
                        ];
                    @endphp
                    @foreach($redisVars as $v)
                    <div class="endpoint-row flex items-center px-6 py-2.5 gap-3">
                        <code class="text-xs bg-white/5 px-2.5 py-0.5 rounded text-indigo-300/70 font-mono min-w-[160px]">{{ $v[0] }}</code>
                        <code class="text-[10px] text-gray-600 font-mono">{{ $v[1] }}</code>
                        <span class="text-xs text-gray-600 flex-1 text-left" dir="rtl">{{ $v[2] }}</span>
                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-white/5 text-gray-500">optional</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- JWT & Security -->
            <div class="glass border border-white/5 rounded-2xl overflow-hidden mb-4">
                <div class="px-6 py-3 border-b border-white/5 bg-white/[0.02]">
                    <h5 class="text-xs font-bold text-gray-400">JWT & Security</h5>
                </div>
                <div class="divide-y divide-white/5">
                    @php
                        $jwtVars = [
                            ['JWT_TTL', '60', 'عمر التوكن (دقائق)'],
                            ['JWT_REFRESH_TTL', '20160', 'عمر تجديد التوكن (دقائق = 14 يوم)'],
                            ['JWT_ALGO', 'HS256', 'خوارزمية التشفير'],
                            ['JWT_BLACKLIST_ENABLED', 'true', 'تفعيل القائمة السوداء للتوكنات'],
                            ['TRUSTED_SERVICE_IPS', '172.0.0.0/8', 'عناوين IP الموثوقة للخدمات'],
                            ['RATE_LIMIT_PER_MINUTE', '60', 'حد الطلبات في الدقيقة'],
                            ['BCRYPT_ROUNDS', '12', 'جولات تشفير كلمات المرور'],
                        ];
                    @endphp
                    @foreach($jwtVars as $v)
                    <div class="endpoint-row flex items-center px-6 py-2.5 gap-3">
                        <code class="text-xs bg-white/5 px-2.5 py-0.5 rounded text-indigo-300/70 font-mono min-w-[160px]">{{ $v[0] }}</code>
                        <code class="text-[10px] text-gray-600 font-mono">{{ $v[1] }}</code>
                        <span class="text-xs text-gray-600 flex-1 text-left" dir="rtl">{{ $v[2] }}</span>
                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-white/5 text-gray-500">optional</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Logging & Mail -->
            <div class="glass border border-white/5 rounded-2xl overflow-hidden">
                <div class="px-6 py-3 border-b border-white/5 bg-white/[0.02]">
                    <h5 class="text-xs font-bold text-gray-400">Logging & Mail</h5>
                </div>
                <div class="divide-y divide-white/5">
                    @php
                        $logVars = [
                            ['LOG_CHANNEL', 'stack', 'قناة التسجيل'],
                            ['LOG_LEVEL', 'warning', 'مستوى التسجيل'],
                            ['MAIL_MAILER', 'log', 'محرك البريد (log, smtp, mailgun)'],
                            ['MAIL_HOST', '(empty)', 'مضيف SMTP'],
                            ['MAIL_PORT', '587', 'منفذ SMTP'],
                            ['MAIL_USERNAME', '(empty)', 'مستخدم SMTP'],
                            ['MAIL_PASSWORD', '(empty)', 'كلمة سر SMTP'],
                            ['MAIL_ENCRYPTION', 'tls', 'تشفير البريد'],
                            ['MAIL_FROM_ADDRESS', 'noreply@mahamexpo.sa', 'عنوان المرسل'],
                            ['FILESYSTEM_DISK', 'local', 'نظام تخزين الملفات'],
                        ];
                    @endphp
                    @foreach($logVars as $v)
                    <div class="endpoint-row flex items-center px-6 py-2.5 gap-3">
                        <code class="text-xs bg-white/5 px-2.5 py-0.5 rounded text-indigo-300/70 font-mono min-w-[160px]">{{ $v[0] }}</code>
                        <code class="text-[10px] text-gray-600 font-mono">{{ $v[1] }}</code>
                        <span class="text-xs text-gray-600 flex-1 text-left" dir="rtl">{{ $v[2] }}</span>
                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-white/5 text-gray-500">optional</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-white/5 mt-8">
        <div class="max-w-7xl mx-auto px-6 py-8 flex flex-col md:flex-row items-center justify-between gap-4 text-sm text-gray-600">
            <div class="flex items-center gap-3">
                <div class="w-7 h-7 bg-indigo-500/20 rounded-lg flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <span>Maham Auth Service v{{ config('auth-service.service_version', '1.0.0') }}</span>
            </div>
            <div class="flex items-center gap-4">
                <a href="https://expo-service-api.mahamexpo.sa/docs" class="hover:text-emerald-400 transition">Expo API</a>
                <a href="https://dashboard.mahamexpo.sa" class="hover:text-orange-400 transition">لوحة التحكم</a>
                <a href="/api/health" class="hover:text-indigo-400 transition">Health</a>
                <a href="/docs" class="hover:text-cyan-400 transition">API Docs</a>
            </div>
            <span>{{ date('Y') }} &copy; Maham Expo</span>
        </div>
    </footer>

    <script>
        // Health Check
        fetch('/api/health').then(r=>r.json()).then(d=>{
            const el=document.getElementById('healthStatus');
            if(d.status==='ok'){
                el.innerHTML='<span class="w-1.5 h-1.5 bg-emerald-400 rounded-full pulse-dot"></span> online';
                el.className='text-[10px] bg-emerald-500/10 text-emerald-400 px-2.5 py-1 rounded-full flex items-center gap-1.5 border border-emerald-500/20';
            }
        }).catch(()=>{});

        // Tab System
        function showTab(name) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
            document.getElementById('content-' + name).classList.remove('hidden');
            document.getElementById('tab-' + name).classList.add('active');
        }

        // Copy Code
        function copyCode(name) {
            const el = document.getElementById('code-' + name);
            const text = el.textContent;
            navigator.clipboard.writeText(text).then(() => {
                const btn = document.querySelector(`[data-target="${name}"]`);
                const orig = btn.innerHTML;
                btn.innerHTML = '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Copied!';
                btn.classList.add('text-emerald-400');
                setTimeout(() => { btn.innerHTML = orig; btn.classList.remove('text-emerald-400'); }, 2000);
            });
        }

        // Smooth scroll for nav links
        document.querySelectorAll('a[href^="#"]').forEach(a => {
            a.addEventListener('click', e => {
                e.preventDefault();
                document.querySelector(a.getAttribute('href'))?.scrollIntoView({ behavior: 'smooth' });
            });
        });
    </script>
</body>
</html>
