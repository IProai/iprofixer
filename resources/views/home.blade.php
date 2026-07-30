<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <meta name="description" content="{{ app()->getLocale() === 'ar' ? 'عناية متخصصة بأصول الضيافة للفنادق والمطاعم وفرق الفعاليات.' : 'Specialist hospitality asset care for hotels, restaurants and event operations.' }}">
    <link rel="canonical" href="{{ route('home') }}">
    <title>{{ app()->getLocale() === 'ar' ? 'آي برو فيكسر | عناية أذكى بأصول الضيافة' : 'IProFixer | Smarter Hospitality Asset Care' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
@php($ar = app()->getLocale() === 'ar')
<a class="skip-link" href="#main">{{ $ar ? 'انتقل إلى المحتوى' : 'Skip to content' }}</a>
<header class="site-header">
    <div class="announcement"><div class="container announcement-inner"><span>{{ $ar ? 'خدمة متخصصة لعمليات الضيافة في الإمارات والسعودية' : 'Specialist support for hospitality operations across the UAE & KSA' }}</span><a href="{{ route('contact') }}">{{ $ar ? 'ابدأ التقييم' : 'Start an assessment' }} ↗</a></div></div>
    <div class="container nav-wrap">
        <a class="brand" href="{{ route('home') }}" aria-label="IProFixer home"><span class="brand-symbol">I</span><span>IProFixer</span></a>
        <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="site-nav">{{ $ar ? 'القائمة' : 'Menu' }}</button>
        <nav id="site-nav" class="site-nav" aria-label="{{ $ar ? 'التنقل الرئيسي' : 'Primary navigation' }}">
            <a href="{{ route('services') }}">{{ $ar ? 'الخدمات' : 'Services' }}</a>
            <a href="{{ route('industries') }}">{{ $ar ? 'القطاعات' : 'Industries' }}</a>
            <a href="{{ route('process') }}">{{ $ar ? 'آلية العمل' : 'Process' }}</a>
            <a href="{{ route('results') }}">{{ $ar ? 'النتائج' : 'Results' }}</a>
            <a href="{{ route('about') }}">{{ $ar ? 'عن الشركة' : 'About' }}</a>
        </nav>
        <div class="nav-actions">
            <a class="portal-link" href="{{ route('portal') }}">{{ $ar ? 'بوابة العملاء' : 'Client portal' }}</a>
            <form method="post" action="{{ route('locale.update', $ar ? 'en' : 'ar') }}">@csrf<button class="language-switch" type="submit">{{ $ar ? 'EN' : 'العربية' }}</button></form>
            <a class="button button-dark button-small" href="{{ route('contact') }}">{{ $ar ? 'اطلب تقييماً' : 'Request assessment' }}</a>
        </div>
    </div>
</header>

<main id="main">
    <section class="hero-new">
        <div class="container hero-new-grid">
            <div class="hero-new-copy">
                <p class="eyebrow">{{ $ar ? 'عناية دقيقة. تشغيل أذكى.' : 'Precise care. Smarter operations.' }}</p>
                <h1>{{ $ar ? 'احمِ جودة الخدمة قبل أن تتحول الأصول المتعبة إلى تكلفة تشغيلية.' : 'Protect presentation quality before tired assets become an operating cost.' }}</h1>
                <p class="hero-lead">{{ $ar ? 'نساعد الفنادق والمطاعم وفرق الفعاليات على تقييم أدوات المائدة والقطع المجوفة والعناية بها ضمن مسار واضح، من الاستلام وحتى التسليم.' : 'We help hotels, restaurants and event teams assess, restore and manage cutlery and hollowware through one clear route from intake to return.' }}</p>
                <div class="hero-actions"><a class="button button-gold" href="{{ route('contact') }}">{{ $ar ? 'ابدأ بتقييم واضح' : 'Start with a clear assessment' }}</a><a class="text-link light-link" href="{{ route('services') }}">{{ $ar ? 'استكشف الخدمات' : 'Explore services' }} →</a></div>
                <div class="hero-proof"><span>{{ $ar ? 'تقييم قبل التنفيذ' : 'Assessment before work' }}</span><span>{{ $ar ? 'توثيق للكميات والحالة' : 'Condition & quantity records' }}</span><span>{{ $ar ? 'فحص قبل التسليم' : 'Quality release check' }}</span></div>
            </div>
            <div class="hero-art" aria-label="{{ $ar ? 'تكوين بصري تجريدي لأدوات ضيافة مصقولة' : 'Abstract composition inspired by polished hospitality assets' }}">
                <div class="art-panel art-panel-main"><div class="metal-orbit orbit-one"></div><div class="metal-orbit orbit-two"></div><div class="metal-stem"></div><div class="art-caption"><small>{{ $ar ? 'نظام العناية' : 'CARE SYSTEM' }}</small><strong>{{ $ar ? 'من الفحص إلى العودة' : 'From review to return' }}</strong></div></div>
                <div class="art-panel art-panel-note"><span>01</span><p>{{ $ar ? 'المظهر ليس تفصيلاً. إنه جزء من تجربة الضيف.' : 'Presentation is not decoration. It is part of the guest experience.' }}</p></div>
            </div>
        </div>
    </section>

    <section class="signal-strip"><div class="container signal-grid"><div><span>01</span><strong>{{ $ar ? 'استلام موثق' : 'Documented intake' }}</strong></div><div><span>02</span><strong>{{ $ar ? 'تقييم فني' : 'Technical review' }}</strong></div><div><span>03</span><strong>{{ $ar ? 'معالجة مضبوطة' : 'Controlled care' }}</strong></div><div><span>04</span><strong>{{ $ar ? 'فحص وتسليم' : 'Quality release' }}</strong></div></div></section>

    <section class="section intro-section">
        <div class="container editorial-grid">
            <div><p class="eyebrow">{{ $ar ? 'لماذا آي برو فيكسر' : 'Why IProFixer' }}</p><h2>{{ $ar ? 'نحوّل العناية بالأصول من مهمة طارئة إلى مسار تشغيلي يمكن إدارته.' : 'We turn asset care from an emergency task into a manageable operating route.' }}</h2></div>
            <div class="editorial-copy"><p>{{ $ar ? 'بدلاً من القرارات المتأخرة أو الاستبدال غير المدروس، يبدأ العمل بتقييم الحالة والكميات والأولوية، ثم تحديد النطاق المناسب دون ادعاءات أو وعود غير موثقة.' : 'Instead of late decisions or unplanned replacement, work begins with condition, quantity and priority—then moves into a defined scope without unsupported promises.' }}</p><a class="text-link" href="{{ route('process') }}">{{ $ar ? 'شاهد آلية العمل' : 'See how the process works' }} →</a></div>
        </div>
    </section>

    <section class="section services-showcase">
        <div class="container">
            <div class="section-topline"><div><p class="eyebrow">{{ $ar ? 'الخدمات الأساسية' : 'Core services' }}</p><h2>{{ $ar ? 'خدمات مبنية حول حالة الأصل وواقع التشغيل.' : 'Services built around asset condition and operating reality.' }}</h2></div><a class="text-link" href="{{ route('services') }}">{{ $ar ? 'كل الخدمات' : 'All services' }} →</a></div>
            <div class="service-stack">
                <a class="service-row" href="{{ route('services.show', 'cutlery-restoration') }}"><span>01</span><div><h3>{{ $ar ? 'ترميم أدوات المائدة' : 'Cutlery restoration' }}</h3><p>{{ $ar ? 'عناية بالمظهر والسطح وفق الحالة الفعلية للقطع.' : 'Surface and presentation care based on actual condition.' }}</p></div><b>↗</b></a>
                <a class="service-row" href="{{ route('services.show', 'hollowware-care') }}"><span>02</span><div><h3>{{ $ar ? 'العناية بالقطع المجوفة' : 'Hollowware care' }}</h3><p>{{ $ar ? 'معالجة مدروسة لقطع التقديم المستخدمة بكثافة.' : 'Structured care for high-use service pieces.' }}</p></div><b>↗</b></a>
                <a class="service-row" href="{{ route('services.show', 'asset-condition-review') }}"><span>03</span><div><h3>{{ $ar ? 'التقييم الفني للمخزون' : 'Asset condition review' }}</h3><p>{{ $ar ? 'فهم الحالة والكميات والأولوية قبل اعتماد النطاق.' : 'Condition, quantity and priority review before scope approval.' }}</p></div><b>↗</b></a>
                <a class="service-row" href="{{ route('services.show', 'recurring-care-plans') }}"><span>04</span><div><h3>{{ $ar ? 'برامج العناية الدورية' : 'Recurring care plans' }}</h3><p>{{ $ar ? 'دورات خدمة مخططة للمنشآت ذات الاستخدام المتكرر.' : 'Planned service cycles for high-frequency operations.' }}</p></div><b>↗</b></a>
            </div>
        </div>
    </section>

    <section class="section dark-section">
        <div class="container split-feature">
            <div><p class="eyebrow light-eyebrow">{{ $ar ? 'مصمم للضيافة' : 'Built for hospitality' }}</p><h2>{{ $ar ? 'نخاطب فرق التشغيل، لا الزوار فقط.' : 'Designed for operating teams—not just for show.' }}</h2><p>{{ $ar ? 'يربط الموقع الخدمة بالقطاع والدور التشغيلي، حتى يصل العميل بسرعة إلى المسار المناسب بدلاً من تصفح صفحات عامة.' : 'The website connects service, industry and operational role so buyers reach the right path quickly instead of browsing generic brochure pages.' }}</p></div>
            <div class="industry-grid"><a href="{{ route('industries.show', 'hotels-resorts') }}"><span>01</span><strong>{{ $ar ? 'الفنادق والمنتجعات' : 'Hotels & resorts' }}</strong></a><a href="{{ route('industries.show', 'restaurants-groups') }}"><span>02</span><strong>{{ $ar ? 'المطاعم والمجموعات' : 'Restaurants & groups' }}</strong></a><a href="{{ route('industries.show', 'catering-events') }}"><span>03</span><strong>{{ $ar ? 'التموين والفعاليات' : 'Catering & events' }}</strong></a><a href="{{ route('industries.show', 'procurement-operations') }}"><span>04</span><strong>{{ $ar ? 'المشتريات والتشغيل' : 'Procurement & operations' }}</strong></a></div>
        </div>
    </section>

    <section class="section assessment-section" id="assessment">
        <div class="container assessment-grid">
            <div><p class="eyebrow">{{ $ar ? 'ابدأ من الواقع' : 'Start with reality' }}</p><h2>{{ $ar ? 'شارك نوع الأصول والكميات والأولوية. نبدأ بعدها بمراجعة واضحة.' : 'Share the asset type, quantity and urgency. We begin with a clear review.' }}</h2><p>{{ $ar ? 'إرسال الطلب لا يمثل عرض سعر أو التزاماً بالتنفيذ. يستخدم لفهم الحالة وتحديد الخطوة التالية.' : 'Submitting a request is not a quotation or service commitment. It helps us understand the condition and define the next step.' }}</p></div>
            <div>
                @if(session('rfq_submitted'))<div class="form-status form-status-success" role="status"><strong>{{ $ar ? 'تم استلام الطلب.' : 'Request received.' }}</strong><span>{{ $ar ? 'مرجع الطلب' : 'Request reference' }}: {{ session('rfq_submitted') }}</span></div>@endif
                @if($errors->any())<div class="form-status form-status-error" role="alert"><strong>{{ $ar ? 'راجع الحقول المطلوبة.' : 'Please review the required fields.' }}</strong></div>@endif
                <form class="quick-assessment" method="post" action="{{ route('rfq.store') }}">@csrf<input type="hidden" name="source_page" value="home"><input class="honeypot" type="text" name="website" tabindex="-1" autocomplete="off"><label>{{ $ar ? 'الاسم' : 'Name' }}<input name="name" value="{{ old('name') }}" required></label><label>{{ $ar ? 'البريد الإلكتروني' : 'Email' }}<input type="email" name="email" value="{{ old('email') }}" required></label><label>{{ $ar ? 'المنشأة' : 'Property / company' }}<input name="company" value="{{ old('company') }}"></label><label>{{ $ar ? 'رقم التواصل' : 'Phone' }}<input name="phone" value="{{ old('phone') }}" inputmode="tel"></label><label>{{ $ar ? 'نوع المنشأة' : 'Property type' }}<select name="property_type"><option value="">{{ $ar ? 'اختر' : 'Select' }}</option><option value="hotel">{{ $ar ? 'فندق أو منتجع' : 'Hotel or resort' }}</option><option value="restaurant">{{ $ar ? 'مطعم أو مجموعة' : 'Restaurant or group' }}</option><option value="catering">{{ $ar ? 'تموين أو فعاليات' : 'Catering or events' }}</option><option value="other">{{ $ar ? 'أخرى' : 'Other' }}</option></select></label><label>{{ $ar ? 'نوع الخدمة' : 'Service need' }}<select name="service"><option value="assessment">{{ $ar ? 'تقييم فني' : 'Technical assessment' }}</option><option value="cutlery">{{ $ar ? 'ترميم أدوات المائدة' : 'Cutlery restoration' }}</option><option value="hollowware">{{ $ar ? 'العناية بالقطع المجوفة' : 'Hollowware care' }}</option><option value="recurring">{{ $ar ? 'خطة عناية دورية' : 'Recurring care plan' }}</option></select></label><label>{{ $ar ? 'الكمية التقريبية' : 'Estimated quantity' }}<input type="number" min="1" name="estimated_quantity" value="{{ old('estimated_quantity') }}"></label><label>{{ $ar ? 'الأولوية' : 'Urgency' }}<select name="urgency"><option value="standard">{{ $ar ? 'اعتيادية' : 'Standard' }}</option><option value="priority">{{ $ar ? 'أولوية' : 'Priority' }}</option><option value="urgent">{{ $ar ? 'عاجلة' : 'Urgent' }}</option></select></label><label class="full-field">{{ $ar ? 'ملاحظات مختصرة' : 'Brief notes' }}<textarea name="message" rows="4">{{ old('message') }}</textarea></label><label class="consent full-field"><input type="checkbox" name="consent" value="1" required><span>{{ $ar ? 'أوافق على استخدام المعلومات للتواصل بخصوص هذا الطلب.' : 'I agree that this information may be used to contact me about this request.' }}</span></label><button class="button button-dark full-field" type="submit">{{ $ar ? 'إرسال طلب التقييم' : 'Send assessment request' }}</button></form>
            </div>
        </div>
    </section>
</main>

<footer class="site-footer"><div class="container footer-grid"><div><a class="brand footer-brand" href="{{ route('home') }}"><span class="brand-symbol">I</span><span>IProFixer</span></a><p>{{ $ar ? 'عناية متخصصة بأصول الضيافة.' : 'Specialist hospitality asset care.' }}</p></div><div><a href="{{ route('services') }}">{{ $ar ? 'الخدمات' : 'Services' }}</a><a href="{{ route('industries') }}">{{ $ar ? 'القطاعات' : 'Industries' }}</a><a href="{{ route('process') }}">{{ $ar ? 'آلية العمل' : 'Process' }}</a></div><div><a href="{{ route('resources') }}">{{ $ar ? 'الموارد' : 'Resources' }}</a><a href="{{ route('portal') }}">{{ $ar ? 'بوابة العملاء' : 'Client portal' }}</a><a href="{{ route('contact') }}">{{ $ar ? 'تواصل معنا' : 'Contact' }}</a></div></div><div class="container footer-bottom"><span>© {{ date('Y') }} IProFixer</span><span>{{ $ar ? 'لا ادعاءات أو نتائج غير موثقة.' : 'No unverified claims or fabricated proof.' }}</span></div></footer>
</body>
</html>
