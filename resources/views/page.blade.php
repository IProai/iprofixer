<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    @php
        $ar = app()->getLocale() === 'ar';
        $serviceDetails = [
            'cutlery-restoration' => ['Cutlery restoration','ترميم أدوات المائدة','Structured surface and presentation care for frequently used cutlery.','عناية منظمة بالسطح والمظهر لأدوات المائدة المستخدمة بكثافة.'],
            'hollowware-care' => ['Hollowware care','العناية بالقطع المجوفة','Condition-led care for serving pieces, trays and presentation assets.','عناية مبنية على حالة قطع التقديم والصواني وأصول العرض.'],
            'asset-condition-review' => ['Asset condition review','تقييم حالة الأصول','A documented review of condition, quantity and service priority before work begins.','مراجعة موثقة للحالة والكميات وأولوية الخدمة قبل بدء العمل.'],
            'recurring-care-plans' => ['Recurring care plans','خطط العناية الدورية','Planned service cycles for operations with repeated asset use.','دورات خدمة مخططة للمنشآت ذات الاستخدام المتكرر للأصول.'],
        ];
        $industryDetails = [
            'hotels-resorts' => ['Hotels & resorts','الفنادق والمنتجعات','Protect presentation standards across restaurants, banqueting and room-service operations.','حماية معايير المظهر في المطاعم والولائم وخدمات الغرف.'],
            'restaurants-groups' => ['Restaurants & groups','المطاعم والمجموعات','Coordinate asset care across single venues or multi-location restaurant operations.','تنسيق العناية بالأصول للمطاعم المستقلة أو المجموعات متعددة الفروع.'],
            'catering-events' => ['Catering & events','التموين والفعاليات','Prepare high-volume service assets around event calendars and turnaround windows.','تجهيز أصول الخدمة ذات الأحجام الكبيرة وفق جداول الفعاليات وفترات التسليم.'],
            'procurement-operations' => ['Procurement & operations','المشتريات والتشغيل','Give decision-makers a clearer basis for restore, retain or replace decisions.','منح فرق القرار أساساً أوضح لقرارات الترميم أو الاحتفاظ أو الاستبدال.'],
        ];
        $pages = [
            'services' => ['Services','الخدمات','Focused care routes built around asset condition and operating reality.','مسارات عناية متخصصة مبنية على حالة الأصل وواقع التشغيل.'],
            'industries' => ['Industries','القطاعات','Built around the operating realities of hospitality teams.','مصمم وفق واقع التشغيل لدى فرق الضيافة.'],
            'process' => ['Our process','آلية العمل','A controlled route from assessment and intake to quality release and return.','مسار منضبط يبدأ بالتقييم والاستلام وينتهي بالفحص والتسليم.'],
            'results' => ['Proof & results','النتائج والإثبات','Evidence-led delivery without unsupported claims.','تنفيذ قائم على الدليل دون ادعاءات غير موثقة.'],
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
            $copy = $ar
                ? [$copy[0], $translation->title, $copy[2], $translation->summary ?: $copy[3]]
                : [$translation->title, $copy[1], $translation->summary ?: $copy[2], $copy[3]];
        }

        $seoTitle = $translation?->seo_title ?: ($ar ? $copy[1] : $copy[0]);
        $seoDescription = $translation?->seo_description ?: ($ar ? $copy[3] : $copy[2]);
    @endphp
    <meta name="description" content="{{ $seoDescription }}">
    <title>{{ $seoTitle }} | IProFixer</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<a class="skip-link" href="#main">{{ $ar ? 'انتقل إلى المحتوى' : 'Skip to content' }}</a>
<header class="site-header">
    <div class="announcement"><div class="container announcement-inner"><span>{{ $ar ? 'خدمة متخصصة لعمليات الضيافة في الإمارات والسعودية' : 'Specialist support for hospitality operations across the UAE & KSA' }}</span><a href="{{ route('contact') }}">{{ $ar ? 'ابدأ التقييم' : 'Start an assessment' }} ↗</a></div></div>
    <div class="container nav-wrap">
        <a class="brand" href="{{ route('home') }}"><span class="brand-symbol">I</span><span>IProFixer</span></a>
        <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="site-nav">{{ $ar ? 'القائمة' : 'Menu' }}</button>
        <nav id="site-nav" class="site-nav"><a href="{{ route('services') }}">{{ $ar ? 'الخدمات' : 'Services' }}</a><a href="{{ route('industries') }}">{{ $ar ? 'القطاعات' : 'Industries' }}</a><a href="{{ route('process') }}">{{ $ar ? 'آلية العمل' : 'Process' }}</a><a href="{{ route('results') }}">{{ $ar ? 'النتائج' : 'Results' }}</a><a href="{{ route('about') }}">{{ $ar ? 'عن الشركة' : 'About' }}</a></nav>
        <div class="nav-actions"><a class="portal-link" href="{{ route('portal') }}">{{ $ar ? 'بوابة العملاء' : 'Client portal' }}</a><form method="post" action="{{ route('locale.update', $ar ? 'en' : 'ar') }}">@csrf<button class="language-switch" type="submit">{{ $ar ? 'EN' : 'العربية' }}</button></form><a class="button button-dark button-small" href="{{ route('contact') }}">{{ $ar ? 'اطلب تقييماً' : 'Request assessment' }}</a></div>
    </div>
