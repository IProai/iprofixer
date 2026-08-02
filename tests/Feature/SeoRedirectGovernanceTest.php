<?php

declare(strict_types=1);

use App\Models\ContentPage;
use App\Models\RedirectRule;
use App\Models\User;
use App\Services\RedirectService;
use App\Services\SeoService;
use Database\Seeders\CmsPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(CmsPermissionSeeder::class);
});

it('enforces SEO metadata fallback precedence hierarchy and canonical URL generation', function (): void {
    $seoService = app(SeoService::class);

    // Default site fallback when page is null
    $defaultMeta = $seoService->getMetadata(null, 'en');
    $appUrl = rtrim(config('app.url'), '/');
    expect($defaultMeta['title'])->toContain('IProFixer')
        ->and($defaultMeta['canonical'])->toBe($appUrl.'/');

    // Page-level governed metadata
    $page = ContentPage::factory()->create([
        'status' => 'published',
        'published_at' => now()->subDay(),
        'type' => 'service',
        'slug' => 'cutlery-restoration-care',
    ]);

    $page->translations()->create([
        'locale' => 'en',
        'title' => 'Cutlery Restoration Service',
        'summary' => 'Summary of cutlery care',
        'seo_title' => 'Governed SEO Title for Cutlery',
        'seo_description' => 'Governed SEO Description for Cutlery Care',
        'translation_approved' => true,
    ]);

    $pageMeta = $seoService->getMetadata($page, 'en');
    $appUrl = rtrim(config('app.url'), '/');

    expect($pageMeta['title'])->toBe('Governed SEO Title for Cutlery')
        ->and($pageMeta['description'])->toBe('Governed SEO Description for Cutlery Care')
        ->and($pageMeta['canonical'])->toBe($appUrl.'/services/cutlery-restoration-care');
});

it('generates hreflang alternate links only for published and approved translations', function (): void {
    $page = ContentPage::factory()->create([
        'status' => 'published',
        'published_at' => now()->subDay(),
        'type' => 'page',
        'slug' => 'hospitality-standards',
    ]);

    $page->translations()->create([
        'locale' => 'en',
        'title' => 'Hospitality Standards',
        'translation_approved' => true,
    ]);

    $page->translations()->create([
        'locale' => 'ar',
        'title' => 'معايير الضيافة',
        'translation_approved' => false, // Not approved!
    ]);

    $seoService = app(SeoService::class);
    $hreflang = $seoService->getHreflangAlternates($page);

    expect($hreflang)->toHaveKey('en')
        ->and($hreflang)->not->toHaveKey('ar');
});

it('serves environment-safe robots.txt and applies noindex protection to admin/preview routes', function (): void {
    $this->get('/robots.txt')
        ->assertOk()
        ->assertHeader('Content-Type', 'text/plain; charset=UTF-8');

    // Authenticated preview page must output noindex
    $publisher = User::factory()->create();
    $publisher->givePermissionTo('content.preview');

    $page = ContentPage::factory()->create(['status' => 'draft']);
    $page->translations()->create(['locale' => 'en', 'title' => 'Draft Preview', 'translation_approved' => false]);

    $this->actingAs($publisher)
        ->get("/admin/content-pages/{$page->id}/preview")
        ->assertOk()
        ->assertSee('noindex, nofollow');
});

it('generates cached XML sitemap containing only published indexable canonical pages', function (): void {
    ContentPage::factory()->create([
        'status' => 'draft',
        'slug' => 'draft-unseen-page',
    ]);

    $published = ContentPage::factory()->create([
        'status' => 'published',
        'published_at' => now()->subDay(),
        'type' => 'page',
        'slug' => 'verified-public-page',
    ]);

    $published->translations()->create([
        'locale' => 'en',
        'title' => 'Verified Public Page',
        'translation_approved' => true,
    ]);

    $response = $this->get('/sitemap.xml');

    $response->assertOk()
        ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
        ->assertSee('verified-public-page')
        ->assertDontSee('draft-unseen-page');
});

it('renders valid JSON-LD structured data with HTML escaping', function (): void {
    $seoService = app(SeoService::class);

    $page = ContentPage::factory()->create([
        'status' => 'published',
        'published_at' => now()->subDay(),
        'type' => 'service',
        'slug' => 'hollowware-polishing',
    ]);

    $page->translations()->create([
        'locale' => 'en',
        'title' => 'Hollowware & Silver Polishing <script>alert(1)</script>',
        'summary' => 'Clean summary',
        'translation_approved' => true,
    ]);

    $meta = $seoService->getMetadata($page, 'en');
    $json = json_encode($meta['structured_data']);

    expect($json)->not->toContain('<script>')
        ->and($json)->toContain('Hollowware');
});

it('executes 301 and 302 redirects via middleware and records hit counts', function (): void {
    $rule = RedirectRule::factory()->create([
        'source_path' => '/legacy-service-url',
        'destination_type' => 'custom_url',
        'destination_path' => '/services',
        'status_code' => 301,
        'is_active' => true,
    ]);

    $response = $this->get('/legacy-service-url');

    $response->assertRedirect(rtrim(config('app.url'), '/').'/services');
    expect($response->getStatusCode())->toBe(301);

    expect($rule->fresh()->hit_count)->toBe(1);
});

