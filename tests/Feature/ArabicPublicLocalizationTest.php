<?php

declare(strict_types=1);

it('renders the governed Arabic homepage copy', function (): void {
    $response = $this->withSession(['locale' => 'ar'])->get('/');

    $response
        ->assertOk()
        ->assertSee('حافظ على جودة التقديم قبل أن تتحول أدوات الضيافة المتضررة إلى تكلفة استبدال غير ضرورية.')
        ->assertSee('خدمات متخصصة لتجديد أدوات ومستلزمات الضيافة في الإمارات والسعودية')
        ->assertSee('تجديد أدوات المائدة');
});

it('normalizes Arabic public interior pages and SEO copy', function (): void {
    $response = $this->withSession(['locale' => 'ar'])->get('/services');

    $response
        ->assertOk()
        ->assertSee('خدمات متخصصة تُحدَّد وفق الحالة الفنية ومتطلبات التشغيل.')
        ->assertSee('نبدأ بفحص الحالة الفعلية قبل تحديد الخدمة المناسبة.');
});

it('keeps prohibited literal phrases governed by the translation memory', function (): void {
    $phrases = config('arabic-copy.phrases');

    expect($phrases)
        ->toHaveKey('احمِ جودة الخدمة قبل أن تتحول الأصول المتعبة إلى تكلفة تشغيلية.')
        ->toHaveKey('العناية بالقطع المجوفة')
        ->toHaveKey('كل قطاع يملك وتيرة استخدام ونافذة تسليم مختلفة.')
        ->and($phrases['العناية بالقطع المجوفة'])
        ->toBe('تجديد أواني وقطع التقديم المعدنية');
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
