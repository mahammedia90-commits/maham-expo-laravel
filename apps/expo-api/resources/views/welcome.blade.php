<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maham Expo API - API Documentation</title>
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
        .gradient-bg { background: linear-gradient(145deg, #022c22 0%, #064e3b 30%, #1e1b4b 70%, #022c22 100%); }
        .card-hover { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .card-hover:hover { transform: translateY(-3px); box-shadow: 0 20px 40px rgba(0,0,0,0.3); border-color: rgba(16, 185, 129, 0.3); }
        .pulse-dot { animation: pulse-dot 2s ease-in-out infinite; }
        @keyframes pulse-dot { 0%, 100% { opacity: 1; transform: scale(1); } 50% { opacity: 0.4; transform: scale(0.8); } }
        .glow-green { box-shadow: 0 0 30px rgba(16, 185, 129, 0.4), 0 0 60px rgba(16, 185, 129, 0.1); }
        .glow-line { background: linear-gradient(90deg, transparent, rgba(16, 185, 129, 0.3), transparent); height: 1px; }
        .code-block { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); }
        .nav-link { position: relative; }
        .nav-link::after { content: ''; position: absolute; bottom: -2px; right: 0; width: 0; height: 2px; background: #10b981; transition: width 0.3s ease; }
        .nav-link:hover::after, .nav-link.active::after { width: 100%; }
        .endpoint-row { transition: all 0.2s ease; }
        .endpoint-row:hover { background: rgba(255,255,255,0.03); }
        .method-badge { font-size: 10px; letter-spacing: 0.5px; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        .glass { background: rgba(255,255,255,0.03); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); }
        .tab-btn.active { background: rgba(16, 185, 129, 0.2); color: #6ee7b7; border-color: rgba(16, 185, 129, 0.4); }
        .section-anchor { scroll-margin-top: 80px; }
        pre code .key { color: #6ee7b7; }
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
                <div class="w-9 h-9 bg-emerald-500 rounded-xl flex items-center justify-center glow-green">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-sm font-bold tracking-tight">Maham Expo</h1>
                    <p class="text-[10px] text-gray-500">API Documentation</p>
                </div>
            </div>
            <div class="hidden md:flex items-center gap-6 text-sm">
                <a href="#features" class="nav-link text-gray-400 hover:text-white transition">الميزات</a>
                <a href="#events-api" class="nav-link text-gray-400 hover:text-white transition">الفعاليات</a>
                <a href="#spaces-api" class="nav-link text-gray-400 hover:text-white transition">المساحات</a>
                <a href="#requests-api" class="nav-link text-gray-400 hover:text-white transition">الطلبات</a>
                <a href="#admin-api" class="nav-link text-gray-400 hover:text-white transition">الإدارة</a>
                <a href="#quickstart" class="nav-link text-gray-400 hover:text-white transition">البدء السريع</a>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-[10px] bg-emerald-500/20 text-emerald-400 px-2.5 py-1 rounded-full font-semibold border border-emerald-500/20">v{{ config('expo-api.service_version', '1.0.0') }}</span>
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
            <div class="absolute top-20 left-1/4 w-72 h-72 bg-emerald-500/20 rounded-full blur-[120px]"></div>
            <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-teal-500/10 rounded-full blur-[150px]"></div>
        </div>
        <div class="max-w-7xl mx-auto px-6 text-center relative z-10">
            <div class="inline-flex items-center gap-2 bg-white/5 border border-white/10 rounded-full px-4 py-1.5 mb-8">
                <span class="w-2 h-2 bg-emerald-400 rounded-full pulse-dot"></span>
                <span class="text-xs text-gray-400">منصة المعارض تعمل بنجاح</span>
            </div>
            <h2 class="text-5xl md:text-6xl font-black mb-6 leading-tight">
                <span class="bg-gradient-to-l from-emerald-400 via-teal-400 to-cyan-400 bg-clip-text text-transparent">منصة إدارة</span>
                <br>
                <span class="text-white/90">المعارض والفعاليات</span>
            </h2>
            <p class="text-gray-400 text-lg max-w-2xl mx-auto mb-10 leading-relaxed">
                API متكامل لإدارة الفعاليات، المساحات، طلبات التأجير والزيارة مع نظام ملفات تجارية ومصادقة عبر <span class="text-emerald-400 font-semibold">Auth Service</span>
            </p>
            <div class="flex items-center justify-center gap-4 flex-wrap">
                <a href="#events-api" class="bg-emerald-500 hover:bg-emerald-600 text-white px-7 py-3 rounded-xl font-bold transition-all hover:shadow-lg hover:shadow-emerald-500/25 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    تصفح الـ API
                </a>
                <a href="/api/health" target="_blank" class="bg-white/5 hover:bg-white/10 text-white px-7 py-3 rounded-xl font-bold transition-all border border-white/10 hover:border-white/20 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Health Check
                </a>
                <a href="http://localhost:8001" class="bg-white/5 hover:bg-white/10 text-white px-7 py-3 rounded-xl font-bold transition-all border border-white/10 hover:border-white/20 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    Auth Service
                </a>
            </div>
        </div>
    </section>

    <div class="glow-line max-w-4xl mx-auto"></div>

    <!-- Stats -->
    <section class="max-w-7xl mx-auto px-6 py-12">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="glass border border-white/5 rounded-2xl p-5 text-center">
                <div class="text-3xl font-black text-emerald-400 mb-1">Events</div>
                <div class="text-xs text-gray-500">Exhibitions & Events</div>
            </div>
            <div class="glass border border-white/5 rounded-2xl p-5 text-center">
                <div class="text-3xl font-black text-blue-400 mb-1">Spaces</div>
                <div class="text-xs text-gray-500">Exhibition Spaces</div>
            </div>
            <div class="glass border border-white/5 rounded-2xl p-5 text-center">
                <div class="text-3xl font-black text-amber-400 mb-1">Rentals</div>
                <div class="text-xs text-gray-500">Space Rental System</div>
            </div>
            <div class="glass border border-white/5 rounded-2xl p-5 text-center">
                <div class="text-3xl font-black text-purple-400 mb-1">Profiles</div>
                <div class="text-xs text-gray-500">Business Profiles</div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section id="features" class="section-anchor max-w-7xl mx-auto px-6 pb-16">
        <div class="flex items-center gap-3 mb-8">
            <div class="w-1 h-8 bg-emerald-500 rounded-full"></div>
            <h3 class="text-2xl font-bold">الميزات الرئيسية</h3>
        </div>
        <div class="grid md:grid-cols-3 gap-4">
            @php
                $features = [
                    ['إدارة الفعاليات', 'إنشاء وإدارة الفعاليات والمعارض مع دعم الفعاليات المميزة والتصنيفات والمدن والبحث المتقدم.', 'emerald', 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                    ['إدارة المساحات', 'مساحات عرض متعددة الأحجام والأسعار مرتبطة بالفعاليات مع إدارة التوفر والسعة.', 'blue', 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                    ['طلبات التأجير', 'نظام طلبات تأجير المساحات مع دعم الموافقة والرفض وتتبع المدفوعات وحساب الأسعار.', 'amber', 'M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                    ['طلبات الزيارة', 'حجز زيارات للفعاليات مع تحديد التاريخ وعدد الزوار والموافقة من الإدارة.', 'purple', 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z'],
                    ['الملفات التجارية', 'ملفات تجارية للمستثمرين والتجار مع نظام تحقق وموافقة ورفع المستندات الرسمية.', 'rose', 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                    ['المفضلة والإشعارات', 'نظام مفضلة للفعاليات والمساحات مع إشعارات فورية للتحديثات والموافقات.', 'cyan', 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z'],
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

    <!-- Events & Categories -->
    <section id="events-api" class="section-anchor max-w-7xl mx-auto px-6 py-16">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-1 h-8 bg-emerald-500 rounded-full"></div>
            <h3 class="text-2xl font-bold">الفعاليات والتصنيفات</h3>
            <span class="text-xs bg-emerald-500/10 text-emerald-400 px-2.5 py-1 rounded-full">Events & Categories</span>
        </div>
        <p class="text-gray-500 text-sm mb-8 mr-4">عرض الفعاليات، البحث المتقدم، التصنيفات والمدن</p>

        <!-- GET /api/events -->
        <div class="mb-8 glass border border-white/5 rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-white/5 flex items-center gap-3">
                <span class="method-badge font-bold px-2.5 py-1 rounded-md bg-emerald-500/20 text-emerald-400 font-mono">GET</span>
                <code class="text-sm text-gray-300 font-mono" dir="ltr">/api/events</code>
                <span class="text-xs text-gray-600 mr-auto">قائمة الفعاليات مع فلترة متقدمة</span>
                <span class="text-[10px] px-2 py-0.5 rounded-full bg-green-500/10 text-green-400 font-semibold">public</span>
            </div>
            <div class="grid md:grid-cols-2 divide-y md:divide-y-0 md:divide-x md:divide-x-reverse divide-white/5">
                <div class="p-6">
                    <h5 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Query Parameters</h5>
                    <div class="space-y-3">
                        <div class="flex items-start gap-2">
                            <code class="text-xs bg-white/5 px-2 py-0.5 rounded text-emerald-300 font-mono">search</code>
                            <span class="text-[10px] text-gray-500 font-bold">optional</span>
                            <span class="text-xs text-gray-500">string - البحث بالاسم أو الوصف</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <code class="text-xs bg-white/5 px-2 py-0.5 rounded text-emerald-300 font-mono">city_id</code>
                            <span class="text-[10px] text-gray-500 font-bold">optional</span>
                            <span class="text-xs text-gray-500">uuid - فلترة بالمدينة</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <code class="text-xs bg-white/5 px-2 py-0.5 rounded text-emerald-300 font-mono">category_id</code>
                            <span class="text-[10px] text-gray-500 font-bold">optional</span>
                            <span class="text-xs text-gray-500">uuid - فلترة بالتصنيف</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <code class="text-xs bg-white/5 px-2 py-0.5 rounded text-emerald-300 font-mono">status</code>
                            <span class="text-[10px] text-gray-500 font-bold">optional</span>
                            <span class="text-xs text-gray-500">ongoing | upcoming</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <code class="text-xs bg-white/5 px-2 py-0.5 rounded text-emerald-300 font-mono">featured</code>
                            <span class="text-[10px] text-gray-500 font-bold">optional</span>
                            <span class="text-xs text-gray-500">boolean - المميزة فقط</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <code class="text-xs bg-white/5 px-2 py-0.5 rounded text-emerald-300 font-mono">per_page</code>
                            <span class="text-[10px] text-gray-500 font-bold">optional</span>
                            <span class="text-xs text-gray-500">int - عدد النتائج (max: 50, default: 15)</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <code class="text-xs bg-white/5 px-2 py-0.5 rounded text-emerald-300 font-mono">sort_by</code>
                            <span class="text-[10px] text-gray-500 font-bold">optional</span>
                            <span class="text-xs text-gray-500">string - ترتيب حسب (default: start_date)</span>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <h5 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Response <span class="text-emerald-400">200</span></h5>
                    <div class="code-block rounded-xl p-4 text-xs overflow-x-auto" dir="ltr">
<pre class="text-gray-300"><code>{
  <span class="key">"success"</span>: <span class="text-emerald-400">true</span>,
  <span class="key">"data"</span>: [
    {
      <span class="key">"id"</span>: <span class="str">"uuid"</span>,
      <span class="key">"name"</span>: <span class="str">"معرض الرياض"</span>,
      <span class="key">"city"</span>: <span class="str">"الرياض"</span>,
      <span class="key">"category"</span>: <span class="str">"تقنية"</span>,
      <span class="key">"start_date"</span>: <span class="str">"2025-03-01"</span>,
      <span class="key">"is_featured"</span>: <span class="text-emerald-400">true</span>,
      <span class="key">"spaces_count"</span>: <span class="num">24</span>
    }
  ],
  <span class="key">"pagination"</span>: {
    <span class="key">"current_page"</span>: <span class="num">1</span>,
    <span class="key">"total"</span>: <span class="num">100</span>,
    <span class="key">"per_page"</span>: <span class="num">15</span>
  }
}</code></pre>
                    </div>
                </div>
            </div>
        </div>

        <!-- Other Public Event endpoints -->
        <div class="grid md:grid-cols-2 gap-4 mb-8">
            <div class="glass border border-white/5 rounded-2xl p-5">
                <div class="flex items-center gap-2 mb-3">
                    <span class="method-badge font-bold px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-400 font-mono">GET</span>
                    <code class="text-xs text-gray-400 font-mono" dir="ltr">/api/events/featured</code>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-green-500/10 text-green-400 font-semibold mr-auto">public</span>
                </div>
                <p class="text-xs text-gray-500 mb-2">الفعاليات المميزة</p>
                <div class="text-xs text-gray-600 space-y-1">
                    <div>Param: <code class="text-emerald-300/60">limit</code> (default: 10, max: 20)</div>
                    <div>Returns: EventListResource[]</div>
                </div>
            </div>
            <div class="glass border border-white/5 rounded-2xl p-5">
                <div class="flex items-center gap-2 mb-3">
                    <span class="method-badge font-bold px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-400 font-mono">GET</span>
                    <code class="text-xs text-gray-400 font-mono" dir="ltr">/api/events/{id}</code>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-green-500/10 text-green-400 font-semibold mr-auto">public</span>
                </div>
                <p class="text-xs text-gray-500 mb-2">تفاصيل فعالية (يزيد عدد المشاهدات)</p>
                <div class="text-xs text-gray-600 space-y-1">
                    <div>Returns: EventResource + spaces_count</div>
                    <div class="text-amber-400/60">404 إذا غير منشورة</div>
                </div>
            </div>
        </div>

        <!-- Categories & Cities -->
        <div class="glass border border-white/5 rounded-2xl overflow-hidden">
            <div class="px-6 py-3 border-b border-white/5 bg-white/[0.02]">
                <h5 class="text-xs font-bold text-gray-400">التصنيفات والمدن</h5>
            </div>
            <div class="divide-y divide-white/5">
                @php
                    $catEps = [
                        ['GET', '/api/categories', 'قائمة التصنيفات', 'public'],
                        ['GET', '/api/categories/{id}', 'تفاصيل تصنيف', 'public'],
                        ['GET', '/api/cities', 'قائمة المدن', 'public'],
                        ['GET', '/api/cities/{id}', 'تفاصيل مدينة', 'public'],
                        ['GET', '/api/events/{id}/spaces', 'مساحات الفعالية', 'public'],
                    ];
                @endphp
                @foreach($catEps as $ep)
                <div class="endpoint-row flex items-center px-6 py-3 gap-3">
                    <span class="method-badge font-bold px-2 py-0.5 rounded font-mono min-w-[56px] text-center bg-emerald-500/20 text-emerald-400">{{ $ep[0] }}</span>
                    <code class="text-xs text-gray-300 font-mono whitespace-nowrap" dir="ltr">{{ $ep[1] }}</code>
                    <span class="text-xs text-gray-600">{{ $ep[2] }}</span>
                    <span class="mr-auto"></span>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-green-500/10 text-green-400 font-semibold">{{ $ep[3] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <div class="glow-line max-w-4xl mx-auto"></div>

    <!-- Spaces API -->
    <section id="spaces-api" class="section-anchor max-w-7xl mx-auto px-6 py-16">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-1 h-8 bg-blue-500 rounded-full"></div>
            <h3 class="text-2xl font-bold">المساحات</h3>
            <span class="text-xs bg-blue-500/10 text-blue-400 px-2.5 py-1 rounded-full">Spaces</span>
        </div>
        <p class="text-gray-500 text-sm mb-8 mr-4">عرض وإدارة مساحات العرض المرتبطة بالفعاليات</p>

        <div class="glass border border-white/5 rounded-2xl p-5 mb-8">
            <div class="flex items-center gap-2 mb-4">
                <span class="method-badge font-bold px-2.5 py-1 rounded-md bg-emerald-500/20 text-emerald-400 font-mono">GET</span>
                <code class="text-sm text-gray-300 font-mono" dir="ltr">/api/spaces/{id}</code>
                <span class="text-[10px] px-2 py-0.5 rounded-full bg-green-500/10 text-green-400 font-semibold mr-auto">public</span>
            </div>
            <p class="text-xs text-gray-500 mb-4">تفاصيل مساحة عرض تشمل الأبعاد، السعر، الحالة والفعالية المرتبطة</p>
            <div class="code-block rounded-xl p-4 text-xs overflow-x-auto" dir="ltr">
<pre class="text-gray-300"><code>{
  <span class="key">"success"</span>: <span class="text-emerald-400">true</span>,
  <span class="key">"data"</span>: {
    <span class="key">"id"</span>: <span class="str">"uuid"</span>,
    <span class="key">"name"</span>: <span class="str">"مساحة A1"</span>,
    <span class="key">"size"</span>: <span class="str">"3x3"</span>,
    <span class="key">"price_per_day"</span>: <span class="num">500.00</span>,
    <span class="key">"price_total"</span>: <span class="num">5000.00</span>,
    <span class="key">"status"</span>: <span class="str">"available"</span>,
    <span class="key">"event"</span>: { <span class="key">"id"</span>: <span class="str">"uuid"</span>, <span class="key">"name"</span>: <span class="str">"..."</span> }
  }
}</code></pre>
            </div>
        </div>
    </section>

    <div class="glow-line max-w-4xl mx-auto"></div>

    <!-- Requests API (Visit + Rental + Profile + Favorites + Notifications) -->
    <section id="requests-api" class="section-anchor max-w-7xl mx-auto px-6 py-16">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-1 h-8 bg-amber-500 rounded-full"></div>
            <h3 class="text-2xl font-bold">الطلبات والملفات</h3>
            <span class="text-xs bg-amber-500/10 text-amber-400 px-2.5 py-1 rounded-full">Requests & Profiles</span>
        </div>
        <p class="text-gray-500 text-sm mb-8 mr-4">طلبات الزيارة والتأجير، الملفات التجارية، المفضلة والإشعارات (تتطلب مصادقة)</p>

        <div class="p-3 bg-blue-500/5 border border-blue-500/10 rounded-xl mb-8">
            <p class="text-xs text-blue-400/80 flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <span>جميع الـ Endpoints في هذا القسم تتطلب مصادقة عبر Auth Service. يتم إرسال التوكن في Header: <code class="text-blue-300">Authorization: Bearer {token}</code></span>
            </p>
        </div>

        <!-- Visit Requests -->
        <div class="mb-8 glass border border-white/5 rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-white/5 flex items-center gap-3">
                <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <h4 class="font-bold text-purple-400">طلبات الزيارة</h4>
                <span class="text-xs text-gray-600">Visit Requests</span>
            </div>
            <div class="divide-y divide-white/5">
                @php
                    $visitEps = [
                        ['GET', '/api/visit-requests', 'طلباتي', 'قائمة طلبات الزيارة للمستخدم الحالي'],
                        ['POST', '/api/visit-requests', 'إنشاء طلب', 'event_id (uuid), visit_date (date)'],
                        ['GET', '/api/visit-requests/{id}', 'تفاصيل طلب', 'بيانات الطلب مع حالة الموافقة'],
                        ['PUT', '/api/visit-requests/{id}', 'تعديل طلب', 'تعديل قبل الموافقة فقط'],
                        ['DELETE', '/api/visit-requests/{id}', 'إلغاء طلب', 'حذف الطلب'],
                    ];
                @endphp
                @foreach($visitEps as $ep)
                <div class="endpoint-row flex items-center px-6 py-3 gap-3">
                    <span class="method-badge font-bold px-2 py-0.5 rounded font-mono min-w-[56px] text-center {{ $ep[0] === 'GET' ? 'bg-emerald-500/20 text-emerald-400' : ($ep[0] === 'POST' ? 'bg-blue-500/20 text-blue-400' : ($ep[0] === 'PUT' ? 'bg-amber-500/20 text-amber-400' : 'bg-rose-500/20 text-rose-400')) }}">{{ $ep[0] }}</span>
                    <code class="text-xs text-gray-300 font-mono whitespace-nowrap" dir="ltr">{{ $ep[1] }}</code>
                    <span class="text-xs text-gray-600">{{ $ep[2] }}</span>
                    <span class="mr-auto"></span>
                    <span class="text-[10px] text-gray-600 hidden md:block">{{ $ep[3] }}</span>
                </div>
                @endforeach
            </div>
            <div class="p-4 bg-white/[0.02]">
                <p class="text-[10px] text-amber-400/60"><span class="font-bold">ملاحظة:</span> لا يمكن إنشاء طلب زيارة مكرر لنفس الفعالية والتاريخ إذا كان هناك طلب pending أو approved</p>
            </div>
        </div>

        <!-- Rental Requests -->
        <div class="mb-8 glass border border-white/5 rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-white/5 flex items-center gap-3">
                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <h4 class="font-bold text-amber-400">طلبات التأجير</h4>
                <span class="text-xs text-gray-600">Rental Requests</span>
                <span class="text-[10px] px-2 py-0.5 rounded-full bg-rose-500/10 text-rose-400 mr-auto">يتطلب ملف تجاري موثق</span>
            </div>
            <div class="grid md:grid-cols-2 divide-y md:divide-y-0 md:divide-x md:divide-x-reverse divide-white/5">
                <div class="p-6">
                    <h5 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">POST /api/rental-requests</h5>
                    <div class="space-y-3">
                        <div class="flex items-start gap-2">
                            <code class="text-xs bg-white/5 px-2 py-0.5 rounded text-amber-300 font-mono">space_id</code>
                            <span class="text-[10px] text-rose-400 font-bold">required</span>
                            <span class="text-xs text-gray-500">uuid - معرف المساحة</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <code class="text-xs bg-white/5 px-2 py-0.5 rounded text-amber-300 font-mono">start_date</code>
                            <span class="text-[10px] text-rose-400 font-bold">required</span>
                            <span class="text-xs text-gray-500">date - تاريخ البداية</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <code class="text-xs bg-white/5 px-2 py-0.5 rounded text-amber-300 font-mono">end_date</code>
                            <span class="text-[10px] text-rose-400 font-bold">required</span>
                            <span class="text-xs text-gray-500">date - تاريخ النهاية</span>
                        </div>
                    </div>
                    <div class="mt-4 p-3 bg-amber-500/5 border border-amber-500/10 rounded-lg">
                        <p class="text-[10px] text-amber-400/80"><span class="font-bold">حساب السعر:</span> (price_per_day × عدد الأيام) أو price_total للمساحة</p>
                    </div>
                </div>
                <div class="p-6">
                    <h5 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">CRUD Endpoints</h5>
                    <div class="space-y-2">
                        @php
                            $rentalEps = [
                                ['GET', '/api/rental-requests', 'قائمة طلباتي'],
                                ['GET', '/api/rental-requests/{id}', 'تفاصيل طلب'],
                                ['PUT', '/api/rental-requests/{id}', 'تعديل طلب'],
                                ['DELETE', '/api/rental-requests/{id}', 'إلغاء طلب'],
                            ];
                        @endphp
                        @foreach($rentalEps as $ep)
                        <div class="flex items-center gap-2">
                            <span class="method-badge font-bold px-1.5 py-0.5 rounded font-mono min-w-[40px] text-center {{ $ep[0] === 'GET' ? 'bg-emerald-500/15 text-emerald-400' : ($ep[0] === 'PUT' ? 'bg-amber-500/15 text-amber-400' : 'bg-rose-500/15 text-rose-400') }}">{{ $ep[0] }}</span>
                            <code class="text-[11px] text-gray-400 font-mono" dir="ltr">{{ $ep[1] }}</code>
                            <span class="text-[11px] text-gray-600 mr-auto">{{ $ep[2] }}</span>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-4 p-3 bg-rose-500/5 border border-rose-500/10 rounded-lg">
                        <p class="text-[10px] text-rose-400/80"><span class="font-bold">شرط:</span> لازم يكون عندك ملف تجاري موثق (approved) عشان تقدر تقدم طلب تأجير</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Business Profile -->
        <div class="mb-8 glass border border-white/5 rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-white/5 flex items-center gap-3">
                <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <h4 class="font-bold text-rose-400">الملف التجاري</h4>
                <span class="text-xs text-gray-600">Business Profile</span>
            </div>
            <div class="grid md:grid-cols-2 divide-y md:divide-y-0 md:divide-x md:divide-x-reverse divide-white/5">
                <div class="p-6">
                    <h5 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">POST /api/profile <span class="text-rose-400">(multipart/form-data)</span></h5>
                    <div class="space-y-3">
                        <div class="flex items-start gap-2">
                            <code class="text-xs bg-white/5 px-2 py-0.5 rounded text-rose-300 font-mono">commercial_registration_image</code>
                            <span class="text-[10px] text-rose-400 font-bold">file</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <code class="text-xs bg-white/5 px-2 py-0.5 rounded text-rose-300 font-mono">national_id_image</code>
                            <span class="text-[10px] text-rose-400 font-bold">file</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <code class="text-xs bg-white/5 px-2 py-0.5 rounded text-rose-300 font-mono">company_logo</code>
                            <span class="text-[10px] text-rose-400 font-bold">file</span>
                        </div>
                    </div>
                    <p class="text-[10px] text-gray-600 mt-3">+ بيانات الشركة الإضافية حسب النموذج</p>
                </div>
                <div class="p-6">
                    <h5 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Endpoints</h5>
                    <div class="space-y-3">
                        <div class="flex items-center gap-2">
                            <span class="method-badge font-bold px-1.5 py-0.5 rounded bg-emerald-500/15 text-emerald-400 font-mono">GET</span>
                            <code class="text-[11px] text-gray-400 font-mono" dir="ltr">/api/profile</code>
                            <span class="text-[11px] text-gray-600 mr-auto">عرض الملف</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="method-badge font-bold px-1.5 py-0.5 rounded bg-blue-500/15 text-blue-400 font-mono">POST</span>
                            <code class="text-[11px] text-gray-400 font-mono" dir="ltr">/api/profile</code>
                            <span class="text-[11px] text-gray-600 mr-auto">إنشاء ملف</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="method-badge font-bold px-1.5 py-0.5 rounded bg-amber-500/15 text-amber-400 font-mono">PUT</span>
                            <code class="text-[11px] text-gray-400 font-mono" dir="ltr">/api/profile</code>
                            <span class="text-[11px] text-gray-600 mr-auto">تحديث الملف</span>
                        </div>
                    </div>
                    <div class="mt-4 p-3 bg-rose-500/5 border border-rose-500/10 rounded-lg">
                        <p class="text-[10px] text-rose-400/80">ملف واحد فقط لكل مستخدم. يتم التحقق والموافقة من الإدارة.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Favorites & Notifications -->
        <div class="grid md:grid-cols-2 gap-6">
            <div class="glass border border-white/5 rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-white/5">
                    <h4 class="font-bold text-cyan-400 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        المفضلة - Favorites
                    </h4>
                </div>
                <div class="divide-y divide-white/5">
                    @php
                        $favEps = [
                            ['GET', '/api/favorites', 'قائمة المفضلة'],
                            ['POST', '/api/favorites', 'إضافة مفضلة'],
                            ['DELETE', '/api/favorites/{id}', 'إزالة مفضلة'],
                        ];
                    @endphp
                    @foreach($favEps as $ep)
                    <div class="endpoint-row flex items-center px-5 py-2.5 gap-2">
                        <span class="method-badge font-bold px-1.5 py-0.5 rounded font-mono min-w-[44px] text-center {{ $ep[0] === 'GET' ? 'bg-emerald-500/15 text-emerald-400' : ($ep[0] === 'POST' ? 'bg-blue-500/15 text-blue-400' : 'bg-rose-500/15 text-rose-400') }}">{{ $ep[0] }}</span>
                        <code class="text-[11px] text-gray-400 font-mono" dir="ltr">{{ $ep[1] }}</code>
                        <span class="text-[11px] text-gray-600 mr-auto">{{ $ep[2] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="glass border border-white/5 rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-white/5">
                    <h4 class="font-bold text-amber-400 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        الإشعارات - Notifications
                    </h4>
                </div>
                <div class="divide-y divide-white/5">
                    @php
                        $notifEps = [
                            ['GET', '/api/notifications', 'قائمة الإشعارات'],
                            ['GET', '/api/notifications/unread-count', 'عدد غير المقروءة'],
                            ['PUT', '/api/notifications/{id}/read', 'تعليم كمقروء'],
                            ['PUT', '/api/notifications/read-all', 'قراءة الكل'],
                        ];
                    @endphp
                    @foreach($notifEps as $ep)
                    <div class="endpoint-row flex items-center px-5 py-2.5 gap-2">
                        <span class="method-badge font-bold px-1.5 py-0.5 rounded font-mono min-w-[44px] text-center {{ $ep[0] === 'GET' ? 'bg-emerald-500/15 text-emerald-400' : 'bg-amber-500/15 text-amber-400' }}">{{ $ep[0] }}</span>
                        <code class="text-[11px] text-gray-400 font-mono" dir="ltr">{{ $ep[1] }}</code>
                        <span class="text-[11px] text-gray-600 mr-auto">{{ $ep[2] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <div class="glow-line max-w-4xl mx-auto"></div>

    <!-- Admin API -->
    <section id="admin-api" class="section-anchor max-w-7xl mx-auto px-6 py-16">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-1 h-8 bg-rose-500 rounded-full"></div>
            <h3 class="text-2xl font-bold">لوحة الإدارة</h3>
            <span class="text-xs bg-rose-500/10 text-rose-400 px-2.5 py-1 rounded-full">Admin Panel</span>
        </div>
        <p class="text-gray-500 text-sm mb-4 mr-4">إدارة الفعاليات، المساحات، الطلبات والملفات التجارية</p>

        <div class="p-3 bg-rose-500/5 border border-rose-500/10 rounded-xl mb-8">
            <p class="text-xs text-rose-400/80 flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>جميع endpoints الإدارة تتطلب دور <code class="text-rose-300 font-bold">admin</code> أو <code class="text-rose-300 font-bold">super-admin</code></span>
            </p>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            <!-- Admin Events -->
            <div class="glass border border-white/5 rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-white/5">
                    <h4 class="font-bold text-emerald-400 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        إدارة الفعاليات
                    </h4>
                </div>
                <div class="divide-y divide-white/5">
                    @php
                        $adminEventEps = [
                            ['GET', '/api/admin/events', 'القائمة'],
                            ['POST', '/api/admin/events', 'إنشاء فعالية'],
                            ['GET', '/api/admin/events/{id}', 'التفاصيل'],
                            ['PUT', '/api/admin/events/{id}', 'تحديث'],
                            ['DELETE', '/api/admin/events/{id}', 'حذف'],
                        ];
                    @endphp
                    @foreach($adminEventEps as $ep)
                    <div class="endpoint-row flex items-center px-5 py-2.5 gap-2">
                        <span class="method-badge font-bold px-1.5 py-0.5 rounded font-mono min-w-[44px] text-center {{ $ep[0] === 'GET' ? 'bg-emerald-500/15 text-emerald-400' : ($ep[0] === 'POST' ? 'bg-blue-500/15 text-blue-400' : ($ep[0] === 'PUT' ? 'bg-amber-500/15 text-amber-400' : 'bg-rose-500/15 text-rose-400')) }}">{{ $ep[0] }}</span>
                        <code class="text-[11px] text-gray-400 font-mono" dir="ltr">{{ $ep[1] }}</code>
                        <span class="text-[11px] text-gray-600 mr-auto">{{ $ep[2] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Admin Spaces -->
            <div class="glass border border-white/5 rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-white/5">
                    <h4 class="font-bold text-blue-400 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
                        إدارة المساحات
                    </h4>
                </div>
                <div class="divide-y divide-white/5">
                    @php
                        $adminSpaceEps = [
                            ['GET', '/api/admin/events/{id}/spaces', 'مساحات فعالية'],
                            ['POST', '/api/admin/events/{id}/spaces', 'إنشاء مساحة'],
                            ['GET', '/api/admin/spaces/{id}', 'تفاصيل مساحة'],
                            ['PUT', '/api/admin/spaces/{id}', 'تحديث مساحة'],
                            ['DELETE', '/api/admin/spaces/{id}', 'حذف مساحة'],
                        ];
                    @endphp
                    @foreach($adminSpaceEps as $ep)
                    <div class="endpoint-row flex items-center px-5 py-2.5 gap-2">
                        <span class="method-badge font-bold px-1.5 py-0.5 rounded font-mono min-w-[44px] text-center {{ $ep[0] === 'GET' ? 'bg-emerald-500/15 text-emerald-400' : ($ep[0] === 'POST' ? 'bg-blue-500/15 text-blue-400' : ($ep[0] === 'PUT' ? 'bg-amber-500/15 text-amber-400' : 'bg-rose-500/15 text-rose-400')) }}">{{ $ep[0] }}</span>
                        <code class="text-[11px] text-gray-400 font-mono" dir="ltr">{{ $ep[1] }}</code>
                        <span class="text-[11px] text-gray-600 mr-auto">{{ $ep[2] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Admin Visit Requests -->
            <div class="glass border border-white/5 rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-white/5">
                    <h4 class="font-bold text-purple-400 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        إدارة طلبات الزيارة
                    </h4>
                </div>
                <div class="divide-y divide-white/5">
                    @php
                        $adminVisitEps = [
                            ['GET', '/api/admin/visit-requests', 'جميع الطلبات'],
                            ['GET', '/api/admin/visit-requests/{id}', 'تفاصيل طلب'],
                            ['PUT', '/api/admin/visit-requests/{id}/approve', 'موافقة'],
                            ['PUT', '/api/admin/visit-requests/{id}/reject', 'رفض'],
                        ];
                    @endphp
                    @foreach($adminVisitEps as $ep)
                    <div class="endpoint-row flex items-center px-5 py-2.5 gap-2">
                        <span class="method-badge font-bold px-1.5 py-0.5 rounded font-mono min-w-[44px] text-center {{ $ep[0] === 'GET' ? 'bg-emerald-500/15 text-emerald-400' : 'bg-amber-500/15 text-amber-400' }}">{{ $ep[0] }}</span>
                        <code class="text-[11px] text-gray-400 font-mono" dir="ltr">{{ $ep[1] }}</code>
                        <span class="text-[11px] text-gray-600 mr-auto">{{ $ep[2] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Admin Rental Requests -->
            <div class="glass border border-white/5 rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-white/5">
                    <h4 class="font-bold text-amber-400 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2"/></svg>
                        إدارة طلبات التأجير
                    </h4>
                </div>
                <div class="divide-y divide-white/5">
                    @php
                        $adminRentalEps = [
                            ['GET', '/api/admin/rental-requests', 'جميع الطلبات'],
                            ['GET', '/api/admin/rental-requests/{id}', 'تفاصيل طلب'],
                            ['PUT', '/api/admin/rental-requests/{id}/approve', 'موافقة'],
                            ['PUT', '/api/admin/rental-requests/{id}/reject', 'رفض'],
                            ['POST', '/api/admin/rental-requests/{id}/payment', 'تسجيل دفعة'],
                        ];
                    @endphp
                    @foreach($adminRentalEps as $ep)
                    <div class="endpoint-row flex items-center px-5 py-2.5 gap-2">
                        <span class="method-badge font-bold px-1.5 py-0.5 rounded font-mono min-w-[44px] text-center {{ $ep[0] === 'GET' ? 'bg-emerald-500/15 text-emerald-400' : ($ep[0] === 'POST' ? 'bg-blue-500/15 text-blue-400' : 'bg-amber-500/15 text-amber-400') }}">{{ $ep[0] }}</span>
                        <code class="text-[11px] text-gray-400 font-mono" dir="ltr">{{ $ep[1] }}</code>
                        <span class="text-[11px] text-gray-600 mr-auto">{{ $ep[2] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Admin Business Profiles -->
        <div class="mt-6 glass border border-white/5 rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-white/5">
                <h4 class="font-bold text-rose-400 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    إدارة الملفات التجارية
                </h4>
            </div>
            <div class="divide-y divide-white/5">
                @php
                    $adminProfileEps = [
                        ['GET', '/api/admin/profiles', 'جميع الملفات'],
                        ['GET', '/api/admin/profiles/{id}', 'تفاصيل ملف'],
                        ['PUT', '/api/admin/profiles/{id}/approve', 'موافقة على ملف'],
                        ['PUT', '/api/admin/profiles/{id}/reject', 'رفض ملف'],
                    ];
                @endphp
                @foreach($adminProfileEps as $ep)
                <div class="endpoint-row flex items-center px-6 py-3 gap-3">
                    <span class="method-badge font-bold px-2 py-0.5 rounded font-mono min-w-[56px] text-center {{ $ep[0] === 'GET' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-amber-500/20 text-amber-400' }}">{{ $ep[0] }}</span>
                    <code class="text-xs text-gray-300 font-mono whitespace-nowrap" dir="ltr">{{ $ep[1] }}</code>
                    <span class="text-xs text-gray-600 mr-auto">{{ $ep[2] }}</span>
                    <span class="text-[9px] px-2 py-0.5 rounded-full bg-rose-500/10 text-rose-400/70 font-mono">admin</span>
                </div>
                @endforeach
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
            <button onclick="showTab('events')" class="tab-btn active text-xs px-4 py-2 rounded-lg border border-white/10 text-gray-400 hover:text-white transition whitespace-nowrap" id="tab-events">الفعاليات</button>
            <button onclick="showTab('visit')" class="tab-btn text-xs px-4 py-2 rounded-lg border border-white/10 text-gray-400 hover:text-white transition whitespace-nowrap" id="tab-visit">طلب زيارة</button>
            <button onclick="showTab('rental')" class="tab-btn text-xs px-4 py-2 rounded-lg border border-white/10 text-gray-400 hover:text-white transition whitespace-nowrap" id="tab-rental">طلب تأجير</button>
            <button onclick="showTab('admin')" class="tab-btn text-xs px-4 py-2 rounded-lg border border-white/10 text-gray-400 hover:text-white transition whitespace-nowrap" id="tab-admin">إنشاء فعالية</button>
        </div>

        <!-- Events Tab -->
        <div id="content-events" class="tab-content">
            <div class="code-block border border-white/5 rounded-2xl overflow-hidden">
                <div class="px-5 py-3 border-b border-white/5 flex items-center justify-between bg-white/[0.02]">
                    <span class="text-xs text-gray-500">Get Featured Events</span>
                    <button onclick="copyCode('events')" class="text-xs text-gray-500 hover:text-white transition flex items-center gap-1 copy-btn" data-target="events">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        Copy
                    </button>
                </div>
                <pre class="p-5 text-sm overflow-x-auto" dir="ltr"><code class="text-gray-300" id="code-events"><span class="cmd">curl</span> <span class="url">http://localhost:8002/api/events/featured?limit=5</span></code></pre>
            </div>
        </div>

        <!-- Visit Tab -->
        <div id="content-visit" class="tab-content hidden">
            <div class="code-block border border-white/5 rounded-2xl overflow-hidden">
                <div class="px-5 py-3 border-b border-white/5 flex items-center justify-between bg-white/[0.02]">
                    <span class="text-xs text-gray-500">Create Visit Request</span>
                    <button onclick="copyCode('visit')" class="text-xs text-gray-500 hover:text-white transition flex items-center gap-1 copy-btn" data-target="visit">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        Copy
                    </button>
                </div>
                <pre class="p-5 text-sm overflow-x-auto" dir="ltr"><code class="text-gray-300" id="code-visit"><span class="cmd">curl</span> <span class="flag">-X</span> POST <span class="url">http://localhost:8002/api/visit-requests</span> \
  <span class="flag">-H</span> <span class="str">"Content-Type: application/json"</span> \
  <span class="flag">-H</span> <span class="str">"Authorization: Bearer your-jwt-token"</span> \
  <span class="flag">-d</span> <span class="str">'{
    "event_id": "event-uuid-here",
    "visit_date": "2025-03-15"
  }'</span></code></pre>
            </div>
        </div>

        <!-- Rental Tab -->
        <div id="content-rental" class="tab-content hidden">
            <div class="code-block border border-white/5 rounded-2xl overflow-hidden">
                <div class="px-5 py-3 border-b border-white/5 flex items-center justify-between bg-white/[0.02]">
                    <span class="text-xs text-gray-500">Create Rental Request</span>
                    <button onclick="copyCode('rental')" class="text-xs text-gray-500 hover:text-white transition flex items-center gap-1 copy-btn" data-target="rental">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        Copy
                    </button>
                </div>
                <pre class="p-5 text-sm overflow-x-auto" dir="ltr"><code class="text-gray-300" id="code-rental"><span class="cmd">curl</span> <span class="flag">-X</span> POST <span class="url">http://localhost:8002/api/rental-requests</span> \
  <span class="flag">-H</span> <span class="str">"Content-Type: application/json"</span> \
  <span class="flag">-H</span> <span class="str">"Authorization: Bearer your-jwt-token"</span> \
  <span class="flag">-d</span> <span class="str">'{
    "space_id": "space-uuid-here",
    "start_date": "2025-03-01",
    "end_date": "2025-03-10"
  }'</span></code></pre>
            </div>
        </div>

        <!-- Admin Tab -->
        <div id="content-admin" class="tab-content hidden">
            <div class="code-block border border-white/5 rounded-2xl overflow-hidden">
                <div class="px-5 py-3 border-b border-white/5 flex items-center justify-between bg-white/[0.02]">
                    <span class="text-xs text-gray-500">Create Event (Admin)</span>
                    <button onclick="copyCode('admin')" class="text-xs text-gray-500 hover:text-white transition flex items-center gap-1 copy-btn" data-target="admin">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        Copy
                    </button>
                </div>
                <pre class="p-5 text-sm overflow-x-auto" dir="ltr"><code class="text-gray-300" id="code-admin"><span class="cmd">curl</span> <span class="flag">-X</span> POST <span class="url">http://localhost:8002/api/admin/events</span> \
  <span class="flag">-H</span> <span class="str">"Content-Type: application/json"</span> \
  <span class="flag">-H</span> <span class="str">"Authorization: Bearer admin-jwt-token"</span> \
  <span class="flag">-d</span> <span class="str">'{
    "name": "معرض الرياض التقني 2025",
    "description": "أكبر معرض تقني في المملكة",
    "city_id": "city-uuid",
    "category_id": "category-uuid",
    "start_date": "2025-06-01",
    "end_date": "2025-06-05",
    "is_featured": true
  }'</span></code></pre>
            </div>
        </div>
    </section>

    <div class="glow-line max-w-4xl mx-auto"></div>

    <!-- Architecture -->
    <section class="max-w-7xl mx-auto px-6 py-16">
        <div class="flex items-center gap-3 mb-8">
            <div class="w-1 h-8 bg-cyan-500 rounded-full"></div>
            <h3 class="text-2xl font-bold">البنية التقنية</h3>
            <span class="text-xs bg-cyan-500/10 text-cyan-400 px-2.5 py-1 rounded-full">Architecture</span>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            <!-- Auth Flow -->
            <div class="glass border border-white/5 rounded-2xl p-6">
                <h4 class="font-bold text-emerald-400 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    مسار المصادقة (S2S)
                </h4>
                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <span class="w-6 h-6 bg-emerald-500/20 rounded-full flex items-center justify-center text-xs text-emerald-400 font-bold shrink-0 mt-0.5">1</span>
                        <div>
                            <div class="text-sm font-semibold text-white/80">المستخدم يسجل دخول</div>
                            <div class="text-xs text-gray-500">عبر Auth Service → يحصل على JWT Token</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="w-6 h-6 bg-emerald-500/20 rounded-full flex items-center justify-center text-xs text-emerald-400 font-bold shrink-0 mt-0.5">2</span>
                        <div>
                            <div class="text-sm font-semibold text-white/80">يرسل طلب لـ Expo</div>
                            <div class="text-xs text-gray-500">مع التوكن في Header: <code class="text-emerald-300/60">Authorization: Bearer {token}</code></div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="w-6 h-6 bg-emerald-500/20 rounded-full flex items-center justify-center text-xs text-emerald-400 font-bold shrink-0 mt-0.5">3</span>
                        <div>
                            <div class="text-sm font-semibold text-white/80">Expo يتحقق من التوكن</div>
                            <div class="text-xs text-gray-500">عبر S2S call لـ Auth Service باستخدام <code class="text-emerald-300/60">X-Service-Token</code></div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="w-6 h-6 bg-emerald-500/20 rounded-full flex items-center justify-center text-xs text-emerald-400 font-bold shrink-0 mt-0.5">4</span>
                        <div>
                            <div class="text-sm font-semibold text-white/80">التنفيذ</div>
                            <div class="text-xs text-gray-500">Expo ينفذ العملية مع بيانات المستخدم والأدوار</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Request Flow -->
            <div class="glass border border-white/5 rounded-2xl p-6">
                <h4 class="font-bold text-amber-400 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2"/></svg>
                    مسار الطلبات
                </h4>
                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <span class="w-6 h-6 bg-amber-500/20 rounded-full flex items-center justify-center text-xs text-amber-400 font-bold shrink-0 mt-0.5">1</span>
                        <div>
                            <div class="text-sm font-semibold text-white/80">إنشاء ملف تجاري</div>
                            <div class="text-xs text-gray-500">رفع المستندات (سجل تجاري، هوية، شعار)</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="w-6 h-6 bg-amber-500/20 rounded-full flex items-center justify-center text-xs text-amber-400 font-bold shrink-0 mt-0.5">2</span>
                        <div>
                            <div class="text-sm font-semibold text-white/80">موافقة الإدارة</div>
                            <div class="text-xs text-gray-500">Admin يوافق على الملف التجاري</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="w-6 h-6 bg-amber-500/20 rounded-full flex items-center justify-center text-xs text-amber-400 font-bold shrink-0 mt-0.5">3</span>
                        <div>
                            <div class="text-sm font-semibold text-white/80">تقديم طلب تأجير</div>
                            <div class="text-xs text-gray-500">اختيار مساحة + تحديد التواريخ → حساب السعر تلقائي</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="w-6 h-6 bg-amber-500/20 rounded-full flex items-center justify-center text-xs text-amber-400 font-bold shrink-0 mt-0.5">4</span>
                        <div>
                            <div class="text-sm font-semibold text-white/80">الموافقة والدفع</div>
                            <div class="text-xs text-gray-500">Admin يوافق ← تسجيل الدفعة ← تأكيد الحجز</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="glow-line max-w-4xl mx-auto"></div>

    <!-- Docker Services -->
    <section class="max-w-7xl mx-auto px-6 py-16">
        <div class="flex items-center gap-3 mb-8">
            <div class="w-1 h-8 bg-sky-500 rounded-full"></div>
            <h3 class="text-2xl font-bold">Docker Services</h3>
        </div>
        <div class="grid md:grid-cols-3 gap-4">
            <div class="glass border border-white/5 rounded-2xl p-5 card-hover">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-9 h-9 bg-emerald-500/15 rounded-xl flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
                    </div>
                    <div>
                        <div class="font-bold text-sm">Expo API</div>
                        <div class="text-[10px] text-gray-500">PHP 8.3 + Events + Redis</div>
                    </div>
                </div>
                <div class="space-y-2 text-xs">
                    <div class="flex justify-between text-gray-500"><span>API</span><a href="http://localhost:8002" class="font-mono text-emerald-400 hover:underline">localhost:8002</a></div>
                    <div class="flex justify-between text-gray-500"><span>MySQL</span><span class="font-mono text-gray-400">localhost:3308</span></div>
                    <div class="flex justify-between text-gray-500"><span>Redis</span><span class="font-mono text-gray-400">localhost:6381</span></div>
                </div>
            </div>
            <div class="glass border border-white/5 rounded-2xl p-5 card-hover">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-9 h-9 bg-indigo-500/15 rounded-xl flex items-center justify-center">
                        <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <div>
                        <div class="font-bold text-sm">Auth Service</div>
                        <div class="text-[10px] text-gray-500">JWT + RBAC + S2S</div>
                    </div>
                </div>
                <div class="space-y-2 text-xs">
                    <div class="flex justify-between text-gray-500"><span>API</span><a href="http://localhost:8001" class="font-mono text-indigo-400 hover:underline">localhost:8001</a></div>
                    <div class="flex justify-between text-gray-500"><span>MySQL</span><span class="font-mono text-gray-400">localhost:3307</span></div>
                    <div class="flex justify-between text-gray-500"><span>Redis</span><span class="font-mono text-gray-400">localhost:6380</span></div>
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
                    <div class="flex justify-between text-gray-500"><span>Interface</span><a href="http://localhost:8080" class="font-mono text-orange-400 hover:underline">localhost:8080</a></div>
                    <div class="flex justify-between text-gray-500"><span>Auth DB</span><span class="font-mono text-gray-400">auth-mysql:3306</span></div>
                    <div class="flex justify-between text-gray-500"><span>Expo DB</span><span class="font-mono text-gray-400">expo-mysql:3306</span></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-white/5 mt-8">
        <div class="max-w-7xl mx-auto px-6 py-8 flex flex-col md:flex-row items-center justify-between gap-4 text-sm text-gray-600">
            <div class="flex items-center gap-3">
                <div class="w-7 h-7 bg-emerald-500/20 rounded-lg flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
                </div>
                <span>Maham Expo API v{{ config('expo-api.service_version', '1.0.0') }}</span>
            </div>
            <div class="flex items-center gap-4">
                <a href="http://localhost:8001" class="hover:text-indigo-400 transition">Auth Service</a>
                <a href="http://localhost:8080" class="hover:text-orange-400 transition">phpMyAdmin</a>
                <a href="/api/health" class="hover:text-emerald-400 transition">Health</a>
            </div>
            <span>{{ now()->format('Y') }} &copy; Maham Expo</span>
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
