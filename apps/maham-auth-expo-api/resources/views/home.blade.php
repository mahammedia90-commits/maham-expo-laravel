<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maham Auth Service</title>
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
        .gradient-bg { background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%); }
        .card-hover { transition: all 0.3s ease; }
        .card-hover:hover { transform: translateY(-2px); box-shadow: 0 12px 24px rgba(0,0,0,0.15); }
        .pulse-dot { animation: pulse-dot 2s ease-in-out infinite; }
        @keyframes pulse-dot { 0%, 100% { opacity: 1; } 50% { opacity: 0.4; } }
        .glow { box-shadow: 0 0 20px rgba(59, 130, 246, 0.3); }
    </style>
</head>
<body class="gradient-bg min-h-screen text-white">

    <!-- Header -->
    <header class="border-b border-white/10">
        <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center glow">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-lg font-bold">Maham Auth Service</h1>
                    <p class="text-xs text-gray-400">Central Authentication & Authorization</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs bg-blue-500/20 text-blue-400 px-3 py-1 rounded-full font-semibold">v{{ config('auth-service.service_version', '1.0.0') }}</span>
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
            خدمة المصادقة المركزية
        </h2>
        <p class="text-gray-400 text-lg max-w-2xl mx-auto mb-8">
            نظام مصادقة وإدارة صلاحيات متكامل يعتمد على JWT مع دعم الأدوار والخدمات المتعددة
        </p>
        <div class="flex items-center justify-center gap-4">
            <a href="/docs" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2.5 rounded-lg font-semibold transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                API Documentation
            </a>
            <a href="/api/health" target="_blank" class="bg-white/10 hover:bg-white/20 text-white px-6 py-2.5 rounded-lg font-semibold transition flex items-center gap-2 border border-white/10">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Health Check
            </a>
        </div>
    </section>

    <!-- Features Grid -->
    <section class="max-w-6xl mx-auto px-6 pb-12">
        <h3 class="text-xl font-bold mb-6 text-gray-200">الميزات الرئيسية</h3>
        <div class="grid md:grid-cols-3 gap-4">
            <div class="bg-white/5 border border-white/10 rounded-xl p-6 card-hover">
                <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                </div>
                <h4 class="font-bold mb-2">JWT Authentication</h4>
                <p class="text-sm text-gray-400">مصادقة بتوكنات JWT مع دعم التجديد والإلغاء. صلاحية التوكن {{ config('auth-service.jwt.ttl', 60) }} دقيقة.</p>
            </div>
            <div class="bg-white/5 border border-white/10 rounded-xl p-6 card-hover">
                <div class="w-10 h-10 bg-emerald-500/20 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <h4 class="font-bold mb-2">Roles & Permissions</h4>
                <p class="text-sm text-gray-400">نظام أدوار وصلاحيات متعدد المستويات مع دعم الرفض الصريح والصلاحيات المؤقتة.</p>
            </div>
            <div class="bg-white/5 border border-white/10 rounded-xl p-6 card-hover">
                <div class="w-10 h-10 bg-purple-500/20 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <h4 class="font-bold mb-2">Service-to-Service</h4>
                <p class="text-sm text-gray-400">تواصل آمن بين الخدمات عبر توكنات مخصصة مع التحقق من IP وتسجيل الأنشطة.</p>
            </div>
            <div class="bg-white/5 border border-white/10 rounded-xl p-6 card-hover">
                <div class="w-10 h-10 bg-amber-500/20 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
                <h4 class="font-bold mb-2">Service Roles</h4>
                <p class="text-sm text-gray-400">تحديد أدوار لكل خدمة للتحكم بمن يقدر يسجل دخول. إضافة وتعديل الأدوار ديناميكياً.</p>
            </div>
            <div class="bg-white/5 border border-white/10 rounded-xl p-6 card-hover">
                <div class="w-10 h-10 bg-rose-500/20 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                </div>
                <h4 class="font-bold mb-2">Audit Trail</h4>
                <p class="text-sm text-gray-400">تسجيل شامل لجميع الأحداث: تسجيل دخول، تغييرات الأدوار، تعديلات الصلاحيات مع تتبع IP.</p>
            </div>
            <div class="bg-white/5 border border-white/10 rounded-xl p-6 card-hover">
                <div class="w-10 h-10 bg-cyan-500/20 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg>
                </div>
                <h4 class="font-bold mb-2">Redis Caching</h4>
                <p class="text-sm text-gray-400">كاش ذكي للصلاحيات والأدوار لأداء سريع مع TTL قابل للتخصيص.</p>
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
                        ['POST', '/api/auth/register', 'تسجيل مستخدم جديد', 'public'],
                        ['POST', '/api/auth/login', 'تسجيل الدخول (يدعم service_name)', 'public'],
                        ['GET', '/api/auth/me', 'بيانات المستخدم الحالي', 'auth'],
                        ['POST', '/api/auth/refresh', 'تجديد التوكن', 'auth'],
                        ['POST', '/api/services', 'تسجيل خدمة جديدة (مع roles)', 'admin'],
                        ['PUT', '/api/services/{id}', 'تحديث خدمة (تعديل roles)', 'admin'],
                        ['POST', '/api/services/{id}/roles', 'إضافة أدوار للخدمة', 'admin'],
                        ['POST', '/api/service/verify-token', 'التحقق من توكن (S2S)', 'service'],
                    ];
                @endphp
                @foreach($endpoints as $ep)
                <div class="flex items-center px-5 py-3 hover:bg-white/5 transition">
                    <span class="text-xs font-bold px-2 py-0.5 rounded font-mono min-w-[52px] text-center {{ $ep[0] === 'GET' ? 'bg-emerald-500/20 text-emerald-400' : ($ep[0] === 'POST' ? 'bg-blue-500/20 text-blue-400' : ($ep[0] === 'PUT' ? 'bg-amber-500/20 text-amber-400' : 'bg-rose-500/20 text-rose-400')) }}">{{ $ep[0] }}</span>
                    <code class="text-sm text-gray-300 mr-3 font-mono" dir="ltr">{{ $ep[1] }}</code>
                    <span class="text-sm text-gray-500 mr-auto">{{ $ep[2] }}</span>
                    <span class="text-[10px] px-2 py-0.5 rounded-full font-semibold {{ $ep[3] === 'public' ? 'bg-green-500/10 text-green-400' : ($ep[3] === 'auth' ? 'bg-blue-500/10 text-blue-400' : ($ep[3] === 'admin' ? 'bg-amber-500/10 text-amber-400' : 'bg-purple-500/10 text-purple-400')) }}">{{ $ep[3] }}</span>
                </div>
                @endforeach
            </div>
            <div class="px-5 py-3 bg-white/5 text-center">
                <a href="/docs" class="text-sm text-blue-400 hover:text-blue-300 transition">عرض جميع نقاط الوصول &larr;</a>
            </div>
        </div>
    </section>

    <!-- Quick Start -->
    <section class="max-w-6xl mx-auto px-6 pb-12">
        <h3 class="text-xl font-bold mb-6 text-gray-200">البدء السريع</h3>
        <div class="bg-slate-800/50 border border-white/10 rounded-xl overflow-hidden">
            <div class="bg-slate-700/50 px-4 py-2 flex items-center justify-between">
                <span class="text-xs text-gray-400">Login Request</span>
                <button onclick="copyCode()" class="text-xs text-gray-400 hover:text-white transition flex items-center gap-1" id="copyBtn">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    Copy
                </button>
            </div>
            <pre class="p-4 text-sm overflow-x-auto" dir="ltr"><code class="text-gray-300"><span class="text-amber-400">curl</span> -X POST <span class="text-green-400">http://localhost:8001/api/auth/login</span> \
  -H <span class="text-sky-400">"Content-Type: application/json"</span> \
  -d <span class="text-sky-400">'{
    "identifier": "admin@auth-service.local",
    "password": "password",
    "service_name": "expo-app"
  }'</span></code></pre>
        </div>
    </section>

    <!-- Docker Info -->
    <section class="max-w-6xl mx-auto px-6 pb-12">
        <h3 class="text-xl font-bold mb-6 text-gray-200">Docker Services</h3>
        <div class="grid md:grid-cols-2 gap-4">
            <div class="bg-white/5 border border-white/10 rounded-xl p-5">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 bg-blue-500/20 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <div>
                        <div class="font-bold text-sm">Auth Service</div>
                        <a href="http://localhost:8001" class="text-xs text-gray-400 hover:text-blue-400 transition">http://localhost:8001</a>
                    </div>
                    <span class="mr-auto text-xs bg-green-500/20 text-green-400 px-2 py-0.5 rounded-full">active</span>
                </div>
                <div class="space-y-2 text-xs text-gray-400">
                    <div class="flex justify-between"><span>MySQL</span><span class="font-mono text-gray-300">localhost:3307</span></div>
                    <div class="flex justify-between"><span>Redis</span><span class="font-mono text-gray-300">localhost:6380</span></div>
                </div>
            </div>
            <div class="bg-white/5 border border-white/10 rounded-xl p-5">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 bg-emerald-500/20 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div>
                        <div class="font-bold text-sm">Expo API</div>
                        <a href="http://localhost:8002" class="text-xs text-gray-400 hover:text-emerald-400 transition">http://localhost:8002</a>
                    </div>
                    <span id="expoStatus" class="mr-auto text-xs bg-gray-500/20 text-gray-400 px-2 py-0.5 rounded-full">checking...</span>
                </div>
                <div class="space-y-2 text-xs text-gray-400">
                    <div class="flex justify-between"><span>MySQL</span><span class="font-mono text-gray-300">localhost:3308</span></div>
                    <div class="flex justify-between"><span>Redis</span><span class="font-mono text-gray-300">localhost:6381</span></div>
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
            <span>Maham Auth Service v{{ config('auth-service.service_version', '1.0.0') }}</span>
            <span>{{ now()->format('Y') }} &copy; Maham Expo</span>
        </div>
    </footer>

    <script>
        fetch('/api/health').then(r=>r.json()).then(d=>{
            const el=document.getElementById('healthStatus');
            if(d.status==='ok'){el.innerHTML='<span class="w-2 h-2 bg-green-400 rounded-full pulse-dot"></span> online';el.className=el.className.replace('text-gray-400','text-green-400').replace('bg-gray-700/50','bg-green-500/10')}
        }).catch(()=>{});

        fetch('http://localhost:8002/api/health').then(r=>r.json()).then(d=>{
            const el=document.getElementById('expoStatus');
            if(d.status==='ok'){el.textContent='active';el.className=el.className.replace('text-gray-400','text-green-400').replace('bg-gray-500/20','bg-green-500/20')}
        }).catch(()=>{const el=document.getElementById('expoStatus');el.textContent='offline';el.className=el.className.replace('text-gray-400','text-rose-400').replace('bg-gray-500/20','bg-rose-500/20')});

        function copyCode(){
            navigator.clipboard.writeText(`curl -X POST http://localhost:8001/api/auth/login \\\n  -H "Content-Type: application/json" \\\n  -d '{"identifier":"admin@auth-service.local","password":"password","service_name":"expo-app"}'`);
            const b=document.getElementById('copyBtn');b.innerHTML='<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Copied!';
            setTimeout(()=>{b.innerHTML='<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg> Copy'},2000);
        }
    </script>
</body>
</html>
