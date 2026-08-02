<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

final class CrmActivity extends Model
{
    use HasFactory;
    use HasUuids;

    public const TYPES = ['call', 'meeting', 'email', 'note', 'site_visit', 'other'];

    public const DIRECTIONS = ['inbound', 'outbound'];

    protected $fillable = [
        'subject_type',
        'subject_id',
        'type',
        'direction',
        'title',
        'body',
        'occurred_at',
        'user_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'occurred_at' => 'immutable_datetime',
        ];
    }

    /** @return MorphTo<Model, $this> */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
