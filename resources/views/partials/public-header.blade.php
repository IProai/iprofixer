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
        <a class="brand" href="{{ route('home') }}" aria-label="{{ $ar ? 'الصفحة الرئيسية لآي برو فيكسر' : 'IProFixer home' }}">
            <span class="brand-symbol" aria-hidden="true">I</span><span>IProFixer</span>
        </a>
        <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="site-nav" aria-label="{{ $ar ? 'فتح القائمة الرئيسية' : 'Open main menu' }}">
            <span>{{ $ar ? 'القائمة' : 'Menu' }}</span>
        </button>
        <nav id="site-nav" class="site-nav" aria-label="{{ $ar ? 'التنقل الرئيسي' : 'Primary navigation' }}">
            <a href="{{ route('services') }}" @class(['is-current' => request()->routeIs('services*')]) @if(request()->routeIs('services*')) aria-current="page" @endif>{{ $ar ? 'الخدمات' : 'Services' }}</a>
            <a href="{{ route('industries') }}" @class(['is-current' => request()->routeIs('industries*')]) @if(request()->routeIs('industries*')) aria-current="page" @endif>{{ $ar ? 'القطاعات' : 'Industries' }}</a>
            <a href="{{ route('process') }}" @class(['is-current' => request()->routeIs('process')]) @if(request()->routeIs('process')) aria-current="page" @endif>{{ $ar ? 'آلية العمل' : 'Process' }}</a>
            <a href="{{ route('results') }}" @class(['is-current' => request()->routeIs('results')]) @if(request()->routeIs('results')) aria-current="page" @endif>{{ $ar ? 'الإثبات والنتائج' : 'Proof & Results' }}</a>
            <a href="{{ route('about') }}" @class(['is-current' => request()->routeIs('about')]) @if(request()->routeIs('about')) aria-current="page" @endif>{{ $ar ? 'عن الشركة' : 'About' }}</a>
            <a href="{{ route('resources') }}" @class(['is-current' => request()->routeIs('resources')]) @if(request()->routeIs('resources')) aria-current="page" @endif>{{ $ar ? 'الموارد' : 'Resources' }}</a>
            <div class="mobile-nav-actions">
                <a href="{{ route('portal') }}">{{ $ar ? 'بوابة العملاء' : 'Client portal' }}</a>
                <a class="button button-gold" href="{{ route('contact') }}">{{ $ar ? 'اطلب تقييماً' : 'Request assessment' }}</a>
            </div>
        </nav>
        <div class="nav-actions">
            <a class="portal-link" href="{{ route('portal') }}">{{ $ar ? 'بوابة العملاء' : 'Client portal' }}</a>
            <form method="post" action="{{ route('locale.update', $ar ? 'en' : 'ar') }}">@csrf<button class="language-switch" type="submit" aria-label="{{ $ar ? 'Switch to English' : 'التبديل إلى العربية' }}">{{ $ar ? 'EN' : 'العربية' }}</button></form>
            <a class="button button-dark button-small" href="{{ route('contact') }}">{{ $ar ? 'اطلب تقييماً' : 'Request assessment' }}</a>
        </div>
    </div>
</header>
<style>
.site-nav a.is-current::after{transform:scaleX(1)}
.site-nav a.is-current{color:var(--ink);font-weight:800}
.mobile-nav-actions{display:none}
body.nav-open{overflow:hidden}
@media(max-width:1050px){.site-nav{max-height:calc(100vh - 70px);overflow:auto}.mobile-nav-actions{display:grid;width:100%;gap:.75rem;border-top:1px solid var(--line);margin-top:.5rem;padding-top:1.25rem}.mobile-nav-actions>a:not(.button){font-weight:800}.mobile-nav-actions .button{width:100%}}
</style>
