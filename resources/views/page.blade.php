<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    @php
        $ar = app()->getLocale() === 'ar';
        $pages = [
            'services' => ['Services', 'الخدمات', 'Focused care for hospitality assets that must perform and present well.', 'عناية متخصصة بأصول الضيافة التي يجب أن تعمل بكفاءة وتحافظ على مظهرها.'],
            'industries' => ['Industries', 'القطاعات', 'Built around the operating realities of hotels, restaurants, catering and event teams.', 'مصمم وفق واقع التشغيل في الفنادق والمطاعم والتموين وفرق الفعاليات.'],
            'process' => ['Our process', 'آلية العمل', 'A controlled route from assessment and collection to quality release and return.', 'مسار منضبط يبدأ بالتقييم والاستلام وينتهي بالفحص والتسليم.'],
            'results' => ['Proof & results', 'النتائج والإثبات', 'Evidence-led delivery without unsupported claims. Case material will be published only when verified.', 'تنفيذ قائم على الدليل دون ادعاءات غير موثقة. لا تُنشر الحالات إلا بعد اعتمادها.'],
            'about' => ['About IProFixer', 'عن آي برو فيكسر', 'A specialist hospitality asset-care partner for operational teams across the UAE and KSA.', 'شريك متخصص في العناية بأصول الضيافة لفرق التشغيل في الإمارات والسعودية.'],
            'resources' => ['Resources', 'المعرفة والموارد', 'Practical guidance for extending asset life, protecting presentation quality and planning service cycles.', 'إرشادات عملية لإطالة عمر الأصول والحفاظ على جودة المظهر وتخطيط دورات الخدمة.'],
            'contact' => ['Start a consultation', 'ابدأ الاستشارة', 'Share the asset type, quantity, location and urgency. We will begin with a clear review.', 'شارك نوع الأصول والكميات والموقع والأولوية، وسنبدأ بمراجعة واضحة.'],
            'portal' => ['Client portal', 'بوابة العملاء', 'Invitation-only access for approved clients. Portal activation follows account verification.', 'دخول بالدعوة فقط للعملاء المعتمدين بعد التحقق من الحساب.'],
        ];
        $copy = $pages[$page] ?? $pages['about'];
    @endphp
    <title>{{ $ar ? $copy[1] : $copy[0] }} | IProFixer</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<a class="skip-link" href="#main">{{ $ar ? 'انتقل إلى المحتوى' : 'Skip to content' }}</a>
<header class="site-header">
    <div class="container nav-wrap">
        <a class="brand" href="{{ route('home') }}"><span class="brand-symbol">I</span><span>IProFixer</span></a>
        <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="site-nav">Menu</button>
        <nav id="site-nav" class="site-nav">
            <a href="{{ route('services') }}">{{ $ar ? 'الخدمات' : 'Services' }}</a>
            <a href="{{ route('industries') }}">{{ $ar ? 'القطاعات' : 'Industries' }}</a>
            <a href="{{ route('process') }}">{{ $ar ? 'آلية العمل' : 'Process' }}</a>
            <a href="{{ route('results') }}">{{ $ar ? 'النتائج' : 'Results' }}</a>
            <a href="{{ route('about') }}">{{ $ar ? 'عن الشركة' : 'About' }}</a>
        </nav>
        <div class="nav-actions">
            <form method="post" action="{{ route('locale.update', $ar ? 'en' : 'ar') }}">@csrf<button class="language-switch" type="submit">{{ $ar ? 'EN' : 'العربية' }}</button></form>
            <a class="button button-dark button-small" href="{{ route('contact') }}">{{ $ar ? 'اطلب تقييماً' : 'Request assessment' }}</a>
        </div>
    </div>
</header>
<main id="main">
    <section class="page-hero">
        <div class="container page-hero-grid">
            <div>
                <a class="back-link" href="{{ route('home') }}">{{ $ar ? 'العودة للرئيسية' : 'Back to home' }}</a>
                <p class="eyebrow">IProFixer / {{ $ar ? $copy[1] : $copy[0] }}</p>
                <h1>{{ $ar ? $copy[1] : $copy[0] }}</h1>
                <p class="page-lead">{{ $ar ? $copy[3] : $copy[2] }}</p>
            </div>
            <div class="page-index">0{{ array_search($page, array_keys($pages), true) + 1 }}</div>
        </div>
    </section>

    <section class="section compact-section">
        <div class="container editorial-grid">
            <div>
                <p class="eyebrow">{{ $ar ? 'نقطة المراجعة الحالية' : 'Current release checkpoint' }}</p>
                <h2>{{ $ar ? 'هيكل جاهز للتوسع دون ادعاءات غير موثقة.' : 'A scalable structure without fabricated proof.' }}</h2>
            </div>
            <div class="editorial-copy">
                <p>{{ $ar ? 'هذه الصفحة جزء من إعادة بناء الموقع التجاري. يتم الآن ربط المحتوى التفصيلي والخدمات والقطاعات ومسار طلب التقييم ضمن نظام موحد ثنائي اللغة.' : 'This page is part of the commercial website rebuild. Detailed content, services, industries and the guided assessment journey are now being connected through one bilingual system.' }}</p>
                <a class="text-link" href="{{ route('contact') }}">{{ $ar ? 'ابدأ طلب التقييم' : 'Start an assessment request' }} →</a>
            </div>
        </div>
    </section>
</main>
<footer class="site-footer"><div class="container footer-grid"><div><a class="brand footer-brand" href="{{ route('home') }}"><span class="brand-symbol">I</span><span>IProFixer</span></a><p>{{ $ar ? 'عناية متخصصة بأصول الضيافة.' : 'Specialist hospitality asset care.' }}</p></div><div><a href="{{ route('services') }}">{{ $ar ? 'الخدمات' : 'Services' }}</a><a href="{{ route('industries') }}">{{ $ar ? 'القطاعات' : 'Industries' }}</a><a href="{{ route('resources') }}">{{ $ar ? 'الموارد' : 'Resources' }}</a></div><div><a href="{{ route('portal') }}">{{ $ar ? 'بوابة العملاء' : 'Client portal' }}</a><a href="{{ route('contact') }}">{{ $ar ? 'تواصل معنا' : 'Contact' }}</a></div></div></footer>
</body>
</html>
