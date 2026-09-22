<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'نظام فرز وتأهيل العملاء بالذكاء الاصطناعي')</title>
    
    <!-- Google Fonts: Cairo -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Cairo', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            900: '#312e81',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f8fafc;
        }
    </style>
</head>
<body class="min-h-screen text-slate-800 antialiased flex flex-col justify-between">
    <!-- Navigation Bar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <!-- Logo & Brand -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-600 to-indigo-500 flex items-center justify-center text-white shadow-md shadow-brand-500/20">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <div>
                        <a href="{{ url('/') }}" class="font-bold text-lg text-slate-900 tracking-tight flex items-center gap-2">
                            AI Lead Triage System
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-brand-50 text-brand-700 border border-brand-200">Production</span>
                        </a>
                        <p class="text-xs text-slate-500">نظام استقبال وفرز العملاء المؤتمت بالذكاء الاصطناعي</p>
                    </div>
                </div>

                <!-- Navigation Links -->
                <nav class="flex items-center gap-2 sm:gap-3">
                    <a href="{{ route('leads.create') }}" 
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('leads.create') ? 'bg-brand-50 text-brand-700 border border-brand-200' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        📝 إرسال طلب عميل
                    </a>
                    <a href="{{ route('leads.dashboard') }}" 
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('leads.dashboard') ? 'bg-brand-50 text-brand-700 border border-brand-200' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                        📊 لوحة فرز المشرف
                    </a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 text-center text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row justify-between items-center gap-2">
            <span>Laravel 13 + n8n Automation Engine + Google Gemini AI</span>
            <div class="flex items-center gap-4">
                <span class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    نظام الطابور والأتمتة نشط
                </span>
            </div>
        </div>
    </footer>

    @yield('scripts')
</body>
</html>
