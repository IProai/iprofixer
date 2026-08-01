@php
    $locale = app()->getLocale();
    $ar = $locale === 'ar';
    $navService = app(\App\Services\NavigationService::class);
    $exploreMenu = $navService->getPublicMenu('footer_explore', $locale);
    $startMenu = $navService->getPublicMenu('footer_start', $locale);
@endphp
<footer class="site-footer">
    <div class="container footer-grid">
        <div>
            <a class="brand footer-brand" href="{{ route('home') }}"><span class="brand-symbol">I</span><span>IProFixer</span></a>
            <p>{{ $ar ? 'عناية متخصصة بأصول الضيافة عبر مسار واضح من التقييم إلى التسليم.' : 'Specialist hospitality asset care through a clear route from assessment to return.' }}</p>
        </div>
        <div>
            <strong>{{ $ar ? 'استكشف' : 'Explore' }}</strong>
            @if (count($exploreMenu) > 0)
                @foreach ($exploreMenu as $item)
                    <a href="{{ $item['url'] }}" @if(!empty($item['target_blank'])) target="_blank" @endif @if(!empty($item['rel'])) rel="{{ $item['rel'] }}" @endif>{{ $item['label'] }}</a>
                @endforeach
            @else
                <a href="{{ route('services') }}">{{ $ar ? 'الخدمات' : 'Services' }}</a>
                <a href="{{ route('industries') }}">{{ $ar ? 'القطاعات' : 'Industries' }}</a>
                <a href="{{ route('process') }}">{{ $ar ? 'آلية العمل' : 'Process' }}</a>
                <a href="{{ route('results') }}">{{ $ar ? 'الإثبات والنتائج' : 'Proof & Results' }}</a>
            @endif
        </div>
        <div>
            <strong>{{ $ar ? 'ابدأ' : 'Start' }}</strong>
            @if (count($startMenu) > 0)
                @foreach ($startMenu as $item)
                    <a href="{{ $item['url'] }}" @if(!empty($item['target_blank'])) target="_blank" @endif @if(!empty($item['rel'])) rel="{{ $item['rel'] }}" @endif>{{ $item['label'] }}</a>
                @endforeach
            @else
                <a href="{{ route('resources') }}">{{ $ar ? 'الموارد' : 'Resources' }}</a>
                <a href="{{ route('portal') }}">{{ $ar ? 'بوابة العملاء' : 'Client portal' }}</a>
                <a href="{{ route('contact') }}">{{ $ar ? 'طلب تقييم' : 'Request assessment' }}</a>
            @endif
        </div>
    </div>
    <div class="container footer-bottom">
        <span>© {{ date('Y') }} IProFixer</span>
        <span>{{ $ar ? 'لا تُعرض شعارات أو نتائج أو شهادات غير موثقة.' : 'No unverified logos, results or testimonials are displayed.' }}</span>
    </div>
</footer>
