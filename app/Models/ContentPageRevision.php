<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ContentPageRevision extends Model
{
    use HasUuids;

    protected $fillable = [
        'content_page_id',
        'created_by',
        'revision_number',
        'status',
        'snapshot',
        'change_summary',
    ];

    protected function casts(): array
    {
        return [
            'snapshot' => 'array',
            'revision_number' => 'integer',
        ];
    }

    /** @return BelongsTo<ContentPage, $this> */
    public function page(): BelongsTo
    {
        return $this->belongsTo(ContentPage::class, 'content_page_id');
    }

    /** @return BelongsTo<User, $this> */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
