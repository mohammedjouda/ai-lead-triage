@extends('layouts.app')

@section('title', 'لوحة فرز ومتابعة العملاء بالذكاء الاصطناعي — AI Lead Triage Dashboard')

@section('content')
<div>
    <!-- Top Header & Controls -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 flex items-center gap-2.5">
                📊 لوحة فرز وتحليل العملاء
                <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200">
                    Supervisor View
                </span>
            </h1>
            <p class="text-sm text-slate-500 mt-1">
                استعراض طلبات العملاء غير المنظمة، والتحليل الآلي المنظم، والأولويات والإجراءات المقترحة.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <!-- Auto-refresh switch -->
            <label class="inline-flex items-center gap-2 cursor-pointer bg-white px-3 py-2 rounded-xl border border-slate-200 shadow-sm text-xs font-semibold text-slate-600">
                <input type="checkbox" id="autoRefreshToggle" onchange="toggleAutoRefresh(this)" class="sr-only peer">
                <div class="w-8 h-4 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:right-[4px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-3 after:w-3 after:transition-all peer-checked:bg-emerald-500 relative"></div>
                <span id="refreshLabel">تحديث تلقائي (موقوف)</span>
            </label>

            <!-- Manual Refresh -->
            <a href="{{ request()->fullUrl() }}" 
               class="px-4 py-2 rounded-xl bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 shadow-sm text-xs font-bold transition-all flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                تحديث
            </a>

            <!-- Submit New Lead Button -->
            <a href="{{ route('leads.create') }}" 
               class="px-4 py-2 rounded-xl bg-brand-600 hover:bg-brand-700 text-white shadow-md shadow-brand-500/20 text-xs font-bold transition-all flex items-center gap-1.5">
                + إرسال طلب جديد
            </a>
        </div>
    </div>

    <!-- Metric Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Total Leads -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-xl font-bold flex-shrink-0">
                📁
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400">إجمالي الطلبات المستلمة</p>
                <h3 class="text-2xl font-black text-slate-900 mt-0.5">{{ $metrics['total'] }}</h3>
            </div>
        </div>

        <!-- Qualified Leads -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold flex-shrink-0">
                🤖
            </div>
            <div>
                <p class="text-xs font-bold text-emerald-700">تم تأهيلها بالذكاء الاصطناعي</p>
                <h3 class="text-2xl font-black text-slate-900 mt-0.5">{{ $metrics['qualified'] }}</h3>
            </div>
        </div>

        <!-- HOT Priority Leads -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl font-bold flex-shrink-0">
                🔥
            </div>
            <div>
                <p class="text-xs font-bold text-rose-700">أولوية قصوى (HOT / High)</p>
                <h3 class="text-2xl font-black text-slate-900 mt-0.5">{{ $metrics['hot'] }}</h3>
            </div>
        </div>

        <!-- Pending Automation -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl font-bold flex-shrink-0">
                ⏳
            </div>
            <div>
                <p class="text-xs font-bold text-amber-700">قيد المعالجة في الطابور</p>
                <h3 class="text-2xl font-black text-slate-900 mt-0.5">{{ $metrics['pending'] }}</h3>
            </div>
        </div>
    </div>

    <!-- Filter Pills -->
    <div class="flex flex-wrap items-center gap-2 mb-6">
        <span class="text-xs font-bold text-slate-400 ml-2">فلترة الطلبات:</span>
        <a href="{{ route('leads.dashboard') }}" 
           class="text-xs px-3 py-1.5 rounded-lg font-bold transition-all {{ !request('filter') ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            جميع الطلبات ({{ $metrics['total'] }})
        </a>
        <a href="{{ route('leads.dashboard', ['filter' => 'hot']) }}" 
           class="text-xs px-3 py-1.5 rounded-lg font-bold transition-all {{ request('filter') === 'hot' ? 'bg-rose-600 text-white' : 'bg-white text-rose-700 hover:bg-rose-50 border border-rose-200' }}">
            🔥 العملاء الساخنون (HOT &ge; 80)
        </a>
        <a href="{{ route('leads.dashboard', ['filter' => 'warm']) }}" 
           class="text-xs px-3 py-1.5 rounded-lg font-bold transition-all {{ request('filter') === 'warm' ? 'bg-amber-600 text-white' : 'bg-white text-amber-700 hover:bg-amber-50 border border-amber-200' }}">
            ⚡ عملاء محتملون (WARM 50-79)
        </a>
        <a href="{{ route('leads.dashboard', ['filter' => 'cold']) }}" 
           class="text-xs px-3 py-1.5 rounded-lg font-bold transition-all {{ request('filter') === 'cold' ? 'bg-sky-600 text-white' : 'bg-white text-sky-700 hover:bg-sky-50 border border-sky-200' }}">
            ❄️ استفسارات عادية (COLD &lt; 50)
        </a>
        <a href="{{ route('leads.dashboard', ['filter' => 'pending']) }}" 
           class="text-xs px-3 py-1.5 rounded-lg font-bold transition-all {{ request('filter') === 'pending' ? 'bg-indigo-600 text-white' : 'bg-white text-indigo-700 hover:bg-indigo-50 border border-indigo-200' }}">
            ⏳ بانتظار اكتمال التحليل
        </a>
    </div>

    <!-- Leads List -->
    @if($leads->isEmpty())
        <div class="bg-white rounded-3xl p-12 text-center border border-slate-200 shadow-sm">
            <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-2xl mb-4">
                🔍
            </div>
            <h3 class="text-lg font-bold text-slate-800">لا توجد طلبات تطابق الفلتر المحدد</h3>
            <p class="text-sm text-slate-500 mt-1">يمكنك إرسال طلب جديد لتجربة دورة الفرز الآلي كاملة.</p>
            <a href="{{ route('leads.create') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-brand-600 text-white text-xs font-bold">
                + إرسال أول طلب عميل
            </a>
        </div>
    @else
        <div class="space-y-6">
            @foreach($leads as $lead)
                @php
                    $analysis = $lead->analysis;
                    $score = $analysis ? $analysis->lead_score : null;
                    $classification = 'COLD';
                    if ($score >= 80) $classification = 'HOT';
                    elseif ($score >= 50) $classification = 'WARM';

                    $cardBorderColor = 'border-slate-200';
                    if ($analysis) {
                        if ($classification === 'HOT') $cardBorderColor = 'border-rose-300 ring-1 ring-rose-300/50';
                        elseif ($classification === 'WARM') $cardBorderColor = 'border-amber-300';
                    }
                @endphp

                <div class="bg-white rounded-2xl border {{ $cardBorderColor }} p-5 sm:p-7 shadow-sm hover:shadow-md transition-all">
                    <!-- Top Bar: Meta Info & Status Badges -->
                    <div class="flex flex-wrap justify-between items-start gap-4 pb-4 border-b border-slate-100">
                        <div class="flex flex-wrap items-center gap-2.5">
                            <span class="text-sm font-black text-slate-900 bg-slate-100 px-2.5 py-1 rounded-lg">
                                #{{ $lead->id }}
                            </span>
                            <h2 class="text-base font-bold text-slate-900">
                                {{ $lead->name ?? 'عميل غير محدد' }}
                            </h2>
                            @if($lead->company)
                                <span class="text-xs text-slate-500 bg-slate-50 px-2 py-0.5 rounded border border-slate-200">
                                    🏢 {{ $lead->company }}
                                </span>
                            @endif
                            <span class="text-xs text-slate-400">
                                🕒 {{ $lead->created_at->diffForHumans() }}
                            </span>
                        </div>

                        <!-- Statuses -->
                        <div class="flex flex-wrap items-center gap-2">
                            <!-- Source Badge -->
                            <span class="text-xs font-medium px-2.5 py-1 rounded-md bg-slate-100 text-slate-600">
                                المصدر: {{ $lead->source }}
                            </span>

                            <!-- Automation Status -->
                            @if($lead->automation_status === 'processed')
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    تم الفرز عبر n8n
                                </span>
                            @elseif($lead->automation_status === 'processing')
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 border border-blue-200 flex items-center gap-1 animate-pulse">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                    قيد الفرز بالـ AI
                                </span>
                            @elseif($lead->automation_status === 'failed')
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-rose-50 text-rose-700 border border-rose-200">
                                    فشلت الأتمتة
                                </span>
                            @else
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-slate-50 text-slate-600 border border-slate-200">
                                    بانتظار الطابور
                                </span>
                            @endif

                            <!-- Business Lead Status -->
                            <span class="text-xs font-bold px-2.5 py-1 rounded-md 
                                {{ $lead->status === 'qualified' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : '' }}
                                {{ $lead->status === 'contacted' ? 'bg-sky-50 text-sky-700 border border-sky-200' : '' }}
                                {{ $lead->status === 'converted' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : '' }}
                                {{ $lead->status === 'new' ? 'bg-slate-100 text-slate-700' : '' }}">
                                الحالة: {{ ucfirst($lead->status) }}
                            </span>
                        </div>
                    </div>

                    <!-- Main Grid: Original Lead vs AI Triage Result -->
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 pt-5">
                        <!-- Right Column: Original Customer Data (5 cols) -->
                        <div class="lg:col-span-5 space-y-3">
                            <div class="flex items-center justify-between">
                                <h4 class="text-xs font-extrabold text-slate-400 uppercase tracking-wider">
                                    📄 رسالة العميل الأصلية (غير المنظمة)
                                </h4>
                            </div>

                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-sm text-slate-800 leading-relaxed italic relative">
                                &ldquo;{{ $lead->message }}&rdquo;
                            </div>

                            <!-- Contact info pills -->
                            <div class="flex flex-wrap gap-2 text-xs pt-1">
                                @if($lead->email)
                                    <a href="mailto:{{ $lead->email }}" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center gap-1 font-mono transition-colors" dir="ltr">
                                        ✉️ {{ $lead->email }}
                                    </a>
                                @endif
                                @if($lead->phone)
                                    <a href="tel:{{ $lead->phone }}" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center gap-1 font-mono transition-colors" dir="ltr">
                                        📞 {{ $lead->phone }}
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- Left Column: AI Triage Decision & Analysis (7 cols) -->
                        <div class="lg:col-span-7">
                            @if($analysis)
                                <div class="rounded-xl border p-5 
                                     {{ $classification === 'HOT' ? 'bg-rose-50/50 border-rose-200' : '' }}
                                     {{ $classification === 'WARM' ? 'bg-amber-50/40 border-amber-200' : '' }}
                                     {{ $classification === 'COLD' ? 'bg-slate-50 border-slate-200' : '' }}">
                                    
                                    <!-- AI Classification Header -->
                                    <div class="flex flex-wrap justify-between items-center gap-2 mb-4 pb-3 border-b border-slate-200/60">
                                        <div class="flex items-center gap-2">
                                            @if($classification === 'HOT')
                                                <span class="px-3 py-1 rounded-lg bg-rose-600 text-white text-xs font-black flex items-center gap-1 shadow-sm">
                                                    🔥 تصنيف عالي الأولوية (HOT)
                                                </span>
                                            @elseif($classification === 'WARM')
                                                <span class="px-3 py-1 rounded-lg bg-amber-500 text-white text-xs font-black flex items-center gap-1 shadow-sm">
                                                    ⚡ تصنيف متوسط (WARM)
                                                </span>
                                            @else
                                                <span class="px-3 py-1 rounded-lg bg-slate-600 text-white text-xs font-black flex items-center gap-1 shadow-sm">
                                                    ❄️ تصنيف عادي (COLD)
                                                </span>
                                            @endif

                                            <span class="text-xs font-bold text-slate-700 bg-white px-2.5 py-1 rounded-md border border-slate-200">
                                                الغرض: <span class="text-brand-600">{{ $analysis->intent }}</span>
                                            </span>
                                        </div>

                                        <!-- Score & Urgency -->
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-bold px-2 py-0.5 rounded
                                                {{ $analysis->urgency === 'high' ? 'bg-rose-100 text-rose-800' : '' }}
                                                {{ $analysis->urgency === 'medium' ? 'bg-amber-100 text-amber-800' : '' }}
                                                {{ $analysis->urgency === 'low' ? 'bg-slate-100 text-slate-700' : '' }}">
                                                الاستعجال: {{ strtoupper($analysis->urgency) }}
                                            </span>
                                            <span class="text-sm font-black text-slate-900 bg-white px-2.5 py-1 rounded-md border border-slate-200">
                                                ⭐ {{ $analysis->lead_score }}/100
                                            </span>
                                        </div>
                                    </div>

                                    <!-- AI Summary -->
                                    <div class="mb-3">
                                        <h5 class="text-xs font-extrabold text-slate-500 mb-1 flex items-center gap-1">
                                            💡 الملخص التشغيلي للطلب (AI Summary):
                                        </h5>
                                        <p class="text-sm text-slate-900 font-semibold bg-white p-3 rounded-lg border border-slate-200/80 leading-relaxed">
                                            {{ $analysis->summary }}
                                        </p>
                                    </div>

                                    <!-- Recommended Action -->
                                    <div class="mb-3">
                                        <h5 class="text-xs font-extrabold text-indigo-900 mb-1 flex items-center gap-1">
                                            🎯 الإجراء المقترح للمبيعات (Recommended Next Action):
                                        </h5>
                                        <div class="p-3 rounded-lg bg-indigo-50/80 border border-indigo-200 text-sm font-bold text-indigo-950 flex items-center gap-2">
                                            <span class="text-base">&rarr;</span>
                                            <span>{{ $analysis->recommended_action }}</span>
                                        </div>
                                    </div>

                                    <!-- Confidence & Timestamp footer -->
                                    <div class="flex justify-between items-center text-xs text-slate-400 pt-2 border-t border-slate-200/40">
                                        <span>نسبة ثقة التحليل: {{ $analysis->confidence ? number_format($analysis->confidence * 100, 0) . '%' : 'N/A' }}</span>
                                        <span>وقت المعالجة: {{ $analysis->created_at->format('Y-m-d H:i') }}</span>
                                    </div>
                                </div>
                            @else
                                <!-- Analysis In-Progress placeholder -->
                                <div class="rounded-xl border border-dashed border-amber-300 bg-amber-50/40 p-6 text-center">
                                    <div class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-amber-100 text-amber-600 mb-3 animate-pulse">
                                        ⏳
                                    </div>
                                    <h4 class="text-sm font-bold text-amber-900">جارٍ تحليل هذا الطلب عبر الذكاء الاصطناعي (Gemini)...</h4>
                                    <p class="text-xs text-amber-700 mt-1 max-w-sm mx-auto">
                                        الطلب في طابور الأتمتة وسيتم تحديث هذه الخانة بالملخص والتقييم والتصنيف فور انتهاء مسار n8n.
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Footer Action Bar: Supervisor controls -->
                    <div class="mt-5 pt-4 border-t border-slate-100 flex flex-wrap justify-between items-center gap-3">
                        <span class="text-xs text-slate-400">تحديث الحالة يدويًا:</span>
                        <form action="{{ route('leads.updateStatus', $lead) }}" method="POST" class="flex items-center gap-2">
                            @csrf
                            @method('PATCH')
                            <select name="status" onchange="this.form.submit()" 
                                    class="text-xs px-3 py-1.5 rounded-lg border border-slate-200 bg-white font-semibold text-slate-700 outline-none focus:ring-2 focus:ring-brand-500/20">
                                <option value="new" {{ $lead->status === 'new' ? 'selected' : '' }}>🔵 جديد (New)</option>
                                <option value="qualified" {{ $lead->status === 'qualified' ? 'selected' : '' }}>🤖 مؤهل آلياً (Qualified)</option>
                                <option value="contacted" {{ $lead->status === 'contacted' ? 'selected' : '' }}>📞 تم التواصل (Contacted)</option>
                                <option value="converted" {{ $lead->status === 'converted' ? 'selected' : '' }}>🏆 تم التحويل لصفقة (Converted)</option>
                                <option value="rejected" {{ $lead->status === 'rejected' ? 'selected' : '' }}>❌ غير مهتم / مرفوض (Rejected)</option>
                            </select>
                        </form>
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
            label.textContent = "تحديث تلقائي (شغال كل 6 ثوانٍ)";
            label.classList.add('text-emerald-600');
            refreshInterval = setInterval(() => {
                window.location.reload();
            }, 6000);
            localStorage.setItem('leadTriageAutoRefresh', 'true');
        } else {
            label.textContent = "تحديث تلقائي (موقوف)";
            label.classList.remove('text-emerald-600');
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
