<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class ContentPage extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'slug',
        'type',
        'status',
        'published_at',
        'scheduled_for',
        'created_by',
        'updated_by',
    ];

    protected $with = ['translations'];

    protected function casts(): array
    {
        return [
            'published_at' => 'immutable_datetime',
            'scheduled_for' => 'immutable_datetime',
        ];
    }

    /** @return HasMany<ContentTranslation, $this> */
    public function translations(): HasMany
    {
        return $this->hasMany(ContentTranslation::class);
    }

    /** @return HasMany<ContentPageRevision, $this> */
    public function revisions(): HasMany
    {
        return $this->hasMany(ContentPageRevision::class)->latest('revision_number');
    }

    public function translation(string $locale): ?ContentTranslation
    {
        return $this->translations->firstWhere('locale', $locale);
    }

    public function isPublished(): bool
    {
        return $this->status === 'published'
            && $this->published_at !== null
            && $this->published_at->isPast();
    }

    public function getTitleEnAttribute(): ?string
    {
        return $this->translation('en')?->title;
    }

    public function getTitleArAttribute(): ?string
    {
        return $this->translation('ar')?->title;
    }

    public function getSummaryEnAttribute(): ?string
    {
        return $this->translation('en')?->summary;
    }

    public function getSummaryArAttribute(): ?string
    {
        return $this->translation('ar')?->summary;
    }

    public function getBodyEnAttribute(): ?string
    {
        return $this->translation('en')?->body;
    }

    public function getBodyArAttribute(): ?string
    {
        return $this->translation('ar')?->body;
    }

    public function getSeoTitleEnAttribute(): ?string
    {
        return $this->translation('en')?->seo_title;
    }

    public function getSeoTitleArAttribute(): ?string
    {
        return $this->translation('ar')?->seo_title;
    }

    public function getSeoDescriptionEnAttribute(): ?string
    {
        return $this->translation('en')?->seo_description;
    }

    public function getSeoDescriptionArAttribute(): ?string
    {
        return $this->translation('ar')?->seo_description;
    }
}