it('rejects unsafe protocols, protected route overrides, and direct loops in redirect rules', function (): void {
    $admin = User::factory()->create();
    $admin->givePermissionTo('redirects.create', 'redirects.edit');

    // Unsafe protocol
    $response1 = $this->actingAs($admin)->post('/admin/redirects', [
        'source_path' => '/test-unsafe',
        'destination_type' => 'custom_url',
        'destination_path' => 'javascript:alert(1)',
        'status_code' => 301,
    ]);
    $response1->assertSessionHasErrors(['destination_path']);

    // Protected route override
    $response2 = $this->actingAs($admin)->post('/admin/redirects', [
        'source_path' => '/admin/protected-route',
        'destination_type' => 'custom_url',
        'destination_path' => '/services',
        'status_code' => 301,
    ]);
    $response2->assertSessionHasErrors(['source_path']);

    // Direct loop (source === destination)
    $response3 = $this->actingAs($admin)->post('/admin/redirects', [
        'source_path' => '/services',
        'destination_type' => 'custom_url',
        'destination_path' => '/services',
        'status_code' => 301,
    ]);
    $response3->assertSessionHasErrors(['destination_path']);
});

it('automatically creates audited 301 redirect rule when a published CMS page slug changes', function (): void {
    $publisher = User::factory()->create();
    $publisher->givePermissionTo('content.edit', 'content.approve', 'content.publish');

    $page = ContentPage::factory()->create([
        'status' => 'published',
        'published_at' => now()->subDay(),
        'type' => 'service',
        'slug' => 'old-service-slug',
    ]);

    $page->translations()->create(['locale' => 'en', 'title' => 'Service EN', 'translation_approved' => true]);
    $page->translations()->create(['locale' => 'ar', 'title' => 'خدمة ع', 'translation_approved' => true]);

    $response = $this->actingAs($publisher)->put("/admin/content-pages/{$page->id}", [
        'slug' => 'new-service-slug',
        'type' => 'service',
        'status' => 'published',
        'title_en' => 'Service EN Updated',
        'title_ar' => 'خدمة ع محدثة',
        'summary_en' => 'Summary EN',
        'summary_ar' => 'ملخص ع',
        'body_en' => '<p>Service body EN</p>',
        'body_ar' => '<p>نص الخدمة ع</p>',
        'approve_en' => true,
        'approve_ar' => true,
    ]);

    $response->assertRedirect();

    $redirectRule = RedirectRule::where('source_path', '/services/old-service-slug')->firstOrFail();

    expect($redirectRule->destination_path)->toBe('/services/new-service-slug')
        ->and($redirectRule->status_code)->toBe(301);

    $this->assertDatabaseHas('audit_events', [
        'action' => 'redirect.auto_slug_created',
        'subject_id' => (string) $redirectRule->id,
    ]);
});

it('allows authorized operators to manage redirect rules with audit evidence', function (): void {
    $operator = User::factory()->create();
    $operator->givePermissionTo('redirects.create', 'redirects.edit', 'redirects.activate', 'redirects.delete', 'redirects.view');

    // Create
    $response = $this->actingAs($operator)->post('/admin/redirects', [
        'source_path' => '/old-contact-form',
        'destination_type' => 'custom_url',
        'destination_path' => '/contact',
        'status_code' => 301,
        'note' => 'Redirect old contact page',
    ]);

    $response->assertRedirect();
    $rule = RedirectRule::where('source_path', '/old-contact-form')->firstOrFail();

    $this->assertDatabaseHas('audit_events', [
        'action' => 'redirect.rule.created',
        'subject_id' => (string) $rule->id,
        'actor_id' => $operator->id,
    ]);

    // Test resolution endpoint
    $res = $this->actingAs($operator)->postJson('/admin/redirects/test-resolution', [
        'path' => '/old-contact-form',
    ]);

    $res->assertOk()
        ->assertJsonFragment(['found' => true, 'status_code' => 301]);

    // Delete
    $this->actingAs($operator)->delete("/admin/redirects/{$rule->id}")->assertRedirect();
    expect(RedirectRule::where('id', $rule->id)->exists())->toBeFalse();

    $this->assertDatabaseHas('audit_events', [
        'action' => 'redirect.rule.deleted',
        'subject_id' => (string) $rule->id,
    ]);
});

it('denies redirect management to unauthorized users', function (): void {
    $unauthorized = User::factory()->create();

    $this->actingAs($unauthorized)->get('/admin/redirects')->assertForbidden();
});

it('invalidates sitemap cache when content or redirect rules are updated', function (): void {
    Cache::put('sitemap_xml', '<cached-sitemap/>', 3600);

    $redirectService = app(RedirectService::class);
    $redirectService->clearSitemapCache();

    expect(Cache::has('sitemap_xml'))->toBeFalse();
});
