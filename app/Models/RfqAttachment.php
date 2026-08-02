<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class RfqAttachment extends Model
{
    use HasUuids;

    protected $fillable = [
        'form_submission_id',
        'disk',
        'path',
        'original_name',
        'mime_type',
        'size_bytes',
        'sha256',
    ];

    protected function casts(): array
    {
        return [
            'size_bytes' => 'integer',
        ];
    }

    /** @return BelongsTo<FormSubmission, $this> */
    public function submission(): BelongsTo
    {
        return $this->belongsTo(FormSubmission::class, 'form_submission_id');
    }
}
