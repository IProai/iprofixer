<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    @php
        $ar = app()->getLocale() === 'ar';
        $serviceDetails = [
            'cutlery-restoration' => [
                'Cutlery restoration','ترميم أدوات المائدة',
                'Structured surface and presentation care for frequently used cutlery.','عناية منظمة بالسطح والمظهر لأدوات المائدة المستخدمة بكثافة.',
                ['Condition review','Surface-care route','Quality release record'],
                ['مراجعة الحالة','مسار عناية بالسطح','سجل فحص قبل التسليم'],
            ],
            'hollowware-care' => [
                'Hollowware care','العناية بالقطع المجوفة',
                'Condition-led care for serving pieces, trays and presentation assets.','عناية مبنية على حالة قطع التقديم والصواني وأصول العرض.',
                ['Item grouping','Condition-led treatment','Return-ready inspection'],
                ['تجميع القطع','معالجة حسب الحالة','فحص الجاهزية للتسليم'],
            ],
            'asset-condition-review' => [
                'Asset condition review','تقييم حالة الأصول',
                'A documented review of condition, quantity and service priority before work begins.','مراجعة موثقة للحالة والكميات وأولوية الخدمة قبل بدء العمل.',
                ['Quantity capture','Condition categories','Restore-retain-replace guidance'],
                ['حصر الكميات','تصنيف الحالة','توجيه للترميم أو الاحتفاظ أو الاستبدال'],
            ],
            'recurring-care-plans' => [
                'Recurring care plans','خطط العناية الدورية',
                'Planned service cycles for operations with repeated asset use.','دورات خدمة مخططة للمنشآت ذات الاستخدام المتكرر للأصول.',
                ['Cycle planning','Priority batches','Service records'],
                ['تخطيط الدورات','دفعات حسب الأولوية','سجلات الخدمة'],
            ],
        ];
        $industryDetails = [
            'hotels-resorts' => ['Hotels & resorts','الفنادق والمنتجعات','Protect presentation standards across restaurants, banqueting and room-service operations.','حماية معايير المظهر في المطاعم والولائم وخدمات الغرف.',['F&B operations','Banqueting','Room service'],['عمليات الأغذية والمشروبات','الولائم','خدمة الغرف']],
            'restaurants-groups' => ['Restaurants & groups','المطاعم والمجموعات','Coordinate asset care across single venues or multi-location restaurant operations.','تنسيق العناية بالأصول للمطاعم المستقلة أو المجموعات متعددة الفروع.',['Single venues','Multi-site groups','Opening readiness'],['المطاعم المستقلة','المجموعات متعددة الفروع','الجاهزية للافتتاح']],
            'catering-events' => ['Catering & events','التموين والفعاليات','Prepare high-volume service assets around event calendars and turnaround windows.','تجهيز أصول الخدمة ذات الأحجام الكبيرة وفق جداول الفعاليات وفترات التسليم.',['Event windows','High-volume batches','Turnaround planning'],['فترات الفعاليات','دفعات كبيرة','تخطيط سرعة الإنجاز']],
            'procurement-operations' => ['Procurement & operations','المشتريات والتشغيل','Give decision-makers a clearer basis for restore, retain or replace decisions.','منح فرق القرار أساساً أوضح لقرارات الترميم أو الاحتفاظ أو الاستبدال.',['Budget context','Condition evidence','Decision records'],['سياق الميزانية','دليل الحالة','سجلات القرار']],
        ];
        $pages = [
            'services' => ['Services','الخدمات','Focused care routes built around asset condition and operating reality.','مسارات عناية متخصصة مبنية على حالة الأصل وواقع التشغيل.'],
            'industries' => ['Industries','القطاعات','Built around the operating realities of hospitality teams.','مصمم وفق واقع التشغيل لدى فرق الضيافة.'],
            'process' => ['Our process','آلية العمل','A controlled route from assessment and intake to quality release and return.','مسار منضبط يبدأ بالتقييم والاستلام وينتهي بالفحص والتسليم.'],
            'results' => ['Proof & results','الإثبات والنتائج','Evidence-led delivery without unsupported claims.','تنفيذ قائم على الدليل دون ادعاءات غير موثقة.'],
            'about' => ['About IProFixer','عن آي برو فيكسر','A specialist hospitality asset-care partner for operational teams.','شريك متخصص في العناية بأصول الضيافة لفرق التشغيل.'],
            'resources' => ['Resources','المعرفة والموارد','Practical guidance for extending asset life and planning care cycles.','إرشادات عملية لإطالة عمر الأصول وتخطيط دورات العناية.'],
            'contact' => ['Start a consultation','ابدأ الاستشارة','Share the asset type, quantity, location and urgency.','شارك نوع الأصول والكميات والموقع والأولوية.'],
            'portal' => ['Client portal','بوابة العملاء','Invitation-only access for approved clients.','دخول بالدعوة فقط للعملاء المعتمدين.'],
        ];
        $copy = match ($page) {
            'service-detail' => $serviceDetails[$slug],
            'industry-detail' => $industryDetails[$slug],
            default => $pages[$page] ?? $pages['about'],
        };
        $translation = isset($cmsPage) && $cmsPage ? $cmsPage->translation($ar ? 'ar' : 'en') : null;
        if ($translation) {
            $copy = $ar ? [$copy[0], $translation->title, $copy[2], $translation->summary ?: $copy[3]] : [$translation->title, $copy[1], $translation->summary ?: $copy[2], $copy[3]];
        }
        $seoTitle = $translation?->seo_title ?: ($ar ? $copy[1] : $copy[0]);
        $seoDescription = $translation?->seo_description ?: ($ar ? $copy[3] : $copy[2]);
    @endphp
    @if(!empty($isPreview))
        <meta name="robots" content="noindex, nofollow">
    @endif
    <meta name="description" content="{{ $seoDescription }}">
    <title>{{ $seoTitle }} | IProFixer</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
