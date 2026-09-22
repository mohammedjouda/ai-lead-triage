@extends('layouts.app')

@section('title', 'لوحة فرز وتحليل العملاء — AI Lead Triage Dashboard')

@section('content')
<div>
    <!-- Dashboard Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    لوحة فرز وتحليل العملاء
                </h1>
                <span class="text-xs font-semibold px-2.5 py-1 rounded bg-slate-100 text-slate-700 border border-slate-200">
                    Supervisor Console
                </span>
            </div>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                استعراض طلبات العملاء الواردة، التحليل الآلي للمتطلبات، وتصنيف درجات الأولوية والإجراء المقترح.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Auto-refresh switch -->
            <label class="inline-flex items-center gap-2 cursor-pointer bg-white px-3 py-2 rounded-lg border border-slate-200 shadow-sm text-xs font-medium text-slate-600 hover:border-slate-300 transition-colors">
                <input type="checkbox" id="autoRefreshToggle" onchange="toggleAutoRefresh(this)" class="sr-only peer">
                <div class="w-7 h-4 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[3px] after:right-[3px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-2.5 after:w-2.5 after:transition-all peer-checked:bg-slate-900 relative"></div>
                <span id="refreshLabel">تحديث تلقائي (موقوف)</span>
            </label>

            <!-- Manual Refresh -->
            <a href="{{ request()->fullUrl() }}" 
               class="px-3.5 py-2 rounded-lg bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 shadow-sm text-xs font-semibold transition-all inline-flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                <span>تحديث</span>
            </a>

            <!-- Submit New Lead Button -->
            <a href="{{ route('leads.create') }}" 
               class="px-3.5 py-2 rounded-lg bg-slate-900 hover:bg-slate-800 text-white shadow-sm text-xs font-semibold transition-all inline-flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>تقديم طلب جديد</span>
            </a>
        </div>
    </div>

    <!-- Metric Overview Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Total Inquiries -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">إجمالي الطلبات</span>
                <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ $metrics['total'] }}</span>
                <span class="text-xs text-slate-400 mr-1.5 font-medium">سجل مسجل</span>
            </div>
        </div>

        <!-- AI Qualified -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">تم فرزها بالـ AI</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ $metrics['qualified'] }}</span>
                <span class="text-xs text-emerald-700 mr-1.5 font-medium">مكتمل التحليل</span>
            </div>
        </div>

        <!-- High Urgency (Hot) -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">أولوية قصوى (HOT)</span>
                <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-700 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ $metrics['hot'] }}</span>
                <span class="text-xs text-rose-700 mr-1.5 font-medium">تحتاج تواصل عاجل</span>
            </div>
        </div>

        <!-- Pending Processing -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">قيد المعالجة</span>
                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ $metrics['pending'] }}</span>
                <span class="text-xs text-amber-700 mr-1.5 font-medium">في مسار الفرز</span>
            </div>
        </div>
    </div>

    <!-- Filter Pills -->
    <div class="flex flex-wrap items-center gap-2 mb-6">
        <span class="text-xs font-bold text-slate-400 ml-1">تصفية العرض:</span>
        <a href="{{ route('leads.dashboard') }}" 
           class="text-xs px-3 py-1.5 rounded-lg font-semibold transition-all {{ !request('filter') ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            الكل ({{ $metrics['total'] }})
        </a>
        <a href="{{ route('leads.dashboard', ['filter' => 'hot']) }}" 
           class="text-xs px-3 py-1.5 rounded-lg font-semibold transition-all {{ request('filter') === 'hot' ? 'bg-rose-700 text-white' : 'bg-white text-rose-800 hover:bg-rose-50 border border-rose-200' }}">
            أولوية قصوى (&ge; 80)
        </a>
        <a href="{{ route('leads.dashboard', ['filter' => 'warm']) }}" 
           class="text-xs px-3 py-1.5 rounded-lg font-semibold transition-all {{ request('filter') === 'warm' ? 'bg-amber-700 text-white' : 'bg-white text-amber-800 hover:bg-amber-50 border border-amber-200' }}">
            أولوية متوسطة (50-79)
        </a>
        <a href="{{ route('leads.dashboard', ['filter' => 'cold']) }}" 
           class="text-xs px-3 py-1.5 rounded-lg font-semibold transition-all {{ request('filter') === 'cold' ? 'bg-slate-700 text-white' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
            استفسارات عامة (&lt; 50)
        </a>
        <a href="{{ route('leads.dashboard', ['filter' => 'pending']) }}" 
           class="text-xs px-3 py-1.5 rounded-lg font-semibold transition-all {{ request('filter') === 'pending' ? 'bg-slate-800 text-white' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
            بانتظار اكتمال التحليل
        </a>
    </div>

    <!-- Leads List -->
    @if($leads->isEmpty())
        <div class="bg-white rounded-2xl p-12 text-center border border-slate-200 shadow-sm">
            <div class="w-12 h-12 rounded-lg bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <h3 class="text-base font-bold text-slate-800">لا توجد طلبات تطابق الفلتر المحدد</h3>
            <p class="text-xs text-slate-500 mt-1">يمكنك تقديم طلب استفسار جديد لبدء دورة الفرز الآلي وتجربتها بشكل عملي.</p>
            <a href="{{ route('leads.create') }}" class="mt-4 inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-slate-900 text-white text-xs font-semibold hover:bg-slate-800 transition-all">
                <span>تقديم طلب جديد</span>
                <span>&larr;</span>
            </a>
        </div>
    @else
        <div class="space-y-5">
            @foreach($leads as $lead)
                @php
                    $analysis = $lead->analysis;
                    $score = $analysis ? $analysis->lead_score : null;
                    $classification = 'COLD';
                    if ($score >= 80) $classification = 'HOT';
                    elseif ($score >= 50) $classification = 'WARM';

                    $scoreColor = 'text-slate-700 bg-slate-100';
                    $scoreBarColor = 'bg-slate-500';
                    if ($score >= 80) {
                        $scoreColor = 'text-rose-800 bg-rose-50 border border-rose-200';
                        $scoreBarColor = 'bg-rose-600';
                    } elseif ($score >= 50) {
                        $scoreColor = 'text-amber-800 bg-amber-50 border border-amber-200';
                        $scoreBarColor = 'bg-amber-600';
                    }
                @endphp

                <div class="bg-white rounded-xl border border-slate-200 p-5 sm:p-6 shadow-sm hover:border-slate-300 transition-all">
                    <!-- Card Top Header -->
                    <div class="flex flex-wrap justify-between items-start gap-3 pb-4 border-b border-slate-100">
                        <div class="flex flex-wrap items-center gap-2.5">
                            <span class="font-mono text-xs font-bold text-slate-700 bg-slate-100 px-2.5 py-1 rounded">
                                #{{ $lead->id }}
                            </span>
                            <h2 class="text-sm font-bold text-slate-900">
                                {{ $lead->name ?? 'جهة غير محددة' }}
                            </h2>
                            @if($lead->company)
                                <span class="text-xs text-slate-600 bg-slate-50 px-2.5 py-0.5 rounded border border-slate-200 font-medium">
                                    {{ $lead->company }}
                                </span>
                            @endif
                            <span class="text-[11px] text-slate-400 font-medium">
                                {{ $lead->created_at->diffForHumans() }}
                            </span>
                        </div>

                        <!-- Status Tags -->
                        <div class="flex flex-wrap items-center gap-2">
                            <!-- Channel/Source -->
                            <span class="text-[11px] font-medium px-2 py-0.5 rounded bg-slate-100 text-slate-600">
                                المصدر: {{ $lead->source }}
                            </span>

                            <!-- Automation Status -->
                            @if($lead->automation_status === 'processed')
                                <span class="text-[11px] font-semibold px-2 py-0.5 rounded bg-emerald-50 text-emerald-800 border border-emerald-200 inline-flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                    تم الفرز عبر n8n
                                </span>
                            @elseif($lead->automation_status === 'processing')
                                <span class="text-[11px] font-semibold px-2 py-0.5 rounded bg-sky-50 text-sky-800 border border-sky-200 inline-flex items-center gap-1 animate-pulse">
                                    <span class="w-1.5 h-1.5 rounded-full bg-sky-600"></span>
                                    قيد المعالجة بالـ AI
                                </span>
                            @elseif($lead->automation_status === 'failed')
                                <span class="text-[11px] font-semibold px-2 py-0.5 rounded bg-rose-50 text-rose-800 border border-rose-200">
                                    فشل الإرسال
                                </span>
                            @else
                                <span class="text-[11px] font-semibold px-2 py-0.5 rounded bg-slate-100 text-slate-600 border border-slate-200">
                                    بانتظار الطابور
                                </span>
                            @endif

                            <!-- Business Lead Status -->
                            <span class="text-[11px] font-bold px-2 py-0.5 rounded 
                                {{ $lead->status === 'qualified' ? 'bg-slate-900 text-white' : '' }}
                                {{ $lead->status === 'contacted' ? 'bg-sky-100 text-sky-900' : '' }}
                                {{ $lead->status === 'converted' ? 'bg-emerald-100 text-emerald-900' : '' }}
                                {{ $lead->status === 'new' ? 'bg-slate-100 text-slate-800' : '' }}
                                {{ $lead->status === 'rejected' ? 'bg-rose-100 text-rose-900' : '' }}">
                                {{ ucfirst($lead->status) }}
                            </span>
                        </div>
                    </div>

                    <!-- Split View: Original Inquiry vs Structured Triage Result -->
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 pt-4">
                        <!-- Right: Original Customer Message (5 cols) -->
                        <div class="lg:col-span-5 space-y-3">
                            <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                نص الطلب الوارد من العميل
                            </span>

                            <div class="p-4 rounded-lg bg-slate-50 border border-slate-200 text-xs sm:text-sm text-slate-800 leading-relaxed">
                                {{ $lead->message }}
                            </div>

                            <!-- Contact Links -->
                            <div class="flex flex-wrap gap-2 text-xs pt-1">
                                @if($lead->email)
                                    <a href="mailto:{{ $lead->email }}" class="px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 inline-flex items-center gap-1.5 font-mono text-[11px] transition-colors" dir="ltr">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        <span>{{ $lead->email }}</span>
                                    </a>
                                @endif
                                @if($lead->phone)
                                    <a href="tel:{{ $lead->phone }}" class="px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 inline-flex items-center gap-1.5 font-mono text-[11px] transition-colors" dir="ltr">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                        </svg>
                                        <span>{{ $lead->phone }}</span>
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- Left: AI Triage Decision & Assessment (7 cols) -->
                        <div class="lg:col-span-7">
                            @if($analysis)
                                <div class="rounded-lg border border-slate-200 bg-white p-4 sm:p-5 shadow-[0_1px_2px_0_rgba(0,0,0,0.02)]">
                                    <!-- Analysis Header with Score & Intent -->
                                    <div class="flex flex-wrap justify-between items-center gap-2 mb-4 pb-3 border-b border-slate-100">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <!-- Classification Badge -->
                                            @if($classification === 'HOT')
                                                <span class="px-2.5 py-1 rounded text-xs font-bold bg-rose-50 text-rose-800 border border-rose-200">
                                                    أولوية قصوى (HOT)
                                                </span>
                                            @elseif($classification === 'WARM')
                                                <span class="px-2.5 py-1 rounded text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                                    أولوية متوسطة (WARM)
                                                </span>
                                            @else
                                                <span class="px-2.5 py-1 rounded text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                                    أولوية عادية (COLD)
                                                </span>
                                            @endif

                                            <!-- Intent -->
                                            <span class="text-xs font-semibold text-slate-700 bg-slate-50 px-2 py-0.5 rounded border border-slate-200">
                                                التصنيف: <strong class="text-slate-900 font-mono">{{ $analysis->intent }}</strong>
                                            </span>
                                        </div>

                                        <!-- Score & Urgency -->
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-bold px-2 py-0.5 rounded
                                                {{ $analysis->urgency === 'high' ? 'bg-rose-100 text-rose-900' : '' }}
                                                {{ $analysis->urgency === 'medium' ? 'bg-amber-100 text-amber-900' : '' }}
                                                {{ $analysis->urgency === 'low' ? 'bg-slate-100 text-slate-800' : '' }}">
                                                الاستعجال: {{ strtoupper($analysis->urgency) }}
                                            </span>
                                            <span class="text-xs font-extrabold px-2.5 py-1 rounded {{ $scoreColor }}">
                                                {{ $analysis->lead_score }}/100
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Score Progress Bar -->
                                    <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden mb-4">
                                        <div class="h-full {{ $scoreBarColor }}" style="width: {{ $analysis->lead_score }}%"></div>
                                    </div>

                                    <!-- AI Operational Summary -->
                                    <div class="mb-3">
                                        <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">
                                            الملخص التنفيذي للطلب
                                        </span>
                                        <p class="text-xs sm:text-sm text-slate-900 font-medium bg-slate-50/80 p-3 rounded border border-slate-200/80 leading-relaxed">
                                            {{ $analysis->summary }}
                                        </p>
                                    </div>

                                    <!-- Recommended Action -->
                                    <div class="mb-3">
                                        <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">
                                            الإجراء المقترح لفريق المبيعات
                                        </span>
                                        <div class="p-3 rounded bg-sky-50/60 border border-sky-100 text-xs sm:text-sm font-semibold text-sky-950 flex items-start gap-2">
                                            <span class="text-sky-600 font-bold">&larr;</span>
                                            <span>{{ $analysis->recommended_action }}</span>
                                        </div>
                                    </div>

                                    <!-- Confidence & Timestamp -->
                                    <div class="flex justify-between items-center text-[11px] text-slate-400 pt-2 border-t border-slate-100">
                                        <span>نسبة دقة التقييم: {{ $analysis->confidence ? number_format($analysis->confidence * 100, 0) . '%' : 'N/A' }}</span>
                                        <span>تاريخ الفرز: {{ $analysis->created_at->format('Y-m-d H:i') }}</span>
                                    </div>
                                </div>
                            @else
                                <!-- Analysis In-Progress State -->
                                <div class="rounded-lg border border-dashed border-slate-300 bg-slate-50/50 p-6 text-center">
                                    <div class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-slate-100 text-slate-600 mb-2.5">
                                        <svg class="animate-spin w-4 h-4 text-slate-600" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                    <h4 class="text-xs sm:text-sm font-bold text-slate-800">الطلب قيد المعالجة في مسار الفرز الآلي...</h4>
                                    <p class="text-[11px] text-slate-500 mt-1 max-w-sm mx-auto">
                                        سيقوم نموذج الذكاء الاصطناعي (Gemini) بتحديد الأولوية والملخص وتحديث هذه الخانة فور اكتمال دورة n8n.
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Bottom Action Controls Bar -->
                    <div class="mt-4 pt-3 border-t border-slate-100 flex flex-wrap justify-between items-center gap-3">
                        <!-- Manual Re-dispatch to n8n Button -->
                        <div>
                            <form action="{{ route('leads.retry', $lead) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" 
                                        class="px-3 py-1.5 rounded text-xs font-semibold bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 hover:border-slate-300 shadow-sm transition-all inline-flex items-center gap-1.5"
                                        title="إعادة إرسال هذا الطلب إلى webhook مسار n8n">
                                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    <span>إعادة إرسال للفرز (n8n)</span>
                                </button>
                            </form>
                        </div>

                        <!-- Update Business Status Dropdown -->
                        <div class="flex items-center gap-2">
                            <span class="text-[11px] text-slate-500 font-medium">حالة الطلب:</span>
                            <form action="{{ route('leads.updateStatus', $lead) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <select name="status" onchange="this.form.submit()" 
                                        class="text-xs px-2.5 py-1 rounded border border-slate-200 bg-white font-semibold text-slate-700 outline-none focus:ring-1 focus:ring-slate-800">
                                    <option value="new" {{ $lead->status === 'new' ? 'selected' : '' }}>جديد (New)</option>
                                    <option value="qualified" {{ $lead->status === 'qualified' ? 'selected' : '' }}>مؤهل آلياً (Qualified)</option>
                                    <option value="contacted" {{ $lead->status === 'contacted' ? 'selected' : '' }}>تم التواصل (Contacted)</option>
                                    <option value="converted" {{ $lead->status === 'converted' ? 'selected' : '' }}>صفقة مكتملة (Converted)</option>
                                    <option value="rejected" {{ $lead->status === 'rejected' ? 'selected' : '' }}>مرفوض / غير مهتم (Rejected)</option>
                                </select>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $leads->links() }}
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    let refreshInterval = null;

    function toggleAutoRefresh(checkbox) {
        const label = document.getElementById('refreshLabel');
        if (checkbox.checked) {
            label.textContent = "تحديث تلقائي (يعمل كل 5 ثوانٍ)";
            label.classList.add('text-slate-900', 'font-bold');
            refreshInterval = setInterval(() => {
                window.location.reload();
            }, 5000);
            localStorage.setItem('leadTriageAutoRefresh', 'true');
        } else {
            label.textContent = "تحديث تلقائي (موقوف)";
            label.classList.remove('text-slate-900', 'font-bold');
            clearInterval(refreshInterval);
            localStorage.removeItem('leadTriageAutoRefresh');
        }
    }

    // Preserve auto refresh state across reloads
    document.addEventListener('DOMContentLoaded', () => {
        const saved = localStorage.getItem('leadTriageAutoRefresh');
        if (saved === 'true') {
            const toggle = document.getElementById('autoRefreshToggle');
            if (toggle) {
                toggle.checked = true;
                toggleAutoRefresh(toggle);
            }
        }
    });
</script>
@endsection
