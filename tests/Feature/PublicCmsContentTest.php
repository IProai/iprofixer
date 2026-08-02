<?php

declare(strict_types=1);

use App\Models\ContentPage;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders only currently published CMS content on a governed public page', function (): void {
    $published = ContentPage::query()->create([
        'slug' => 'about',
        'type' => 'page',
        'status' => 'published',
        'published_at' => now()->subMinute(),
    ]);

    $published->translations()->createMany([
        [
            'locale' => 'en',
            'title' => 'Verified company profile',
            'summary' => 'Approved English summary.',
            'body' => 'Approved English body.',
            'seo_title' => 'Verified IProFixer profile',
            'seo_description' => 'Approved English SEO description.',
            'translation_approved' => true,
        ],
        [
            'locale' => 'ar',
            'title' => 'نبذة موثقة عن الشركة',
            'summary' => 'ملخص عربي معتمد.',
            'body' => 'محتوى عربي معتمد.',
            'seo_title' => 'نبذة آي برو فيكسر الموثقة',
            'seo_description' => 'وصف عربي معتمد.',
            'translation_approved' => true,
        ],
    ]);

    $this->get('/about')
        ->assertOk()
        ->assertSee('Verified company profile')
        ->assertSee('Approved English body.')
        ->assertSee('Verified IProFixer profile');

    $this->post('/locale/ar');

    $this->get('/about')
        ->assertOk()
        ->assertSee('dir="rtl"', false)
        ->assertSee('نبذة موثقة عن الشركة')
        ->assertSee('محتوى عربي معتمد.');
});

it('does not expose draft CMS content publicly', function (): void {
    $draft = ContentPage::query()->create([
        'slug' => 'about',
        'type' => 'page',
        'status' => 'draft',
    ]);

    $draft->translations()->create([
        'locale' => 'en',
        'title' => 'Private draft title',
        'summary' => 'Private draft summary.',
        'body' => 'Private draft body.',
        'translation_approved' => false,
    ]);

    $this->get('/about')
        ->assertOk()
        ->assertSee('About IProFixer')
        ->assertDontSee('Private draft title');
});
