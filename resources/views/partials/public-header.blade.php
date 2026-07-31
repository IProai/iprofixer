@php($ar = app()->getLocale() === 'ar')
<a class="skip-link" href="#main">{{ $ar ? 'انتقل إلى المحتوى' : 'Skip to content' }}</a>
<header class="site-header">
    <div class="announcement">
        <div class="container announcement-inner">
            <span>{{ $ar ? 'عناية متخصصة بأصول الضيافة في الإمارات والسعودية' : 'Specialist hospitality asset care across the UAE & KSA' }}</span>
            <a href="{{ route('contact') }}">{{ $ar ? 'ابدأ التقييم' : 'Start an assessment' }} ↗</a>
        </div>
    </div>
    <div class="container nav-wrap">
        <a class="brand" href="{{ route('home') }}" aria-label="IProFixer home">
            <span class="brand-symbol">I</span><span>IProFixer</span>
        </a>
        <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="site-nav">{{ $ar ? 'القائمة' : 'Menu' }}</button>
        <nav id="site-nav" class="site-nav" aria-label="{{ $ar ? 'التنقل الرئيسي' : 'Primary navigation' }}">
            <a href="{{ route('services') }}">{{ $ar ? 'الخدمات' : 'Services' }}</a>
            <a href="{{ route('industries') }}">{{ $ar ? 'القطاعات' : 'Industries' }}</a>
            <a href="{{ route('process') }}">{{ $ar ? 'آلية العمل' : 'Process' }}</a>
            <a href="{{ route('results') }}">{{ $ar ? 'الإثبات والنتائج' : 'Proof & Results' }}</a>
            <a href="{{ route('about') }}">{{ $ar ? 'عن الشركة' : 'About' }}</a>
            <a href="{{ route('resources') }}">{{ $ar ? 'الموارد' : 'Resources' }}</a>
        </nav>
        <div class="nav-actions">
            <a class="portal-link" href="{{ route('portal') }}">{{ $ar ? 'بوابة العملاء' : 'Client portal' }}</a>
            <form method="post" action="{{ route('locale.update', $ar ? 'en' : 'ar') }}">@csrf<button class="language-switch" type="submit">{{ $ar ? 'EN' : 'العربية' }}</button></form>
            <a class="button button-dark button-small" href="{{ route('contact') }}">{{ $ar ? 'اطلب تقييماً' : 'Request assessment' }}</a>
        </div>
    </div>
</header>
