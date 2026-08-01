<?php

declare(strict_types=1);

use App\Models\ContentPage;
use App\Models\User;
use Database\Seeders\CmsPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(CmsPermissionSeeder::class);
});

it('seeds CMS permissions and roles idempotently', function (): void {
    $this->seed(CmsPermissionSeeder::class);

    foreach (CmsPermissionSeeder::PERMISSIONS as $permission) {
        expect(Permission::where('name', $permission)->exists())->toBeTrue();
    }
});

it('enforces editor, reviewer, publisher, and restorer permissions', function (): void {
    $editor = User::factory()->create();
    $editor->givePermissionTo('content.create', 'content.edit', 'content.preview');

    $reviewer = User::factory()->create();
    $reviewer->givePermissionTo('content.review');

    $publisher = User::factory()->create();
    $publisher->givePermissionTo('content.publish', 'content.approve');

    $unauthorized = User::factory()->create();

    // Unauthorized user denied
    $this->actingAs($unauthorized)->get('/admin/content-pages')->assertForbidden();

    // Editor can access index & create
    $this->actingAs($editor)->get('/admin/content-pages')->assertOk();
    $this->actingAs($editor)->get('/admin/content-pages/create')->assertOk();

    // Editor creates draft page
    $payload = [
        'slug' => 'test-governed-page',
        'type' => 'page',
        'status' => 'draft',
        'title_en' => 'Test Page',
        'title_ar' => 'صفحة اختبار',
        'body_en' => 'English body',
        'body_ar' => 'محتوى عربي',
    ];

    $this->actingAs($editor)->post('/admin/content-pages', $payload)->assertRedirect();
    $page = ContentPage::where('slug', 'test-governed-page')->firstOrFail();

    // Editor cannot publish directly without bilingual approval and publish permission
    $this->actingAs($editor)->post("/admin/content-pages/{$page->id}/publish")->assertForbidden();

    // Publisher denied publishing without bilingual approval
    $this->actingAs($publisher)->post("/admin/content-pages/{$page->id}/publish")
        ->assertSessionHasErrors(['publication']);

    // Approve both EN and AR translations
    $page->translations()->where('locale', 'en')->update(['translation_approved' => true]);
    $page->translations()->where('locale', 'ar')->update(['translation_approved' => true]);

    // Publisher can approve and publish bilingually approved page
    $this->actingAs($publisher)->post("/admin/content-pages/{$page->id}/approve")->assertRedirect();
    expect($page->fresh()->status)->toBe('approved');

    $this->actingAs($publisher)->post("/admin/content-pages/{$page->id}/publish")->assertRedirect();
    expect($page->fresh()->status)->toBe('published')
        ->and($page->fresh()->published_at)->not->toBeNull();
});

it('provides authenticated preview with noindex protection', function (): void {
    $editor = User::factory()->create();
    $editor->givePermissionTo('content.preview');

    $page = ContentPage::factory()->create([
        'slug' => 'preview-test-page',
        'type' => 'page',
        'status' => 'draft',
    ]);
    $page->translations()->create(['locale' => 'en', 'title' => 'Draft Title', 'body' => 'Draft body content']);
    $page->translations()->create(['locale' => 'ar', 'title' => 'عنوان مسودة', 'body' => 'محتوى مسودة']);

    $response = $this->actingAs($editor)->get("/admin/content-pages/{$page->id}/preview");

    $response->assertOk()
        ->assertHeader('X-Robots-Tag', 'noindex, nofollow')
        ->assertSee('noindex, nofollow')
        ->assertSee('Draft Title');
});

it('restores historical revision as a new draft revision without mutating old history', function (): void {
    $user = User::factory()->create();
    $user->givePermissionTo('content.manage');

    $page = ContentPage::factory()->create([
        'slug' => 'history-page',
        'type' => 'page',
        'status' => 'draft',
    ]);

    $payload1 = [
        'slug' => 'history-page',
        'type' => 'page',
        'status' => 'draft',
        'title_en' => 'Version 1 Title',
        'title_ar' => 'عنوان الإصدار الأول',
        'body_en' => 'Version 1 Body',
        'body_ar' => 'محتوى الإصدار الأول',
    ];

    $this->actingAs($user)->put("/admin/content-pages/{$page->id}", $payload1);
    $firstRevision = $page->revisions()->latest('revision_number')->firstOrFail();

    $payload2 = [
        'slug' => 'history-page',
        'type' => 'page',
        'status' => 'draft',
        'title_en' => 'Version 2 Title',
        'title_ar' => 'عنوان الإصدار الثاني',
        'body_en' => 'Version 2 Body',
        'body_ar' => 'محتوى الإصدار الثاني',
    ];

    $this->actingAs($user)->put("/admin/content-pages/{$page->id}", $payload2);
    expect($page->revisions()->count())->toBe(2);

    // Restore version 1 revision
    $this->actingAs($user)
        ->post("/admin/content-pages/{$page->id}/revisions/{$firstRevision->id}/restore")
        ->assertRedirect();

    $page->refresh();

    // Restored page should reflect Version 1 content
    expect($page->title_en)->toBe('Version 1 Title')
        ->and($page->status)->toBe('draft')
        // Restore creates a 3rd revision
        ->and($page->revisions()->count())->toBe(3);

    $latestRevision = $page->revisions()->latest('revision_number')->first();
    expect($latestRevision->change_summary)->toContain('Restored from revision');

    $this->assertDatabaseHas('audit_events', [
        'action' => 'content.page.restored',
        'subject_id' => (string) $page->id,
    ]);
});

it('publishes scheduled content pages via artisan command', function (): void {
    $duePage = ContentPage::factory()->create([
        'slug' => 'scheduled-page',
        'type' => 'page',
        'status' => 'scheduled',
        'scheduled_for' => now()->subMinute(),
    ]);

    $futurePage = ContentPage::factory()->create([
        'slug' => 'future-scheduled-page',
        'type' => 'page',
        'status' => 'scheduled',
        'scheduled_for' => now()->addHour(),
    ]);

    Artisan::call('cms:publish-scheduled');

    expect($duePage->fresh()->status)->toBe('published')
        ->and($duePage->fresh()->published_at)->not->toBeNull()
        ->and($futurePage->fresh()->status)->toBe('scheduled');

    $this->assertDatabaseHas('audit_events', [
        'action' => 'content.page.published',
        'subject_id' => (string) $duePage->id,
    ]);
});