@include('partials.public-header')
<main id="main">
    <section class="page-hero">
        <div class="container page-hero-grid">
            <div>
                <a class="back-link" href="{{ route('home') }}">{{ $ar ? 'العودة للرئيسية' : 'Back to home' }}</a>
                <p class="eyebrow">IProFixer / {{ $ar ? $copy[1] : $copy[0] }}</p>
                <h1>{{ $ar ? $copy[1] : $copy[0] }}</h1>
                <p class="page-lead">{{ $ar ? $copy[3] : $copy[2] }}</p>
                @if(in_array($page, ['service-detail','industry-detail'], true))
                    <a class="button button-gold" href="{{ route('contact') }}">{{ $ar ? 'اطلب تقييماً لهذا النطاق' : 'Request an assessment for this scope' }}</a>
                @endif
            </div>
            <div class="page-index">{{ in_array($page, ['service-detail','industry-detail'], true) ? '↗' : '0'.(array_search($page, array_keys($pages), true)+1) }}</div>
        </div>
    </section>

    @if($translation?->body)
        <section class="section"><div class="container editorial-grid"><div><p class="eyebrow">{{ $ar ? 'محتوى معتمد' : 'Approved content' }}</p><h2>{{ $translation->title }}</h2></div><div class="editorial-copy">{!! nl2br(e($translation->body)) !!}</div></div></section>
    @elseif($page === 'services')
        <section class="section"><div class="container"><div class="section-topline"><div><p class="eyebrow">{{ $ar ? 'مسارات الخدمة' : 'Service routes' }}</p><h2>{{ $ar ? 'ابدأ بالحالة الفعلية، لا بافتراض مسبق.' : 'Start with actual condition—not a preset assumption.' }}</h2></div></div><div class="detail-card-grid">@foreach($serviceDetails as $key => $item)<a class="detail-card" href="{{ route('services.show',$key) }}"><span>0{{ $loop->iteration }}</span><h3>{{ $ar ? $item[1] : $item[0] }}</h3><p>{{ $ar ? $item[3] : $item[2] }}</p><b>{{ $ar ? 'استكشف المسار' : 'Explore route' }} ↗</b></a>@endforeach</div></div></section>
    @elseif($page === 'industries')
        <section class="section"><div class="container"><div class="section-topline"><div><p class="eyebrow">{{ $ar ? 'حسب واقع التشغيل' : 'By operating reality' }}</p><h2>{{ $ar ? 'كل قطاع يملك وتيرة استخدام ونافذة تسليم مختلفة.' : 'Each sector has a different usage rhythm and turnaround window.' }}</h2></div></div><div class="detail-card-grid">@foreach($industryDetails as $key => $item)<a class="detail-card" href="{{ route('industries.show',$key) }}"><span>0{{ $loop->iteration }}</span><h3>{{ $ar ? $item[1] : $item[0] }}</h3><p>{{ $ar ? $item[3] : $item[2] }}</p><b>{{ $ar ? 'استكشف القطاع' : 'Explore industry' }} ↗</b></a>@endforeach</div></div></section>
    @elseif($page === 'service-detail' || $page === 'industry-detail')
        @php($items = $ar ? $copy[5] : $copy[4])
        <section class="section scope-section"><div class="container scope-grid"><div><p class="eyebrow">{{ $page === 'service-detail' ? ($ar ? 'ما يشمله المسار' : 'What the route covers') : ($ar ? 'أولويات القطاع' : 'Industry priorities') }}</p><h2>{{ $ar ? 'نطاق واضح قبل بدء أي التزام.' : 'A clear scope before any commitment begins.' }}</h2><p>{{ $ar ? 'تبدأ كل حالة بمراجعة الكمية والحالة والأولوية ونافذة التشغيل. بعدها فقط يُحدد النطاق المناسب.' : 'Every case starts with quantity, condition, urgency and operating-window review. Only then is the appropriate scope defined.' }}</p></div><div class="scope-list">@foreach($items as $item)<article><span>0{{ $loop->iteration }}</span><strong>{{ $item }}</strong></article>@endforeach</div></div></section>
        <section class="section dark-section"><div class="container editorial-grid"><div><p class="eyebrow light-eyebrow">{{ $ar ? 'قبل التنفيذ' : 'Before work begins' }}</p><h2>{{ $ar ? 'لا سعر عام ولا وعد غير موثق.' : 'No generic price and no unsupported promise.' }}</h2></div><div class="editorial-copy"><p>{{ $ar ? 'تُراجع الصور أو العينة أو قائمة الجرد، ثم تُحدد الخطوة التالية وفق الحالة الفعلية وموقع العميل.' : 'Images, a sample or an inventory list are reviewed before the next step is defined around actual condition and client location.' }}</p><a class="button button-gold" href="{{ route('contact') }}">{{ $ar ? 'ابدأ الاستشارة' : 'Start consultation' }}</a></div></div></section>
    @elseif($page === 'process')
        <section class="section"><div class="container process-list">@foreach([['Request & scope','الطلب وتحديد النطاق'],['Documented intake','استلام موثق'],['Technical review','مراجعة فنية'],['Approved care route','مسار عناية معتمد'],['Quality release','فحص الجودة'],['Return & records','التسليم والسجلات']] as $step)<article><span>0{{ $loop->iteration }}</span><h2>{{ $ar ? $step[1] : $step[0] }}</h2><p>{{ $ar ? 'تُحدد المسؤولية والبيانات المطلوبة قبل الانتقال إلى المرحلة التالية.' : 'Responsibility and required information are made clear before the next stage.' }}</p></article>@endforeach</div></section>
    @elseif($page === 'results')
        <section class="section"><div class="container proof-grid"><div><p class="eyebrow">{{ $ar ? 'الدليل قبل الادعاء' : 'Evidence before claims' }}</p><h2>{{ $ar ? 'لن ننشر نتيجة قبل توثيقها واعتمادها.' : 'No result will be published before it is documented and approved.' }}</h2><p>{{ $ar ? 'سيعرض هذا القسم لاحقاً دراسات حالة معتمدة تتضمن الحالة الأولية والنطاق والصور المصرح بها والنتيجة الفعلية.' : 'This area will later publish approved case studies with starting condition, scope, authorised imagery and actual outcome.' }}</p></div><div class="proof-placeholder"><span>{{ $ar ? 'مساحة محفوظة لدراسة حالة موثقة' : 'Reserved for a verified case study' }}</span><small>{{ $ar ? 'لا توجد حالياً شعارات أو أرقام أو شهادات منشورة دون اعتماد.' : 'No logos, figures or testimonials are currently published without approval.' }}</small></div></div></section>
    @elseif($page === 'about')
        <section class="section"><div class="container editorial-grid"><div><p class="eyebrow">{{ $ar ? 'لماذا وُجدنا' : 'Why we exist' }}</p><h2>{{ $ar ? 'لنجعل قرار العناية بالأصول أوضح لفرق الضيافة.' : 'To make hospitality asset-care decisions clearer for operating teams.' }}</h2></div><div class="editorial-copy"><p>{{ $ar ? 'آي برو فيكسر يركز على أدوات المائدة والقطع المجوفة وأصول العرض المستخدمة في التشغيل اليومي. نبدأ بالتقييم والتوثيق، ولا نستخدم ادعاءات أو شهادات غير موثقة.' : 'IProFixer focuses on cutlery, hollowware and presentation assets used in daily hospitality operations. We begin with assessment and records, and we do not use unsupported claims or testimonials.' }}</p><ul class="feature-list"><li>{{ $ar ? 'نطاق واضح قبل التنفيذ' : 'Clear scope before work' }}</li><li>{{ $ar ? 'تواصل موجه لفرق التشغيل والمشتريات' : 'Communication for operations and procurement teams' }}</li><li>{{ $ar ? 'تسليم قائم على الفحص والسجلات' : 'Release based on inspection and records' }}</li></ul></div></div></section>
    @elseif($page === 'resources')
        <section class="section"><div class="container"><div class="detail-card-grid"><article class="detail-card"><span>01</span><h3>{{ $ar ? 'قائمة فحص حالة الأصول' : 'Asset condition checklist' }}</h3><p>{{ $ar ? 'ما الذي يجب تسجيله قبل طلب تقييم أو اتخاذ قرار استبدال.' : 'What to record before requesting an assessment or making a replacement decision.' }}</p><b>{{ $ar ? 'قريباً بعد اعتماد المحتوى' : 'Coming after content approval' }}</b></article><article class="detail-card"><span>02</span><h3>{{ $ar ? 'دليل تخطيط دورات العناية' : 'Care-cycle planning guide' }}</h3><p>{{ $ar ? 'أسئلة تساعد فرق التشغيل على تحديد الأولوية والدفعات ونافذة الإنجاز.' : 'Questions that help operations teams define priority, batches and turnaround windows.' }}</p><b>{{ $ar ? 'قريباً بعد اعتماد المحتوى' : 'Coming after content approval' }}</b></article></div></div></section>
    @elseif($page === 'contact')
        <section class="section assessment-section"><div class="container assessment-grid"><div><p class="eyebrow">{{ $ar ? 'طلب تقييم' : 'Assessment request' }}</p><h2>{{ $ar ? 'أرسل المعلومات الأساسية وسنحدد الخطوة التالية.' : 'Send the essentials and we will define the next step.' }}</h2><p>{{ $ar ? 'إرسال النموذج لا يمثل عرض سعر أو التزاماً بالتنفيذ.' : 'Submitting this form is not a quotation or service commitment.' }}</p></div><form class="quick-assessment" method="post" action="{{ route('rfq.store') }}" enctype="multipart/form-data">@csrf<input type="hidden" name="source_page" value="contact"><label>{{ $ar ? 'الاسم' : 'Name' }}<input name="contact_name" value="{{ old('contact_name') }}" required></label><label>{{ $ar ? 'البريد الإلكتروني' : 'Email' }}<input type="email" name="email" value="{{ old('email') }}" required></label><label>{{ $ar ? 'المنشأة' : 'Property / company' }}<input name="organization_name" value="{{ old('organization_name') }}"></label><label>{{ $ar ? 'الهاتف' : 'Phone' }}<input name="phone" value="{{ old('phone') }}"></label><label>{{ $ar ? 'نوع المنشأة' : 'Property type' }}<select name="property_type"><option value="hotel">{{ $ar ? 'فندق أو منتجع' : 'Hotel or resort' }}</option><option value="restaurant">{{ $ar ? 'مطعم' : 'Restaurant' }}</option><option value="catering">{{ $ar ? 'تموين أو فعاليات' : 'Catering or events' }}</option><option value="other">{{ $ar ? 'أخرى' : 'Other' }}</option></select></label><label>{{ $ar ? 'الخدمة المطلوبة' : 'Service need' }}<select name="service_code"><option value="assessment">{{ $ar ? 'تقييم فني' : 'Technical assessment' }}</option><option value="cutlery-restoration">{{ $ar ? 'ترميم أدوات المائدة' : 'Cutlery restoration' }}</option><option value="hollowware-care">{{ $ar ? 'العناية بالقطع المجوفة' : 'Hollowware care' }}</option><option value="recurring-care-plan">{{ $ar ? 'خطة دورية' : 'Recurring plan' }}</option></select></label><label>{{ $ar ? 'الكمية التقديرية' : 'Estimated quantity' }}<input type="number" min="1" name="estimated_quantity" value="{{ old('estimated_quantity') }}"></label><label>{{ $ar ? 'الأولوية' : 'Urgency' }}<select name="urgency"><option value="standard">{{ $ar ? 'اعتيادية' : 'Standard' }}</option><option value="priority">{{ $ar ? 'أولوية' : 'Priority' }}</option><option value="urgent">{{ $ar ? 'عاجلة' : 'Urgent' }}</option></select></label><label class="full-field">{{ $ar ? 'التفاصيل' : 'Details' }}<textarea name="message" rows="5">{{ old('message') }}</textarea></label><input class="honeypot" tabindex="-1" autocomplete="off" name="website"><label class="consent full-field"><input type="checkbox" name="consent" value="1" required><span>{{ $ar ? 'أوافق على استخدام المعلومات لمتابعة هذا الطلب.' : 'I agree that this information may be used to follow up this request.' }}</span></label><button class="button button-dark full-field" type="submit">{{ $ar ? 'إرسال طلب التقييم' : 'Submit assessment request' }}</button></form></div></section>
    @elseif($page === 'portal')
        <section class="section"><div class="container portal-panel"><div><p class="eyebrow">{{ $ar ? 'دخول منضبط' : 'Controlled access' }}</p><h2>{{ $ar ? 'البوابة مخصصة للحسابات التي تم التحقق منها.' : 'The portal is reserved for verified accounts.' }}</h2><p>{{ $ar ? 'لن نعرض شاشة دخول وهمية قبل اكتمال نطاق الهوية والصلاحيات.' : 'No simulated sign-in is shown before identity and access scope are complete.' }}</p></div><div class="portal-status"><span>{{ $ar ? 'الحالة' : 'Status' }}</span><strong>{{ $ar ? 'التفعيل المنضبط قيد التجهيز' : 'Controlled activation pending' }}</strong></div></div></section>
    @endif
</main>
@include('partials.public-footer')
</body>
</html>
