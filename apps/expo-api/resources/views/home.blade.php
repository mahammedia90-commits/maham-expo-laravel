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
                <a href="#statistics-api" class="nav-link text-gray-400 hover:text-white transition">الإحصائيات</a>
                <a href="#user-api" class="nav-link text-gray-400 hover:text-white transition">المستخدم</a>
                <a href="#sponsor-api" class="nav-link text-gray-400 hover:text-white transition">الراعي</a>
                <a href="#merchant-api" class="nav-link text-gray-400 hover:text-white transition">التاجر</a>
                <a href="#investor-api" class="nav-link text-gray-400 hover:text-white transition">المستثمر</a>
                <a href="#admin-api" class="nav-link text-gray-400 hover:text-white transition">الإدارة</a>
                <a href="#supervisor-api" class="nav-link text-gray-400 hover:text-white transition">المشرف</a>
                <a href="#superadmin-api" class="nav-link text-gray-400 hover:text-white transition">المدير العام</a>
                <a href="#quickstart" class="nav-link text-gray-400 hover:text-white transition">البدء السريع</a>
                <a href="#env-vars" class="nav-link text-gray-400 hover:text-white transition">المتغيرات</a>
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
                <a href="https://auth-service-api.mahamexpo.sa/docs" class="bg-white/5 hover:bg-white/10 text-white px-7 py-3 rounded-xl font-bold transition-all border border-white/10 hover:border-white/20 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    Auth Service
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
                    ['Spaces & Approval', 'نظام مساحات مع مراجعة إدارية (موافقة/رفض) قبل النشر. دعم فلترة حسب الحالة وأقسام قابلة للتخصيص.', 'teal', 'M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z'],
                    ['Visit Requests', 'طلبات زيارة المعارض مع نظام موافقة ورفض وتتبع الحالات الكامل.', 'cyan', 'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z'],
                    ['Rental Requests', 'نظام إيجار متكامل مع إدارة المدفوعات والحالات المتعددة وتتبع الإيرادات.', 'amber', 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'],
                    ['Business Profiles', 'ملفات تجارية قابلة للتحقق مع نظام موافقة إدارية ورفع المستندات.', 'purple', 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                    ['OTP & Email Auth', 'تسجيل دخول بطريقتين: رقم الجوال مع رمز OTP أو البريد الإلكتروني وكلمة المرور. مع وضع اختبار قابل للتفعيل.', 'rose', 'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z'],
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
                        ['GET', '/api/v1/categories', 'قائمة التصنيفات', 'يدعم الفلترة والبحث'],
                        ['GET', '/api/v1/categories/{category}', 'تفاصيل تصنيف', 'بيانات التصنيف الكاملة'],
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
                        ['GET', '/api/v1/cities', 'قائمة المدن', 'جميع المدن المتاحة'],
                        ['GET', '/api/v1/cities/{city}', 'تفاصيل مدينة', 'بيانات المدينة والفعاليات المرتبطة'],
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
                        ['GET', '/api/v1/events', 'قائمة الفعاليات', 'يدعم الفلترة والبحث والترتيب مع pagination'],
                        ['GET', '/api/v1/events/featured', 'الفعاليات المميزة', 'الفعاليات المميزة والنشطة حالياً'],
                        ['GET', '/api/v1/events/{event}', 'تفاصيل فعالية', 'بيانات الفعالية الكاملة مع الإحصائيات'],
                        ['GET', '/api/v1/events/{event}/spaces', 'مساحات الفعالية', 'جميع المساحات المتاحة في الفعالية'],
                        ['GET', '/api/v1/events/{event}/sections', 'أقسام الفعالية', 'الأقسام والمساحات المرتبطة بها'],
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
                    <code class="text-xs text-gray-300 font-mono" dir="ltr">/api/v1/spaces/{space}</code>
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
                    <code class="text-xs text-gray-300 font-mono" dir="ltr">/api/v1/services</code>
                    <span class="text-xs text-gray-600">قائمة الخدمات</span>
                    <span class="mr-auto"></span>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-green-500/10 text-green-400 font-semibold">public</span>
                </div>
            </div>
        </div>

        <!-- Statistics (Public) -->
        <div id="statistics-api" class="glass border border-white/5 rounded-2xl overflow-hidden">
            <div class="px-6 py-3 border-b border-white/5 bg-white/[0.02]">
                <h5 class="text-xs font-bold text-gray-400 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    الإحصائيات - Statistics
                </h5>
            </div>
            @php
                $statsEps = [
                    ['GET', '/api/v1/statistics', 'إحصائيات المنصة', 'نظرة عامة شاملة — الفعاليات، المساحات، التصنيفات، المدن'],
                    ['GET', '/api/v1/statistics/events', 'إحصائيات الفعاليات', 'التوزيع حسب التصنيف والمدينة مع الأعداد'],
                    ['GET', '/api/v1/statistics/spaces', 'إحصائيات المساحات', 'التوزيع حسب الحالة والنوع ونطاق الأسعار'],
                ];
            @endphp
            @foreach($statsEps as $ep)
            <div class="endpoint-row flex items-center px-6 py-3.5 gap-3 {{ !$loop->last ? 'border-b border-white/5' : '' }}">
                <span class="method-badge font-bold px-2 py-0.5 rounded font-mono min-w-[56px] text-center bg-emerald-500/20 text-emerald-400">{{ $ep[0] }}</span>
                <code class="text-xs text-gray-300 font-mono" dir="ltr">{{ $ep[1] }}</code>
                <span class="text-xs text-gray-600">{{ $ep[2] }}</span>
                <span class="mr-auto"></span>
                <span class="text-[10px] px-2 py-0.5 rounded-full bg-green-500/10 text-green-400 font-semibold">public</span>
            </div>
            @endforeach
        </div>

        <!-- Sponsors (Public) -->
        <div class="glass border border-white/5 rounded-2xl overflow-hidden mt-4">
            <div class="px-6 py-3 border-b border-white/5 bg-white/[0.02]">
                <h5 class="text-xs font-bold text-gray-400">الرعاة - Sponsors (Public)</h5>
            </div>
            @php
                $sponsorPublicEps = [
                    ['GET', '/api/v1/events/{event}/sponsors', 'رعاة الفعالية', 'قائمة رعاة فعالية معينة'],
                    ['GET', '/api/v1/events/{event}/sponsor-packages', 'باقات الرعاية', 'الباقات المتاحة للرعاية'],
                ];
            @endphp
            @foreach($sponsorPublicEps as $ep)
            <div class="endpoint-row flex items-center px-6 py-3.5 gap-3 {{ !$loop->last ? 'border-b border-white/5' : '' }}">
                <span class="method-badge font-bold px-2 py-0.5 rounded font-mono min-w-[56px] text-center bg-emerald-500/20 text-emerald-400">{{ $ep[0] }}</span>
                <code class="text-xs text-gray-300 font-mono" dir="ltr">{{ $ep[1] }}</code>
                <span class="text-xs text-gray-600">{{ $ep[2] }}</span>
                <span class="mr-auto"></span>
                <span class="text-[10px] px-2 py-0.5 rounded-full bg-green-500/10 text-green-400 font-semibold">public</span>
            </div>
            @endforeach
        </div>

        <!-- Ratings (Public) -->
        <div class="glass border border-white/5 rounded-2xl overflow-hidden mt-4">
            <div class="px-6 py-3 border-b border-white/5 bg-white/[0.02]">
                <h5 class="text-xs font-bold text-gray-400">التقييمات - Ratings (Public)</h5>
            </div>
            @php
                $ratingPublicEps = [
                    ['GET', '/api/v1/ratings', 'قائمة التقييمات', 'التقييمات المعتمدة مع pagination'],
                    ['GET', '/api/v1/ratings/summary', 'ملخص التقييمات', 'المتوسط والتوزيع حسب النجوم'],
                ];
            @endphp
            @foreach($ratingPublicEps as $ep)
            <div class="endpoint-row flex items-center px-6 py-3.5 gap-3 {{ !$loop->last ? 'border-b border-white/5' : '' }}">
                <span class="method-badge font-bold px-2 py-0.5 rounded font-mono min-w-[56px] text-center bg-emerald-500/20 text-emerald-400">{{ $ep[0] }}</span>
                <code class="text-xs text-gray-300 font-mono" dir="ltr">{{ $ep[1] }}</code>
                <span class="text-xs text-gray-600">{{ $ep[2] }}</span>
                <span class="mr-auto"></span>
                <span class="text-[10px] px-2 py-0.5 rounded-full bg-green-500/10 text-green-400 font-semibold">public</span>
            </div>
            @endforeach
        </div>

        <!-- Pages, FAQs, Banners (Public CMS) -->
        <div class="glass border border-white/5 rounded-2xl overflow-hidden mt-4">
            <div class="px-6 py-3 border-b border-white/5 bg-white/[0.02]">
                <h5 class="text-xs font-bold text-gray-400">المحتوى - Pages, FAQs & Banners (Public)</h5>
            </div>
            @php
                $cmsPublicEps = [
                    ['GET', '/api/v1/pages', 'قائمة الصفحات', 'الصفحات المنشورة'],
                    ['GET', '/api/v1/pages/{slug}', 'عرض صفحة', 'محتوى الصفحة بالـ slug'],
                    ['GET', '/api/v1/faqs', 'قائمة الأسئلة الشائعة', 'مع الفلترة والبحث'],
                    ['GET', '/api/v1/faqs/categories', 'تصنيفات الأسئلة', 'التصنيفات المتاحة'],
                    ['GET', '/api/v1/faqs/{faq}', 'تفاصيل سؤال', 'السؤال والإجابة الكاملة'],
                    ['POST', '/api/v1/faqs/{faq}/helpful', 'تقييم الإجابة', 'هل كانت الإجابة مفيدة؟'],
                    ['GET', '/api/v1/banners', 'قائمة البانرات', 'البانرات النشطة حالياً'],
                    ['POST', '/api/v1/banners/{banner}/click', 'تسجيل نقرة', 'تتبع نقرات البانر'],
                ];
            @endphp
            @foreach($cmsPublicEps as $ep)
            <div class="endpoint-row flex items-center px-6 py-3.5 gap-3 {{ !$loop->last ? 'border-b border-white/5' : '' }}">
                <span class="method-badge font-bold px-2 py-0.5 rounded font-mono min-w-[56px] text-center {{ $ep[0] === 'GET' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-blue-500/20 text-blue-400' }}">{{ $ep[0] }}</span>
                <code class="text-xs text-gray-300 font-mono" dir="ltr">{{ $ep[1] }}</code>
                <span class="text-xs text-gray-600">{{ $ep[2] }}</span>
                <span class="mr-auto"></span>
                <span class="text-[10px] px-2 py-0.5 rounded-full bg-green-500/10 text-green-400 font-semibold">public</span>
            </div>
            @endforeach
        </div>

        <!-- Business Activity Types (Public) -->
        <div class="glass border border-white/5 rounded-2xl overflow-hidden mt-4">
            <div class="px-6 py-3 border-b border-white/5 bg-white/[0.02]">
                <h5 class="text-xs font-bold text-gray-400">أنواع النشاط التجاري - Business Activity Types (Public)</h5>
            </div>
            @php
                $batPublicEps = [
                    ['GET', '/api/v1/business-activity-types', 'قائمة أنواع النشاط', 'أنواع النشاط التجاري النشطة'],
                    ['GET', '/api/v1/business-activity-types/{id}', 'تفاصيل نوع نشاط', 'معلومات نوع النشاط التجاري'],
                ];
            @endphp
            @foreach($batPublicEps as $ep)
            <div class="endpoint-row flex items-center px-6 py-3.5 gap-3 {{ !$loop->last ? 'border-b border-white/5' : '' }}">
                <span class="method-badge font-bold px-2 py-0.5 rounded font-mono min-w-[56px] text-center bg-emerald-500/20 text-emerald-400">{{ $ep[0] }}</span>
                <code class="text-xs text-gray-300 font-mono" dir="ltr">{{ $ep[1] }}</code>
                <span class="text-xs text-gray-600">{{ $ep[2] }}</span>
                <span class="mr-auto"></span>
                <span class="text-[10px] px-2 py-0.5 rounded-full bg-green-500/10 text-green-400 font-semibold">public</span>
            </div>
            @endforeach
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
                        ['GET', '/api/v1/profile', 'عرض الملف التجاري', 'بيانات الملف التجاري الحالي مع حالة التحقق'],
                        ['POST', '/api/v1/profile', 'إنشاء ملف تجاري', 'formdata: company_name, phone, type, avatar, logo, CR image, ID image'],
                        ['PUT', '/api/v1/profile', 'تحديث الملف التجاري', 'formdata: تحديث بيانات الملف مع رفع ملفات جديدة'],
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
                        ['GET', '/api/v1/favorites', 'قائمة المفضلة', 'عرض جميع العناصر المفضلة'],
                        ['POST', '/api/v1/favorites', 'إضافة للمفضلة', 'favoritable_type, favoritable_id'],
                        ['DELETE', '/api/v1/favorites/{favorite}', 'إزالة من المفضلة', 'حذف عنصر من المفضلة'],
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
                        ['GET', '/api/v1/notifications', 'قائمة الإشعارات', 'جميع الإشعارات مع pagination'],
                        ['GET', '/api/v1/notifications/unread-count', 'عدد غير المقروءة', 'عدد الإشعارات غير المقروءة'],
                        ['PUT', '/api/v1/notifications/{notification}/read', 'تحديد كمقروء', 'تحديد إشعار واحد كمقروء'],
                        ['PUT', '/api/v1/notifications/read-all', 'قراءة الكل', 'تحديد جميع الإشعارات كمقروءة'],
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

        <!-- Notification Preferences -->
        <div class="mb-6 glass border border-white/5 rounded-2xl overflow-hidden">
            <div class="px-6 py-3 border-b border-white/5 bg-white/[0.02]">
                <h5 class="text-xs font-bold text-gray-400">تفضيلات الإشعارات - Notification Preferences</h5>
            </div>
            <div class="divide-y divide-white/5">
                @php
                    $notifPrefEps = [
                        ['GET', '/api/v1/notifications/preferences', 'عرض التفضيلات', 'notification-preferences.view', 'تفضيلات الإشعارات الحالية'],
                        ['PUT', '/api/v1/notifications/preferences', 'تحديث التفضيلات', 'notification-preferences.update', 'تعديل تفضيلات الإشعارات'],
                    ];
                @endphp
                @foreach($notifPrefEps as $ep)
                <div class="endpoint-row flex items-center px-6 py-3.5 gap-3">
                    <span class="method-badge font-bold px-2 py-0.5 rounded font-mono min-w-[56px] text-center {{ $ep[0] === 'GET' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-amber-500/20 text-amber-400' }}">{{ $ep[0] }}</span>
                    <code class="text-xs text-gray-300 font-mono whitespace-nowrap" dir="ltr">{{ $ep[1] }}</code>
                    <span class="text-xs text-gray-600">{{ $ep[2] }}</span>
                    <span class="mr-auto"></span>
                    <span class="text-[9px] px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-400/70 font-mono">{{ $ep[3] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Ratings (Authenticated CRUD) -->
        <div class="mb-6 glass border border-white/5 rounded-2xl overflow-hidden">
            <div class="px-6 py-3 border-b border-white/5 bg-white/[0.02]">
                <h5 class="text-xs font-bold text-gray-400">التقييمات - Ratings (Authenticated)</h5>
            </div>
            <div class="divide-y divide-white/5">
                @php
                    $ratingAuthEps = [
                        ['POST', '/api/v1/ratings', 'إنشاء تقييم', 'ratings.create'],
                        ['PUT', '/api/v1/ratings/{rating}', 'تعديل تقييم', 'ratings.update'],
                        ['DELETE', '/api/v1/ratings/{rating}', 'حذف تقييم', 'ratings.delete'],
                    ];
                @endphp
                @foreach($ratingAuthEps as $ep)
                <div class="endpoint-row flex items-center px-6 py-3.5 gap-3">
                    <span class="method-badge font-bold px-2 py-0.5 rounded font-mono min-w-[56px] text-center {{ $ep[0] === 'POST' ? 'bg-blue-500/20 text-blue-400' : ($ep[0] === 'PUT' ? 'bg-amber-500/20 text-amber-400' : 'bg-rose-500/20 text-rose-400') }}">{{ $ep[0] }}</span>
                    <code class="text-xs text-gray-300 font-mono whitespace-nowrap" dir="ltr">{{ $ep[1] }}</code>
                    <span class="text-xs text-gray-600">{{ $ep[2] }}</span>
                    <span class="mr-auto"></span>
                    <span class="text-[9px] px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-400/70 font-mono">{{ $ep[3] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Support Tickets -->
        <div class="mb-6 glass border border-white/5 rounded-2xl overflow-hidden">
            <div class="px-6 py-3 border-b border-white/5 bg-white/[0.02]">
                <h5 class="text-xs font-bold text-gray-400">تذاكر الدعم - Support Tickets</h5>
            </div>
            <div class="divide-y divide-white/5">
                @php
                    $ticketEps = [
                        ['GET', '/api/v1/support-tickets', 'قائمة التذاكر', 'support-tickets.view'],
                        ['POST', '/api/v1/support-tickets', 'إنشاء تذكرة', 'support-tickets.create'],
                        ['GET', '/api/v1/support-tickets/{id}', 'تفاصيل تذكرة', 'support-tickets.view'],
                        ['POST', '/api/v1/support-tickets/{id}/reply', 'الرد على تذكرة', 'support-tickets.reply'],
                        ['PUT', '/api/v1/support-tickets/{id}/close', 'إغلاق تذكرة', 'support-tickets.close'],
                        ['PUT', '/api/v1/support-tickets/{id}/reopen', 'إعادة فتح تذكرة', 'support-tickets.create'],
                    ];
                @endphp
                @foreach($ticketEps as $ep)
                <div class="endpoint-row flex items-center px-6 py-3.5 gap-3">
                    <span class="method-badge font-bold px-2 py-0.5 rounded font-mono min-w-[56px] text-center {{ $ep[0] === 'GET' ? 'bg-emerald-500/20 text-emerald-400' : ($ep[0] === 'POST' ? 'bg-blue-500/20 text-blue-400' : 'bg-amber-500/20 text-amber-400') }}">{{ $ep[0] }}</span>
                    <code class="text-xs text-gray-300 font-mono whitespace-nowrap" dir="ltr">{{ $ep[1] }}</code>
                    <span class="text-xs text-gray-600">{{ $ep[2] }}</span>
                    <span class="mr-auto"></span>
                    <span class="text-[9px] px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-400/70 font-mono">{{ $ep[3] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Invoices (User - own) -->
        <div class="mb-6 glass border border-white/5 rounded-2xl overflow-hidden">
            <div class="px-6 py-3 border-b border-white/5 bg-white/[0.02]">
                <h5 class="text-xs font-bold text-gray-400">الفواتير - Invoices (Own)</h5>
            </div>
            <div class="divide-y divide-white/5">
                @php
                    $invoiceUserEps = [
                        ['GET', '/api/v1/invoices', 'قائمة الفواتير', 'invoices.view'],
                        ['GET', '/api/v1/invoices/{invoice}', 'تفاصيل فاتورة', 'invoices.view'],
                    ];
                @endphp
                @foreach($invoiceUserEps as $ep)
                <div class="endpoint-row flex items-center px-6 py-3.5 gap-3">
                    <span class="method-badge font-bold px-2 py-0.5 rounded font-mono min-w-[56px] text-center bg-emerald-500/20 text-emerald-400">{{ $ep[0] }}</span>
                    <code class="text-xs text-gray-300 font-mono whitespace-nowrap" dir="ltr">{{ $ep[1] }}</code>
                    <span class="text-xs text-gray-600">{{ $ep[2] }}</span>
                    <span class="mr-auto"></span>
                    <span class="text-[9px] px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-400/70 font-mono">{{ $ep[3] }}</span>
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
                        ['GET', '/api/v1/visit-requests', 'قائمة طلبات الزيارة', 'طلبات المستخدم الحالي'],
                        ['POST', '/api/v1/visit-requests', 'إنشاء طلب زيارة', 'event_id, visit_date, visitors_count, notes'],
                        ['GET', '/api/v1/visit-requests/{visitRequest}', 'تفاصيل طلب زيارة', 'بيانات الطلب الكاملة'],
                        ['PUT', '/api/v1/visit-requests/{visitRequest}', 'تحديث طلب زيارة', 'تعديل بيانات الطلب'],
                        ['DELETE', '/api/v1/visit-requests/{visitRequest}', 'حذف طلب زيارة', 'إلغاء الطلب'],
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
                        ['GET', '/api/v1/rental-requests', 'قائمة طلبات الإيجار', 'طلبات المستخدم الحالي'],
                        ['POST', '/api/v1/rental-requests', 'إنشاء طلب إيجار', 'space_id, start_date, end_date, notes'],
                        ['GET', '/api/v1/rental-requests/{rentalRequest}', 'تفاصيل طلب إيجار', 'بيانات الطلب مع المدفوعات'],
                        ['PUT', '/api/v1/rental-requests/{rentalRequest}', 'تحديث طلب إيجار', 'تعديل بيانات الطلب'],
                        ['DELETE', '/api/v1/rental-requests/{rentalRequest}', 'حذف طلب إيجار', 'إلغاء الطلب'],
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
                <code class="text-sm text-gray-300 font-mono" dir="ltr">/api/v1/admin/dashboard</code>
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

        <!-- Admin Statistics -->
        <div class="mb-6 glass border border-white/5 rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-white/5 flex items-center gap-3">
                <span class="method-badge font-bold px-2.5 py-1 rounded-md bg-emerald-500/20 text-emerald-400 font-mono">GET</span>
                <code class="text-sm text-gray-300 font-mono" dir="ltr">/api/v1/admin/statistics</code>
                <span class="text-xs text-gray-600 mr-auto">الإحصائيات — نفس بيانات لوحة التحكم</span>
                <span class="text-[10px] px-2 py-0.5 rounded-full bg-rose-500/10 text-rose-400 font-semibold">admin</span>
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
                        ['GET', '/api/v1/admin/events', 'قائمة الفعاليات', 'يدعم الفلترة والبحث والترتيب'],
                        ['POST', '/api/v1/admin/events', 'إنشاء فعالية', 'name, description, start_date, end_date, city_id, category_id'],
                        ['GET', '/api/v1/admin/events/{event}', 'تفاصيل فعالية', 'البيانات الكاملة مع الأقسام والمساحات'],
                        ['PUT', '/api/v1/admin/events/{event}', 'تحديث فعالية', 'تعديل بيانات الفعالية'],
                        ['DELETE', '/api/v1/admin/events/{event}', 'حذف فعالية', 'حذف الفعالية وجميع المرتبطات'],
                        ['GET', '/api/v1/admin/events/{event}/sections', 'أقسام الفعالية', 'عرض جميع الأقسام'],
                        ['POST', '/api/v1/admin/events/{event}/sections', 'إنشاء قسم', 'إضافة قسم جديد للفعالية'],
                        ['GET', '/api/v1/admin/events/{event}/spaces', 'مساحات الفعالية', 'عرض جميع المساحات'],
                        ['POST', '/api/v1/admin/events/{event}/spaces', 'إنشاء مساحة', 'إضافة مساحة جديدة للفعالية'],
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
                            ['GET', '/api/v1/admin/sections/{section}', 'التفاصيل'],
                            ['PUT', '/api/v1/admin/sections/{section}', 'تحديث'],
                            ['DELETE', '/api/v1/admin/sections/{section}', 'حذف'],
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
                            ['GET', '/api/v1/admin/spaces/{space}', 'التفاصيل'],
                            ['PUT', '/api/v1/admin/spaces/{space}', 'تحديث'],
                            ['DELETE', '/api/v1/admin/spaces/{space}', 'حذف'],
                            ['PUT', '/api/v1/admin/spaces/{space}/approve', 'الموافقة على المساحة'],
                            ['PUT', '/api/v1/admin/spaces/{space}/reject', 'رفض المساحة (مع سبب)'],
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
                        ['GET', '/api/v1/admin/services', 'قائمة الخدمات'],
                        ['POST', '/api/v1/admin/services', 'إنشاء خدمة'],
                        ['GET', '/api/v1/admin/services/{service}', 'تفاصيل خدمة'],
                        ['PUT', '/api/v1/admin/services/{service}', 'تحديث خدمة'],
                        ['DELETE', '/api/v1/admin/services/{service}', 'حذف خدمة'],
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
                        ['GET', '/api/v1/admin/visit-requests', 'قائمة جميع الطلبات', 'فلترة حسب الحالة والفعالية'],
                        ['GET', '/api/v1/admin/visit-requests/{visitRequest}', 'تفاصيل طلب', 'بيانات الطلب مع بيانات المستخدم'],
                        ['PUT', '/api/v1/admin/visit-requests/{visitRequest}/approve', 'قبول الطلب', 'الموافقة على طلب الزيارة'],
                        ['PUT', '/api/v1/admin/visit-requests/{visitRequest}/reject', 'رفض الطلب', 'رفض طلب الزيارة مع سبب'],
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
                        ['GET', '/api/v1/admin/rental-requests', 'قائمة جميع الطلبات', 'فلترة حسب الحالة والمدفوعات'],
                        ['GET', '/api/v1/admin/rental-requests/{rentalRequest}', 'تفاصيل طلب', 'بيانات الطلب مع سجل المدفوعات'],
                        ['PUT', '/api/v1/admin/rental-requests/{rentalRequest}/approve', 'قبول الطلب', 'الموافقة مع تحديد المبلغ'],
                        ['PUT', '/api/v1/admin/rental-requests/{rentalRequest}/reject', 'رفض الطلب', 'رفض الطلب مع سبب'],
                        ['POST', '/api/v1/admin/rental-requests/{rentalRequest}/payment', 'تسجيل دفعة', 'تسجيل دفعة جديدة للطلب'],
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
                        ['GET', '/api/v1/admin/profiles', 'قائمة الملفات التجارية', 'فلترة حسب حالة التحقق'],
                        ['GET', '/api/v1/admin/profiles/{profile}', 'تفاصيل ملف تجاري', 'بيانات الملف مع المستندات'],
                        ['PUT', '/api/v1/admin/profiles/{profile}/approve', 'قبول الملف', 'الموافقة على الملف التجاري'],
                        ['PUT', '/api/v1/admin/profiles/{profile}/reject', 'رفض الملف', 'رفض الملف مع سبب'],
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

        <!-- Admin Sponsors Management -->
        <div class="glass border border-white/5 rounded-2xl overflow-hidden mb-4">
            <div class="px-6 py-3 border-b border-white/5 bg-white/[0.02]">
                <h5 class="text-xs font-bold text-gray-400">إدارة الرعاة - Sponsors Management</h5>
            </div>
            <div class="divide-y divide-white/5">
                @php
                    $adminSponsorEps = [
                        ['GET', '/api/v1/admin/sponsors', 'قائمة الرعاة'],
                        ['POST', '/api/v1/admin/sponsors', 'إنشاء راعي'],
                        ['GET', '/api/v1/admin/sponsors/{sponsor}', 'تفاصيل راعي'],
                        ['PUT', '/api/v1/admin/sponsors/{sponsor}', 'تحديث راعي'],
                        ['DELETE', '/api/v1/admin/sponsors/{sponsor}', 'حذف راعي'],
                        ['PUT', '/api/v1/admin/sponsors/{sponsor}/approve', 'قبول راعي'],
                        ['PUT', '/api/v1/admin/sponsors/{sponsor}/activate', 'تفعيل راعي'],
                        ['PUT', '/api/v1/admin/sponsors/{sponsor}/suspend', 'تعليق راعي'],
                    ];
                @endphp
                @foreach($adminSponsorEps as $ep)
                <div class="endpoint-row flex items-center px-5 py-2.5 gap-2">
                    <span class="method-badge font-bold px-1.5 py-0.5 rounded font-mono min-w-[44px] text-center {{ $ep[0] === 'GET' ? 'bg-emerald-500/15 text-emerald-400' : ($ep[0] === 'POST' ? 'bg-blue-500/15 text-blue-400' : ($ep[0] === 'PUT' ? 'bg-amber-500/15 text-amber-400' : 'bg-rose-500/15 text-rose-400')) }}">{{ $ep[0] }}</span>
                    <code class="text-[11px] text-gray-400 font-mono" dir="ltr">{{ $ep[1] }}</code>
                    <span class="text-[11px] text-gray-600 mr-auto">{{ $ep[2] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Admin Sponsor Packages, Contracts, Payments, Benefits, Assets -->
        <div class="glass border border-white/5 rounded-2xl overflow-hidden mb-4">
            <div class="px-6 py-3 border-b border-white/5 bg-white/[0.02]">
                <h5 class="text-xs font-bold text-gray-400">باقات الرعاية والعقود والمدفوعات - Sponsor Packages, Contracts, Payments, Benefits & Assets</h5>
            </div>
            <div class="divide-y divide-white/5">
                @php
                    $adminSponsorSubEps = [
                        ['GET', '/api/v1/admin/events/{event}/sponsor-packages', 'باقات الفعالية'],
                        ['POST', '/api/v1/admin/events/{event}/sponsor-packages', 'إنشاء باقة'],
                        ['GET', '/api/v1/admin/sponsor-packages/{id}', 'تفاصيل باقة'],
                        ['PUT', '/api/v1/admin/sponsor-packages/{id}', 'تحديث باقة'],
                        ['DELETE', '/api/v1/admin/sponsor-packages/{id}', 'حذف باقة'],
                        ['GET', '/api/v1/admin/sponsor-contracts', 'قائمة العقود'],
                        ['POST', '/api/v1/admin/sponsor-contracts', 'إنشاء عقد'],
                        ['GET', '/api/v1/admin/sponsor-contracts/{id}', 'تفاصيل عقد'],
                        ['PUT', '/api/v1/admin/sponsor-contracts/{id}', 'تحديث عقد'],
                        ['PUT', '/api/v1/admin/sponsor-contracts/{id}/approve', 'قبول عقد'],
                        ['PUT', '/api/v1/admin/sponsor-contracts/{id}/reject', 'رفض عقد'],
                        ['PUT', '/api/v1/admin/sponsor-contracts/{id}/complete', 'إتمام عقد'],
                        ['GET', '/api/v1/admin/sponsor-payments', 'قائمة المدفوعات'],
                        ['POST', '/api/v1/admin/sponsor-payments', 'إنشاء دفعة'],
                        ['GET', '/api/v1/admin/sponsor-payments/{id}', 'تفاصيل دفعة'],
                        ['PUT', '/api/v1/admin/sponsor-payments/{id}', 'تحديث دفعة'],
                        ['PUT', '/api/v1/admin/sponsor-payments/{id}/mark-paid', 'تأكيد الدفع'],
                        ['GET', '/api/v1/admin/sponsor-benefits', 'قائمة المزايا'],
                        ['POST', '/api/v1/admin/sponsor-benefits', 'إنشاء ميزة'],
                        ['GET', '/api/v1/admin/sponsor-benefits/{id}', 'تفاصيل ميزة'],
                        ['PUT', '/api/v1/admin/sponsor-benefits/{id}', 'تحديث ميزة'],
                        ['PUT', '/api/v1/admin/sponsor-benefits/{id}/deliver', 'تأكيد تسليم ميزة'],
                        ['GET', '/api/v1/admin/sponsor-assets', 'قائمة ملفات الرعاة'],
                        ['GET', '/api/v1/admin/sponsor-assets/{id}', 'تفاصيل ملف'],
                        ['PUT', '/api/v1/admin/sponsor-assets/{id}/approve', 'قبول ملف'],
                        ['PUT', '/api/v1/admin/sponsor-assets/{id}/reject', 'رفض ملف'],
                    ];
                @endphp
                @foreach($adminSponsorSubEps as $ep)
                <div class="endpoint-row flex items-center px-5 py-2.5 gap-2">
                    <span class="method-badge font-bold px-1.5 py-0.5 rounded font-mono min-w-[44px] text-center {{ $ep[0] === 'GET' ? 'bg-emerald-500/15 text-emerald-400' : ($ep[0] === 'POST' ? 'bg-blue-500/15 text-blue-400' : ($ep[0] === 'PUT' ? 'bg-amber-500/15 text-amber-400' : 'bg-rose-500/15 text-rose-400')) }}">{{ $ep[0] }}</span>
                    <code class="text-[11px] text-gray-400 font-mono" dir="ltr">{{ $ep[1] }}</code>
                    <span class="text-[11px] text-gray-600 mr-auto">{{ $ep[2] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Admin Ratings, Support Tickets, Rental Contracts, Invoices -->
        <div class="glass border border-white/5 rounded-2xl overflow-hidden mb-4">
            <div class="px-6 py-3 border-b border-white/5 bg-white/[0.02]">
                <h5 class="text-xs font-bold text-gray-400">التقييمات والتذاكر والعقود والفواتير - Ratings, Tickets, Contracts & Invoices</h5>
            </div>
            <div class="divide-y divide-white/5">
                @php
                    $adminMiscEps = [
                        ['GET', '/api/v1/admin/ratings', 'قائمة التقييمات', 'ratings.view-all'],
                        ['GET', '/api/v1/admin/ratings/{rating}', 'تفاصيل تقييم', 'ratings.view-all'],
                        ['PUT', '/api/v1/admin/ratings/{rating}/approve', 'قبول تقييم', 'ratings.approve'],
                        ['PUT', '/api/v1/admin/ratings/{rating}/reject', 'رفض تقييم', 'ratings.reject'],
                        ['DELETE', '/api/v1/admin/ratings/{rating}', 'حذف تقييم', 'ratings.delete'],
                        ['GET', '/api/v1/admin/support-tickets', 'قائمة التذاكر', 'support-tickets.view-all'],
                        ['GET', '/api/v1/admin/support-tickets/{id}', 'تفاصيل تذكرة', 'support-tickets.view-all'],
                        ['PUT', '/api/v1/admin/support-tickets/{id}/assign', 'تعيين موظف', 'support-tickets.assign'],
                        ['POST', '/api/v1/admin/support-tickets/{id}/reply', 'الرد', 'support-tickets.reply'],
                        ['PUT', '/api/v1/admin/support-tickets/{id}/resolve', 'حل التذكرة', 'support-tickets.close'],
                        ['PUT', '/api/v1/admin/support-tickets/{id}/close', 'إغلاق التذكرة', 'support-tickets.close'],
                        ['DELETE', '/api/v1/admin/support-tickets/{id}', 'حذف تذكرة', 'support-tickets.delete'],
                        ['GET', '/api/v1/admin/rental-contracts', 'قائمة العقود', 'rental-contracts.view-all'],
                        ['POST', '/api/v1/admin/rental-contracts', 'إنشاء عقد', 'rental-contracts.create'],
                        ['GET', '/api/v1/admin/rental-contracts/{id}', 'تفاصيل عقد', 'rental-contracts.view-all'],
                        ['PUT', '/api/v1/admin/rental-contracts/{id}', 'تحديث عقد', 'rental-contracts.update'],
                        ['PUT', '/api/v1/admin/rental-contracts/{id}/approve', 'قبول عقد', 'rental-contracts.approve'],
                        ['PUT', '/api/v1/admin/rental-contracts/{id}/reject', 'رفض عقد', 'rental-contracts.reject'],
                        ['PUT', '/api/v1/admin/rental-contracts/{id}/terminate', 'إنهاء عقد', 'rental-contracts.terminate'],
                        ['GET', '/api/v1/admin/invoices', 'قائمة الفواتير', 'invoices.view-all'],
                        ['POST', '/api/v1/admin/invoices', 'إنشاء فاتورة', 'invoices.create'],
                        ['GET', '/api/v1/admin/invoices/{invoice}', 'تفاصيل فاتورة', 'invoices.view-all'],
                        ['PUT', '/api/v1/admin/invoices/{invoice}', 'تحديث فاتورة', 'invoices.update'],
                        ['PUT', '/api/v1/admin/invoices/{invoice}/issue', 'إصدار فاتورة', 'invoices.issue'],
                        ['PUT', '/api/v1/admin/invoices/{invoice}/mark-paid', 'تأكيد الدفع', 'invoices.mark-paid'],
                        ['PUT', '/api/v1/admin/invoices/{invoice}/cancel', 'إلغاء فاتورة', 'invoices.cancel'],
                    ];
                @endphp
                @foreach($adminMiscEps as $ep)
                <div class="endpoint-row flex items-center px-5 py-2.5 gap-2">
                    <span class="method-badge font-bold px-1.5 py-0.5 rounded font-mono min-w-[44px] text-center {{ $ep[0] === 'GET' ? 'bg-emerald-500/15 text-emerald-400' : ($ep[0] === 'POST' ? 'bg-blue-500/15 text-blue-400' : ($ep[0] === 'PUT' ? 'bg-amber-500/15 text-amber-400' : 'bg-rose-500/15 text-rose-400')) }}">{{ $ep[0] }}</span>
                    <code class="text-[11px] text-gray-400 font-mono" dir="ltr">{{ $ep[1] }}</code>
                    <span class="text-[11px] text-gray-600">{{ $ep[2] }}</span>
                    <span class="mr-auto"></span>
                    <span class="text-[9px] px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-400/70 font-mono">{{ $ep[3] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Admin Pages, FAQs, Banners -->
        <div class="glass border border-white/5 rounded-2xl overflow-hidden mb-4">
            <div class="px-6 py-3 border-b border-white/5 bg-white/[0.02]">
                <h5 class="text-xs font-bold text-gray-400">إدارة المحتوى - Pages, FAQs & Banners Management</h5>
            </div>
            <div class="divide-y divide-white/5">
                @php
                    $adminCmsEps = [
                        ['GET', '/api/v1/admin/pages', 'قائمة الصفحات', 'pages.view'],
                        ['POST', '/api/v1/admin/pages', 'إنشاء صفحة', 'pages.create'],
                        ['GET', '/api/v1/admin/pages/{page}', 'تفاصيل صفحة', 'pages.view'],
                        ['PUT', '/api/v1/admin/pages/{page}', 'تحديث صفحة', 'pages.update'],
                        ['DELETE', '/api/v1/admin/pages/{page}', 'حذف صفحة', 'pages.delete'],
                        ['GET', '/api/v1/admin/faqs', 'قائمة الأسئلة', 'faqs.view'],
                        ['POST', '/api/v1/admin/faqs', 'إنشاء سؤال', 'faqs.create'],
                        ['GET', '/api/v1/admin/faqs/{faq}', 'تفاصيل سؤال', 'faqs.view'],
                        ['PUT', '/api/v1/admin/faqs/{faq}', 'تحديث سؤال', 'faqs.update'],
                        ['DELETE', '/api/v1/admin/faqs/{faq}', 'حذف سؤال', 'faqs.delete'],
                        ['GET', '/api/v1/admin/banners', 'قائمة البانرات', 'banners.view'],
                        ['POST', '/api/v1/admin/banners', 'إنشاء بانر', 'banners.create'],
                        ['GET', '/api/v1/admin/banners/{banner}', 'تفاصيل بانر', 'banners.view'],
                        ['PUT', '/api/v1/admin/banners/{banner}', 'تحديث بانر', 'banners.update'],
                        ['DELETE', '/api/v1/admin/banners/{banner}', 'حذف بانر', 'banners.delete'],
                    ];
                @endphp
                @foreach($adminCmsEps as $ep)
                <div class="endpoint-row flex items-center px-5 py-2.5 gap-2">
                    <span class="method-badge font-bold px-1.5 py-0.5 rounded font-mono min-w-[44px] text-center {{ $ep[0] === 'GET' ? 'bg-emerald-500/15 text-emerald-400' : ($ep[0] === 'POST' ? 'bg-blue-500/15 text-blue-400' : ($ep[0] === 'PUT' ? 'bg-amber-500/15 text-amber-400' : 'bg-rose-500/15 text-rose-400')) }}">{{ $ep[0] }}</span>
                    <code class="text-[11px] text-gray-400 font-mono" dir="ltr">{{ $ep[1] }}</code>
                    <span class="text-[11px] text-gray-600">{{ $ep[2] }}</span>
                    <span class="mr-auto"></span>
                    <span class="text-[9px] px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-400/70 font-mono">{{ $ep[3] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Admin Member Types & Business Activity Types -->
        <div class="glass border border-white/5 rounded-2xl overflow-hidden mb-4">
            <div class="px-6 py-3 border-b border-white/5 bg-white/[0.02]">
                <h5 class="text-xs font-bold text-gray-400">أنواع الأعضاء والنشاط التجاري - Member Types & Business Activity Types (Admin)</h5>
            </div>
            <div class="divide-y divide-white/5">
                @php
                    $adminTypeEps = [
                        ['GET', '/api/v1/manage/member-types', 'قائمة أنواع الأعضاء', 'member-types.view'],
                        ['POST', '/api/v1/manage/member-types', 'إنشاء نوع عضو', 'member-types.create'],
                        ['GET', '/api/v1/manage/member-types/{id}', 'تفاصيل نوع عضو', 'member-types.view'],
                        ['PUT', '/api/v1/manage/member-types/{id}', 'تحديث نوع عضو', 'member-types.update'],
                        ['DELETE', '/api/v1/manage/member-types/{id}', 'حذف نوع عضو', 'member-types.delete'],
                        ['GET', '/api/v1/manage/business-activity-types', 'قائمة أنواع النشاط', 'business-activity-types.view'],
                        ['POST', '/api/v1/manage/business-activity-types', 'إنشاء نوع نشاط', 'business-activity-types.create'],
                        ['GET', '/api/v1/manage/business-activity-types/{id}', 'تفاصيل نوع نشاط', 'business-activity-types.view'],
                        ['PUT', '/api/v1/manage/business-activity-types/{id}', 'تحديث نوع نشاط', 'business-activity-types.update'],
                        ['DELETE', '/api/v1/manage/business-activity-types/{id}', 'حذف نوع نشاط', 'business-activity-types.delete'],
                    ];
                @endphp
                @foreach($adminTypeEps as $ep)
                <div class="endpoint-row flex items-center px-5 py-2.5 gap-2">
                    <span class="method-badge font-bold px-1.5 py-0.5 rounded font-mono min-w-[44px] text-center {{ $ep[0] === 'GET' ? 'bg-emerald-500/15 text-emerald-400' : ($ep[0] === 'POST' ? 'bg-blue-500/15 text-blue-400' : ($ep[0] === 'PUT' ? 'bg-amber-500/15 text-amber-400' : 'bg-rose-500/15 text-rose-400')) }}">{{ $ep[0] }}</span>
                    <code class="text-[11px] text-gray-400 font-mono" dir="ltr">{{ $ep[1] }}</code>
                    <span class="text-[11px] text-gray-600">{{ $ep[2] }}</span>
                    <span class="mr-auto"></span>
                    <span class="text-[9px] px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-400/70 font-mono">{{ $ep[3] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Admin Investors CRUD -->
        <div class="glass border border-white/5 rounded-2xl overflow-hidden mb-4">
            <div class="px-6 py-3 border-b border-white/5 bg-white/[0.02]">
                <h5 class="text-xs font-bold text-gray-400 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    إدارة المستثمرين - Investors Management (Admin)
                </h5>
            </div>
            <div class="divide-y divide-white/5">
                @php
                    $adminInvestorEps = [
                        ['GET', '/api/v1/manage/investors', 'قائمة المستثمرين', 'profiles.view-all'],
                        ['POST', '/api/v1/manage/investors', 'إنشاء مستثمر', 'profiles.approve'],
                        ['GET', '/api/v1/manage/investors/{investor}', 'تفاصيل مستثمر', 'profiles.view-all'],
                        ['PUT', '/api/v1/manage/investors/{investor}', 'تحديث مستثمر', 'profiles.approve'],
                        ['DELETE', '/api/v1/manage/investors/{investor}', 'حذف مستثمر', 'profiles.approve'],
                    ];
                @endphp
                @foreach($adminInvestorEps as $ep)
                <div class="endpoint-row flex items-center px-5 py-2.5 gap-2">
                    <span class="method-badge font-bold px-1.5 py-0.5 rounded font-mono min-w-[44px] text-center {{ $ep[0] === 'GET' ? 'bg-emerald-500/15 text-emerald-400' : ($ep[0] === 'POST' ? 'bg-blue-500/15 text-blue-400' : ($ep[0] === 'PUT' ? 'bg-amber-500/15 text-amber-400' : 'bg-rose-500/15 text-rose-400')) }}">{{ $ep[0] }}</span>
                    <code class="text-[11px] text-gray-400 font-mono" dir="ltr">{{ $ep[1] }}</code>
                    <span class="text-[11px] text-gray-600">{{ $ep[2] }}</span>
                    <span class="mr-auto"></span>
                    <span class="text-[9px] px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-400/70 font-mono">{{ $ep[3] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Admin Merchants CRUD -->
        <div class="glass border border-white/5 rounded-2xl overflow-hidden mb-4">
            <div class="px-6 py-3 border-b border-white/5 bg-white/[0.02]">
                <h5 class="text-xs font-bold text-gray-400 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                    إدارة التجار - Merchants Management (Admin)
                </h5>
            </div>
            <div class="divide-y divide-white/5">
                @php
                    $adminMerchantEps = [
                        ['GET', '/api/v1/manage/merchants', 'قائمة التجار', 'profiles.view-all'],
                        ['POST', '/api/v1/manage/merchants', 'إنشاء تاجر', 'profiles.approve'],
                        ['GET', '/api/v1/manage/merchants/{merchant}', 'تفاصيل تاجر', 'profiles.view-all'],
                        ['PUT', '/api/v1/manage/merchants/{merchant}', 'تحديث تاجر', 'profiles.approve'],
                        ['DELETE', '/api/v1/manage/merchants/{merchant}', 'حذف تاجر', 'profiles.approve'],
                    ];
                @endphp
                @foreach($adminMerchantEps as $ep)
                <div class="endpoint-row flex items-center px-5 py-2.5 gap-2">
                    <span class="method-badge font-bold px-1.5 py-0.5 rounded font-mono min-w-[44px] text-center {{ $ep[0] === 'GET' ? 'bg-emerald-500/15 text-emerald-400' : ($ep[0] === 'POST' ? 'bg-blue-500/15 text-blue-400' : ($ep[0] === 'PUT' ? 'bg-amber-500/15 text-amber-400' : 'bg-rose-500/15 text-rose-400')) }}">{{ $ep[0] }}</span>
                    <code class="text-[11px] text-gray-400 font-mono" dir="ltr">{{ $ep[1] }}</code>
                    <span class="text-[11px] text-gray-600">{{ $ep[2] }}</span>
                    <span class="mr-auto"></span>
                    <span class="text-[9px] px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-400/70 font-mono">{{ $ep[3] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Admin Sponsor Leads CRUD -->
        <div class="glass border border-white/5 rounded-2xl overflow-hidden mb-4">
            <div class="px-6 py-3 border-b border-white/5 bg-white/[0.02]">
                <h5 class="text-xs font-bold text-gray-400 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    عملاء الرعاة المحتملون - Sponsor Leads (Admin)
                </h5>
            </div>
            <div class="divide-y divide-white/5">
                @php
                    $adminLeadEps = [
                        ['GET', '/api/v1/manage/sponsor-leads', 'قائمة العملاء المحتملين', 'sponsor-leads.view-all'],
                        ['POST', '/api/v1/manage/sponsor-leads', 'إنشاء عميل محتمل', 'sponsor-leads.create'],
                        ['GET', '/api/v1/manage/sponsor-leads/{id}', 'تفاصيل عميل', 'sponsor-leads.view-all'],
                        ['PUT', '/api/v1/manage/sponsor-leads/{id}', 'تحديث عميل', 'sponsor-leads.update'],
                        ['DELETE', '/api/v1/manage/sponsor-leads/{id}', 'حذف عميل', 'sponsor-leads.delete'],
                    ];
                @endphp
                @foreach($adminLeadEps as $ep)
                <div class="endpoint-row flex items-center px-5 py-2.5 gap-2">
                    <span class="method-badge font-bold px-1.5 py-0.5 rounded font-mono min-w-[44px] text-center {{ $ep[0] === 'GET' ? 'bg-emerald-500/15 text-emerald-400' : ($ep[0] === 'POST' ? 'bg-blue-500/15 text-blue-400' : ($ep[0] === 'PUT' ? 'bg-amber-500/15 text-amber-400' : 'bg-rose-500/15 text-rose-400')) }}">{{ $ep[0] }}</span>
                    <code class="text-[11px] text-gray-400 font-mono" dir="ltr">{{ $ep[1] }}</code>
                    <span class="text-[11px] text-gray-600">{{ $ep[2] }}</span>
                    <span class="mr-auto"></span>
                    <span class="text-[9px] px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-400/70 font-mono">{{ $ep[3] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Admin Sponsor Deliverables CRUD -->
        <div class="glass border border-white/5 rounded-2xl overflow-hidden mb-4">
            <div class="px-6 py-3 border-b border-white/5 bg-white/[0.02]">
                <h5 class="text-xs font-bold text-gray-400 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    تسليمات الرعاية - Sponsor Deliverables (Admin)
                </h5>
            </div>
            <div class="divide-y divide-white/5">
                @php
                    $adminDeliverableEps = [
                        ['GET', '/api/v1/manage/sponsor-deliverables', 'قائمة التسليمات', 'sponsor-deliverables.view-all'],
                        ['POST', '/api/v1/manage/sponsor-deliverables', 'إنشاء تسليم', 'sponsor-deliverables.create'],
                        ['GET', '/api/v1/manage/sponsor-deliverables/{id}', 'تفاصيل تسليم', 'sponsor-deliverables.view-all'],
                        ['PUT', '/api/v1/manage/sponsor-deliverables/{id}', 'تحديث تسليم', 'sponsor-deliverables.update'],
                        ['DELETE', '/api/v1/manage/sponsor-deliverables/{id}', 'حذف تسليم', 'sponsor-deliverables.delete'],
                        ['PUT', '/api/v1/manage/sponsor-deliverables/{id}/approve', 'اعتماد تسليم', 'sponsor-deliverables.approve'],
                        ['PUT', '/api/v1/manage/sponsor-deliverables/{id}/reject', 'رفض تسليم', 'sponsor-deliverables.approve'],
                    ];
                @endphp
                @foreach($adminDeliverableEps as $ep)
                <div class="endpoint-row flex items-center px-5 py-2.5 gap-2">
                    <span class="method-badge font-bold px-1.5 py-0.5 rounded font-mono min-w-[44px] text-center {{ $ep[0] === 'GET' ? 'bg-emerald-500/15 text-emerald-400' : ($ep[0] === 'POST' ? 'bg-blue-500/15 text-blue-400' : ($ep[0] === 'PUT' ? 'bg-amber-500/15 text-amber-400' : 'bg-rose-500/15 text-rose-400')) }}">{{ $ep[0] }}</span>
                    <code class="text-[11px] text-gray-400 font-mono" dir="ltr">{{ $ep[1] }}</code>
                    <span class="text-[11px] text-gray-600">{{ $ep[2] }}</span>
                    <span class="mr-auto"></span>
                    <span class="text-[9px] px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-400/70 font-mono">{{ $ep[3] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    <section id="supervisor-api" class="section-anchor max-w-7xl mx-auto px-6 py-16">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-1 h-8 bg-orange-500 rounded-full"></div>
            <h3 class="text-2xl font-bold">واجهة المشرف</h3>
            <span class="text-xs bg-orange-500/10 text-orange-400 px-2.5 py-1 rounded-full">Supervisor API</span>
        </div>
        <p class="text-gray-500 text-sm mb-8 mr-4">نقاط الوصول للمشرف - تحكم كامل بجميع العمليات (نفس صلاحيات المدير)</p>

        <div class="mb-4 p-3 bg-orange-500/5 border border-orange-500/10 rounded-xl">
            <p class="text-xs text-orange-400/80"><span class="font-bold">الصلاحيات:</span> المشرف لديه تحكم كامل — نفس صلاحيات <code class="text-orange-300/60">admin</code> (إنشاء، تعديل، حذف، اعتماد، رفض)</p>
        </div>

        <!-- Supervisor Dashboard + Read-only -->
        <div class="mb-6 glass border border-white/5 rounded-2xl overflow-hidden">
            <div class="px-6 py-3 border-b border-white/5 bg-white/[0.02]">
                <h5 class="text-xs font-bold text-gray-400 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    لوحة التحكم واستعراض البيانات - Dashboard & Browse (Read-Only)
                </h5>
            </div>
            <div class="divide-y divide-white/5">
                @php
                    $supBrowseEps = [
                        ['GET', '/api/v1/supervisor/dashboard', 'إحصائيات لوحة التحكم', 'نفس إحصائيات الأدمن'],
                        ['GET', '/api/v1/supervisor/statistics', 'الإحصائيات', 'نفس بيانات لوحة التحكم'],
                        ['GET', '/api/v1/supervisor/events', 'قائمة الفعاليات', 'استعراض فقط'],
                        ['GET', '/api/v1/supervisor/events/{event}', 'تفاصيل فعالية', 'بدون تعديل/حذف'],
                        ['GET', '/api/v1/supervisor/events/{event}/sections', 'أقسام الفعالية', 'استعراض فقط'],
                        ['GET', '/api/v1/supervisor/events/{event}/spaces', 'مساحات الفعالية', 'استعراض فقط'],
                        ['GET', '/api/v1/supervisor/sections/{section}', 'تفاصيل قسم', 'استعراض فقط'],
                        ['GET', '/api/v1/supervisor/spaces/{space}', 'تفاصيل مساحة', 'استعراض فقط'],
                        ['GET', '/api/v1/supervisor/services', 'قائمة الخدمات', 'استعراض فقط'],
                        ['GET', '/api/v1/supervisor/services/{service}', 'تفاصيل خدمة', 'استعراض فقط'],
                    ];
                @endphp
                @foreach($supBrowseEps as $ep)
                <div class="endpoint-row flex items-center px-6 py-3 gap-3">
                    <span class="method-badge font-bold px-2 py-0.5 rounded font-mono min-w-[56px] text-center bg-emerald-500/20 text-emerald-400">{{ $ep[0] }}</span>
                    <code class="text-xs text-gray-300 font-mono whitespace-nowrap" dir="ltr">{{ $ep[1] }}</code>
                    <span class="text-xs text-gray-600">{{ $ep[2] }}</span>
                    <span class="mr-auto"></span>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-orange-500/10 text-orange-400 font-semibold">supervisor</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Supervisor Visit/Rental Requests -->
        <div class="mb-6 glass border border-white/5 rounded-2xl overflow-hidden">
            <div class="px-6 py-3 border-b border-white/5 bg-white/[0.02]">
                <h5 class="text-xs font-bold text-gray-400 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    إدارة الطلبات - Request Management (بعد موافقة المستثمر)
                </h5>
            </div>
            <div class="divide-y divide-white/5">
                @php
                    $supRequestEps = [
                        ['GET', '/api/v1/supervisor/visit-requests', 'قائمة طلبات الزيارة', 'فلترة حسب الحالة'],
                        ['GET', '/api/v1/supervisor/visit-requests/{id}', 'تفاصيل طلب زيارة', 'البيانات الكاملة'],
                        ['PUT', '/api/v1/supervisor/visit-requests/{id}/approve', 'قبول طلب زيارة', 'بعد موافقة المستثمر'],
                        ['PUT', '/api/v1/supervisor/visit-requests/{id}/reject', 'رفض طلب زيارة', 'مع سبب الرفض'],
                        ['GET', '/api/v1/supervisor/rental-requests', 'قائمة طلبات الإيجار', 'فلترة حسب الحالة والمدفوعات'],
                        ['GET', '/api/v1/supervisor/rental-requests/{id}', 'تفاصيل طلب إيجار', 'مع سجل المدفوعات'],
                        ['PUT', '/api/v1/supervisor/rental-requests/{id}/approve', 'قبول طلب إيجار', 'بعد موافقة المستثمر'],
                        ['PUT', '/api/v1/supervisor/rental-requests/{id}/reject', 'رفض طلب إيجار', 'مع سبب الرفض'],
                        ['POST', '/api/v1/supervisor/rental-requests/{id}/payment', 'تسجيل دفعة', 'تسجيل دفعة للإيجار'],
                    ];
                @endphp
                @foreach($supRequestEps as $ep)
                <div class="endpoint-row flex items-center px-6 py-3 gap-3">
                    <span class="method-badge font-bold px-2 py-0.5 rounded font-mono min-w-[56px] text-center {{ $ep[0] === 'GET' ? 'bg-emerald-500/20 text-emerald-400' : ($ep[0] === 'POST' ? 'bg-blue-500/20 text-blue-400' : 'bg-amber-500/20 text-amber-400') }}">{{ $ep[0] }}</span>
                    <code class="text-xs text-gray-300 font-mono whitespace-nowrap" dir="ltr">{{ $ep[1] }}</code>
                    <span class="text-xs text-gray-600">{{ $ep[2] }}</span>
                    <span class="mr-auto"></span>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-orange-500/10 text-orange-400 font-semibold">supervisor</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Supervisor Profiles -->
        <div class="glass border border-white/5 rounded-2xl overflow-hidden">
            <div class="px-6 py-3 border-b border-white/5 bg-white/[0.02]">
                <h5 class="text-xs font-bold text-gray-400 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    إدارة الملفات التجارية - Profiles Management
                </h5>
            </div>
            <div class="divide-y divide-white/5">
                @php
                    $supProfileEps = [
                        ['GET', '/api/v1/supervisor/profiles', 'قائمة الملفات التجارية', 'فلترة حسب الحالة'],
                        ['GET', '/api/v1/supervisor/profiles/{id}', 'تفاصيل ملف تجاري', 'البيانات مع المستندات'],
                        ['PUT', '/api/v1/supervisor/profiles/{id}/approve', 'قبول الملف', 'الموافقة على الملف'],
                        ['PUT', '/api/v1/supervisor/profiles/{id}/reject', 'رفض الملف', 'مع سبب الرفض'],
                    ];
                @endphp
                @foreach($supProfileEps as $ep)
                <div class="endpoint-row flex items-center px-6 py-3 gap-3">
                    <span class="method-badge font-bold px-2 py-0.5 rounded font-mono min-w-[56px] text-center {{ $ep[0] === 'GET' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-amber-500/20 text-amber-400' }}">{{ $ep[0] }}</span>
                    <code class="text-xs text-gray-300 font-mono whitespace-nowrap" dir="ltr">{{ $ep[1] }}</code>
                    <span class="text-xs text-gray-600">{{ $ep[2] }}</span>
                    <span class="mr-auto"></span>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-orange-500/10 text-orange-400 font-semibold">supervisor</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Supervisor Sponsors & Contracts -->
        <div class="glass border border-white/5 rounded-2xl overflow-hidden mb-4">
            <div class="px-6 py-3 border-b border-white/5 bg-white/[0.02]">
                <h5 class="text-xs font-bold text-gray-400">الرعاة والعقود - Sponsors & Contracts (Supervisor)</h5>
            </div>
            <div class="divide-y divide-white/5">
                @php
                    $supSponsorEps = [
                        ['GET', '/api/v1/supervisor/sponsors', 'قائمة الرعاة'],
                        ['GET', '/api/v1/supervisor/sponsors/{sponsor}', 'تفاصيل راعي'],
                        ['GET', '/api/v1/supervisor/sponsor-contracts', 'قائمة العقود'],
                        ['GET', '/api/v1/supervisor/sponsor-contracts/{id}', 'تفاصيل عقد'],
                        ['PUT', '/api/v1/supervisor/sponsor-contracts/{id}/approve', 'قبول عقد'],
                        ['PUT', '/api/v1/supervisor/sponsor-contracts/{id}/reject', 'رفض عقد'],
                    ];
                @endphp
                @foreach($supSponsorEps as $ep)
                <div class="endpoint-row flex items-center px-5 py-2.5 gap-2">
                    <span class="method-badge font-bold px-1.5 py-0.5 rounded font-mono min-w-[44px] text-center {{ $ep[0] === 'GET' ? 'bg-emerald-500/15 text-emerald-400' : 'bg-amber-500/15 text-amber-400' }}">{{ $ep[0] }}</span>
                    <code class="text-[11px] text-gray-400 font-mono" dir="ltr">{{ $ep[1] }}</code>
                    <span class="text-[11px] text-gray-600 mr-auto">{{ $ep[2] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Supervisor Support Tickets & Rental Contracts -->
        <div class="glass border border-white/5 rounded-2xl overflow-hidden mb-4">
            <div class="px-6 py-3 border-b border-white/5 bg-white/[0.02]">
                <h5 class="text-xs font-bold text-gray-400">التذاكر وعقود الإيجار - Tickets & Rental Contracts (Supervisor)</h5>
            </div>
            <div class="divide-y divide-white/5">
                @php
                    $supMiscEps = [
                        ['GET', '/api/v1/supervisor/support-tickets', 'قائمة التذاكر'],
                        ['GET', '/api/v1/supervisor/support-tickets/{id}', 'تفاصيل تذكرة'],
                        ['POST', '/api/v1/supervisor/support-tickets/{id}/reply', 'الرد على تذكرة'],
                        ['GET', '/api/v1/supervisor/rental-contracts', 'قائمة العقود'],
                        ['GET', '/api/v1/supervisor/rental-contracts/{id}', 'تفاصيل عقد'],
                    ];
                @endphp
                @foreach($supMiscEps as $ep)
                <div class="endpoint-row flex items-center px-5 py-2.5 gap-2">
                    <span class="method-badge font-bold px-1.5 py-0.5 rounded font-mono min-w-[44px] text-center {{ $ep[0] === 'GET' ? 'bg-emerald-500/15 text-emerald-400' : ($ep[0] === 'POST' ? 'bg-blue-500/15 text-blue-400' : 'bg-amber-500/15 text-amber-400') }}">{{ $ep[0] }}</span>
                    <code class="text-[11px] text-gray-400 font-mono" dir="ltr">{{ $ep[1] }}</code>
                    <span class="text-[11px] text-gray-600 mr-auto">{{ $ep[2] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <div class="glow-line max-w-4xl mx-auto"></div>

    <!-- Super Admin API Endpoints -->
    <section id="superadmin-api" class="section-anchor max-w-7xl mx-auto px-6 py-16">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-1 h-8 bg-red-500 rounded-full"></div>
            <h3 class="text-2xl font-bold">واجهة المدير العام</h3>
            <span class="text-xs bg-red-500/10 text-red-400 px-2.5 py-1 rounded-full">Super Admin API</span>
        </div>
        <p class="text-gray-500 text-sm mb-8 mr-4">صلاحيات كاملة - إدارة النظام والتصنيفات والمدن والمستخدمين والإعدادات</p>

        <div class="mb-4 p-3 bg-red-500/5 border border-red-500/10 rounded-xl">
            <p class="text-xs text-red-400/80"><span class="font-bold">الصلاحيات:</span> تتطلب دور <code class="text-red-300/60">super-admin</code> فقط - أعلى مستوى صلاحيات</p>
        </div>

        <!-- Super Admin Dashboard -->
        <div class="mb-6 glass border border-white/5 rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-white/5 flex items-center gap-3">
                <span class="method-badge font-bold px-2.5 py-1 rounded-md bg-emerald-500/20 text-emerald-400 font-mono">GET</span>
                <code class="text-sm text-gray-300 font-mono" dir="ltr">/api/v1/super-admin/dashboard</code>
                <span class="text-xs text-gray-600 mr-auto">لوحة تحكم مع إحصائيات النظام والتحليلات</span>
                <span class="text-[10px] px-2 py-0.5 rounded-full bg-red-500/10 text-red-400 font-semibold">super-admin</span>
            </div>
            <div class="p-4 bg-white/[0.02]">
                <p class="text-[10px] text-gray-600"><span class="text-red-400/60 font-bold">Query Params:</span> <code class="text-gray-500">analytics_period, spaces_period, revenue_period</code> (all|today|week|month|year) + <code class="text-gray-500">event_id</code></p>
            </div>
        </div>

        <!-- Super Admin Statistics -->
        <div class="mb-6 glass border border-white/5 rounded-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-white/5 flex items-center gap-3">
                <span class="method-badge font-bold px-2.5 py-1 rounded-md bg-emerald-500/20 text-emerald-400 font-mono">GET</span>
                <code class="text-sm text-gray-300 font-mono" dir="ltr">/api/v1/super-admin/statistics</code>
                <span class="text-xs text-gray-600 mr-auto">الإحصائيات — نفس بيانات لوحة التحكم</span>
                <span class="text-[10px] px-2 py-0.5 rounded-full bg-red-500/10 text-red-400 font-semibold">super-admin</span>
            </div>
        </div>

        <!-- Super Admin Categories -->
        <div class="mb-6 glass border border-white/5 rounded-2xl overflow-hidden">
            <div class="px-6 py-3 border-b border-white/5 bg-white/[0.02]">
                <h5 class="text-xs font-bold text-gray-400 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    إدارة التصنيفات - Categories CRUD
                </h5>
            </div>
            <div class="divide-y divide-white/5">
                @php
                    $saCatEps = [
                        ['GET', '/api/v1/super-admin/categories', 'قائمة التصنيفات', 'بحث وفلترة'],
                        ['POST', '/api/v1/super-admin/categories', 'إنشاء تصنيف', 'name, name_ar, icon, is_active'],
                        ['GET', '/api/v1/super-admin/categories/{id}', 'تفاصيل تصنيف', 'البيانات الكاملة'],
                        ['PUT', '/api/v1/super-admin/categories/{id}', 'تحديث تصنيف', 'تعديل البيانات'],
                        ['DELETE', '/api/v1/super-admin/categories/{id}', 'حذف تصنيف', 'لا يمكن حذفه إذا مرتبط بفعاليات'],
                    ];
                @endphp
                @foreach($saCatEps as $ep)
                <div class="endpoint-row flex items-center px-6 py-3 gap-3">
                    <span class="method-badge font-bold px-2 py-0.5 rounded font-mono min-w-[56px] text-center {{ $ep[0] === 'GET' ? 'bg-emerald-500/20 text-emerald-400' : ($ep[0] === 'POST' ? 'bg-blue-500/20 text-blue-400' : ($ep[0] === 'PUT' ? 'bg-amber-500/20 text-amber-400' : 'bg-rose-500/20 text-rose-400')) }}">{{ $ep[0] }}</span>
                    <code class="text-xs text-gray-300 font-mono whitespace-nowrap" dir="ltr">{{ $ep[1] }}</code>
                    <span class="text-xs text-gray-600">{{ $ep[2] }}</span>
                    <span class="mr-auto"></span>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-red-500/10 text-red-400 font-semibold">super-admin</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Super Admin Cities -->
        <div class="mb-6 glass border border-white/5 rounded-2xl overflow-hidden">
            <div class="px-6 py-3 border-b border-white/5 bg-white/[0.02]">
                <h5 class="text-xs font-bold text-gray-400 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    إدارة المدن - Cities CRUD
                </h5>
            </div>
            <div class="divide-y divide-white/5">
                @php
                    $saCityEps = [
                        ['GET', '/api/v1/super-admin/cities', 'قائمة المدن', 'بحث وفلترة حسب المنطقة'],
                        ['POST', '/api/v1/super-admin/cities', 'إنشاء مدينة', 'name, name_ar, region, lat, lng'],
                        ['GET', '/api/v1/super-admin/cities/{id}', 'تفاصيل مدينة', 'البيانات الكاملة'],
                        ['PUT', '/api/v1/super-admin/cities/{id}', 'تحديث مدينة', 'تعديل البيانات'],
                        ['DELETE', '/api/v1/super-admin/cities/{id}', 'حذف مدينة', 'لا يمكن حذفها إذا مرتبطة بفعاليات'],
                    ];
                @endphp
                @foreach($saCityEps as $ep)
                <div class="endpoint-row flex items-center px-6 py-3 gap-3">
                    <span class="method-badge font-bold px-2 py-0.5 rounded font-mono min-w-[56px] text-center {{ $ep[0] === 'GET' ? 'bg-emerald-500/20 text-emerald-400' : ($ep[0] === 'POST' ? 'bg-blue-500/20 text-blue-400' : ($ep[0] === 'PUT' ? 'bg-amber-500/20 text-amber-400' : 'bg-rose-500/20 text-rose-400')) }}">{{ $ep[0] }}</span>
                    <code class="text-xs text-gray-300 font-mono whitespace-nowrap" dir="ltr">{{ $ep[1] }}</code>
                    <span class="text-xs text-gray-600">{{ $ep[2] }}</span>
                    <span class="mr-auto"></span>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-red-500/10 text-red-400 font-semibold">super-admin</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Super Admin Users + Settings -->
        <div class="grid md:grid-cols-2 gap-4">
            <div class="glass border border-white/5 rounded-2xl overflow-hidden">
                <div class="px-6 py-3 border-b border-white/5 bg-white/[0.02]">
                    <h5 class="text-xs font-bold text-gray-400 flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        إدارة المستخدمين - Users
                    </h5>
                </div>
                <div class="divide-y divide-white/5">
                    @php
                        $saUserEps = [
                            ['GET', '/api/v1/super-admin/users', 'القائمة'],
                            ['GET', '/api/v1/super-admin/users/{id}', 'التفاصيل'],
                            ['PUT', '/api/v1/super-admin/users/{id}/approve', 'قبول'],
                            ['PUT', '/api/v1/super-admin/users/{id}/reject', 'رفض'],
                            ['PUT', '/api/v1/super-admin/users/{id}/suspend', 'تعليق'],
                        ];
                    @endphp
                    @foreach($saUserEps as $ep)
                    <div class="endpoint-row flex items-center px-5 py-2.5 gap-2">
                        <span class="method-badge font-bold px-1.5 py-0.5 rounded font-mono min-w-[44px] text-center {{ $ep[0] === 'GET' ? 'bg-emerald-500/15 text-emerald-400' : 'bg-amber-500/15 text-amber-400' }}">{{ $ep[0] }}</span>
                        <code class="text-[11px] text-gray-400 font-mono" dir="ltr">{{ $ep[1] }}</code>
                        <span class="text-[11px] text-gray-600 mr-auto">{{ $ep[2] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="glass border border-white/5 rounded-2xl overflow-hidden">
                <div class="px-6 py-3 border-b border-white/5 bg-white/[0.02]">
                    <h5 class="text-xs font-bold text-gray-400 flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        إعدادات النظام - Settings
                    </h5>
                </div>
                <div class="divide-y divide-white/5">
                    @php
                        $saSettEps = [
                            ['GET', '/api/v1/super-admin/settings', 'جميع الإعدادات'],
                            ['GET', '/api/v1/super-admin/settings/{key}', 'إعداد محدد'],
                            ['PUT', '/api/v1/super-admin/settings', 'تحديث الإعدادات'],
                        ];
                    @endphp
                    @foreach($saSettEps as $ep)
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

    <!-- Investor API Endpoints -->
    <section id="investor-api" class="section-anchor max-w-7xl mx-auto px-6 py-16">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-1 h-8 bg-indigo-500 rounded-full"></div>
            <h3 class="text-2xl font-bold">واجهة المستثمر</h3>
            <span class="text-xs bg-indigo-500/10 text-indigo-400 px-2.5 py-1 rounded-full">Investor API</span>
        </div>
        <p class="text-gray-500 text-sm mb-8 mr-4">إدارة المساحات الخاصة، الموافقة على الطلبات، تتبع الإيرادات والمدفوعات</p>

        <div class="mb-4 p-3 bg-indigo-500/5 border border-indigo-500/10 rounded-xl">
            <p class="text-xs text-indigo-400/80"><span class="font-bold">الصلاحيات:</span> تتطلب دور <code class="text-indigo-300/60">investor</code></p>
        </div>

        <!-- Investor Spaces -->
        <div class="mb-6 glass border border-white/5 rounded-2xl overflow-hidden">
            <div class="px-6 py-3 border-b border-white/5 bg-white/[0.02]">
                <h5 class="text-xs font-bold text-gray-400 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    لوحة التحكم وإدارة المساحات - Dashboard & Spaces
                </h5>
            </div>
            <div class="divide-y divide-white/5">
                @php
                    $invSpaceEps = [
                        ['GET', '/api/v1/investor/dashboard', 'لوحة التحكم', 'إحصائيات المستثمر'],
                        ['GET', '/api/v1/investor/statistics', 'الإحصائيات', 'نفس بيانات لوحة التحكم'],
                        ['GET', '/api/v1/investor/spaces', 'قائمة مساحاتي', 'المساحات الخاصة بالمستثمر'],
                        ['POST', '/api/v1/investor/spaces', 'إنشاء مساحة', 'event_id, section_id, name, area, price, type'],
                        ['GET', '/api/v1/investor/spaces/{id}', 'تفاصيل مساحة', 'البيانات مع الخدمات'],
                        ['PUT', '/api/v1/investor/spaces/{id}', 'تحديث مساحة', 'تعديل بيانات المساحة'],
                        ['DELETE', '/api/v1/investor/spaces/{id}', 'حذف مساحة', 'حذف المساحة الخاصة'],
                        ['POST', '/api/v1/investor/spaces/{id}/services', 'إضافة خدمات', 'ربط خدمات بالمساحة'],
                        ['DELETE', '/api/v1/investor/spaces/{id}/services', 'إزالة خدمات', 'فك ربط خدمات'],
                    ];
                @endphp
                @foreach($invSpaceEps as $ep)
                <div class="endpoint-row flex items-center px-6 py-3 gap-3">
                    <span class="method-badge font-bold px-2 py-0.5 rounded font-mono min-w-[56px] text-center {{ $ep[0] === 'GET' ? 'bg-emerald-500/20 text-emerald-400' : ($ep[0] === 'POST' ? 'bg-blue-500/20 text-blue-400' : ($ep[0] === 'PUT' ? 'bg-amber-500/20 text-amber-400' : 'bg-rose-500/20 text-rose-400')) }}">{{ $ep[0] }}</span>
                    <code class="text-xs text-gray-300 font-mono whitespace-nowrap" dir="ltr">{{ $ep[1] }}</code>
                    <span class="text-xs text-gray-600">{{ $ep[2] }}</span>
                    <span class="mr-auto"></span>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-indigo-500/10 text-indigo-400 font-semibold">investor</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Investor Rental & Visit Requests + Payments -->
        <div class="glass border border-white/5 rounded-2xl overflow-hidden">
            <div class="px-6 py-3 border-b border-white/5 bg-white/[0.02]">
                <h5 class="text-xs font-bold text-gray-400 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    الطلبات والمدفوعات - Requests & Payments
                </h5>
            </div>
            <div class="divide-y divide-white/5">
                @php
                    $invRequestEps = [
                        ['GET', '/api/v1/investor/rental-requests', 'طلبات الإيجار', 'الطلبات على مساحات المستثمر'],
                        ['GET', '/api/v1/investor/rental-requests/pending-count', 'عدد المعلّقة', 'طلبات بانتظار الموافقة'],
                        ['GET', '/api/v1/investor/rental-requests/{id}', 'تفاصيل طلب إيجار', 'البيانات الكاملة'],
                        ['PUT', '/api/v1/investor/rental-requests/{id}/approve', 'قبول طلب إيجار', 'الخطوة الأولى من الموافقة'],
                        ['PUT', '/api/v1/investor/rental-requests/{id}/reject', 'رفض طلب إيجار', 'مع ملاحظات'],
                        ['GET', '/api/v1/investor/visit-requests', 'طلبات الزيارة', 'لفعاليات المستثمر'],
                        ['GET', '/api/v1/investor/visit-requests/pending-count', 'عدد المعلّقة', 'زيارات بانتظار الموافقة'],
                        ['GET', '/api/v1/investor/visit-requests/{id}', 'تفاصيل طلب زيارة', 'البيانات الكاملة'],
                        ['PUT', '/api/v1/investor/visit-requests/{id}/approve', 'قبول طلب زيارة', 'الخطوة الأولى'],
                        ['PUT', '/api/v1/investor/visit-requests/{id}/reject', 'رفض طلب زيارة', 'مع ملاحظات'],
                        ['GET', '/api/v1/investor/payments', 'سجل المدفوعات', 'جميع المدفوعات مع الفلترة'],
                        ['GET', '/api/v1/investor/payments/summary', 'ملخص الإيرادات', 'إجمالي ومفصّل'],
                        ['GET', '/api/v1/investor/payments/{id}', 'تفاصيل دفعة', 'البيانات الكاملة'],
                    ];
                @endphp
                @foreach($invRequestEps as $ep)
                <div class="endpoint-row flex items-center px-6 py-3 gap-3">
                    <span class="method-badge font-bold px-2 py-0.5 rounded font-mono min-w-[56px] text-center {{ $ep[0] === 'GET' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-amber-500/20 text-amber-400' }}">{{ $ep[0] }}</span>
                    <code class="text-xs text-gray-300 font-mono whitespace-nowrap" dir="ltr">{{ $ep[1] }}</code>
                    <span class="text-xs text-gray-600">{{ $ep[2] }}</span>
                    <span class="mr-auto"></span>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-indigo-500/10 text-indigo-400 font-semibold">investor</span>
                </div>
                @endforeach
            </div>
            <div class="p-4 bg-white/[0.02]">
                <p class="text-[10px] text-gray-600"><span class="text-indigo-400/60 font-bold">ملاحظة:</span> موافقة المستثمر هي الخطوة الأولى - بعدها يحتاج الطلب موافقة المشرف/الأدمن</p>
            </div>
        </div>

        <!-- Investor Rental Contracts -->
        <div class="glass border border-white/5 rounded-2xl overflow-hidden mb-4">
            <div class="px-6 py-3 border-b border-white/5 bg-white/[0.02]">
                <h5 class="text-xs font-bold text-gray-400">عقود الإيجار - Rental Contracts (Investor)</h5>
            </div>
            <div class="divide-y divide-white/5">
                @php
                    $invContractEps = [
                        ['GET', '/api/v1/investor/rental-contracts', 'قائمة العقود', 'عقود مساحات المستثمر'],
                        ['GET', '/api/v1/investor/rental-contracts/{id}', 'تفاصيل عقد', 'البيانات الكاملة'],
                        ['PUT', '/api/v1/investor/rental-contracts/{id}/sign', 'توقيع العقد', 'توقيع عقد الإيجار'],
                    ];
                @endphp
                @foreach($invContractEps as $ep)
                <div class="endpoint-row flex items-center px-6 py-3 gap-3">
                    <span class="method-badge font-bold px-2 py-0.5 rounded font-mono min-w-[56px] text-center {{ $ep[0] === 'GET' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-amber-500/20 text-amber-400' }}">{{ $ep[0] }}</span>
                    <code class="text-xs text-gray-300 font-mono whitespace-nowrap" dir="ltr">{{ $ep[1] }}</code>
                    <span class="text-xs text-gray-600">{{ $ep[2] }}</span>
                    <span class="mr-auto"></span>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-indigo-500/10 text-indigo-400 font-semibold">investor</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Investor Team Members -->
        <div class="glass border border-white/5 rounded-2xl overflow-hidden mb-4">
            <div class="px-6 py-3 border-b border-white/5 bg-white/[0.02]">
                <h5 class="text-xs font-bold text-gray-400">فريق العمل - Team Members (Investor)</h5>
            </div>
            <div class="divide-y divide-white/5">
                @php
                    $invTeamEps = [
                        ['GET', '/api/v1/my/investor-team/member-types', 'أنواع الأعضاء', 'الأنواع المتاحة للمستثمرين'],
                        ['GET', '/api/v1/my/investor-team', 'قائمة الفريق', 'أعضاء فريق المستثمر'],
                        ['POST', '/api/v1/my/investor-team', 'إضافة عضو', 'إضافة عضو جديد للفريق'],
                        ['GET', '/api/v1/my/investor-team/{id}', 'تفاصيل عضو', 'بيانات عضو الفريق'],
                        ['PUT', '/api/v1/my/investor-team/{id}', 'تحديث عضو', 'تعديل بيانات عضو'],
                        ['DELETE', '/api/v1/my/investor-team/{id}', 'حذف عضو', 'إزالة عضو من الفريق'],
                    ];
                @endphp
                @foreach($invTeamEps as $ep)
                <div class="endpoint-row flex items-center px-6 py-3 gap-3">
                    <span class="method-badge font-bold px-2 py-0.5 rounded font-mono min-w-[56px] text-center {{ $ep[0] === 'GET' ? 'bg-emerald-500/20 text-emerald-400' : ($ep[0] === 'POST' ? 'bg-blue-500/20 text-blue-400' : ($ep[0] === 'DELETE' ? 'bg-red-500/20 text-red-400' : 'bg-amber-500/20 text-amber-400')) }}">{{ $ep[0] }}</span>
                    <code class="text-xs text-gray-300 font-mono whitespace-nowrap" dir="ltr">{{ $ep[1] }}</code>
                    <span class="text-xs text-gray-600">{{ $ep[2] }}</span>
                    <span class="mr-auto"></span>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-indigo-500/10 text-indigo-400 font-semibold">investor</span>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <div class="glow-line max-w-4xl mx-auto"></div>

    <!-- Sponsor Self-Service API -->
    <section id="sponsor-api" class="section-anchor max-w-7xl mx-auto px-6 py-16">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-1 h-8 bg-yellow-500 rounded-full"></div>
            <h3 class="text-2xl font-bold">واجهة الراعي</h3>
            <span class="text-xs bg-yellow-500/10 text-yellow-400 px-2.5 py-1 rounded-full">Sponsor API</span>
        </div>
        <p class="text-gray-500 text-sm mb-8 mr-4">خدمة ذاتية للرعاة - إدارة العقود والمدفوعات والملفات والتعرض</p>

        <div class="mb-4 p-3 bg-yellow-500/5 border border-yellow-500/10 rounded-xl">
            <p class="text-xs text-yellow-400/80"><span class="font-bold">الصلاحيات:</span> تتطلب دور <code class="text-yellow-300/60">sponsor</code></p>
        </div>

        <div class="glass border border-white/5 rounded-2xl overflow-hidden mb-4">
            <div class="divide-y divide-white/5">
                @php
                    $sponsorSelfEps = [
                        ['GET', '/api/v1/sponsor/dashboard', 'لوحة التحكم', 'إحصائيات الراعي'],
                        ['GET', '/api/v1/sponsor/contracts', 'قائمة العقود', 'عقود الراعي الحالية'],
                        ['GET', '/api/v1/sponsor/contracts/{id}', 'تفاصيل عقد', 'البيانات الكاملة'],
                        ['PUT', '/api/v1/sponsor/contracts/{id}/sign', 'توقيع العقد', 'قبول وتوقيع العقد'],
                        ['GET', '/api/v1/sponsor/payments', 'قائمة المدفوعات', 'سجل المدفوعات'],
                        ['GET', '/api/v1/sponsor/payments/{id}', 'تفاصيل دفعة', 'البيانات الكاملة'],
                        ['POST', '/api/v1/sponsor/payments/{id}/proof', 'رفع إثبات دفع', 'إرفاق صورة الدفع'],
                        ['GET', '/api/v1/sponsor/assets', 'قائمة الملفات', 'ملفات الراعي المرفوعة'],
                        ['POST', '/api/v1/sponsor/assets', 'رفع ملف', 'لوغو، بانر، فيديو'],
                        ['GET', '/api/v1/sponsor/assets/{id}', 'تفاصيل ملف', 'البيانات والحالة'],
                        ['PUT', '/api/v1/sponsor/assets/{id}', 'تحديث ملف', 'تعديل البيانات'],
                        ['DELETE', '/api/v1/sponsor/assets/{id}', 'حذف ملف', 'إزالة الملف'],
                        ['GET', '/api/v1/sponsor/exposure', 'تقرير التعرض', 'إحصائيات الظهور والنقرات'],
                        ['GET', '/api/v1/my/sponsor-leads', 'عملائي المحتملون', 'قائمة العملاء المحتملين من الفعاليات'],
                        ['GET', '/api/v1/my/sponsor-leads/{id}', 'تفاصيل عميل محتمل', 'البيانات الكاملة للعميل'],
                        ['GET', '/api/v1/my/sponsor-deliverables', 'تسليماتي', 'قائمة التسليمات والمتطلبات'],
                        ['GET', '/api/v1/my/sponsor-deliverables/{id}', 'تفاصيل تسليم', 'البيانات والحالة والموعد'],
                        ['POST', '/api/v1/my/sponsor-deliverables/{id}/upload', 'رفع ملف تسليم', 'رفع الملف وتحويل الحالة للمراجعة'],
                    ];
                @endphp
                @foreach($sponsorSelfEps as $ep)
                <div class="endpoint-row flex items-center px-6 py-3 gap-3">
                    <span class="method-badge font-bold px-2 py-0.5 rounded font-mono min-w-[56px] text-center {{ $ep[0] === 'GET' ? 'bg-emerald-500/20 text-emerald-400' : ($ep[0] === 'POST' ? 'bg-blue-500/20 text-blue-400' : ($ep[0] === 'PUT' ? 'bg-amber-500/20 text-amber-400' : 'bg-rose-500/20 text-rose-400')) }}">{{ $ep[0] }}</span>
                    <code class="text-xs text-gray-300 font-mono whitespace-nowrap" dir="ltr">{{ $ep[1] }}</code>
                    <span class="text-xs text-gray-600">{{ $ep[2] }}</span>
                    <span class="mr-auto"></span>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-yellow-500/10 text-yellow-400 font-semibold">sponsor</span>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <div class="glow-line max-w-4xl mx-auto"></div>

    <!-- Merchant API Endpoints -->
    <section id="merchant-api" class="section-anchor max-w-7xl mx-auto px-6 py-16">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-1 h-8 bg-sky-500 rounded-full"></div>
            <h3 class="text-2xl font-bold">واجهة التاجر</h3>
            <span class="text-xs bg-sky-500/10 text-sky-400 px-2.5 py-1 rounded-full">Merchant API</span>
        </div>
        <p class="text-gray-500 text-sm mb-8 mr-4">استعراض الفعاليات والمساحات والخدمات، تقديم طلبات الزيارة والإيجار</p>

        <div class="mb-4 p-3 bg-sky-500/5 border border-sky-500/10 rounded-xl">
            <p class="text-xs text-sky-400/80"><span class="font-bold">الصلاحيات:</span> تتطلب دور <code class="text-sky-300/60">merchant</code> - طلبات الإيجار تتطلب ملف تجاري موثق</p>
        </div>

        <!-- Merchant Browse -->
        <div class="mb-6 glass border border-white/5 rounded-2xl overflow-hidden">
            <div class="px-6 py-3 border-b border-white/5 bg-white/[0.02]">
                <h5 class="text-xs font-bold text-gray-400 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    لوحة التحكم واستعراض البيانات - Dashboard & Browse
                </h5>
            </div>
            <div class="divide-y divide-white/5">
                @php
                    $mBrowseEps = [
                        ['GET', '/api/v1/merchant/dashboard', 'لوحة التحكم', 'إحصائيات التاجر'],
                        ['GET', '/api/v1/merchant/statistics', 'الإحصائيات', 'نفس بيانات لوحة التحكم'],
                        ['GET', '/api/v1/merchant/events', 'قائمة الفعاليات', 'بحث وفلترة'],
                        ['GET', '/api/v1/merchant/events/{id}', 'تفاصيل فعالية', 'البيانات الكاملة'],
                        ['GET', '/api/v1/merchant/events/{id}/sections', 'أقسام الفعالية', 'عرض الأقسام'],
                        ['GET', '/api/v1/merchant/events/{id}/spaces', 'مساحات الفعالية', 'المساحات المتاحة'],
                        ['GET', '/api/v1/merchant/spaces', 'تصفح المساحات', 'فلترة حسب النوع والسعر والمساحة'],
                        ['GET', '/api/v1/merchant/spaces/{id}', 'تفاصيل مساحة', 'البيانات مع الخدمات'],
                        ['GET', '/api/v1/merchant/services', 'قائمة الخدمات', 'الخدمات المتاحة'],
                        ['GET', '/api/v1/merchant/services/{id}', 'تفاصيل خدمة', 'البيانات الكاملة'],
                    ];
                @endphp
                @foreach($mBrowseEps as $ep)
                <div class="endpoint-row flex items-center px-6 py-3 gap-3">
                    <span class="method-badge font-bold px-2 py-0.5 rounded font-mono min-w-[56px] text-center bg-emerald-500/20 text-emerald-400">{{ $ep[0] }}</span>
                    <code class="text-xs text-gray-300 font-mono whitespace-nowrap" dir="ltr">{{ $ep[1] }}</code>
                    <span class="text-xs text-gray-600">{{ $ep[2] }}</span>
                    <span class="mr-auto"></span>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-sky-500/10 text-sky-400 font-semibold">merchant</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Merchant Requests -->
        <div class="glass border border-white/5 rounded-2xl overflow-hidden">
            <div class="px-6 py-3 border-b border-white/5 bg-white/[0.02]">
                <h5 class="text-xs font-bold text-gray-400 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    طلبات الزيارة والإيجار - Visit & Rental Requests
                </h5>
            </div>
            <div class="divide-y divide-white/5">
                @php
                    $mRequestEps = [
                        ['GET', '/api/v1/merchant/visit-requests', 'طلبات الزيارة', 'طلبات التاجر'],
                        ['POST', '/api/v1/merchant/visit-requests', 'طلب زيارة جديد', 'event_id, visit_date, visitors_count'],
                        ['GET', '/api/v1/merchant/visit-requests/{id}', 'تفاصيل طلب', 'البيانات الكاملة'],
                        ['PUT', '/api/v1/merchant/visit-requests/{id}', 'تحديث الطلب', 'تعديل البيانات'],
                        ['DELETE', '/api/v1/merchant/visit-requests/{id}', 'إلغاء الطلب', 'حذف الطلب'],
                        ['GET', '/api/v1/merchant/rental-requests', 'طلبات الإيجار', 'يتطلب ملف موثق'],
                        ['POST', '/api/v1/merchant/rental-requests', 'طلب إيجار جديد', 'space_id, start_date, end_date'],
                        ['GET', '/api/v1/merchant/rental-requests/{id}', 'تفاصيل طلب', 'مع المدفوعات'],
                        ['PUT', '/api/v1/merchant/rental-requests/{id}', 'تحديث الطلب', 'تعديل البيانات'],
                        ['DELETE', '/api/v1/merchant/rental-requests/{id}', 'إلغاء الطلب', 'حذف الطلب'],
                    ];
                @endphp
                @foreach($mRequestEps as $ep)
                <div class="endpoint-row flex items-center px-6 py-3 gap-3">
                    <span class="method-badge font-bold px-2 py-0.5 rounded font-mono min-w-[56px] text-center {{ $ep[0] === 'GET' ? 'bg-emerald-500/20 text-emerald-400' : ($ep[0] === 'POST' ? 'bg-blue-500/20 text-blue-400' : ($ep[0] === 'PUT' ? 'bg-amber-500/20 text-amber-400' : 'bg-rose-500/20 text-rose-400')) }}">{{ $ep[0] }}</span>
                    <code class="text-xs text-gray-300 font-mono whitespace-nowrap" dir="ltr">{{ $ep[1] }}</code>
                    <span class="text-xs text-gray-600">{{ $ep[2] }}</span>
                    <span class="mr-auto"></span>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-sky-500/10 text-sky-400 font-semibold">merchant</span>
                </div>
                @endforeach
            </div>
            <div class="p-4 bg-white/[0.02]">
                <p class="text-[10px] text-gray-600"><span class="text-purple-400/60 font-bold">ملاحظة:</span> طلبات الإيجار تتطلب ملف تجاري موثق وتمر بموافقة المستثمر ثم المشرف/الأدمن</p>
            </div>
        </div>

        <!-- Merchant Rental Contracts -->
        <div class="glass border border-white/5 rounded-2xl overflow-hidden mb-4">
            <div class="px-6 py-3 border-b border-white/5 bg-white/[0.02]">
                <h5 class="text-xs font-bold text-gray-400">عقود الإيجار - Rental Contracts (Merchant)</h5>
            </div>
            <div class="divide-y divide-white/5">
                @php
                    $mContractEps = [
                        ['GET', '/api/v1/merchant/rental-contracts', 'قائمة العقود', 'عقود التاجر'],
                        ['GET', '/api/v1/merchant/rental-contracts/{id}', 'تفاصيل عقد', 'البيانات الكاملة'],
                        ['PUT', '/api/v1/merchant/rental-contracts/{id}/sign', 'توقيع العقد', 'توقيع عقد الإيجار'],
                    ];
                @endphp
                @foreach($mContractEps as $ep)
                <div class="endpoint-row flex items-center px-6 py-3 gap-3">
                    <span class="method-badge font-bold px-2 py-0.5 rounded font-mono min-w-[56px] text-center {{ $ep[0] === 'GET' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-amber-500/20 text-amber-400' }}">{{ $ep[0] }}</span>
                    <code class="text-xs text-gray-300 font-mono whitespace-nowrap" dir="ltr">{{ $ep[1] }}</code>
                    <span class="text-xs text-gray-600">{{ $ep[2] }}</span>
                    <span class="mr-auto"></span>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-sky-500/10 text-sky-400 font-semibold">merchant</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Merchant Team Members -->
        <div class="glass border border-white/5 rounded-2xl overflow-hidden mb-4">
            <div class="px-6 py-3 border-b border-white/5 bg-white/[0.02]">
                <h5 class="text-xs font-bold text-gray-400">فريق العمل - Team Members (Merchant)</h5>
            </div>
            <div class="divide-y divide-white/5">
                @php
                    $mTeamEps = [
                        ['GET', '/api/v1/my/merchant-team/member-types', 'أنواع الأعضاء', 'الأنواع المتاحة للتجار'],
                        ['GET', '/api/v1/my/merchant-team', 'قائمة الفريق', 'أعضاء فريق التاجر'],
                        ['POST', '/api/v1/my/merchant-team', 'إضافة عضو', 'إضافة عضو جديد للفريق'],
                        ['GET', '/api/v1/my/merchant-team/{id}', 'تفاصيل عضو', 'بيانات عضو الفريق'],
                        ['PUT', '/api/v1/my/merchant-team/{id}', 'تحديث عضو', 'تعديل بيانات عضو'],
                        ['DELETE', '/api/v1/my/merchant-team/{id}', 'حذف عضو', 'إزالة عضو من الفريق'],
                    ];
                @endphp
                @foreach($mTeamEps as $ep)
                <div class="endpoint-row flex items-center px-6 py-3 gap-3">
                    <span class="method-badge font-bold px-2 py-0.5 rounded font-mono min-w-[56px] text-center {{ $ep[0] === 'GET' ? 'bg-emerald-500/20 text-emerald-400' : ($ep[0] === 'POST' ? 'bg-blue-500/20 text-blue-400' : ($ep[0] === 'DELETE' ? 'bg-red-500/20 text-red-400' : 'bg-amber-500/20 text-amber-400')) }}">{{ $ep[0] }}</span>
                    <code class="text-xs text-gray-300 font-mono whitespace-nowrap" dir="ltr">{{ $ep[1] }}</code>
                    <span class="text-xs text-gray-600">{{ $ep[2] }}</span>
                    <span class="mr-auto"></span>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-sky-500/10 text-sky-400 font-semibold">merchant</span>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <div class="glow-line max-w-4xl mx-auto"></div>

    <!-- Authentication Methods -->
    <section id="auth-methods" class="section-anchor max-w-7xl mx-auto px-6 py-16">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-1 h-8 bg-indigo-500 rounded-full"></div>
            <h3 class="text-2xl font-bold">طرق تسجيل الدخول</h3>
            <span class="text-xs bg-indigo-500/10 text-indigo-400 px-2.5 py-1 rounded-full">Authentication</span>
        </div>
        <p class="text-gray-500 text-sm mb-8 mr-4">المنصة تدعم طريقتين لتسجيل الدخول عبر Auth Service</p>

        <div class="grid md:grid-cols-2 gap-6">
            <!-- Method 1: Phone + OTP -->
            <div class="glass border border-white/5 rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-white/5 bg-emerald-500/5">
                    <h4 class="font-bold text-emerald-400 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        رقم الجوال + رمز OTP
                    </h4>
                    <p class="text-xs text-gray-500 mt-1">الطريقة الرئيسية - عبر SMS أو WhatsApp</p>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-start gap-3">
                        <span class="w-6 h-6 bg-emerald-500/20 rounded-full flex items-center justify-center text-xs text-emerald-400 font-bold shrink-0">1</span>
                        <div>
                            <div class="text-sm font-semibold text-white/80">إرسال رمز OTP</div>
                            <code class="text-xs text-gray-400 font-mono" dir="ltr">POST /api/v1/auth/otp/send</code>
                            <div class="text-xs text-gray-500 mt-1">أرسل <code class="text-indigo-300">phone</code> (مثال: <code class="text-emerald-300">+966500000000</code>)</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="w-6 h-6 bg-emerald-500/20 rounded-full flex items-center justify-center text-xs text-emerald-400 font-bold shrink-0">2</span>
                        <div>
                            <div class="text-sm font-semibold text-white/80">التحقق من الرمز</div>
                            <code class="text-xs text-gray-400 font-mono" dir="ltr">POST /api/v1/auth/otp/verify</code>
                            <div class="text-xs text-gray-500 mt-1">أرسل <code class="text-indigo-300">phone</code> + <code class="text-indigo-300">code</code> (6 أرقام)</div>
                            <div class="text-xs text-gray-500">→ إذا المستخدم موجود: يرجع JWT Token مباشرة</div>
                            <div class="text-xs text-gray-500">→ إذا مستخدم جديد: يرجع <code class="text-amber-300">registration_token</code></div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="w-6 h-6 bg-emerald-500/20 rounded-full flex items-center justify-center text-xs text-emerald-400 font-bold shrink-0">3</span>
                        <div>
                            <div class="text-sm font-semibold text-white/80">إكمال التسجيل (مستخدم جديد فقط)</div>
                            <code class="text-xs text-gray-400 font-mono" dir="ltr">POST /api/v1/auth/otp/complete-registration</code>
                            <div class="text-xs text-gray-500 mt-1">أرسل <code class="text-indigo-300">registration_token</code> + <code class="text-indigo-300">name</code> + <code class="text-indigo-300">email</code></div>
                        </div>
                    </div>
                    <div class="mt-4 p-3 bg-amber-500/5 border border-amber-500/10 rounded-lg">
                        <p class="text-xs text-amber-400/80"><span class="font-bold">وضع الاختبار:</span> عند تفعيل <code class="text-amber-300">sms_test_mode</code> من الإعدادات، الرمز دائماً <code class="text-emerald-300 font-bold">123456</code></p>
                    </div>
                </div>
            </div>

            <!-- Method 2: Email + Password -->
            <div class="glass border border-white/5 rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-white/5 bg-indigo-500/5">
                    <h4 class="font-bold text-indigo-400 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        البريد الإلكتروني + كلمة المرور
                    </h4>
                    <p class="text-xs text-gray-500 mt-1">للوحة التحكم والمستخدمين الإداريين</p>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-start gap-3">
                        <span class="w-6 h-6 bg-indigo-500/20 rounded-full flex items-center justify-center text-xs text-indigo-400 font-bold shrink-0">1</span>
                        <div>
                            <div class="text-sm font-semibold text-white/80">تسجيل الدخول</div>
                            <code class="text-xs text-gray-400 font-mono" dir="ltr">POST /api/v1/auth/login</code>
                            <div class="text-xs text-gray-500 mt-1">أرسل <code class="text-indigo-300">identifier</code> (البريد أو الجوال) + <code class="text-indigo-300">password</code></div>
                            <div class="text-xs text-gray-500">→ يرجع JWT Token + بيانات المستخدم</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="w-6 h-6 bg-indigo-500/20 rounded-full flex items-center justify-center text-xs text-indigo-400 font-bold shrink-0">2</span>
                        <div>
                            <div class="text-sm font-semibold text-white/80">استخدام التوكن</div>
                            <div class="text-xs text-gray-500">أضف الهيدر في كل طلب:</div>
                            <code class="text-xs text-emerald-300 font-mono" dir="ltr">Authorization: Bearer {token}</code>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="w-6 h-6 bg-indigo-500/20 rounded-full flex items-center justify-center text-xs text-indigo-400 font-bold shrink-0">3</span>
                        <div>
                            <div class="text-sm font-semibold text-white/80">تجديد التوكن</div>
                            <code class="text-xs text-gray-400 font-mono" dir="ltr">POST /api/v1/auth/refresh</code>
                            <div class="text-xs text-gray-500 mt-1">التوكن صالح لمدة ساعة - جدده قبل انتهائه</div>
                        </div>
                    </div>
                    <div class="mt-4 p-3 bg-indigo-500/5 border border-indigo-500/10 rounded-lg">
                        <p class="text-xs text-indigo-400/80"><span class="font-bold">ملاحظة:</span> جميع endpoints المصادقة على Auth Service: <code class="text-cyan-300" dir="ltr">https://auth-service-api.mahamexpo.sa</code></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Auth Mode API -->
        <div class="mt-6 glass border border-white/5 rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-3">
                <span class="method-badge font-bold px-2.5 py-1 rounded-md bg-emerald-500/20 text-emerald-400 font-mono">GET</span>
                <code class="text-sm text-gray-300 font-mono" dir="ltr">/api/v1/auth-mode</code>
                <span class="text-xs text-gray-500">— يرجع طريقة المصادقة المفعّلة حالياً</span>
            </div>
            <div class="code-block rounded-xl p-4 text-xs overflow-x-auto" dir="ltr">
<pre class="text-gray-300"><code>{
  <span class="key">"success"</span>: <span class="text-emerald-400">true</span>,
  <span class="key">"data"</span>: {
    <span class="key">"auth_mode"</span>: <span class="str">"phone_and_otp"</span>,
    <span class="key">"available_modes"</span>: [<span class="str">"phone_and_otp"</span>, <span class="str">"email_and_password"</span>, <span class="str">"both"</span>]
  }
}</code></pre>
            </div>
        </div>
    </section>

    <div class="glow-line max-w-4xl mx-auto"></div>

    <!-- Registration Flow -->
    <section id="registration-flow" class="section-anchor max-w-7xl mx-auto px-6 py-16">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-1 h-8 bg-teal-500 rounded-full"></div>
            <h3 class="text-2xl font-bold">طريقة التسجيل</h3>
            <span class="text-xs bg-teal-500/10 text-teal-400 px-2.5 py-1 rounded-full">Registration Flow</span>
        </div>
        <p class="text-gray-500 text-sm mb-8 mr-4">يمكن التسجيل في المنصة بطريقتين: التسجيل العادي أو عبر رمز التحقق OTP</p>

        <div class="grid md:grid-cols-2 gap-6">
            <!-- Method 1: Standard Registration -->
            <div class="glass border border-white/5 rounded-2xl overflow-hidden card-hover">
                <div class="px-6 py-4 border-b border-white/5 bg-white/[0.02]">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="w-6 h-6 bg-blue-500/20 text-blue-400 rounded-full flex items-center justify-center text-xs font-bold">1</span>
                        <h5 class="text-sm font-bold text-white">التسجيل العادي</h5>
                    </div>
                    <p class="text-xs text-gray-500">Standard Registration — الاسم + رقم الجوال</p>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <span class="w-5 h-5 bg-emerald-500/20 text-emerald-400 rounded-full flex items-center justify-center text-[10px] font-bold mt-0.5 shrink-0">①</span>
                            <div>
                                <p class="text-xs text-gray-300 font-semibold mb-1">التسجيل</p>
                                <p class="text-[11px] text-gray-500">POST /api/v1/register — أرسل الاسم ورقم الجوال. الإيميل وكلمة المرور اختياريين. إذا لم ترسل كلمة مرور سيتم استخدام رقم الجوال ككلمة مرور.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="w-5 h-5 bg-emerald-500/20 text-emerald-400 rounded-full flex items-center justify-center text-[10px] font-bold mt-0.5 shrink-0">②</span>
                            <div>
                                <p class="text-xs text-gray-300 font-semibold mb-1">تسجيل الدخول</p>
                                <p class="text-[11px] text-gray-500">POST /api/v1/login — سجّل بالجوال وكلمة المرور</p>
                            </div>
                        </div>
                    </div>
                    <div class="code-block rounded-xl p-4 text-xs mt-4 overflow-x-auto" dir="ltr">
<pre class="text-gray-300"><code><span class="comment"># التسجيل العادي</span>
<span class="cmd">curl</span> <span class="flag">-X</span> POST <span class="url">https://auth-service-api.mahamexpo.sa/api/v1/register</span> \
  <span class="flag">-H</span> <span class="str">"Content-Type: application/json"</span> \
  <span class="flag">-d</span> <span class="str">'{
    "name": "أحمد",
    "phone": "0501234567"
  }'</span>

<span class="comment"># كلمة المرور = رقم الجوال</span>
<span class="cmd">curl</span> <span class="flag">-X</span> POST <span class="url">https://auth-service-api.mahamexpo.sa/api/v1/login</span> \
  <span class="flag">-H</span> <span class="str">"Content-Type: application/json"</span> \
  <span class="flag">-d</span> <span class="str">'{
    "phone": "0501234567",
    "password": "0501234567"
  }'</span></code></pre>
                    </div>
                </div>
            </div>

            <!-- Method 2: OTP Registration -->
            <div class="glass border border-white/5 rounded-2xl overflow-hidden card-hover">
                <div class="px-6 py-4 border-b border-white/5 bg-white/[0.02]">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="w-6 h-6 bg-emerald-500/20 text-emerald-400 rounded-full flex items-center justify-center text-xs font-bold">2</span>
                        <h5 class="text-sm font-bold text-white">التسجيل عبر OTP</h5>
                    </div>
                    <p class="text-xs text-gray-500">OTP Registration — رقم الجوال + رمز التحقق</p>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <span class="w-5 h-5 bg-emerald-500/20 text-emerald-400 rounded-full flex items-center justify-center text-[10px] font-bold mt-0.5 shrink-0">①</span>
                            <div>
                                <p class="text-xs text-gray-300 font-semibold mb-1">طلب OTP</p>
                                <p class="text-[11px] text-gray-500">POST /api/v1/otp/send — أرسل رقم الجوال. يصلك رمز تحقق عبر SMS (في وضع الاختبار الرمز دائماً <code class="text-emerald-300">123456</code>)</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="w-5 h-5 bg-emerald-500/20 text-emerald-400 rounded-full flex items-center justify-center text-[10px] font-bold mt-0.5 shrink-0">②</span>
                            <div>
                                <p class="text-xs text-gray-300 font-semibold mb-1">تأكيد OTP</p>
                                <p class="text-[11px] text-gray-500">POST /api/v1/otp/verify — أرسل الرقم + الرمز. إذا المستخدم جديد يتم تسجيله تلقائياً وكلمة المرور = رقم الجوال</p>
                            </div>
                        </div>
                    </div>
                    <div class="code-block rounded-xl p-4 text-xs mt-4 overflow-x-auto" dir="ltr">
<pre class="text-gray-300"><code><span class="comment"># إرسال رمز التحقق</span>
<span class="cmd">curl</span> <span class="flag">-X</span> POST <span class="url">https://auth-service-api.mahamexpo.sa/api/v1/otp/send</span> \
  <span class="flag">-H</span> <span class="str">"Content-Type: application/json"</span> \
  <span class="flag">-d</span> <span class="str">'{"phone": "0501234567"}'</span>

<span class="comment"># تأكيد الرمز (والتسجيل التلقائي)</span>
<span class="cmd">curl</span> <span class="flag">-X</span> POST <span class="url">https://auth-service-api.mahamexpo.sa/api/v1/otp/verify</span> \
  <span class="flag">-H</span> <span class="str">"Content-Type: application/json"</span> \
  <span class="flag">-d</span> <span class="str">'{
    "phone": "0501234567",
    "otp": "123456"
  }'</span></code></pre>
                    </div>
                    <div class="mt-3 p-3 bg-amber-500/5 border border-amber-500/10 rounded-xl">
                        <p class="text-[11px] text-amber-400/80"><span class="font-bold">ملاحظة:</span> في وضع الاختبار (<code class="text-amber-300">sms_test_mode</code>)، رمز OTP دائماً <code class="text-emerald-300 font-bold">123456</code></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Important Notes -->
        <div class="mt-6 glass border border-white/5 rounded-2xl p-6">
            <h5 class="text-sm font-bold text-gray-300 mb-3">ملاحظات مهمة عن التسجيل</h5>
            <div class="grid md:grid-cols-3 gap-4">
                <div class="flex items-start gap-2">
                    <span class="text-emerald-400 mt-0.5">✓</span>
                    <p class="text-xs text-gray-400"><strong class="text-gray-300">الإيميل اختياري</strong> — يمكنك التسجيل بدون إيميل</p>
                </div>
                <div class="flex items-start gap-2">
                    <span class="text-emerald-400 mt-0.5">✓</span>
                    <p class="text-xs text-gray-400"><strong class="text-gray-300">كلمة المرور الافتراضية</strong> — إذا لم تحدد كلمة مرور، سيتم استخدام رقم الجوال</p>
                </div>
                <div class="flex items-start gap-2">
                    <span class="text-emerald-400 mt-0.5">✓</span>
                    <p class="text-xs text-gray-400"><strong class="text-gray-300">التسجيل التلقائي</strong> — عبر OTP، إذا الرقم جديد يتم إنشاء حساب تلقائياً</p>
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
<span class="cmd">curl</span> <span class="url">https://expo-service-api.mahamexpo.sa/api/v1/events</span> \
  <span class="flag">-H</span> <span class="str">"Accept: application/json"</span> \
  <span class="flag">-H</span> <span class="str">"Accept-Language: ar"</span>

<span class="comment"># الفعاليات المميزة</span>
<span class="cmd">curl</span> <span class="url">https://expo-service-api.mahamexpo.sa/api/v1/events/featured</span> \
  <span class="flag">-H</span> <span class="str">"Accept: application/json"</span>

<span class="comment"># تفاصيل فعالية مع المساحات</span>
<span class="cmd">curl</span> <span class="url">https://expo-service-api.mahamexpo.sa/api/v1/events/{event_id}/spaces</span> \
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
                <pre class="p-5 text-sm overflow-x-auto" dir="ltr"><code class="text-gray-300" id="code-visit"><span class="cmd">curl</span> <span class="flag">-X</span> POST <span class="url">https://expo-service-api.mahamexpo.sa/api/v1/visit-requests</span> \
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
                <pre class="p-5 text-sm overflow-x-auto" dir="ltr"><code class="text-gray-300" id="code-rental"><span class="cmd">curl</span> <span class="flag">-X</span> POST <span class="url">https://expo-service-api.mahamexpo.sa/api/v1/rental-requests</span> \
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
                <pre class="p-5 text-sm overflow-x-auto" dir="ltr"><code class="text-gray-300" id="code-dashboard"><span class="comment"># إحصائيات المنصة (عامة - بدون مصادقة)</span>
<span class="cmd">curl</span> <span class="url">https://expo-service-api.mahamexpo.sa/api/v1/statistics</span>

<span class="comment"># إحصائيات الفعاليات (عامة)</span>
<span class="cmd">curl</span> <span class="url">https://expo-service-api.mahamexpo.sa/api/v1/statistics/events</span>

<span class="comment"># إحصائيات المساحات (عامة)</span>
<span class="cmd">curl</span> <span class="url">https://expo-service-api.mahamexpo.sa/api/v1/statistics/spaces</span>

<span class="comment"># إحصائيات الأدمن (تتطلب مصادقة)</span>
<span class="cmd">curl</span> <span class="url">https://expo-service-api.mahamexpo.sa/api/v1/manage/statistics</span> \
  <span class="flag">-H</span> <span class="str">"Authorization: Bearer {admin-token}"</span>

<span class="comment"># لوحة تحكم الأدمن حسب الفترة</span>
<span class="cmd">curl</span> <span class="str">"<span class="url">https://expo-service-api.mahamexpo.sa/api/v1/manage/dashboard?spaces_period=month&revenue_period=week</span>"</span> \
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
            <div class="w-1 h-8 bg-emerald-500 rounded-full"></div>
            <h3 class="text-2xl font-bold">متغيرات البيئة</h3>
            <span class="text-xs bg-emerald-500/10 text-emerald-400 px-2.5 py-1 rounded-full">Environment Variables</span>
        </div>
        <p class="text-gray-500 text-sm mb-8 mr-4">جميع المتغيرات المطلوبة والاختيارية لتشغيل خدمة المعرض Expo API</p>

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
                        ];
                    @endphp
                    @foreach($requiredVars as $v)
                    <div class="endpoint-row flex items-center px-6 py-3.5 gap-3">
                        <code class="text-xs bg-rose-500/10 px-2.5 py-1 rounded text-rose-300 font-mono min-w-[180px]">{{ $v[0] }}</code>
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
                            ['APP_NAME', 'Maham Expo API', 'اسم التطبيق'],
                            ['APP_ENV', 'production', 'بيئة التشغيل'],
                            ['APP_DEBUG', 'false', 'وضع التصحيح'],
                            ['APP_URL', 'https://expo-service-api.mahamexpo.sa', 'رابط التطبيق الرئيسي'],
                            ['APP_LOCALE', 'ar', 'اللغة الافتراضية'],
                            ['DB_CONNECTION', 'mysql', 'نوع قاعدة البيانات'],
                            ['DB_HOST', 'expo-mysql', 'مضيف قاعدة البيانات'],
                            ['DB_PORT', '3306', 'منفذ قاعدة البيانات'],
                            ['DB_DATABASE', 'maham_expo_api', 'اسم قاعدة البيانات'],
                            ['DB_USERNAME', 'expo_user', 'مستخدم قاعدة البيانات'],
                        ];
                    @endphp
                    @foreach($appVars as $v)
                    <div class="endpoint-row flex items-center px-6 py-2.5 gap-3">
                        <code class="text-xs bg-white/5 px-2.5 py-0.5 rounded text-indigo-300/70 font-mono min-w-[180px]">{{ $v[0] }}</code>
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
                            ['REDIS_HOST', 'expo-redis', 'مضيف Redis'],
                            ['REDIS_PORT', '6379', 'منفذ Redis'],
                            ['REDIS_PASSWORD', '(empty)', 'كلمة سر Redis'],
                            ['CACHE_STORE', 'redis', 'محرك التخزين المؤقت'],
                            ['CACHE_PREFIX', 'expo_', 'بادئة مفاتيح الكاش'],
                            ['QUEUE_CONNECTION', 'redis', 'محرك قائمة المهام'],
                            ['SESSION_DRIVER', 'redis', 'محرك الجلسات'],
                            ['SESSION_LIFETIME', '120', 'عمر الجلسة (دقائق)'],
                        ];
                    @endphp
                    @foreach($redisVars as $v)
                    <div class="endpoint-row flex items-center px-6 py-2.5 gap-3">
                        <code class="text-xs bg-white/5 px-2.5 py-0.5 rounded text-indigo-300/70 font-mono min-w-[180px]">{{ $v[0] }}</code>
                        <code class="text-[10px] text-gray-600 font-mono">{{ $v[1] }}</code>
                        <span class="text-xs text-gray-600 flex-1 text-left" dir="rtl">{{ $v[2] }}</span>
                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-white/5 text-gray-500">optional</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Auth Service Connection -->
            <div class="glass border border-white/5 rounded-2xl overflow-hidden mb-4">
                <div class="px-6 py-3 border-b border-white/5 bg-white/[0.02]">
                    <h5 class="text-xs font-bold text-gray-400">Auth Service Connection - اتصال خدمة المصادقة</h5>
                </div>
                <div class="divide-y divide-white/5">
                    @php
                        $authServiceVars = [
                            ['AUTH_SERVICE_URL', 'http://auth-service', 'رابط خدمة المصادقة الداخلي'],
                            ['AUTH_SERVICE_TIMEOUT', '10', 'مهلة الاتصال بخدمة المصادقة (ثواني)'],
                            ['AUTH_SERVICE_CACHE_TTL', '300', 'مدة تخزين بيانات المصادقة مؤقتاً (ثواني)'],
                        ];
                    @endphp
                    @foreach($authServiceVars as $v)
                    <div class="endpoint-row flex items-center px-6 py-2.5 gap-3">
                        <code class="text-xs bg-white/5 px-2.5 py-0.5 rounded text-indigo-300/70 font-mono min-w-[180px]">{{ $v[0] }}</code>
                        <code class="text-[10px] text-gray-600 font-mono">{{ $v[1] }}</code>
                        <span class="text-xs text-gray-600 flex-1 text-left" dir="rtl">{{ $v[2] }}</span>
                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-white/5 text-gray-500">optional</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Service & Security -->
            <div class="glass border border-white/5 rounded-2xl overflow-hidden mb-4">
                <div class="px-6 py-3 border-b border-white/5 bg-white/[0.02]">
                    <h5 class="text-xs font-bold text-gray-400">Service & Security</h5>
                </div>
                <div class="divide-y divide-white/5">
                    @php
                        $securityVars = [
                            ['SERVICE_NAME', 'expo-api', 'اسم الخدمة'],
                            ['SERVICE_VERSION', '1.0.0', 'إصدار الخدمة'],
                            ['RATE_LIMIT_PER_MINUTE', '60', 'حد الطلبات في الدقيقة'],
                            ['BCRYPT_ROUNDS', '12', 'جولات تشفير كلمات المرور'],
                        ];
                    @endphp
                    @foreach($securityVars as $v)
                    <div class="endpoint-row flex items-center px-6 py-2.5 gap-3">
                        <code class="text-xs bg-white/5 px-2.5 py-0.5 rounded text-indigo-300/70 font-mono min-w-[180px]">{{ $v[0] }}</code>
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
                        <code class="text-xs bg-white/5 px-2.5 py-0.5 rounded text-indigo-300/70 font-mono min-w-[180px]">{{ $v[0] }}</code>
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
                <div class="w-7 h-7 bg-emerald-500/20 rounded-lg flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <span>Maham Expo API v{{ config('app.version', '1.0.0') }}</span>
            </div>
            <div class="flex items-center gap-4">
                <a href="https://auth-service-api.mahamexpo.sa/docs" class="hover:text-indigo-400 transition">Auth Service</a>
                <a href="https://dashboard.mahamexpo.sa" class="hover:text-orange-400 transition">لوحة التحكم</a>
                <a href="/api/health" class="hover:text-emerald-400 transition">Health</a>
                <a href="/docs" class="hover:text-cyan-400 transition">API Docs</a>
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
