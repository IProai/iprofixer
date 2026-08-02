<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ContentTranslation extends Model
{
    protected $fillable = [
        'locale',
        'title',
        'navigation_label',
        'summary',
        'body',
        'seo_title',
        'seo_description',
        'canonical_url',
        'structured_data',
        'og_title',
        'og_description',
        'og_image_id',
        'is_noindex',
        'is_nofollow',
        'translation_approved',
    ];

    protected function casts(): array
    {
        return [
            'structured_data' => 'array',
            'translation_approved' => 'boolean',
            'is_noindex' => 'boolean',
            'is_nofollow' => 'boolean',
        ];
    }

    /** @return BelongsTo<ContentPage, $this> */
    public function contentPage(): BelongsTo
    {
        return $this->belongsTo(ContentPage::class);
    }

    /** @return BelongsTo<MediaAsset, $this> */
    public function ogImage(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class, 'og_image_id');
    }
}
