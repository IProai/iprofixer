<?php

declare(strict_types=1);

it('renders the governed Arabic homepage contract', function (): void {
    $response = $this->withSession(['locale' => 'ar'])->get('/');

    $response
        ->assertOk()
        ->assertSee('lang="ar"', false)
        ->assertSee('dir="rtl"', false)
        ->assertSee('الخدمات')
        ->assertSee('القطاعات')
        ->assertSee('iprofixer-arabic-copy-server-rendered', false);
});

it('renders Arabic public interior pages through the server-side contract', function (): void {
    $response = $this->withSession(['locale' => 'ar'])->get('/services');

    $response
        ->assertOk()
        ->assertSee('lang="ar"', false)
        ->assertSee('dir="rtl"', false)
        ->assertSee('الخدمات')
        ->assertSee('iprofixer-arabic-copy-server-rendered', false);
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

it('uses lightweight server-rendered Arabic normalization without a DOM observer', function (): void {
    $response = $this->withSession(['locale' => 'ar'])->get('/');

    $response
        ->assertOk()
        ->assertSee('iprofixer-arabic-copy-server-rendered', false)
        ->assertDontSee('MutationObserver', false)
        ->assertSee('قبل المعالجة');
});

it('does not apply the Arabic public-copy normalizer to English pages', function (): void {
    $response = $this->withSession(['locale' => 'en'])->get('/');

    $response
        ->assertOk()
        ->assertDontSee('iprofixer-arabic-copy-server-rendered', false)
        ->assertSee('Protect presentation quality before tired assets become an operating cost.');
});
