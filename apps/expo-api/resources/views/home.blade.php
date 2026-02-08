<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maham Expo API</title>
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
        code, .font-mono { font-family: 'JetBrains Mono', monospace; }
        .gradient-bg { background: linear-gradient(135deg, #022c22 0%, #064e3b 50%, #022c22 100%); }
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-2px); box-shadow: 0 12px 24px rgba(0,0,0,0.15); }
        .pulse-dot { animation: pulse-dot 2s ease-in-out infinite; }
        @keyframes pulse-dot { 0%, 100% { opacity: 1; } 50% { opacity: 0.4; } }
        .glow { box-shadow: 0 0 20px rgba(16, 185, 129, 0.3); }
    </style>
</head>
<body class="gradient-bg min-h-screen text-white">

    <!-- Header -->
    <header class="border-b border-white/10">
        <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-emerald-500 rounded-xl flex items-center justify-center glow">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-lg font-bold">Maham Expo API</h1>
                    <p class="text-xs text-gray-400">Exhibition & Events Management</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs bg-emerald-500/20 text-emerald-400 px-3 py-1 rounded-full font-semibold">v{{ config('expo-api.service_version', '1.0.0') }}</span>
                <span id="healthStatus" class="text-xs bg-gray-700/50 text-gray-400 px-3 py-1 rounded-full flex items-center gap-1.5">
                    <span class="w-2 h-2 bg-gray-500 rounded-full"></span>
                    checking...
                </span>
            </div>
        </div>
    </header>

    <!-- Hero -->
    <section class="max-w-6xl mx-auto px-6 py-16 text-center">
        <div class="inline-flex items-center gap-2 bg-white/5 border border-white/10 rounded-full px-4 py-2 mb-6">
            <span class="w-2 h-2 bg-green-400 rounded-full pulse-dot"></span>
            <span class="text-sm text-gray-300">Service is running</span>
        </div>
        <h2 class="text-4xl font-extrabold mb-4 bg-gradient-to-r from-white to-gray-400 bg-clip-text text-transparent">
            منصة إدارة المعارض والفعاليات
        </h2>
        <p class="text-gray-400 text-lg max-w-2xl mx-auto mb-8">
            API متكامل لإدارة المعارض، الفعاليات، المساحات، طلبات الزيارة والتأجير مع نظام ملفات تجارية
        </p>
        <div class="flex items-center justify-center gap-4">
            <a href="/api/health" target="_blank" class="bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-2.5 rounded-lg font-semibold transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Health Check
            </a>
            <a href="http://localhost:8001" target="_blank" class="bg-white/10 hover:bg-white/20 text-white px-6 py-2.5 rounded-lg font-semibold transition flex items-center gap-2 border border-white/10">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                Auth Service
            </a>
        </div>
    </section>

    <!-- Features Grid -->
    <section class="max-w-6xl mx-auto px-6 pb-12">
        <h3 class="text-xl font-bold mb-6 text-gray-200">الميزات الرئيسية</h3>
        <div class="grid md:grid-cols-3 gap-4">
            <div class="bg-white/5 border border-white/10 rounded-xl p-6 card-hover">
                <div class="w-10 h-10 bg-emerald-500/20 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <h4 class="font-bold mb-2">إدارة الفعاليات</h4>
                <p class="text-sm text-gray-400">إنشاء وإدارة الفعاليات والمعارض مع دعم الفعاليات المميزة والتصنيفات والمدن.</p>
            </div>
            <div class="bg-white/5 border border-white/10 rounded-xl p-6 card-hover">
                <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <h4 class="font-bold mb-2">إدارة المساحات</h4>
                <p class="text-sm text-gray-400">مساحات عرض متعددة الأحجام والأسعار مرتبطة بالفعاليات مع إدارة التوفر.</p>
            </div>
            <div class="bg-white/5 border border-white/10 rounded-xl p-6 card-hover">
                <div class="w-10 h-10 bg-amber-500/20 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <h4 class="font-bold mb-2">طلبات التأجير</h4>
                <p class="text-sm text-gray-400">نظام طلبات تأجير المساحات مع دعم الموافقة والرفض وتتبع المدفوعات.</p>
            </div>
            <div class="bg-white/5 border border-white/10 rounded-xl p-6 card-hover">
                <div class="w-10 h-10 bg-purple-500/20 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </div>
                <h4 class="font-bold mb-2">طلبات الزيارة</h4>
                <p class="text-sm text-gray-400">حجز زيارات للفعاليات مع تحديد عدد الزوار والموافقة من الإدارة.</p>
            </div>
            <div class="bg-white/5 border border-white/10 rounded-xl p-6 card-hover">
                <div class="w-10 h-10 bg-rose-500/20 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <h4 class="font-bold mb-2">الملفات التجارية</h4>
                <p class="text-sm text-gray-400">ملفات تجارية للمستثمرين والتجار مع نظام تحقق وموافقة من الإدارة.</p>
            </div>
            <div class="bg-white/5 border border-white/10 rounded-xl p-6 card-hover">
                <div class="w-10 h-10 bg-cyan-500/20 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                </div>
                <h4 class="font-bold mb-2">المفضلة والإشعارات</h4>
                <p class="text-sm text-gray-400">نظام مفضلة للفعاليات والمساحات مع إشعارات فورية للتحديثات.</p>
            </div>
        </div>
    </section>

    <!-- API Endpoints -->
    <section class="max-w-6xl mx-auto px-6 pb-12">
        <h3 class="text-xl font-bold mb-6 text-gray-200">نقاط الوصول الرئيسية</h3>
        <div class="bg-white/5 border border-white/10 rounded-xl overflow-hidden">
            <div class="divide-y divide-white/5">
                @php
                    $endpoints = [
                        ['GET', '/api/events', 'قائمة الفعاليات', 'public'],
                        ['GET', '/api/events/featured', 'الفعاليات المميزة', 'public'],
                        ['GET', '/api/categories', 'التصنيفات', 'public'],
                        ['GET', '/api/cities', 'المدن', 'public'],
                        ['GET', '/api/spaces/{id}', 'تفاصيل المساحة', 'public'],
                        ['POST', '/api/visit-requests', 'إنشاء طلب زيارة', 'auth'],
                        ['POST', '/api/rental-requests', 'إنشاء طلب تأجير', 'auth'],
                        ['GET', '/api/profile', 'الملف التجاري', 'auth'],
                        ['GET', '/api/favorites', 'المفضلة', 'auth'],
                        ['POST', '/api/admin/events', 'إنشاء فعالية', 'admin'],
                        ['PUT', '/api/admin/visit-requests/{id}/approve', 'موافقة طلب زيارة', 'admin'],
                        ['PUT', '/api/admin/rental-requests/{id}/approve', 'موافقة طلب تأجير', 'admin'],
                    ];
                @endphp
                @foreach($endpoints as $ep)
                <div class="flex items-center px-5 py-3 hover:bg-white/5 transition">
                    <span class="text-xs font-bold px-2 py-0.5 rounded font-mono min-w-[52px] text-center {{ $ep[0] === 'GET' ? 'bg-emerald-500/20 text-emerald-400' : ($ep[0] === 'POST' ? 'bg-blue-500/20 text-blue-400' : ($ep[0] === 'PUT' ? 'bg-amber-500/20 text-amber-400' : 'bg-rose-500/20 text-rose-400')) }}">{{ $ep[0] }}</span>
                    <code class="text-sm text-gray-300 mr-3 font-mono" dir="ltr">{{ $ep[1] }}</code>
                    <span class="text-sm text-gray-500 mr-auto">{{ $ep[2] }}</span>
                    <span class="text-[10px] px-2 py-0.5 rounded-full font-semibold {{ $ep[3] === 'public' ? 'bg-green-500/10 text-green-400' : ($ep[3] === 'auth' ? 'bg-blue-500/10 text-blue-400' : 'bg-amber-500/10 text-amber-400') }}">{{ $ep[3] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Docker Info -->
    <section class="max-w-6xl mx-auto px-6 pb-12">
        <h3 class="text-xl font-bold mb-6 text-gray-200">Docker Services</h3>
        <div class="grid md:grid-cols-2 gap-4">
            <div class="bg-white/5 border border-white/10 rounded-xl p-5">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 bg-emerald-500/20 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div>
                        <div class="font-bold text-sm">Expo API</div>
                        <a href="http://localhost:8002" class="text-xs text-gray-400 hover:text-emerald-400 transition">http://localhost:8002</a>
                    </div>
                    <span class="mr-auto text-xs bg-green-500/20 text-green-400 px-2 py-0.5 rounded-full">active</span>
                </div>
                <div class="space-y-2 text-xs text-gray-400">
                    <div class="flex justify-between"><span>MySQL</span><span class="font-mono text-gray-300">localhost:3308</span></div>
                    <div class="flex justify-between"><span>Redis</span><span class="font-mono text-gray-300">localhost:6381</span></div>
                </div>
            </div>
            <div class="bg-white/5 border border-white/10 rounded-xl p-5">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 bg-blue-500/20 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <div>
                        <div class="font-bold text-sm">Auth Service</div>
                        <a href="http://localhost:8001" class="text-xs text-gray-400 hover:text-blue-400 transition">http://localhost:8001</a>
                    </div>
                    <span id="authStatus" class="mr-auto text-xs bg-gray-500/20 text-gray-400 px-2 py-0.5 rounded-full">checking...</span>
                </div>
                <div class="space-y-2 text-xs text-gray-400">
                    <div class="flex justify-between"><span>MySQL</span><span class="font-mono text-gray-300">localhost:3307</span></div>
                    <div class="flex justify-between"><span>Redis</span><span class="font-mono text-gray-300">localhost:6380</span></div>
                </div>
            </div>
        </div>
        <div class="mt-4 bg-white/5 border border-white/10 rounded-xl p-5">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-orange-500/20 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
                </div>
                <div>
                    <div class="font-bold text-sm">phpMyAdmin</div>
                    <div class="text-xs text-gray-400">إدارة قواعد البيانات</div>
                </div>
                <a href="http://localhost:8080" target="_blank" class="mr-auto text-xs bg-orange-500/20 text-orange-400 px-3 py-1 rounded-full hover:bg-orange-500/30 transition">http://localhost:8080 &larr;</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-white/10 mt-8">
        <div class="max-w-6xl mx-auto px-6 py-6 flex items-center justify-between text-sm text-gray-500">
            <span>Maham Expo API v{{ config('expo-api.service_version', '1.0.0') }}</span>
            <span>{{ now()->format('Y') }} &copy; Maham Expo</span>
        </div>
    </footer>

    <script>
        fetch('/api/health').then(r=>r.json()).then(d=>{
            const el=document.getElementById('healthStatus');
            if(d.status==='ok'){el.innerHTML='<span class="w-2 h-2 bg-green-400 rounded-full pulse-dot"></span> online';el.className=el.className.replace('text-gray-400','text-green-400').replace('bg-gray-700/50','bg-green-500/10')}
        }).catch(()=>{});

        fetch('http://localhost:8001/api/health').then(r=>r.json()).then(d=>{
            const el=document.getElementById('authStatus');
            if(d.status==='ok'){el.textContent='active';el.className=el.className.replace('text-gray-400','text-green-400').replace('bg-gray-500/20','bg-green-500/20')}
        }).catch(()=>{const el=document.getElementById('authStatus');el.textContent='offline';el.className=el.className.replace('text-gray-400','text-rose-400').replace('bg-gray-500/20','bg-rose-500/20')});
    </script>
</body>
</html>
