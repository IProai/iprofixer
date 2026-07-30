<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

final class ContentPage extends Model
{
    use HasUuids;
    use SoftDeletes;

    protected $fillable = [
        'slug',
        'type',
        'status',
        'title_en',
        'title_ar',
        'summary_en',
        'summary_ar',
        'body_en',
        'body_ar',
        'seo_title_en',
        'seo_title_ar',
        'seo_description_en',
        'seo_description_ar',
        'schema_payload',
        'published_at',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'schema_payload' => 'array',
            'published_at' => 'immutable_datetime',
        ];
    }

    public function isPublished(): bool
    {
        return $this->status === 'published'
            && $this->published_at !== null
            && $this->published_at->isPast();
    }
}
