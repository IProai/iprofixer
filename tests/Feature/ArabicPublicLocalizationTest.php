<?php

declare(strict_types=1);

it('renders the governed Arabic homepage copy instead of literal legacy wording', function (): void {
    $response = $this->withSession(['locale' => 'ar'])->get('/');

    $response
        ->assertOk()
        ->assertSee('حافظ على جودة التقديم قبل أن تتحول أدوات الضيافة المتضررة إلى تكلفة استبدال غير ضرورية.')
        ->assertSee('خدمات متخصصة لتجديد أدوات ومستلزمات الضيافة في الإمارات والسعودية')
        ->assertSee('تجديد أدوات المائدة')
        ->assertDontSee('الأصول المتعبة')
        ->assertDontSee('العناية بالقطع المجوفة')
        ->assertDontSee('واقع التشغيل');
});

it('normalizes Arabic public interior pages and SEO copy', function (): void {
    $response = $this->withSession(['locale' => 'ar'])->get('/services');

    $response
        ->assertOk()
        ->assertSee('خدمات متخصصة تُحدَّد وفق الحالة الفنية ومتطلبات التشغيل.')
        ->assertSee('نبدأ بفحص الحالة الفعلية قبل تحديد الخدمة المناسبة.')
        ->assertDontSee('مسارات عناية متخصصة مبنية على حالة الأصل وواقع التشغيل.')
        ->assertDontSee('ابدأ بالحالة الفعلية، لا بافتراض مسبق.');
});

it('ships a browser normalizer for Arabic content created by interactive components', function (): void {
    $response = $this->withSession(['locale' => 'ar'])->get('/');

    $response
        ->assertOk()
        ->assertSee('iprofixer-arabic-copy-normalizer', false)
        ->assertSee('MutationObserver', false)
        ->assertSee('قبل المعالجة');
});

it('does not apply the Arabic public-copy normalizer to English pages', function (): void {
    $response = $this->withSession(['locale' => 'en'])->get('/');

    $response
        ->assertOk()
        ->assertDontSee('iprofixer-arabic-copy-normalizer', false)
        ->assertSee('Protect presentation quality before tired assets become an operating cost.');
});
