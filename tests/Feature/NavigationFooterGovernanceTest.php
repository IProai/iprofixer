<?php

declare(strict_types=1);

use App\Models\ContentPage;
use App\Models\NavigationItem;
use App\Models\NavigationMenu;
use App\Models\User;
use App\Services\NavigationService;
use Database\Seeders\CmsPermissionSeeder;
use Database\Seeders\NavigationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed([
        CmsPermissionSeeder::class,
        NavigationSeeder::class,
    ]);
});

it('seeds canonical navigation menus and default items idempotently', function (): void {
    $this->seed(NavigationSeeder::class);

    expect(NavigationMenu::where('location', 'header')->exists())->toBeTrue()
        ->and(NavigationMenu::where('location', 'mobile')->exists())->toBeTrue()
        ->and(NavigationMenu::where('location', 'footer_explore')->exists())->toBeTrue()
        ->and(NavigationMenu::where('location', 'footer_start')->exists())->toBeTrue();

    $headerMenu = NavigationMenu::where('location', 'header')->firstOrFail();
    expect($headerMenu->items()->count())->toBeGreaterThanOrEqual(6);
});

it('allows authorized users to manage navigation items with audit evidence', function (): void {
    $editor = User::factory()->create();
    $editor->givePermissionTo('navigation.edit', 'navigation.view');

    $headerMenu = NavigationMenu::where('location', 'header')->firstOrFail();

    $response = $this->actingAs($editor)->post('/admin/navigation/items', [
        'navigation_menu_id' => $headerMenu->id,
        'label_en' => 'Specialized Route',
        'label_ar' => 'مسار متخصص',
        'destination_type' => 'internal_route',
        'route_name' => 'process',
        'sort_order' => 10,
    ]);

    $response->assertRedirect();

    $item = NavigationItem::where('label_en', 'Specialized Route')->firstOrFail();

    expect($item->label_ar)->toBe('مسار متخصص')
        ->and($item->resolveUrl())->toBe(route('process'));

    $this->assertDatabaseHas('audit_events', [
        'action' => 'navigation.item.created',
        'subject_id' => (string) $item->id,
        'actor_id' => $editor->id,
    ]);
});

it('denies navigation administration to unauthorized users', function (): void {
    $unauthorized = User::factory()->create();

    $this->actingAs($unauthorized)->get('/admin/navigation')->assertForbidden();
});

it('rejects unsafe URL protocols and enforces target_blank rel attribute', function (): void {
    $editor = User::factory()->create();
    $editor->givePermissionTo('navigation.edit');

    $headerMenu = NavigationMenu::where('location', 'header')->firstOrFail();

    // Reject javascript protocol
    $response = $this->actingAs($editor)->post('/admin/navigation/items', [
        'navigation_menu_id' => $headerMenu->id,
        'label_en' => 'Unsafe Link',
        'label_ar' => 'رابط غير آمن',
        'destination_type' => 'external_url',
        'url' => 'javascript:alert(1)',
    ]);

    $response->assertSessionHasErrors(['url']);

    // Valid external URL with target_blank
    $response2 = $this->actingAs($editor)->post('/admin/navigation/items', [
        'navigation_menu_id' => $headerMenu->id,
        'label_en' => 'Safe Partner',
        'label_ar' => 'شريك آمن',
        'destination_type' => 'external_url',
        'url' => 'https://example.com/partner',
        'target_blank' => true,
    ]);

    $response2->assertRedirect();

    $safeItem = NavigationItem::where('label_en', 'Safe Partner')->firstOrFail();
    expect($safeItem->target_blank)->toBeTrue()
        ->and($safeItem->rel)->toBe('noopener noreferrer');
});

it('prevents circular parent relationships in navigation items', function (): void {
    $editor = User::factory()->create();
    $editor->givePermissionTo('navigation.edit');

    $parent = NavigationItem::factory()->create(['label_en' => 'Parent Item']);
    $child = NavigationItem::factory()->create(['parent_id' => $parent->id, 'label_en' => 'Child Item']);

    // Attempting to set parent's parent as child (circular)
    $response = $this->actingAs($editor)->put("/admin/navigation/items/{$parent->id}", [
        'parent_id' => $child->id,
        'label_en' => 'Parent Item',
        'label_ar' => 'عنصر أب',
        'destination_type' => 'internal_route',
        'route_name' => 'services',
    ]);

    $response->assertSessionHasErrors(['parent_id']);
});

it('excludes draft or archived content pages from public navigation', function (): void {
    $headerMenu = NavigationMenu::where('location', 'header')->firstOrFail();

    $draftPage = ContentPage::factory()->create(['status' => 'draft', 'slug' => 'draft-services-page']);
    $publishedPage = ContentPage::factory()->create(['status' => 'published', 'published_at' => now()->subDay(), 'slug' => 'published-services-page']);

    $draftItem = NavigationItem::factory()->create([
        'navigation_menu_id' => $headerMenu->id,
        'destination_type' => 'content_page',
        'content_page_id' => $draftPage->id,
        'label_en' => 'Draft Page Link',
        'label_ar' => 'رابط مسودة',
        'is_active' => true,
    ]);

    $publishedItem = NavigationItem::factory()->create([
        'navigation_menu_id' => $headerMenu->id,
        'destination_type' => 'content_page',
        'content_page_id' => $publishedPage->id,
        'label_en' => 'Published Page Link',
        'label_ar' => 'رابط منشور',
        'is_active' => true,
    ]);

    $navService = app(NavigationService::class);
    $publicMenu = $navService->getPublicMenu('header', 'en');

    $labels = array_column($publicMenu, 'label');

    expect($labels)->toContain('Published Page Link')
        ->and($labels)->not->toContain('Draft Page Link');
});

it('renders dynamic public header and footer with bilingual active state and RTL support', function (): void {
    $this->get('/services')
        ->assertOk()
        ->assertSee('Services')
        ->assertSee('aria-current="page"', false);

    // Test Arabic RTL rendering
    $this->from('/services')->post('/locale/ar')->assertRedirect('/services');

    $this->get('/services')
        ->assertOk()
        ->assertSee('dir="rtl"', false)
        ->assertSee('الخدمات')
        ->assertSee('القطاعات');
});

it('invalidates navigation cache upon menu updates', function (): void {
    $editor = User::factory()->create();
    $editor->givePermissionTo('navigation.edit');

    $navService = app(NavigationService::class);
    $initialMenu = $navService->getPublicMenu('header', 'en');

    $item = NavigationItem::where('label_en', 'Services')->firstOrFail();

    $this->actingAs($editor)->put("/admin/navigation/items/{$item->id}", [
        'label_en' => 'Renamed Services',
        'label_ar' => 'الخدمات المعدلة',
        'destination_type' => 'internal_route',
        'route_name' => 'services',
    ])->assertRedirect();

    $updatedMenu = $navService->getPublicMenu('header', 'en');
    $labels = array_column($updatedMenu, 'label');

    expect($labels)->toContain('Renamed Services')
        ->and($labels)->not->toContain('Services');
});
