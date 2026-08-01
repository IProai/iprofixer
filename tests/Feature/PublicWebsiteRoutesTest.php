<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;

uses(RefreshDatabase::class);

it('serves every revenue-critical public page in English', function (string $uri, string $expected): void {
    /** @var TestResponse $response */
    $response = $this->get($uri);

    $response->assertOk()->assertSee($expected);
})->with([
    ['/', 'Protect presentation quality'],
    ['/services', 'Services'],
    ['/services/cutlery-restoration', 'Cutlery restoration'],
    ['/services/hollowware-care', 'Hollowware care'],
    ['/services/asset-condition-review', 'Asset condition review'],
    ['/services/recurring-care-plans', 'Recurring care plans'],
    ['/industries', 'Industries'],
    ['/industries/hotels-resorts', 'Hotels & resorts'],
    ['/industries/restaurants-groups', 'Restaurants & groups'],
    ['/industries/catering-events', 'Catering & events'],
    ['/industries/procurement-operations', 'Procurement & operations'],
    ['/process', 'Our process'],
    ['/results', 'Proof & results'],
    ['/about', 'About IProFixer'],
    ['/resources', 'Resources'],
    ['/contact', 'Start a consultation'],
    ['/portal', 'Client portal'],
]);

it('switches the public website to Arabic with right-to-left output', function (): void {
    $this->from('/')->post('/locale/ar')->assertRedirect('/');

    $this->get('/')
        ->assertOk()
        ->assertSee('dir="rtl"', false)
        ->assertSee('احمِ جودة الخدمة');
});

it('rejects unsupported public detail slugs', function (): void {
    $this->get('/services/not-a-service')->assertNotFound();
    $this->get('/industries/not-an-industry')->assertNotFound();
});
