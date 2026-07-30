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
        'translation_approved',
    ];

    protected function casts(): array
    {
        return [
            'structured_data' => 'array',
            'translation_approved' => 'boolean',
        ];
    }

    /** @return BelongsTo<ContentPage, $this> */
    public function contentPage(): BelongsTo
    {
        return $this->belongsTo(ContentPage::class);
    }
}