</header>
<main id="main">
    <section class="page-hero"><div class="container page-hero-grid"><div><a class="back-link" href="{{ route('home') }}">{{ $ar ? 'العودة للرئيسية' : 'Back to home' }}</a><p class="eyebrow">IProFixer / {{ $ar ? $copy[1] : $copy[0] }}</p><h1>{{ $ar ? $copy[1] : $copy[0] }}</h1><p class="page-lead">{{ $ar ? $copy[3] : $copy[2] }}</p></div><div class="page-index">{{ in_array($page, ['service-detail','industry-detail'], true) ? '↗' : '0'.(array_search($page, array_keys($pages), true)+1) }}</div></div></section>

    @if($translation?->body)
        <section class="section"><div class="container editorial-grid"><div><p class="eyebrow">{{ $ar ? 'محتوى معتمد' : 'Approved content' }}</p><h2>{{ $translation->title }}</h2></div><div class="editorial-copy">{!! nl2br(e($translation->body)) !!}</div></div></section>
    @elseif($page === 'services')
        <section class="section"><div class="container"><div class="detail-card-grid">@foreach($serviceDetails as $key => $item)<a class="detail-card" href="{{ route('services.show',$key) }}"><span>0{{ $loop->iteration }}</span><h3>{{ $ar ? $item[1] : $item[0] }}</h3><p>{{ $ar ? $item[3] : $item[2] }}</p><b>{{ $ar ? 'استكشف المسار' : 'Explore route' }} ↗</b></a>@endforeach</div></div></section>
    @elseif($page === 'industries')
        <section class="section"><div class="container"><div class="detail-card-grid">@foreach($industryDetails as $key => $item)<a class="detail-card" href="{{ route('industries.show',$key) }}"><span>0{{ $loop->iteration }}</span><h3>{{ $ar ? $item[1] : $item[0] }}</h3><p>{{ $ar ? $item[3] : $item[2] }}</p><b>{{ $ar ? 'استكشف القطاع' : 'Explore industry' }} ↗</b></a>@endforeach</div></div></section>
    @elseif($page === 'process')
        <section class="section"><div class="container process-list">@foreach([['Request & scope','الطلب وتحديد النطاق'],['Documented intake','استلام موثق'],['Technical review','مراجعة فنية'],['Approved care route','مسار عناية معتمد'],['Quality release','فحص الجودة'],['Return & records','التسليم والسجلات']] as $step)<article><span>0{{ $loop->iteration }}</span><h2>{{ $ar ? $step[1] : $step[0] }}</h2><p>{{ $ar ? 'تُحدد المسؤولية والبيانات المطلوبة قبل الانتقال إلى المرحلة التالية.' : 'Responsibility and required information are made clear before the next stage.' }}</p></article>@endforeach</div></section>
    @elseif($page === 'results')
        <section class="section"><div class="container editorial-grid"><div><p class="eyebrow">{{ $ar ? 'الدليل قبل الادعاء' : 'Evidence before claims' }}</p><h2>{{ $ar ? 'لن ننشر نتائج أو شعارات أو شهادات قبل التحقق منها.' : 'No results, logos or testimonials will be published before verification.' }}</h2></div><div class="editorial-copy"><p>{{ $ar ? 'سيعرض هذا القسم لاحقاً دراسات حالة معتمدة وصوراً موثقة ونطاق العمل والنتيجة الفعلية.' : 'This area will publish approved case studies with verified imagery, scope and actual outcomes.' }}</p></div></div></section>
    @elseif($page === 'contact')
        <section class="section assessment-section"><div class="container assessment-grid"><div><p class="eyebrow">{{ $ar ? 'طلب تقييم' : 'Assessment request' }}</p><h2>{{ $ar ? 'أرسل المعلومات الأساسية وسنحدد الخطوة التالية.' : 'Send the essentials and we will define the next step.' }}</h2><p>{{ $ar ? 'إرسال النموذج لا يمثل عرض سعر أو التزاماً بالتنفيذ.' : 'Submitting this form is not a quotation or service commitment.' }}</p></div><form class="quick-assessment" method="post" action="{{ route('rfq.store') }}" enctype="multipart/form-data">@csrf<input type="hidden" name="source_page" value="contact"><label>{{ $ar ? 'الاسم' : 'Name' }}<input name="contact_name" value="{{ old('contact_name') }}" required></label><label>{{ $ar ? 'البريد الإلكتروني' : 'Email' }}<input type="email" name="email" value="{{ old('email') }}" required></label><label>{{ $ar ? 'المنشأة' : 'Property / company' }}<input name="organization_name" value="{{ old('organization_name') }}"></label><label>{{ $ar ? 'الهاتف' : 'Phone' }}<input name="phone" value="{{ old('phone') }}"></label><label>{{ $ar ? 'نوع المنشأة' : 'Property type' }}<select name="property_type"><option value="hotel">{{ $ar ? 'فندق أو منتجع' : 'Hotel or resort' }}</option><option value="restaurant">{{ $ar ? 'مطعم' : 'Restaurant' }}</option><option value="catering">{{ $ar ? 'تموين أو فعاليات' : 'Catering or events' }}</option><option value="other">{{ $ar ? 'أخرى' : 'Other' }}</option></select></label><label>{{ $ar ? 'الخدمة المطلوبة' : 'Service need' }}<select name="service_code"><option value="assessment">{{ $ar ? 'تقييم فني' : 'Technical assessment' }}</option><option value="cutlery-restoration">{{ $ar ? 'ترميم أدوات المائدة' : 'Cutlery restoration' }}</option><option value="hollowware-care">{{ $ar ? 'العناية بالقطع المجوفة' : 'Hollowware care' }}</option><option value="recurring-care-plan">{{ $ar ? 'خطة دورية' : 'Recurring plan' }}</option></select></label><label>{{ $ar ? 'الكمية التقديرية' : 'Estimated quantity' }}<input type="number" min="1" name="estimated_quantity" value="{{ old('estimated_quantity') }}"></label><label>{{ $ar ? 'الأولوية' : 'Urgency' }}<select name="urgency"><option value="standard">{{ $ar ? 'اعتيادية' : 'Standard' }}</option><option value="priority">{{ $ar ? 'أولوية' : 'Priority' }}</option><option value="urgent">{{ $ar ? 'عاجلة' : 'Urgent' }}</option></select></label><label class="full-field">{{ $ar ? 'التفاصيل' : 'Details' }}<textarea name="message" rows="5">{{ old('message') }}</textarea></label><input class="honeypot" tabindex="-1" autocomplete="off" name="website"><label class="consent full-field"><input type="checkbox" name="consent" value="1" required><span>{{ $ar ? 'أوافق على استخدام المعلومات لمتابعة هذا الطلب.' : 'I agree that this information may be used to follow up this request.' }}</span></label><button class="button button-dark full-field" type="submit">{{ $ar ? 'إرسال طلب التقييم' : 'Submit assessment request' }}</button></form></div></section>
    @elseif($page === 'portal')
        <section class="section"><div class="container portal-panel"><div><p class="eyebrow">{{ $ar ? 'دخول منضبط' : 'Controlled access' }}</p><h2>{{ $ar ? 'البوابة مخصصة للحسابات التي تم التحقق منها.' : 'The portal is reserved for verified accounts.' }}</h2></div><div class="portal-status"><span>{{ $ar ? 'الحالة' : 'Status' }}</span><strong>{{ $ar ? 'قيد التجهيز المنضبط' : 'Controlled activation pending' }}</strong></div></div></section>
    @else
        <section class="section"><div class="container editorial-grid"><div><p class="eyebrow">{{ $ar ? 'نطاق معتمد' : 'Governed scope' }}</p><h2>{{ $ar ? $copy[1] : $copy[0] }}</h2></div><div class="editorial-copy"><p>{{ $ar ? $copy[3] : $copy[2] }}</p><a class="button button-dark" href="{{ route('contact') }}">{{ $ar ? 'ابدأ التقييم' : 'Start an assessment' }}</a></div></div></section>
    @endif
</main>
<footer class="site-footer"><div class="container footer-grid"><div><a class="brand footer-brand" href="{{ route('home') }}"><span class="brand-symbol">I</span><span>IProFixer</span></a><p>{{ $ar ? 'عناية متخصصة بأصول الضيافة.' : 'Specialist hospitality asset care.' }}</p></div><div><a href="{{ route('services') }}">{{ $ar ? 'الخدمات' : 'Services' }}</a><a href="{{ route('industries') }}">{{ $ar ? 'القطاعات' : 'Industries' }}</a><a href="{{ route('resources') }}">{{ $ar ? 'الموارد' : 'Resources' }}</a></div><div><a href="{{ route('portal') }}">{{ $ar ? 'بوابة العملاء' : 'Client portal' }}</a><a href="{{ route('contact') }}">{{ $ar ? 'تواصل معنا' : 'Contact' }}</a></div></div><div class="container footer-bottom"><span>© {{ date('Y') }} IProFixer</span><span>{{ $ar ? 'لا تُعرض ادعاءات أو إثباتات غير موثقة.' : 'No unverified claims or proof are displayed.' }}</span></div></footer>
</body>
</html>
