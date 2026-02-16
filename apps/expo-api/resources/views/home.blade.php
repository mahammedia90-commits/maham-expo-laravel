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
        .gradient-bg { background: linear-gradient(145deg, #020617 0%, #0f172a 40%, #052e16 70%, #0f172a 100%); }
        .card-hover { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .card-hover:hover { transform: translateY(-3px); box-shadow: 0 20px 40px rgba(0,0,0,0.3); border-color: rgba(16, 185, 129, 0.3); }
        .pulse-dot { animation: pulse-dot 2s ease-in-out infinite; }
        @keyframes pulse-dot { 0%, 100% { opacity: 1; transform: scale(1); } 50% { opacity: 0.4; transform: scale(0.8); } }
        .glow-green { box-shadow: 0 0 30px rgba(16, 185, 129, 0.4), 0 0 60px rgba(16, 185, 129, 0.1); }
        .glow-line { background: linear-gradient(90deg, transparent, rgba(16, 185, 129, 0.3), transparent); height: 1px; }
        .code-block { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); }
        .shimmer { background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.03) 50%, transparent 100%); background-size: 200% 100%; animation: shimmer 3s ease-in-out infinite; }
        @keyframes shimmer { 0% { background-position: -200% 0; } 100% { background-position: 200% 0; } }
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
                <a href="#public-api" class="nav-link text-gray-400 hover:text-white transition">العامة</a>
                <a href="#user-api" class="nav-link text-gray-400 hover:text-white transition">المستخدم</a>
                <a href="#admin-api" class="nav-link text-gray-400 hover:text-white transition">الإدارة</a>
                <a href="#quickstart" class="nav-link text-gray-400 hover:text-white transition">البدء السريع</a>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-[10px] bg-emerald-500/20 text-emerald-400 px-2.5 py-1 rounded-full font-semibold border border-emerald-500/20">v{{ config('app.version', '1.0.0') }}</span>
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
                <span class="bg-gradient-to-l from-emerald-400 via-teal-400 to-cyan-400 bg-clip-text text-transparent">منصة المعارض</span>
                <br>
                <span class="text-white/90">والفعاليات</span>
            </h2>
            <p class="text-gray-400 text-lg max-w-2xl mx-auto mb-10 leading-relaxed">
                نظام إدارة معارض وفعاليات متكامل يدعم <span class="text-emerald-400 font-semibold">المساحات</span> و<span class="text-teal-400 font-semibold">طلبات الإيجار</span> و<span class="text-cyan-400 font-semibold">الزيارات</span> مع لوحة تحكم إدارية شاملة
            </p>
            <div class="flex items-center justify-center gap-4 flex-wrap">
                <a href="#public-api" class="bg-emerald-500 hover:bg-emerald-600 text-white px-7 py-3 rounded-xl font-bold transition-all hover:shadow-lg hover:shadow-emerald-500/25 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    تصفح الـ API
                </a>
                <a href="/api/health" target="_blank" class="bg-white/5 hover:bg-white/10 text-white px-7 py-3 rounded-xl font-bold transition-all border border-white/10 hover:border-white/20 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Health Check
                </a>
                <a href="http://localhost:8001/docs" class="bg-white/5 hover:bg-white/10 text-white px-7 py-3 rounded-xl font-bold transition-all border border-white/10 hover:border-white/20 flex items-center gap-2">
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
                <div class="text-3xl font-black text-teal-400 mb-1">Spaces</div>
                <div class="text-xs text-gray-500">Rental Spaces</div>
            </div>
            <div class="glass border border-white/5 rounded-2xl p-5 text-center">
                <div class="text-3xl font-black text-cyan-400 mb-1">Rentals</div>
                <div class="text-xs text-gray-500">Rental Management</div>
            </div>
            <div class="glass border border-white/5 rounded-2xl p-5 text-center">
                <div class="text-3xl font-black text-amber-400 mb-1">Profiles</div>
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
                    ['Events Management', 'إدارة المعارض والفعاليات مع دعم الأقسام والمساحات المتعددة والأحداث المميزة.', 'emerald', 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                    ['Spaces & Sections', 'نظام مساحات ذكي مع أقسام قابلة للتخصيص وحالات متعددة (متاح، محجوز، مؤجر).', 'teal', 'M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z'],
                    ['Visit Requests', 'طلبات زيارة المعارض مع نظام موافقة ورفض وتتبع الحالات الكامل.', 'cyan', 'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z'],
                    ['Rental Requests', 'نظام إيجار متكامل مع إدارة المدفوعات والحالات المتعددة وتتبع الإيرادات.', 'amber', 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'],
                    ['Business Profiles', 'ملفات تجارية قابلة للتحقق مع نظام موافقة إدارية ورفع المستندات.', 'purple', 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                    ['Notifications', 'نظام إشعارات متكامل مع دعم القراءة والعد والإشعارات غير المقروءة.', 'rose', 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9'],
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

    <!-- Public API Endpoints -->
    <section id="public-api" class="section-anchor max-w-7xl mx-auto px-6 py-16">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-1 h-8 bg-emerald-500 rounded-full"></div>
            <h3 class="text-2xl font-bold">الـ API العامة</h3>
            <span class="text-xs bg-emerald-500/10 text-emerald-400 px-2.5 py-1 rounded-full">Public API</span>
        </div>
        <p class="text-gray-500 text-sm mb-8 mr-4">نقاط الوصول المتاحة بدون مصادقة - تصفح الفعاليات والمساحات والخدمات</p>

        <!-- Categories -->
        <div class="mb-6 glass border border-white/5 rounded-2xl overflow-hidden">
            <div class="px-6 py-3 border-b border-white/5 bg-white/[0.02]">
                <h5 class="text-xs font-bold text-gray-400 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    التصنيفات - Categories
                </h5>
            </div>
            <div class="divide-y divide-white/5">
                @php
                    $categoryEps = [
                        ['GET', '/api/categories', 'قائمة التصنيفات', 'يدعم الفلترة والبحث'],
                        ['GET', '/api/categories/{category}', 'تفاصيل تصنيف', 'بيانات التصنيف الكاملة'],
                    ];
                @endphp
                @foreach($categoryEps as $ep)
                <div class="endpoint-row flex items-center px-6 py-3.5 gap-3">
                    <span class="method-badge font-bold px-2 py-0.5 rounded font-mono min-w-[56px] text-center bg-emerald-500/20 text-emerald-400">{{ $ep[0] }}</span>
                    <code class="text-xs text-gray-300 font-mono whitespace-nowrap" dir="ltr">{{ $ep[1] }}</code>
                    <span class="text-xs text-gray-600">{{ $ep[2] }}</span>
                    <span class="mr-auto"></span>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-green-500/10 text-green-400 font-semibold">public</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Cities -->
        <div class="mb-6 glass border border-white/5 rounded-2xl overflow-hidden">
            <div class="px-6 py-3 border-b border-white/5 bg-white/[0.02]">
                <h5 class="text-xs font-bold text-gray-400 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    المدن - Cities
                </h5>
            </div>
            <div class="divide-y divide-white/5">
                @php
                    $cityEps = [
                        ['GET', '/api/cities', 'قائمة المدن', 'جميع المدن المتاحة'],
                        ['GET', '/api/cities/{city}', 'تفاصيل مدينة', 'بيانات المدينة والفعاليات المرتبطة'],
                    ];
                @endphp
                @foreach($cityEps as $ep)
                <div class="endpoint-row flex items-center px-6 py-3.5 gap-3">
                    <span class="method-badge font-bold px-2 py-0.5 rounded font-mono min-w-[56px] text-center bg-emerald-500/20 text-emerald-400">{{ $ep[0] }}</span>
                    <code class="text-xs text-gray-300 font-mono whitespace-nowrap" dir="ltr">{{ $ep[1] }}</code>
                    <span class="text-xs text-gray-600">{{ $ep[2] }}</span>
                    <span class="mr-auto"></span>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-green-500/10 text-green-400 font-semibold">public</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Events -->
        <div class="mb-6 glass border border-white/5 rounded-2xl overflow-hidden">
            <div class="px-6 py-3 border-b border-white/5 bg-white/[0.02]">
                <h5 class="text-xs font-bold text-gray-400 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    الفعاليات - Events
                </h5>
            </div>
            <div class="divide-y divide-white/5">
                @php
                    $eventEps = [
                        ['GET', '/api/events', 'قائمة الفعاليات', 'يدعم الفلترة والبحث والترتيب مع pagination'],
                        ['GET', '/api/events/featured', 'الفعاليات المميزة', 'الفعاليات المميزة والنشطة حالياً'],
                        ['GET', '/api/events/{event}', 'تفاصيل فعالية', 'بيانات الفعالية الكاملة مع الإحصائيات'],
                        ['GET', '/api/events/{event}/spaces', 'مساحات الفعالية', 'جميع المساحات المتاحة في الفعالية'],
                        ['GET', '/api/events/{event}/sections', 'أقسام الفعالية', 'الأقسام والمساحات المرتبطة بها'],
                    ];
                @endphp
                @foreach($eventEps as $ep)
                <div class="endpoint-row flex items-center px-6 py-3.5 gap-3">
                    <span class="method-badge font-bold px-2 py-0.5 rounded font-mono min-w-[56px] text-center bg-emerald-500/20 text-emerald-400">{{ $ep[0] }}</span>
                    <code class="text-xs text-gray-300 font-mono whitespace-nowrap" dir="ltr">{{ $ep[1] }}</code>
                    <span class="text-xs text-gray-600">{{ $ep[2] }}</span>
                    <span class="mr-auto"></span>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-green-500/10 text-green-400 font-semibold">public</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Spaces & Services -->
        <div class="grid md:grid-cols-2 gap-4">
            <div class="glass border border-white/5 rounded-2xl overflow-hidden">
                <div class="px-6 py-3 border-b border-white/5 bg-white/[0.02]">
                    <h5 class="text-xs font-bold text-gray-400 flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6z"/></svg>
                        المساحات - Spaces
                    </h5>
                </div>
                <div class="endpoint-row flex items-center px-6 py-3.5 gap-3">
                    <span class="method-badge font-bold px-2 py-0.5 rounded font-mono min-w-[56px] text-center bg-emerald-500/20 text-emerald-400">GET</span>
                    <code class="text-xs text-gray-300 font-mono" dir="ltr">/api/spaces/{space}</code>
                    <span class="text-xs text-gray-600">تفاصيل مساحة</span>
                    <span class="mr-auto"></span>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-green-500/10 text-green-400 font-semibold">public</span>
                </div>
            </div>
            <div class="glass border border-white/5 rounded-2xl overflow-hidden">
                <div class="px-6 py-3 border-b border-white/5 bg-white/[0.02]">
                    <h5 class="text-xs font-bold text-gray-400 flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        الخدمات - Services
                    </h5>
                </div>
                <div class="endpoint-row flex items-center px-6 py-3.5 gap-3">
                    <span class="method-badge font-bold px-2 py-0.5 rounded font-mono min-w-[56px] text-center bg-emerald-500/20 text-emerald-400">GET</span>
                    <code class="text-xs text-gray-300 font-mono" dir="ltr">/api/services</code>
                    <span class="text-xs text-gray-600">قائمة الخدمات</span>
                    <span class="mr-auto"></span>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-green-500/10 text-green-400 font-semibold">public</span>
                </div>
            </div>
        </div>
    </section>

    <div class="glow-line max-w-4xl mx-auto"></div>

    <!-- User API Endpoints -->
    <section id="user-api" class="section-anchor max-w-7xl mx-auto px-6 py-16">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-1 h-8 bg-blue-500 rounded-full"></div>
            <h3 class="text-2xl font-bold">واجهة المستخدم</h3>
            <span class="text-xs bg-blue-500/10 text-blue-400 px-2.5 py-1 rounded-full">Authenticated API</span>
        </div>
        <p class="text-gray-500 text-sm mb-8 mr-4">نقاط الوصول التي تتطلب مصادقة - الملف الشخصي، المفضلة، الإشعارات، الطلبات</p>

        <div class="mb-4 p-3 bg-blue-500/5 border border-blue-500/10 rounded-xl">
            <p class="text-xs text-blue-400/80"><span class="font-bold">المصادقة:</span> جميع هذه الـ Endpoints تتطلب Header: <code class="text-blue-300/60">Authorization: Bearer {token}</code> - يتم التحقق عبر Auth Service</p>
        </div>

        <!-- Business Profile -->
        <div class="mb-6 glass border border-white/5 rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-white/5 flex items-center gap-3">
                <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <h4 class="font-bold text-purple-400">الملف التجاري - Business Profile</h4>
            </div>
            <div class="divide-y divide-white/5">
                @php
                    $profileEps = [
                        ['GET', '/api/profile', 'عرض الملف التجاري', 'بيانات الملف التجاري الحالي مع حالة التحقق'],
                        ['POST', '/api/profile', 'إنشاء ملف تجاري', 'company_name, commercial_reg, phone, city, address, description'],
                        ['PUT', '/api/profile', 'تحديث الملف التجاري', 'تحديث بيانات الملف التجاري'],
                    ];
                @endphp
                @foreach($profileEps as $ep)
                <div class="endpoint-row flex items-center px-6 py-3.5 gap-3">
                    <span class="method-badge font-bold px-2 py-0.5 rounded font-mono min-w-[56px] text-center {{ $ep[0] === 'GET' ? 'bg-emerald-500/20 text-emerald-400' : ($ep[0] === 'POST' ? 'bg-blue-500/20 text-blue-400' : 'bg-amber-500/20 text-amber-400') }}">{{ $ep[0] }}</span>
                    <code class="text-xs text-gray-300 font-mono whitespace-nowrap" dir="ltr">{{ $ep[1] }}</code>
                    <span class="text-xs text-gray-600">{{ $ep[2] }}</span>
                    <span class="mr-auto"></span>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-blue-500/10 text-blue-400 font-semibold">auth</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Favorites -->
        <div class="mb-6 glass border border-white/5 rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-white/5 flex items-center gap-3">
                <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                <h4 class="font-bold text-rose-400">المفضلة - Favorites</h4>
            </div>
            <div class="divide-y divide-white/5">
                @php
                    $favEps = [
                        ['GET', '/api/favorites', 'قائمة المفضلة', 'عرض جميع العناصر المفضلة'],
                        ['POST', '/api/favorites', 'إضافة للمفضلة', 'favoritable_type, favoritable_id'],
                        ['DELETE', '/api/favorites/{favorite}', 'إزالة من المفضلة', 'حذف عنصر من المفضلة'],
                    ];
                @endphp
                @foreach($favEps as $ep)
                <div class="endpoint-row flex items-center px-6 py-3.5 gap-3">
                    <span class="method-badge font-bold px-2 py-0.5 rounded font-mono min-w-[56px] text-center {{ $ep[0] === 'GET' ? 'bg-emerald-500/20 text-emerald-400' : ($ep[0] === 'POST' ? 'bg-blue-500/20 text-blue-400' : 'bg-rose-500/20 text-rose-400') }}">{{ $ep[0] }}</span>
                    <code class="text-xs text-gray-300 font-mono whitespace-nowrap" dir="ltr">{{ $ep[1] }}</code>
                    <span class="text-xs text-gray-600">{{ $ep[2] }}</span>
                    <span class="mr-auto"></span>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-blue-500/10 text-blue-400 font-semibold">auth</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Notifications -->
        <div class="mb-6 glass border border-white/5 rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-white/5 flex items-center gap-3">
                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                <h4 class="font-bold text-amber-400">الإشعارات - Notifications</h4>
            </div>
            <div class="divide-y divide-white/5">
                @php
                    $notifEps = [
                        ['GET', '/api/notifications', 'قائمة الإشعارات', 'جميع الإشعارات مع pagination'],
                        ['GET', '/api/notifications/unread-count', 'عدد غير المقروءة', 'عدد الإشعارات غير المقروءة'],
                        ['PUT', '/api/notifications/{notification}/read', 'تحديد كمقروء', 'تحديد إشعار واحد كمقروء'],
                        ['PUT', '/api/notifications/read-all', 'قراءة الكل', 'تحديد جميع الإشعارات كمقروءة'],
                    ];
                @endphp
                @foreach($notifEps as $ep)
                <div class="endpoint-row flex items-center px-6 py-3.5 gap-3">
                    <span class="method-badge font-bold px-2 py-0.5 rounded font-mono min-w-[56px] text-center {{ $ep[0] === 'GET' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-amber-500/20 text-amber-400' }}">{{ $ep[0] }}</span>
                    <code class="text-xs text-gray-300 font-mono whitespace-nowrap" dir="ltr">{{ $ep[1] }}</code>
                    <span class="text-xs text-gray-600">{{ $ep[2] }}</span>
                    <span class="mr-auto"></span>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-blue-500/10 text-blue-400 font-semibold">auth</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Visit Requests -->
        <div class="mb-6 glass border border-white/5 rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-white/5 flex items-center gap-3">
                <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                <h4 class="font-bold text-cyan-400">طلبات الزيارة - Visit Requests</h4>
            </div>
            <div class="divide-y divide-white/5">
                @php
                    $visitEps = [
                        ['GET', '/api/visit-requests', 'قائمة طلبات الزيارة', 'طلبات المستخدم الحالي'],
                        ['POST', '/api/visit-requests', 'إنشاء طلب زيارة', 'event_id, visit_date, visitors_count, notes'],
                        ['GET', '/api/visit-requests/{visitRequest}', 'تفاصيل طلب زيارة', 'بيانات الطلب الكاملة'],
                        ['PUT', '/api/visit-requests/{visitRequest}', 'تحديث طلب زيارة', 'تعديل بيانات الطلب'],
                        ['DELETE', '/api/visit-requests/{visitRequest}', 'حذف طلب زيارة', 'إلغاء الطلب'],
                    ];
                @endphp
                @foreach($visitEps as $ep)
                <div class="endpoint-row flex items-center px-6 py-3.5 gap-3">
                    <span class="method-badge font-bold px-2 py-0.5 rounded font-mono min-w-[56px] text-center {{ $ep[0] === 'GET' ? 'bg-emerald-500/20 text-emerald-400' : ($ep[0] === 'POST' ? 'bg-blue-500/20 text-blue-400' : ($ep[0] === 'PUT' ? 'bg-amber-500/20 text-amber-400' : 'bg-rose-500/20 text-rose-400')) }}">{{ $ep[0] }}</span>
                    <code class="text-xs text-gray-300 font-mono whitespace-nowrap" dir="ltr">{{ $ep[1] }}</code>
                    <span class="text-xs text-gray-600">{{ $ep[2] }}</span>
                    <span class="mr-auto"></span>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-blue-500/10 text-blue-400 font-semibold">auth</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Rental Requests -->
        <div class="glass border border-white/5 rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-white/5 flex items-center gap-3">
                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <h4 class="font-bold text-amber-400">طلبات الإيجار - Rental Requests</h4>
                <span class="text-[10px] px-2 py-0.5 rounded-full bg-purple-500/10 text-purple-400 font-semibold mr-auto">verified profile required</span>
            </div>
            <div class="divide-y divide-white/5">
                @php
                    $rentalEps = [
                        ['GET', '/api/rental-requests', 'قائمة طلبات الإيجار', 'طلبات المستخدم الحالي'],
                        ['POST', '/api/rental-requests', 'إنشاء طلب إيجار', 'space_id, start_date, end_date, notes'],
                        ['GET', '/api/rental-requests/{rentalRequest}', 'تفاصيل طلب إيجار', 'بيانات الطلب مع المدفوعات'],
                        ['PUT', '/api/rental-requests/{rentalRequest}', 'تحديث طلب إيجار', 'تعديل بيانات الطلب'],
                        ['DELETE', '/api/rental-requests/{rentalRequest}', 'حذف طلب إيجار', 'إلغاء الطلب'],
                    ];
                @endphp
                @foreach($rentalEps as $ep)
                <div class="endpoint-row flex items-center px-6 py-3.5 gap-3">
                    <span class="method-badge font-bold px-2 py-0.5 rounded font-mono min-w-[56px] text-center {{ $ep[0] === 'GET' ? 'bg-emerald-500/20 text-emerald-400' : ($ep[0] === 'POST' ? 'bg-blue-500/20 text-blue-400' : ($ep[0] === 'PUT' ? 'bg-amber-500/20 text-amber-400' : 'bg-rose-500/20 text-rose-400')) }}">{{ $ep[0] }}</span>
                    <code class="text-xs text-gray-300 font-mono whitespace-nowrap" dir="ltr">{{ $ep[1] }}</code>
                    <span class="text-xs text-gray-600">{{ $ep[2] }}</span>
                    <span class="mr-auto"></span>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-blue-500/10 text-blue-400 font-semibold">auth</span>
                </div>
                @endforeach
            </div>
            <div class="p-4 bg-white/[0.02]">
                <p class="text-[10px] text-gray-600"><span class="text-purple-400/60 font-bold">ملاحظة:</span> طلبات الإيجار تتطلب ملف تجاري موثق (verified business profile) قبل إنشاء الطلب</p>
            </div>
        </div>
    </section>

    <div class="glow-line max-w-4xl mx-auto"></div>

    <!-- Admin API Endpoints -->
    <section id="admin-api" class="section-anchor max-w-7xl mx-auto px-6 py-16">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-1 h-8 bg-rose-500 rounded-full"></div>
            <h3 class="text-2xl font-bold">لوحة الإدارة</h3>
            <span class="text-xs bg-rose-500/10 text-rose-400 px-2.5 py-1 rounded-full">Admin API</span>
        </div>
        <p class="text-gray-500 text-sm mb-8 mr-4">نقاط الوصول الإدارية - تتطلب صلاحية admin أو super-admin</p>

        <div class="mb-4 p-3 bg-rose-500/5 border border-rose-500/10 rounded-xl">
            <p class="text-xs text-rose-400/80"><span class="font-bold">الصلاحيات:</span> جميع هذه الـ Endpoints تتطلب دور <code class="text-rose-300/60">admin</code> أو <code class="text-rose-300/60">super-admin</code> بالإضافة للمصادقة</p>
        </div>

        <!-- Dashboard -->
        <div class="mb-6 glass border border-white/5 rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-white/5 flex items-center gap-3">
                <span class="method-badge font-bold px-2.5 py-1 rounded-md bg-emerald-500/20 text-emerald-400 font-mono">GET</span>
                <code class="text-sm text-gray-300 font-mono" dir="ltr">/api/admin/dashboard</code>
                <span class="text-xs text-gray-600 mr-auto">إحصائيات لوحة التحكم</span>
                <span class="text-[10px] px-2 py-0.5 rounded-full bg-rose-500/10 text-rose-400 font-semibold">admin</span>
            </div>
            <div class="grid md:grid-cols-2 divide-y md:divide-y-0 md:divide-x md:divide-x-reverse divide-white/5">
                <div class="p-6">
                    <h5 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Query Parameters</h5>
                    <div class="space-y-3">
                        <div class="flex items-start gap-2">
                            <code class="text-xs bg-white/5 px-2 py-0.5 rounded text-emerald-300 font-mono">spaces_period</code>
                            <span class="text-[10px] text-gray-500 font-bold">optional</span>
                            <span class="text-xs text-gray-500">all|today|week|month|year</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <code class="text-xs bg-white/5 px-2 py-0.5 rounded text-emerald-300 font-mono">revenue_period</code>
                            <span class="text-[10px] text-gray-500 font-bold">optional</span>
                            <span class="text-xs text-gray-500">all|today|week|month|year</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <code class="text-xs bg-white/5 px-2 py-0.5 rounded text-emerald-300 font-mono">event_id</code>
                            <span class="text-[10px] text-gray-500 font-bold">optional</span>
                            <span class="text-xs text-gray-500">uuid - فلتر حسب الفعالية</span>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <h5 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Response <span class="text-emerald-400">200</span></h5>
                    <div class="code-block rounded-xl p-4 text-xs overflow-x-auto" dir="ltr">
<pre class="text-gray-300"><code>{
  <span class="key">"data"</span>: {
    <span class="key">"overview"</span>: {
      <span class="key">"total_revenue"</span>: <span class="num">450000</span>,
      <span class="key">"total_spaces"</span>: <span class="num">122</span>,
      <span class="key">"total_visit_requests"</span>: <span class="num">4</span>,
      <span class="key">"total_rental_requests"</span>: <span class="num">3</span>
    },
    <span class="key">"spaces"</span>: { <span class="comment">/* by_status */</span> },
    <span class="key">"revenue"</span>: { <span class="comment">/* by_payment_status */</span> },
    <span class="key">"visit_requests"</span>: { <span class="comment">/* counts */</span> },
    <span class="key">"rental_requests"</span>: { <span class="comment">/* counts */</span> }
  }
}</code></pre>
                    </div>
                </div>
            </div>
        </div>

        <!-- Admin Events -->
        <div class="mb-6 glass border border-white/5 rounded-2xl overflow-hidden">
            <div class="px-6 py-3 border-b border-white/5 bg-white/[0.02]">
                <h5 class="text-xs font-bold text-gray-400 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    إدارة الفعاليات - Events Management
                </h5>
            </div>
            <div class="divide-y divide-white/5">
                @php
                    $adminEventEps = [
                        ['GET', '/api/admin/events', 'قائمة الفعاليات', 'يدعم الفلترة والبحث والترتيب'],
                        ['POST', '/api/admin/events', 'إنشاء فعالية', 'name, description, start_date, end_date, city_id, category_id'],
                        ['GET', '/api/admin/events/{event}', 'تفاصيل فعالية', 'البيانات الكاملة مع الأقسام والمساحات'],
                        ['PUT', '/api/admin/events/{event}', 'تحديث فعالية', 'تعديل بيانات الفعالية'],
                        ['DELETE', '/api/admin/events/{event}', 'حذف فعالية', 'حذف الفعالية وجميع المرتبطات'],
                        ['GET', '/api/admin/events/{event}/sections', 'أقسام الفعالية', 'عرض جميع الأقسام'],
                        ['POST', '/api/admin/events/{event}/sections', 'إنشاء قسم', 'إضافة قسم جديد للفعالية'],
                        ['GET', '/api/admin/events/{event}/spaces', 'مساحات الفعالية', 'عرض جميع المساحات'],
                        ['POST', '/api/admin/events/{event}/spaces', 'إنشاء مساحة', 'إضافة مساحة جديدة للفعالية'],
                    ];
                @endphp
                @foreach($adminEventEps as $ep)
                <div class="endpoint-row flex items-center px-6 py-3 gap-3">
                    <span class="method-badge font-bold px-2 py-0.5 rounded font-mono min-w-[56px] text-center {{ $ep[0] === 'GET' ? 'bg-emerald-500/20 text-emerald-400' : ($ep[0] === 'POST' ? 'bg-blue-500/20 text-blue-400' : ($ep[0] === 'PUT' ? 'bg-amber-500/20 text-amber-400' : 'bg-rose-500/20 text-rose-400')) }}">{{ $ep[0] }}</span>
                    <code class="text-xs text-gray-300 font-mono whitespace-nowrap" dir="ltr">{{ $ep[1] }}</code>
                    <span class="text-xs text-gray-600">{{ $ep[2] }}</span>
                    <span class="mr-auto"></span>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-rose-500/10 text-rose-400 font-semibold">admin</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Admin Sections & Spaces -->
        <div class="grid md:grid-cols-2 gap-4 mb-6">
            <!-- Sections -->
            <div class="glass border border-white/5 rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-white/5">
                    <h4 class="font-bold text-teal-400 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6z"/></svg>
                        إدارة الأقسام
                    </h4>
                </div>
                <div class="divide-y divide-white/5">
                    @php
                        $sectionEps = [
                            ['GET', '/api/admin/sections/{section}', 'التفاصيل'],
                            ['PUT', '/api/admin/sections/{section}', 'تحديث'],
                            ['DELETE', '/api/admin/sections/{section}', 'حذف'],
                        ];
                    @endphp
                    @foreach($sectionEps as $ep)
                    <div class="endpoint-row flex items-center px-5 py-2.5 gap-2">
                        <span class="method-badge font-bold px-1.5 py-0.5 rounded font-mono min-w-[44px] text-center {{ $ep[0] === 'GET' ? 'bg-emerald-500/15 text-emerald-400' : ($ep[0] === 'PUT' ? 'bg-amber-500/15 text-amber-400' : 'bg-rose-500/15 text-rose-400') }}">{{ $ep[0] }}</span>
                        <code class="text-[11px] text-gray-400 font-mono" dir="ltr">{{ $ep[1] }}</code>
                        <span class="text-[11px] text-gray-600 mr-auto">{{ $ep[2] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Spaces -->
            <div class="glass border border-white/5 rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-white/5">
                    <h4 class="font-bold text-emerald-400 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14v6m-3-3h6M6 10h2a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2zm10 0h2a2 2 0 002-2V6a2 2 0 00-2-2h-2a2 2 0 00-2 2v2a2 2 0 002 2zM6 20h2a2 2 0 002-2v-2a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2z"/></svg>
                        إدارة المساحات
                    </h4>
                </div>
                <div class="divide-y divide-white/5">
                    @php
                        $spaceEps = [
                            ['GET', '/api/admin/spaces/{space}', 'التفاصيل'],
                            ['PUT', '/api/admin/spaces/{space}', 'تحديث'],
                            ['DELETE', '/api/admin/spaces/{space}', 'حذف'],
                        ];
                    @endphp
                    @foreach($spaceEps as $ep)
                    <div class="endpoint-row flex items-center px-5 py-2.5 gap-2">
                        <span class="method-badge font-bold px-1.5 py-0.5 rounded font-mono min-w-[44px] text-center {{ $ep[0] === 'GET' ? 'bg-emerald-500/15 text-emerald-400' : ($ep[0] === 'PUT' ? 'bg-amber-500/15 text-amber-400' : 'bg-rose-500/15 text-rose-400') }}">{{ $ep[0] }}</span>
                        <code class="text-[11px] text-gray-400 font-mono" dir="ltr">{{ $ep[1] }}</code>
                        <span class="text-[11px] text-gray-600 mr-auto">{{ $ep[2] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Admin Services -->
        <div class="mb-6 glass border border-white/5 rounded-2xl overflow-hidden">
            <div class="px-6 py-3 border-b border-white/5 bg-white/[0.02]">
                <h5 class="text-xs font-bold text-gray-400 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    إدارة الخدمات - Services Management
                </h5>
            </div>
            <div class="divide-y divide-white/5">
                @php
                    $adminSvcEps = [
                        ['GET', '/api/admin/services', 'قائمة الخدمات'],
                        ['POST', '/api/admin/services', 'إنشاء خدمة'],
                        ['GET', '/api/admin/services/{service}', 'تفاصيل خدمة'],
                        ['PUT', '/api/admin/services/{service}', 'تحديث خدمة'],
                        ['DELETE', '/api/admin/services/{service}', 'حذف خدمة'],
                    ];
                @endphp
                @foreach($adminSvcEps as $ep)
                <div class="endpoint-row flex items-center px-6 py-3 gap-3">
                    <span class="method-badge font-bold px-2 py-0.5 rounded font-mono min-w-[56px] text-center {{ $ep[0] === 'GET' ? 'bg-emerald-500/20 text-emerald-400' : ($ep[0] === 'POST' ? 'bg-blue-500/20 text-blue-400' : ($ep[0] === 'PUT' ? 'bg-amber-500/20 text-amber-400' : 'bg-rose-500/20 text-rose-400')) }}">{{ $ep[0] }}</span>
                    <code class="text-xs text-gray-300 font-mono whitespace-nowrap" dir="ltr">{{ $ep[1] }}</code>
                    <span class="text-xs text-gray-600">{{ $ep[2] }}</span>
                    <span class="mr-auto"></span>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-rose-500/10 text-rose-400 font-semibold">admin</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Admin Visit Requests -->
        <div class="mb-6 glass border border-white/5 rounded-2xl overflow-hidden">
            <div class="px-6 py-3 border-b border-white/5 bg-white/[0.02]">
                <h5 class="text-xs font-bold text-gray-400 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                    إدارة طلبات الزيارة - Visit Requests Management
                </h5>
            </div>
            <div class="divide-y divide-white/5">
                @php
                    $adminVisitEps = [
                        ['GET', '/api/admin/visit-requests', 'قائمة جميع الطلبات', 'فلترة حسب الحالة والفعالية'],
                        ['GET', '/api/admin/visit-requests/{visitRequest}', 'تفاصيل طلب', 'بيانات الطلب مع بيانات المستخدم'],
                        ['PUT', '/api/admin/visit-requests/{visitRequest}/approve', 'قبول الطلب', 'الموافقة على طلب الزيارة'],
                        ['PUT', '/api/admin/visit-requests/{visitRequest}/reject', 'رفض الطلب', 'رفض طلب الزيارة مع سبب'],
                    ];
                @endphp
                @foreach($adminVisitEps as $ep)
                <div class="endpoint-row flex items-center px-6 py-3 gap-3">
                    <span class="method-badge font-bold px-2 py-0.5 rounded font-mono min-w-[56px] text-center {{ $ep[0] === 'GET' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-amber-500/20 text-amber-400' }}">{{ $ep[0] }}</span>
                    <code class="text-xs text-gray-300 font-mono whitespace-nowrap" dir="ltr">{{ $ep[1] }}</code>
                    <span class="text-xs text-gray-600">{{ $ep[2] }}</span>
                    <span class="mr-auto"></span>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-rose-500/10 text-rose-400 font-semibold">admin</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Admin Rental Requests -->
        <div class="mb-6 glass border border-white/5 rounded-2xl overflow-hidden">
            <div class="px-6 py-3 border-b border-white/5 bg-white/[0.02]">
                <h5 class="text-xs font-bold text-gray-400 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    إدارة طلبات الإيجار - Rental Requests Management
                </h5>
            </div>
            <div class="divide-y divide-white/5">
                @php
                    $adminRentalEps = [
                        ['GET', '/api/admin/rental-requests', 'قائمة جميع الطلبات', 'فلترة حسب الحالة والمدفوعات'],
                        ['GET', '/api/admin/rental-requests/{rentalRequest}', 'تفاصيل طلب', 'بيانات الطلب مع سجل المدفوعات'],
                        ['PUT', '/api/admin/rental-requests/{rentalRequest}/approve', 'قبول الطلب', 'الموافقة مع تحديد المبلغ'],
                        ['PUT', '/api/admin/rental-requests/{rentalRequest}/reject', 'رفض الطلب', 'رفض الطلب مع سبب'],
                        ['POST', '/api/admin/rental-requests/{rentalRequest}/payment', 'تسجيل دفعة', 'تسجيل دفعة جديدة للطلب'],
                    ];
                @endphp
                @foreach($adminRentalEps as $ep)
                <div class="endpoint-row flex items-center px-6 py-3 gap-3">
                    <span class="method-badge font-bold px-2 py-0.5 rounded font-mono min-w-[56px] text-center {{ $ep[0] === 'GET' ? 'bg-emerald-500/20 text-emerald-400' : ($ep[0] === 'POST' ? 'bg-blue-500/20 text-blue-400' : 'bg-amber-500/20 text-amber-400') }}">{{ $ep[0] }}</span>
                    <code class="text-xs text-gray-300 font-mono whitespace-nowrap" dir="ltr">{{ $ep[1] }}</code>
                    <span class="text-xs text-gray-600">{{ $ep[2] }}</span>
                    <span class="mr-auto"></span>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-rose-500/10 text-rose-400 font-semibold">admin</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Admin Business Profiles -->
        <div class="glass border border-white/5 rounded-2xl overflow-hidden">
            <div class="px-6 py-3 border-b border-white/5 bg-white/[0.02]">
                <h5 class="text-xs font-bold text-gray-400 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    إدارة الملفات التجارية - Business Profiles Management
                </h5>
            </div>
            <div class="divide-y divide-white/5">
                @php
                    $adminProfileEps = [
                        ['GET', '/api/admin/profiles', 'قائمة الملفات التجارية', 'فلترة حسب حالة التحقق'],
                        ['GET', '/api/admin/profiles/{profile}', 'تفاصيل ملف تجاري', 'بيانات الملف مع المستندات'],
                        ['PUT', '/api/admin/profiles/{profile}/approve', 'قبول الملف', 'الموافقة على الملف التجاري'],
                        ['PUT', '/api/admin/profiles/{profile}/reject', 'رفض الملف', 'رفض الملف مع سبب'],
                    ];
                @endphp
                @foreach($adminProfileEps as $ep)
                <div class="endpoint-row flex items-center px-6 py-3 gap-3">
                    <span class="method-badge font-bold px-2 py-0.5 rounded font-mono min-w-[56px] text-center {{ $ep[0] === 'GET' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-amber-500/20 text-amber-400' }}">{{ $ep[0] }}</span>
                    <code class="text-xs text-gray-300 font-mono whitespace-nowrap" dir="ltr">{{ $ep[1] }}</code>
                    <span class="text-xs text-gray-600">{{ $ep[2] }}</span>
                    <span class="mr-auto"></span>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-rose-500/10 text-rose-400 font-semibold">admin</span>
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
        <p class="text-gray-500 text-sm mb-8 mr-4">أمثلة سريعة للبدء باستخدام الـ API</p>

        <!-- Tabs -->
        <div class="flex gap-2 mb-6 overflow-x-auto scrollbar-hide">
            <button onclick="showTab('browse')" class="tab-btn active text-xs px-4 py-2 rounded-lg border border-white/10 text-gray-400 hover:text-white transition whitespace-nowrap" id="tab-browse">تصفح الفعاليات</button>
            <button onclick="showTab('visit')" class="tab-btn text-xs px-4 py-2 rounded-lg border border-white/10 text-gray-400 hover:text-white transition whitespace-nowrap" id="tab-visit">طلب زيارة</button>
            <button onclick="showTab('rental')" class="tab-btn text-xs px-4 py-2 rounded-lg border border-white/10 text-gray-400 hover:text-white transition whitespace-nowrap" id="tab-rental">طلب إيجار</button>
            <button onclick="showTab('dashboard')" class="tab-btn text-xs px-4 py-2 rounded-lg border border-white/10 text-gray-400 hover:text-white transition whitespace-nowrap" id="tab-dashboard">لوحة التحكم</button>
        </div>

        <!-- Browse Events Tab -->
        <div id="content-browse" class="tab-content">
            <div class="code-block border border-white/5 rounded-2xl overflow-hidden">
                <div class="px-5 py-3 border-b border-white/5 flex items-center justify-between bg-white/[0.02]">
                    <span class="text-xs text-gray-500">Browse Events & Featured</span>
                    <button onclick="copyCode('browse')" class="text-xs text-gray-500 hover:text-white transition flex items-center gap-1 copy-btn" data-target="browse">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        Copy
                    </button>
                </div>
                <pre class="p-5 text-sm overflow-x-auto" dir="ltr"><code class="text-gray-300" id="code-browse"><span class="comment"># قائمة الفعاليات</span>
<span class="cmd">curl</span> <span class="url">http://localhost:8002/api/events</span> \
  <span class="flag">-H</span> <span class="str">"Accept: application/json"</span> \
  <span class="flag">-H</span> <span class="str">"Accept-Language: ar"</span>

<span class="comment"># الفعاليات المميزة</span>
<span class="cmd">curl</span> <span class="url">http://localhost:8002/api/events/featured</span> \
  <span class="flag">-H</span> <span class="str">"Accept: application/json"</span>

<span class="comment"># تفاصيل فعالية مع المساحات</span>
<span class="cmd">curl</span> <span class="url">http://localhost:8002/api/events/{event_id}/spaces</span> \
  <span class="flag">-H</span> <span class="str">"Accept: application/json"</span></code></pre>
            </div>
        </div>

        <!-- Visit Request Tab -->
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
  <span class="flag">-H</span> <span class="str">"Authorization: Bearer {token}"</span> \
  <span class="flag">-d</span> <span class="str">'{
    "event_id": "event-uuid-here",
    "visit_date": "2025-06-15",
    "visitors_count": 3,
    "notes": "نرغب في زيارة المعرض"
  }'</span></code></pre>
            </div>
        </div>

        <!-- Rental Request Tab -->
        <div id="content-rental" class="tab-content hidden">
            <div class="code-block border border-white/5 rounded-2xl overflow-hidden">
                <div class="px-5 py-3 border-b border-white/5 flex items-center justify-between bg-white/[0.02]">
                    <span class="text-xs text-gray-500">Create Rental Request (Requires Verified Profile)</span>
                    <button onclick="copyCode('rental')" class="text-xs text-gray-500 hover:text-white transition flex items-center gap-1 copy-btn" data-target="rental">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        Copy
                    </button>
                </div>
                <pre class="p-5 text-sm overflow-x-auto" dir="ltr"><code class="text-gray-300" id="code-rental"><span class="cmd">curl</span> <span class="flag">-X</span> POST <span class="url">http://localhost:8002/api/rental-requests</span> \
  <span class="flag">-H</span> <span class="str">"Content-Type: application/json"</span> \
  <span class="flag">-H</span> <span class="str">"Authorization: Bearer {token}"</span> \
  <span class="flag">-d</span> <span class="str">'{
    "space_id": "space-uuid-here",
    "start_date": "2025-06-01",
    "end_date": "2025-06-30",
    "notes": "نريد استئجار المساحة للمعرض"
  }'</span></code></pre>
            </div>
        </div>

        <!-- Dashboard Tab -->
        <div id="content-dashboard" class="tab-content hidden">
            <div class="code-block border border-white/5 rounded-2xl overflow-hidden">
                <div class="px-5 py-3 border-b border-white/5 flex items-center justify-between bg-white/[0.02]">
                    <span class="text-xs text-gray-500">Admin Dashboard Statistics</span>
                    <button onclick="copyCode('dashboard')" class="text-xs text-gray-500 hover:text-white transition flex items-center gap-1 copy-btn" data-target="dashboard">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        Copy
                    </button>
                </div>
                <pre class="p-5 text-sm overflow-x-auto" dir="ltr"><code class="text-gray-300" id="code-dashboard"><span class="comment"># إحصائيات عامة</span>
<span class="cmd">curl</span> <span class="url">http://localhost:8002/api/admin/dashboard</span> \
  <span class="flag">-H</span> <span class="str">"Authorization: Bearer {admin-token}"</span>

<span class="comment"># إحصائيات حسب الفترة</span>
<span class="cmd">curl</span> <span class="str">"<span class="url">http://localhost:8002/api/admin/dashboard?spaces_period=month&revenue_period=week</span>"</span> \
  <span class="flag">-H</span> <span class="str">"Authorization: Bearer {admin-token}"</span></code></pre>
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
            <!-- Request Flow -->
            <div class="glass border border-white/5 rounded-2xl p-6">
                <h4 class="font-bold text-emerald-400 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    مسار الطلب المصادق
                </h4>
                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <span class="w-6 h-6 bg-emerald-500/20 rounded-full flex items-center justify-center text-xs text-emerald-400 font-bold shrink-0 mt-0.5">1</span>
                        <div>
                            <div class="text-sm font-semibold text-white/80">تسجيل الدخول (Auth Service)</div>
                            <div class="text-xs text-gray-500">المستخدم يسجل دخول ويحصل على JWT Token من Auth Service</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="w-6 h-6 bg-emerald-500/20 rounded-full flex items-center justify-center text-xs text-emerald-400 font-bold shrink-0 mt-0.5">2</span>
                        <div>
                            <div class="text-sm font-semibold text-white/80">إرسال الطلب (Expo API)</div>
                            <div class="text-xs text-gray-500">إرسال الطلب مع التوكن في Header: <code class="text-emerald-300/60">Authorization: Bearer {token}</code></div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="w-6 h-6 bg-emerald-500/20 rounded-full flex items-center justify-center text-xs text-emerald-400 font-bold shrink-0 mt-0.5">3</span>
                        <div>
                            <div class="text-sm font-semibold text-white/80">التحقق (S2S)</div>
                            <div class="text-xs text-gray-500">Expo API يتحقق من التوكن عبر Auth Service باستخدام Service Token</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="w-6 h-6 bg-emerald-500/20 rounded-full flex items-center justify-center text-xs text-emerald-400 font-bold shrink-0 mt-0.5">4</span>
                        <div>
                            <div class="text-sm font-semibold text-white/80">الاستجابة</div>
                            <div class="text-xs text-gray-500">بعد التحقق، Expo API يعالج الطلب ويرد بالبيانات المطلوبة</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rental Flow -->
            <div class="glass border border-white/5 rounded-2xl p-6">
                <h4 class="font-bold text-amber-400 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    مسار طلب الإيجار
                </h4>
                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <span class="w-6 h-6 bg-amber-500/20 rounded-full flex items-center justify-center text-xs text-amber-400 font-bold shrink-0 mt-0.5">1</span>
                        <div>
                            <div class="text-sm font-semibold text-white/80">إنشاء ملف تجاري</div>
                            <div class="text-xs text-gray-500">المستخدم ينشئ ملف تجاري ويرفع المستندات المطلوبة</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="w-6 h-6 bg-amber-500/20 rounded-full flex items-center justify-center text-xs text-amber-400 font-bold shrink-0 mt-0.5">2</span>
                        <div>
                            <div class="text-sm font-semibold text-white/80">موافقة الإدارة</div>
                            <div class="text-xs text-gray-500">الأدمن يوافق على الملف التجاري بعد مراجعة المستندات</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="w-6 h-6 bg-amber-500/20 rounded-full flex items-center justify-center text-xs text-amber-400 font-bold shrink-0 mt-0.5">3</span>
                        <div>
                            <div class="text-sm font-semibold text-white/80">تقديم طلب الإيجار</div>
                            <div class="text-xs text-gray-500">المستخدم يختار مساحة ويقدم طلب إيجار مع التواريخ</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="w-6 h-6 bg-amber-500/20 rounded-full flex items-center justify-center text-xs text-amber-400 font-bold shrink-0 mt-0.5">4</span>
                        <div>
                            <div class="text-sm font-semibold text-white/80">الموافقة والدفع</div>
                            <div class="text-xs text-gray-500">الأدمن يوافق على الطلب ويسجل المدفوعات (كامل أو جزئي)</div>
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
                    <div class="w-9 h-9 bg-indigo-500/15 rounded-xl flex items-center justify-center">
                        <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <div>
                        <div class="font-bold text-sm">Auth Service</div>
                        <div class="text-[10px] text-gray-500">PHP 8.3 + JWT + Redis</div>
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
                    <div class="w-9 h-9 bg-emerald-500/15 rounded-xl flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div>
                        <div class="font-bold text-sm">Expo API</div>
                        <div class="text-[10px] text-gray-500">Events + Spaces + Rentals</div>
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
                    <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <span>Maham Expo API v{{ config('app.version', '1.0.0') }}</span>
            </div>
            <div class="flex items-center gap-4">
                <a href="http://localhost:8001/docs" class="hover:text-indigo-400 transition">Auth Service</a>
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
