<?php

declare(strict_types=1);

use App\Models\MediaAsset;
use App\Models\User;
use Database\Seeders\CmsPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->seed(CmsPermissionSeeder::class);
    Storage::fake('public');
});

it('allows authorized users to upload valid media assets with durable metadata and audit evidence', function (): void {
    $uploader = User::factory()->create();
    $uploader->givePermissionTo('media.upload', 'media.view');

    $file = UploadedFile::fake()->image('cutlery-table.jpg', 800, 600);

    $response = $this->actingAs($uploader)->post('/admin/media', [
        'file' => $file,
        'alt_text_en' => 'Restored silver cutlery set on dining table',
        'alt_text_ar' => 'طقم أدوات مائدة فضية مرممة على طاولة الطعام',
        'caption_en' => 'Luxury dining table setup',
        'caption_ar' => 'إعداد طاولة فخمة للضيافة',
        'focal_x' => 0.4,
        'focal_y' => 0.6,
    ]);

    $asset = MediaAsset::query()->sole();

    $response->assertRedirect(route('admin.media.edit', $asset));

    expect($asset->original_name)->toBe('cutlery-table.jpg')
        ->and($asset->extension)->toBe('jpg')
        ->and($asset->mime_type)->toBe('image/jpeg')
        ->and($asset->width)->toBe(800)
        ->and($asset->height)->toBe(600)
        ->and($asset->checksum)->not->toBeEmpty()
        ->and($asset->usage_status)->toBe('approved')
        ->and($asset->focal_x)->toBe(0.4)
        ->and($asset->focal_y)->toBe(0.6);

    Storage::disk('public')->assertExists($asset->path);

    $this->assertDatabaseHas('audit_events', [
        'action' => 'media.asset.uploaded',
        'subject_id' => (string) $asset->id,
        'actor_id' => $uploader->id,
    ]);
});

it('denies media upload to unauthorized users', function (): void {
    $unauthorized = User::factory()->create();
    $file = UploadedFile::fake()->image('unauthorized.jpg');

    $this->actingAs($unauthorized)
        ->post('/admin/media', [
            'file' => $file,
            'alt_text_en' => 'Valid English description',
            'alt_text_ar' => 'وصف عربي صالح',
        ])
        ->assertForbidden();
});

it('rejects SVG file uploads for security governance', function (): void {
    $uploader = User::factory()->create();
    $uploader->givePermissionTo('media.upload');

    $svgFile = UploadedFile::fake()->create('graphic.svg', 10, 'image/svg+xml');

    $response = $this->actingAs($uploader)->post('/admin/media', [
        'file' => $svgFile,
        'alt_text_en' => 'Vector graphic',
        'alt_text_ar' => 'رسم متجهي',
    ]);

    $response->assertSessionHasErrors(['file']);
    expect(MediaAsset::query()->count())->toBe(0);
});

it('rejects executable scripts and double-extension upload attempts', function (): void {
    $uploader = User::factory()->create();
    $uploader->givePermissionTo('media.upload');

    $maliciousFile = UploadedFile::fake()->create('payload.php.jpg', 5, 'image/jpeg');

    $response = $this->actingAs($uploader)->post('/admin/media', [
        'file' => $maliciousFile,
        'alt_text_en' => 'Disguised payload',
        'alt_text_ar' => 'حمولة متخفية',
    ]);

    $response->assertSessionHasErrors(['file']);
    expect(MediaAsset::query()->count())->toBe(0);
});

it('rejects meaningless or generic alt text for non-decorative images', function (): void {
    $uploader = User::factory()->create();
    $uploader->givePermissionTo('media.upload');

    $file = UploadedFile::fake()->image('photo.png');

    $response = $this->actingAs($uploader)->post('/admin/media', [
        'file' => $file,
        'alt_text_en' => 'image.png',
        'alt_text_ar' => 'صورة',
    ]);

    $response->assertSessionHasErrors(['alt_text_en']);
    expect(MediaAsset::query()->count())->toBe(0);
});

