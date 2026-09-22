@extends('layouts.app')

@section('title', 'إرسال طلب استفسار جديد — AI Lead Triage')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Header Hero -->
    <div class="text-center mb-8">
        <h1 class="text-3xl font-extrabold text-slate-900 sm:text-4xl">
            تواصل معنا لتطوير أعمالك
        </h1>
        <p class="mt-2 text-base text-slate-600">
            أرسل طلبك أو استفسارك هنا، وسيتولى نظام التوجيه الذكي (AI Triage) تصنيفه وتوجيهه فوراً للفريق المختص.
        </p>
    </div>

    <!-- Quick Presets for Instant Testing -->
    <div class="mb-6 bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex items-center gap-2 mb-3 text-xs font-bold text-slate-500 uppercase tracking-wider">
            <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            نماذج تجريبية سريعة (اضغط للتعبئة التلقائية):
        </div>
        <div class="flex flex-wrap gap-2">
            <button type="button" onclick="fillPreset(1)" 
                    class="text-xs px-3 py-1.5 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100 font-medium transition-colors border border-rose-200">
                🔥 طلب متجر عاجل (High Urgency)
            </button>
            <button type="button" onclick="fillPreset(2)" 
                    class="text-xs px-3 py-1.5 rounded-lg bg-amber-50 text-amber-700 hover:bg-amber-100 font-medium transition-colors border border-amber-200">
                ⚡ استشارة أتمتة AI (Medium Urgency)
            </button>
            <button type="button" onclick="fillPreset(3)" 
                    class="text-xs px-3 py-1.5 rounded-lg bg-sky-50 text-sky-700 hover:bg-sky-100 font-medium transition-colors border border-sky-200">
                ❄️ استفسار عام وبسيط (Low Urgency)
            </button>
        </div>
    </div>

    <!-- Main Lead Submission Form -->
    <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-xl shadow-slate-200/50 border border-slate-200">
        <form id="leadForm" onsubmit="submitLead(event)" class="space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-semibold text-slate-700 mb-1">اسم العميل / جهة الاتصال</label>
                    <input type="text" id="name" name="name" placeholder="مثال: م. أحمد الخالد"
                           class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 outline-none transition-all text-sm">
                </div>

                <!-- Company -->
                <div>
                    <label for="company" class="block text-sm font-semibold text-slate-700 mb-1">اسم الشركة أو النشاط</label>
                    <input type="text" id="company" name="company" placeholder="مثال: متجر الأفق للمنتجات الرياضية"
                           class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 outline-none transition-all text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-1">البريد الإلكتروني</label>
                    <input type="email" id="email" name="email" placeholder="client@example.com"
                           class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 outline-none transition-all text-sm" dir="ltr">
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-semibold text-slate-700 mb-1">رقم الهاتف / الواتساب</label>
                    <input type="text" id="phone" name="phone" placeholder="+962 79 123 4567"
                           class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 outline-none transition-all text-sm" dir="ltr">
                </div>
            </div>

            <!-- Source -->
            <div>
                <label for="source" class="block text-sm font-semibold text-slate-700 mb-1">مصدر الطلب</label>
                <select id="source" name="source" 
                        class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 outline-none transition-all text-sm bg-white">
                    <option value="website" selected>🌐 الموقع الإلكتروني (Website Form)</option>
                    <option value="whatsapp">💬 واتساب (WhatsApp)</option>
                    <option value="instagram">📸 انستغرام (Instagram)</option>
                    <option value="facebook">📘 فيسبوك (Facebook)</option>
                    <option value="api">🔌 واجهة برمجية (API)</option>
                    <option value="manual">👤 إدخال يدوي (Manual)</option>
                </select>
            </div>

            <!-- Unstructured Message (Core Text) -->
            <div>
                <div class="flex justify-between items-center mb-1">
                    <label for="message" class="block text-sm font-semibold text-slate-700">
                        نص الطلب أو الاستفسار غير المنظم <span class="text-rose-500">*</span>
                    </label>
                    <span class="text-xs text-slate-400">النص الذي سيقوم Gemini بتحليله</span>
                </div>
                <textarea id="message" name="message" rows="4" required
                          placeholder="اكتب هنا طلبك بحرية... مثال: مرحبا، عندي متجر وأريد موقع إلكتروني جديد ولدينا إطلاق بعد 10 أيام. كم السعر؟"
                          class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 outline-none transition-all text-sm leading-relaxed"></textarea>
                <p id="messageError" class="mt-1 text-xs text-rose-600 hidden"></p>
            </div>

            <!-- Submit Button -->
            <div>
                <button type="submit" id="submitBtn" 
                        class="w-full py-3.5 px-6 rounded-xl bg-gradient-to-r from-brand-600 to-indigo-600 hover:from-brand-700 hover:to-indigo-700 text-white font-bold text-base shadow-lg shadow-brand-500/25 transition-all flex items-center justify-center gap-2">
                    <span id="btnText">🚀 إرسال الطلب وتشغيل الفرز الآلي</span>
                    <svg id="btnSpinner" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white hidden" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </div>
        </form>

        <!-- Dynamic Success / Triage In-Progress State -->
        <div id="successBox" class="mt-6 p-6 rounded-2xl bg-emerald-50 border border-emerald-200 hidden transition-all">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-full bg-emerald-500 text-white flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div class="flex-grow">
                    <h3 class="text-base font-bold text-emerald-900">تم استلام طلبك بنجاح!</h3>
                    <p class="text-sm text-emerald-700 mt-1">
                        تم تسجيل الطلب برقم تعريفي <strong id="leadIdBadge" class="bg-emerald-200 text-emerald-900 px-2 py-0.5 rounded">#--</strong>، وبدأت الآن عملية الفرز الآلي عبر الذكاء الاصطناعي (Gemini) وإرسال الإشعارات.
                    </p>
                    <div class="mt-4 flex flex-wrap items-center gap-3">
                        <a href="{{ route('leads.dashboard') }}" 
                           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-all shadow-sm">
                            📊 الذهاب إلى لوحة المشرف لمشاهدة التحليل المباشر &larr;
                        </a>
                        <button type="button" onclick="resetForm()" class="text-xs text-emerald-800 underline font-semibold hover:text-emerald-950">
                            + إرسال طلب تجريبي آخر
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const presets = {
        1: {
            name: "سعيد مروان",
            company: "متجر المستلزمات العصرية",
            email: "saeed@example.com",
            phone: "+962791112233",
            source: "website",
            message: "السلام عليكم، نحن متجر أدوات منزلية بحاجة لبناء متجر إلكتروني متكامل مع بوابات دفع، ولدينا موعد إطلاق بعد 10 أيام. كم التكلفة والمدة التقريبية للتنفيذ؟"
        },
        2: {
            name: "م. رامي حداد",
            company: "أفق للتسويق والبرمجيات",
            email: "rami.ai@example.com",
            phone: "+962788889900",
            source: "whatsapp",
            message: "مرحباً، نريد استشارة تقنية حول أتمتة تصنيف العملاء وتكامل نماذج الذكاء الاصطناعي مع نظام CRM الداخلي خلال الأسبوعين القادمين. هل يمكن ترتيب جلسة مبيعات؟"
        },
        3: {
            name: "هدى العلي",
            company: "صالون لمسات",
            email: "huda@example.com",
            phone: "+962790001122",
            source: "instagram",
            message: "مرحبا، حبيت استفسر بشكل عام شو الخدمات اللي بتقدموها لمواقع الإنترنت؟ وبكم الأسعار التقريبية بدون استعجال؟"
        }
    };

    function fillPreset(id) {
        const data = presets[id];
        if (!data) return;
        document.getElementById('name').value = data.name;
        document.getElementById('company').value = data.company;
        document.getElementById('email').value = data.email;
        document.getElementById('phone').value = data.phone;
        document.getElementById('source').value = data.source;
        document.getElementById('message').value = data.message;
        document.getElementById('messageError').classList.add('hidden');
    }

    async function submitLead(event) {
        event.preventDefault();
        const form = document.getElementById('leadForm');
        const submitBtn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        const btnSpinner = document.getElementById('btnSpinner');
        const successBox = document.getElementById('successBox');
        const leadIdBadge = document.getElementById('leadIdBadge');
        const messageError = document.getElementById('messageError');

        messageError.classList.add('hidden');
        submitBtn.disabled = true;
        btnText.textContent = "جارٍ الحفظ وبدء الفرز الآلي...";
        btnSpinner.classList.remove('hidden');

        const payload = {
            name: document.getElementById('name').value || null,
            company: document.getElementById('company').value || null,
            email: document.getElementById('email').value || null,
            phone: document.getElementById('phone').value || null,
            source: document.getElementById('source').value || 'website',
            message: document.getElementById('message').value,
        };

        try {
            const response = await fetch('/api/leads', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify(payload),
            });

            const data = await response.json();

            if (!response.ok) {
                if (data.errors && data.errors.message) {
                    messageError.textContent = data.errors.message[0];
                    messageError.classList.remove('hidden');
                } else {
                    alert('حدث خطأ أثناء إرسال الطلب: ' + (data.message || 'يرجى مراجعة البيانات.'));
                }
                return;
            }

            // Success state
            form.classList.add('hidden');
            successBox.classList.remove('hidden');
            leadIdBadge.textContent = '#' + data.data.id;

        } catch (error) {
            alert('تعذر الاتصال بالخادم: ' + error.message);
        } finally {
            submitBtn.disabled = false;
            btnText.textContent = "🚀 إرسال الطلب وتشغيل الفرز الآلي";
            btnSpinner.classList.add('hidden');
        }
    }

    function resetForm() {
        document.getElementById('leadForm').reset();
        document.getElementById('leadForm').classList.remove('hidden');
        document.getElementById('successBox').classList.add('hidden');
    }
</script>
@endsection
