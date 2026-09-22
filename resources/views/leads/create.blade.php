@extends('layouts.app')

@section('title', 'تقديم طلب استفسار جديد — AI Lead Triage')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Header -->
    <div class="text-center mb-8">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
            تواصل معنا لتطوير أعمالك
        </h1>
        <p class="mt-2 text-sm text-slate-600 max-w-xl mx-auto leading-relaxed">
            يرجى تزويدنا بتفاصيل مشروعك أو استفسارك التجاري. يتولى نظام الفرز الذكي تحليل المتطلبات وتحديد أولوياتها لضمان توجيهها للفريق المتخصص بالسرعة المطلوبة.
        </p>
    </div>

    <!-- Main Lead Submission Card -->
    <div class="bg-white rounded-2xl p-6 sm:p-10 border border-slate-200 shadow-sm">
        <form id="leadForm" onsubmit="submitLead(event)" class="space-y-6">
            @csrf

            <!-- Contact & Company Row -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        اسم جهة الاتصال / العميل
                    </label>
                    <input type="text" id="name" name="name" placeholder="مثال: أحمد عبد الله"
                           class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-slate-900 text-sm focus:border-slate-800 focus:ring-1 focus:ring-slate-800 outline-none transition-all placeholder:text-slate-400">
                </div>

                <div>
                    <label for="company" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        اسم المنشأة أو الشركة
                    </label>
                    <input type="text" id="company" name="company" placeholder="مثال: شركة التطوير الحديث"
                           class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-slate-900 text-sm focus:border-slate-800 focus:ring-1 focus:ring-slate-800 outline-none transition-all placeholder:text-slate-400">
                </div>
            </div>

            <!-- Email & Phone Row -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        البريد الإلكتروني للعمل
                    </label>
                    <input type="email" id="email" name="email" placeholder="name@company.com" dir="ltr"
                           class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-slate-900 text-sm focus:border-slate-800 focus:ring-1 focus:ring-slate-800 outline-none transition-all placeholder:text-slate-400">
                </div>

                <div>
                    <label for="phone" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        رقم الهاتف / الواتساب
                    </label>
                    <input type="text" id="phone" name="phone" placeholder="+966 50 123 4567" dir="ltr"
                           class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-slate-900 text-sm focus:border-slate-800 focus:ring-1 focus:ring-slate-800 outline-none transition-all placeholder:text-slate-400">
                </div>
            </div>

            <!-- Source Channel -->
            <div>
                <label for="source" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    قناة الاستفسار أو المصدر
                </label>
                <select id="source" name="source" 
                        class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-slate-800 text-sm focus:border-slate-800 focus:ring-1 focus:ring-slate-800 outline-none transition-all bg-white">
                    <option value="website" selected>الموقع الإلكتروني (Website Form)</option>
                    <option value="whatsapp">محادثة واتساب (WhatsApp)</option>
                    <option value="linkedin">لينكد إن (LinkedIn)</option>
                    <option value="email">البريد الإلكتروني المباشر (Direct Email)</option>
                    <option value="manual">إدخال مباشر عبر المبيعات (Manual Entry)</option>
                </select>
            </div>

            <!-- Unstructured Message Content -->
            <div>
                <div class="flex justify-between items-center mb-1.5">
                    <label for="message" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        نص الطلب أو الاستفسار غير المنظم <span class="text-rose-600">*</span>
                    </label>
                    <span class="text-[11px] text-slate-400 font-medium">يقوم نموذج الذكاء الاصطناعي بتحليله وتحديد أولويته</span>
                </div>
                <textarea id="message" name="message" rows="5" required
                          placeholder="يرجى كتابة تفاصيل احتياجك أو متطلباتك هنا بحرية... على سبيل المثال: نحتاج إلى تطوير منصة سحابية لإدارة الطلبات مع ربط بوابات دفع، وموعد إطلاقنا المستهدف خلال 3 أسابيع. نود معرفة التكلفة والمدة التقديرية للتنفيذ."
                          class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-slate-900 text-sm focus:border-slate-800 focus:ring-1 focus:ring-slate-800 outline-none transition-all leading-relaxed placeholder:text-slate-400"></textarea>
                <p id="messageError" class="mt-1.5 text-xs text-rose-600 hidden font-medium"></p>
            </div>

            <!-- Submit Action -->
            <div class="pt-2">
                <button type="submit" id="submitBtn" 
                        class="w-full py-3 px-5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm shadow-sm transition-all flex items-center justify-center gap-2">
                    <svg id="btnIcon" class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    <span id="btnText">إرسال طلب الاستفسار وبدء الفرز</span>
                    <svg id="btnSpinner" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white hidden" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </div>
        </form>

        <!-- Professional Submission Confirmation Card -->
        <div id="successBox" class="mt-4 p-6 sm:p-8 rounded-xl bg-slate-50 border border-slate-200 hidden">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div class="flex-grow">
                    <h3 class="text-base font-bold text-slate-900">تم استلام طلبكم بنجاح</h3>
                    <p class="text-xs sm:text-sm text-slate-600 mt-1 leading-relaxed">
                        تم تسجيل الطلب في السجل برقم مرجعي <strong id="leadIdBadge" class="font-mono bg-white border border-slate-200 text-slate-900 px-2 py-0.5 rounded text-xs font-bold">#--</strong>، وبدأت منظومة الفرز الآلي تحليله واستخراج معايير الأولوية والإجراء المناسب.
                    </p>
                    <div class="mt-5 flex flex-wrap items-center gap-3">
                        <a href="{{ route('leads.dashboard') }}" 
                           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition-all shadow-sm">
                            <span>عرض الطلب في لوحة المشرف</span>
                            <span>&larr;</span>
                        </a>
                        <button type="button" onclick="resetForm()" class="text-xs text-slate-600 hover:text-slate-900 font-semibold px-2 py-1">
                            تقديم استفسار آخر
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
    async function submitLead(event) {
        event.preventDefault();
        const form = document.getElementById('leadForm');
        const submitBtn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        const btnIcon = document.getElementById('btnIcon');
        const btnSpinner = document.getElementById('btnSpinner');
        const successBox = document.getElementById('successBox');
        const leadIdBadge = document.getElementById('leadIdBadge');
        const messageError = document.getElementById('messageError');

        messageError.classList.add('hidden');
        submitBtn.disabled = true;
        btnText.textContent = "جارٍ الحفظ والمعالجة الآلية...";
        btnIcon.classList.add('hidden');
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
                    alert('حدث خطأ أثناء إرسال الطلب: ' + (data.message || 'يرجى مراجعة البيانات والتحقق من الحقول المطلوبة.'));
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
            btnText.textContent = "إرسال طلب الاستفسار وبدء الفرز";
            btnIcon.classList.remove('hidden');
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