it('allows decorative images without requiring alt text', function (): void {
    $uploader = User::factory()->create();
    $uploader->givePermissionTo('media.upload');

    $file = UploadedFile::fake()->image('divider-bg.jpg');

    $response = $this->actingAs($uploader)->post('/admin/media', [
        'file' => $file,
        'is_decorative' => true,
    ]);

    $asset = MediaAsset::query()->sole();

    $response->assertRedirect(route('admin.media.edit', $asset));

    expect($asset->is_decorative)->toBeTrue()
        ->and($asset->usage_status)->toBe('approved');
});

it('validates focal point coordinate boundaries', function (): void {
    $uploader = User::factory()->create();
    $uploader->givePermissionTo('media.upload');

    $file = UploadedFile::fake()->image('focal.jpg');

    $response = $this->actingAs($uploader)->post('/admin/media', [
        'file' => $file,
        'is_decorative' => true,
        'focal_x' => 1.5, // invalid > 1
    ]);

    $response->assertSessionHasErrors(['focal_x']);
});

it('supports archiving and restoring media assets', function (): void {
    $publisher = User::factory()->create();
    $publisher->givePermissionTo('media.archive', 'media.restore', 'media.edit');

    $asset = MediaAsset::factory()->create();

    // Archive
    $this->actingAs($publisher)->delete("/admin/media/{$asset->id}")->assertRedirect();
    expect($asset->fresh()->trashed())->toBeTrue();

    $this->assertDatabaseHas('audit_events', [
        'action' => 'media.asset.archived',
        'subject_id' => (string) $asset->id,
    ]);

    // Restore
    $this->actingAs($publisher)->post("/admin/media/{$asset->id}/restore")->assertRedirect();
    expect($asset->fresh()->trashed())->toBeFalse();

    $this->assertDatabaseHas('audit_events', [
        'action' => 'media.asset.restored',
        'subject_id' => (string) $asset->id,
    ]);
});

it('blocks permanent deletion of referenced media assets and logs audit evidence', function (): void {
    $admin = User::factory()->create();
    $admin->givePermissionTo('media.delete');

    $asset = MediaAsset::factory()->create();

    // Attach asset to proof_items table
    DB::table('proof_items')->insert([
        'slug' => 'proof-sample-1',
        'status' => 'published',
        'evidence_status' => 'verified',
        'title_en' => 'Verified Cutlery Sample',
        'title_ar' => 'عينة أدوات مائدة موثقة',
        'media_asset_id' => $asset->id,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Attempt permanent deletion
    $response = $this->actingAs($admin)->delete("/admin/media/{$asset->id}/force");

    $response->assertSessionHasErrors(['deletion']);
    expect(MediaAsset::query()->where('id', $asset->id)->exists())->toBeTrue();

    $this->assertDatabaseHas('audit_events', [
        'action' => 'media.asset.delete_blocked',
        'subject_id' => (string) $asset->id,
    ]);
});

it('permanently deletes unreferenced media assets and removes disk storage', function (): void {
    $admin = User::factory()->create();
    $admin->givePermissionTo('media.delete');

    $asset = MediaAsset::factory()->create([
        'path' => 'media/unreferenced-file.jpg',
    ]);

    Storage::disk('public')->put($asset->path, 'fake-image-bytes');

    $this->actingAs($admin)->delete("/admin/media/{$asset->id}/force")->assertRedirect();

    expect(MediaAsset::withTrashed()->where('id', $asset->id)->exists())->toBeFalse();
    Storage::disk('public')->assertMissing($asset->path);

    $this->assertDatabaseHas('audit_events', [
        'action' => 'media.asset.deleted',
        'subject_id' => (string) $asset->id,
    ]);
});

it('provides picker JSON endpoint for approved media selection', function (): void {
    $editor = User::factory()->create();
    $editor->givePermissionTo('media.view');

    MediaAsset::factory()->create(['original_name' => 'approved-asset.jpg', 'usage_status' => 'approved']);
    MediaAsset::factory()->create(['original_name' => 'pending-asset.jpg', 'usage_status' => 'pending']);

    $response = $this->actingAs($editor)->get('/admin/media/picker');

    $response->assertOk()
        ->assertJsonCount(1, 'assets')
        ->assertJsonFragment(['name' => 'approved-asset.jpg']);
});
