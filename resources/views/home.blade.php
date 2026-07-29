<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <meta name="description" content="{{ app()->getLocale() === 'ar' ? 'خدمات متخصصة للعناية بأدوات المائدة وأصول الضيافة.' : 'Specialist care for cutlery, hollowware and hospitality assets.' }}">
    <title>{{ app()->getLocale() === 'ar' ? 'آي برو فيكسر | العناية بأصول الضيافة' : 'IProFixer | Hospitality Asset Care' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
@php($ar = app()->getLocale() === 'ar')
<a class="skip-link" href="#main">{{ $ar ? 'انتقل إلى المحتوى' : 'Skip to content' }}</a>
<header class="site-header">
    <div class="utility-bar">
        <div class="container utility-inner">
            <span>{{ $ar ? 'خدمات متخصصة لقطاع الضيافة' : 'Specialist hospitality asset care' }}</span>
            <span>{{ $ar ? 'خدمة موجهة للمنشآت' : 'Built for hospitality operations' }}</span>
        </div>
    </div>
    <div class="container nav-wrap">
        <a class="brand" href="{{ route('home') }}" aria-label="IProFixer home">
            <span class="brand-mark" aria-hidden="true">IP</span>
            <span>IProFixer</span>
        </a>
        <nav aria-label="{{ $ar ? 'التنقل الرئيسي' : 'Primary navigation' }}">
            <a href="#services">{{ $ar ? 'الخدمات' : 'Services' }}</a>
            <a href="#process">{{ $ar ? 'آلية العمل' : 'Process' }}</a>
            <a href="#industries">{{ $ar ? 'القطاعات' : 'Industries' }}</a>
            <a href="#consultation">{{ $ar ? 'تواصل معنا' : 'Contact' }}</a>
        </nav>
        <div class="nav-actions">
            <form method="post" action="{{ route('locale.update', $ar ? 'en' : 'ar') }}">
                @csrf
                <button class="language-switch" type="submit">{{ $ar ? 'EN' : 'العربية' }}</button>
            </form>
            <a class="button button-primary button-small" href="#consultation">{{ $ar ? 'اطلب استشارة' : 'Request consultation' }}</a>
        </div>
    </div>
</header>

<main id="main">
    <section class="hero">
        <div class="container hero-grid">
            <div class="hero-copy">
                <p class="eyebrow">{{ $ar ? 'العناية المتخصصة بأصول الضيافة' : 'Hospitality asset care specialists' }}</p>
                <h1>{{ $ar ? 'نحافظ على جودة أدوات المائدة ونطيل عمرها التشغيلي.' : 'Restore presentation quality. Extend operational life.' }}</h1>
                <p class="hero-lead">{{ $ar ? 'خدمات فنية منظمة لتنظيف وتلميع وترميم أدوات المائدة والقطع المجوفة، مع تقييم واضح ومسار عمل قابل للتتبع.' : 'Structured technical care for cutlery and hollowware, supported by clear assessment, controlled handling and traceable delivery.' }}</p>
                <div class="hero-actions">
                    <a class="button button-primary" href="#consultation">{{ $ar ? 'ابدأ بطلب تقييم' : 'Start with an assessment' }}</a>
                    <a class="button button-secondary" href="#services">{{ $ar ? 'استكشف الخدمات' : 'Explore services' }}</a>
                </div>
                <ul class="evidence-list" aria-label="{{ $ar ? 'مبادئ الخدمة' : 'Service principles' }}">
                    <li>{{ $ar ? 'تقييم قبل التنفيذ' : 'Assessment before work' }}</li>
                    <li>{{ $ar ? 'توثيق الكميات والحالة' : 'Quantity and condition records' }}</li>
                    <li>{{ $ar ? 'فحص جودة قبل التسليم' : 'Quality check before delivery' }}</li>
                </ul>
            </div>
            <div class="hero-visual" role="img" aria-label="{{ $ar ? 'تكوين تجريدي راقٍ لأدوات مائدة مصقولة' : 'Premium abstract composition representing polished tableware' }}">
                <div class="silver-form silver-form-a"></div>
                <div class="silver-form silver-form-b"></div>
                <div class="gold-line"></div>
                <div class="visual-note">{{ $ar ? 'عناية دقيقة. نتائج واضحة.' : 'Precise care. Clear outcomes.' }}</div>
            </div>
        </div>
    </section>

    <section class="trust-strip" aria-label="{{ $ar ? 'منهجية الخدمة' : 'Service methodology' }}">
        <div class="container trust-grid">
            <div><strong>01</strong><span>{{ $ar ? 'استلام موثق' : 'Documented intake' }}</span></div>
            <div><strong>02</strong><span>{{ $ar ? 'تقييم فني' : 'Technical assessment' }}</span></div>
            <div><strong>03</strong><span>{{ $ar ? 'معالجة مضبوطة' : 'Controlled processing' }}</span></div>
            <div><strong>04</strong><span>{{ $ar ? 'فحص وتسليم' : 'Quality release' }}</span></div>
        </div>
    </section>

    <section class="section" id="services">
        <div class="container">
            <div class="section-heading">
                <p class="eyebrow">{{ $ar ? 'الخدمات' : 'Services' }}</p>
                <h2>{{ $ar ? 'حلول عملية للعناية المستمرة بأصول الخدمة.' : 'Practical care for assets that work every day.' }}</h2>
                <p>{{ $ar ? 'نبدأ بحالة الأصل واحتياج التشغيل، ثم نحدد نطاق العمل المناسب دون وعود غير موثقة.' : 'We begin with the asset condition and operational need, then define the right scope without unsupported promises.' }}</p>
            </div>
            <div class="card-grid four-columns">
                <article class="service-card"><span>01</span><h3>{{ $ar ? 'ترميم أدوات المائدة' : 'Cutlery restoration' }}</h3><p>{{ $ar ? 'معالجة مهنية للمظهر والسطح بحسب الحالة الفعلية للقطع.' : 'Professional surface and presentation care based on actual condition.' }}</p></article>
                <article class="service-card"><span>02</span><h3>{{ $ar ? 'العناية بالقطع المجوفة' : 'Hollowware care' }}</h3><p>{{ $ar ? 'تنظيف وتلميع ومعالجة مدروسة لقطع التقديم المستخدمة بكثافة.' : 'Structured cleaning, polishing and care for high-use service pieces.' }}</p></article>
                <article class="service-card"><span>03</span><h3>{{ $ar ? 'التقييم الفني' : 'Technical assessment' }}</h3><p>{{ $ar ? 'تحديد الحالة والكميات والأولوية قبل اعتماد نطاق التنفيذ.' : 'Condition, quantity and priority review before scope approval.' }}</p></article>
                <article class="service-card"><span>04</span><h3>{{ $ar ? 'برامج العناية الدورية' : 'Recurring care plans' }}</h3><p>{{ $ar ? 'دورات خدمة قابلة للتخطيط للمنشآت التي تحتاج استمرارية تشغيلية.' : 'Planned service cycles for properties that need operational continuity.' }}</p></article>
            </div>
        </div>
    </section>

    <section class="section section-soft" id="process">
        <div class="container process-grid">
            <div class="section-heading sticky-heading">
                <p class="eyebrow">{{ $ar ? 'آلية العمل' : 'How it works' }}</p>
                <h2>{{ $ar ? 'مسار واضح من الاستلام حتى التسليم.' : 'A clear path from intake to return.' }}</h2>
                <p>{{ $ar ? 'كل مرحلة لها غرض محدد، ومسؤولية واضحة، ونقطة تحقق قبل الانتقال للمرحلة التالية.' : 'Each stage has a defined purpose, clear ownership and a check before work moves forward.' }}</p>
            </div>
            <ol class="process-list">
                <li><span>01</span><div><h3>{{ $ar ? 'طلب وتقييم أولي' : 'Request and initial review' }}</h3><p>{{ $ar ? 'نجمع نوع المنشأة والخدمة والكميات والأولوية.' : 'We capture property type, service need, quantity and urgency.' }}</p></div></li>
                <li><span>02</span><div><h3>{{ $ar ? 'تأكيد النطاق' : 'Scope confirmation' }}</h3><p>{{ $ar ? 'يتم توضيح نطاق العمل والقيود والخطوات قبل التنفيذ.' : 'The work scope, constraints and next steps are clarified before processing.' }}</p></div></li>
                <li><span>03</span><div><h3>{{ $ar ? 'المعالجة وضبط الجودة' : 'Processing and quality control' }}</h3><p>{{ $ar ? 'تتم المعالجة وفق الحالة مع فحص قبل الإغلاق.' : 'Work follows the actual condition, with inspection before release.' }}</p></div></li>
                <li><span>04</span><div><h3>{{ $ar ? 'التسليم والمتابعة' : 'Delivery and follow-up' }}</h3><p>{{ $ar ? 'تأكيد الكميات والتسليم وتسجيل أي ملاحظة تحتاج متابعة.' : 'Counts, delivery and any required follow-up are recorded.' }}</p></div></li>
            </ol>
        </div>
    </section>

    <section class="section" id="industries">
        <div class="container">
            <div class="section-heading centered">
                <p class="eyebrow">{{ $ar ? 'القطاعات' : 'Industries' }}</p>
                <h2>{{ $ar ? 'مصمم لبيئات الخدمة التي تعتمد على التفاصيل.' : 'Designed for service environments where details matter.' }}</h2>
            </div>
            <div class="card-grid three-columns">
                <article class="industry-card"><h3>{{ $ar ? 'الفنادق والمنتجعات' : 'Hotels and resorts' }}</h3><p>{{ $ar ? 'دعم فرق الأغذية والمشروبات والمشتريات والتشغيل.' : 'Support for F&B, stewarding, procurement and operations teams.' }}</p></article>
                <article class="industry-card"><h3>{{ $ar ? 'المطاعم والضيافة' : 'Restaurants and hospitality groups' }}</h3><p>{{ $ar ? 'خدمات مناسبة للدورات التشغيلية والكميات المتكررة.' : 'Care aligned with operating cycles and recurring volumes.' }}</p></article>
                <article class="industry-card"><h3>{{ $ar ? 'التموين والفعاليات' : 'Catering and events' }}</h3><p>{{ $ar ? 'تقييم وتنظيم للعناية بأصول الخدمة المستخدمة بكثافة.' : 'Assessment and structured care for heavily used service assets.' }}</p></article>
            </div>
        </div>
    </section>

    <section class="section consultation" id="consultation">
        <div class="container consultation-grid">
            <div class="consultation-copy">
                <p class="eyebrow">{{ $ar ? 'طلب استشارة' : 'Request a consultation' }}</p>
                <h2>{{ $ar ? 'أخبرنا بما تحتاجه وسنبدأ بتقييم واضح.' : 'Tell us what you need. We will start with a clear review.' }}</h2>
                <p>{{ $ar ? 'إرسال الطلب لا يمثل عرض سعر أو التزاماً بالتنفيذ. سيستخدم فريقنا المعلومات للتواصل وفهم نطاق الخدمة.' : 'Submitting this form is not a quotation or service commitment. The information is used to contact you and understand the requested scope.' }}</p>
            </div>
            <form class="rfq-form" method="post" action="{{ route('rfq.store') }}" novalidate>
                @csrf
                <input type="hidden" name="source_page" value="home">
                <div class="honeypot" aria-hidden="true"><label>Website<input name="website" tabindex="-1" autocomplete="off"></label></div>
                @if(session('status'))<div class="form-success" role="status">{{ session('status') }}</div>@endif
                @if($errors->any())<div class="form-errors" role="alert"><strong>{{ $ar ? 'يرجى مراجعة الحقول التالية:' : 'Please review the highlighted fields.' }}</strong></div>@endif
                <div class="field-grid">
                    <label><span>{{ $ar ? 'الاسم الكامل' : 'Full name' }} *</span><input name="contact_name" value="{{ old('contact_name') }}" required maxlength="120" autocomplete="name"></label>
                    <label><span>{{ $ar ? 'اسم المنشأة' : 'Organization' }}</span><input name="organization_name" value="{{ old('organization_name') }}" maxlength="160" autocomplete="organization"></label>
                    <label><span>{{ $ar ? 'البريد الإلكتروني' : 'Email' }} *</span><input type="email" name="email" value="{{ old('email') }}" required maxlength="190" autocomplete="email" dir="ltr"></label>
                    <label><span>{{ $ar ? 'رقم الهاتف' : 'Phone' }}</span><input name="phone" value="{{ old('phone') }}" maxlength="40" autocomplete="tel" dir="ltr"></label>
                    <label><span>{{ $ar ? 'الخدمة المطلوبة' : 'Service needed' }}</span><select name="service_code"><option value="">{{ $ar ? 'اختر الخدمة' : 'Select service' }}</option><option value="cutlery-restoration">{{ $ar ? 'ترميم أدوات المائدة' : 'Cutlery restoration' }}</option><option value="hollowware-restoration">{{ $ar ? 'العناية بالقطع المجوفة' : 'Hollowware care' }}</option><option value="maintenance-program">{{ $ar ? 'برنامج عناية دوري' : 'Recurring care plan' }}</option><option value="assessment">{{ $ar ? 'تقييم فني' : 'Technical assessment' }}</option><option value="other">{{ $ar ? 'خدمة أخرى' : 'Other' }}</option></select></label>
                    <label><span>{{ $ar ? 'نوع المنشأة' : 'Property type' }}</span><select name="property_type"><option value="">{{ $ar ? 'اختر النوع' : 'Select type' }}</option><option value="hotel">{{ $ar ? 'فندق أو منتجع' : 'Hotel or resort' }}</option><option value="restaurant">{{ $ar ? 'مطعم' : 'Restaurant' }}</option><option value="catering">{{ $ar ? 'تموين أو فعاليات' : 'Catering or events' }}</option><option value="other">{{ $ar ? 'أخرى' : 'Other' }}</option></select></label>
                    <label><span>{{ $ar ? 'الكمية التقديرية' : 'Estimated quantity' }}</span><input type="number" name="estimated_quantity" value="{{ old('estimated_quantity') }}" min="1" max="1000000" inputmode="numeric"></label>
                    <label><span>{{ $ar ? 'الأولوية' : 'Urgency' }}</span><select name="urgency"><option value="">{{ $ar ? 'اختر الأولوية' : 'Select urgency' }}</option><option value="standard">{{ $ar ? 'اعتيادية' : 'Standard' }}</option><option value="priority">{{ $ar ? 'أولوية' : 'Priority' }}</option><option value="urgent">{{ $ar ? 'عاجلة' : 'Urgent' }}</option></select></label>
                </div>
                <label class="full-field"><span>{{ $ar ? 'تفاصيل إضافية' : 'Additional details' }}</span><textarea name="message" rows="5" maxlength="5000">{{ old('message') }}</textarea></label>
                <label class="consent-field"><input type="checkbox" name="consent" value="1" required @checked(old('consent'))><span>{{ $ar ? 'أوافق على استخدام هذه البيانات للتواصل معي بخصوص هذا الطلب.' : 'I consent to the use of this information to contact me about this request.' }}</span></label>
                <button class="button button-primary" type="submit">{{ $ar ? 'إرسال الطلب' : 'Submit request' }}</button>
            </form>
        </div>
    </section>
</main>

<footer class="site-footer">
    <div class="container footer-grid">
        <div><a class="brand footer-brand" href="{{ route('home') }}"><span class="brand-mark" aria-hidden="true">IP</span><span>IProFixer</span></a><p>{{ $ar ? 'العناية المتخصصة بأصول الضيافة.' : 'Specialist hospitality asset care.' }}</p></div>
        <div><strong>{{ $ar ? 'استكشف' : 'Explore' }}</strong><a href="#services">{{ $ar ? 'الخدمات' : 'Services' }}</a><a href="#process">{{ $ar ? 'آلية العمل' : 'Process' }}</a><a href="#consultation">{{ $ar ? 'طلب استشارة' : 'Consultation' }}</a></div>
        <div><strong>{{ $ar ? 'مهم' : 'Important' }}</strong><p>{{ $ar ? 'تخضع جميع الخدمات للتقييم وتأكيد النطاق.' : 'All services are subject to assessment and scope confirmation.' }}</p></div>
    </div>
    <div class="container footer-bottom"><span>© {{ date('Y') }} IProFixer</span><span>{{ $ar ? 'جميع الحقوق محفوظة' : 'All rights reserved' }}</span></div>
</footer>
</body>
</html>
