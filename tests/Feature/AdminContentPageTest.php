<?php

declare(strict_types=1);

use App\Models\ContentPage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

it('denies CMS access without the governed permission', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/admin/content-pages')
        ->assertForbidden();
});

it('creates and updates bilingual content with audit evidence', function (): void {
    $user = User::factory()->create();
    $permission = Permission::create(['name' => 'content.manage', 'guard_name' => 'web']);
    $user->givePermissionTo($permission);

    $payload = [
        'slug' => 'cutlery-care-guide',
        'type' => 'resource',
        'status' => 'draft',
        'title_en' => 'Cutlery care guide',
        'title_ar' => 'دليل العناية بأدوات المائدة',
        'summary_en' => 'Operational care guidance.',
        'summary_ar' => 'إرشادات تشغيلية للعناية.',
        'body_en' => 'Detailed English guidance.',
        'body_ar' => 'إرشادات عربية تفصيلية.',
        'seo_title_en' => 'Cutlery care guide',
        'seo_title_ar' => 'دليل العناية بأدوات المائدة',
        'seo_description_en' => 'Practical hospitality cutlery care guidance.',
        'seo_description_ar' => 'إرشادات عملية للعناية بأدوات المائدة للضيافة.',
    ];

    $response = $this->actingAs($user)->post('/admin/content-pages', $payload);

    $page = ContentPage::query()->sole();

    $response->assertRedirect(route('admin.content-pages.edit', $page));

    expect($page->translations()->count())->toBe(2)
        ->and($page->fresh()->title_en)->toBe('Cutlery care guide')
        ->and($page->fresh()->title_ar)->toBe('دليل العناية بأدوات المائدة');

    $this->assertDatabaseHas('audit_events', [
        'action' => 'content.page.created',
        'subject_id' => (string) $page->getKey(),
        'actor_id' => $user->getKey(),
    ]);

    $this->actingAs($user)
        ->put("/admin/content-pages/{$page->getKey()}", [
            ...$payload,
            'status' => 'published',
            'title_en' => 'Updated cutlery care guide',
        ])
        ->assertRedirect();

    $page->refresh();

    expect($page->status)->toBe('published')
        ->and($page->published_at)->not->toBeNull()
        ->and($page->title_en)->toBe('Updated cutlery care guide');

    $this->assertDatabaseHas('audit_events', [
        'action' => 'content.page.updated',
        'subject_id' => (string) $page->getKey(),
    ]);
});
