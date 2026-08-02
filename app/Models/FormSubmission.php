<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class FormSubmission extends Model
{
    use HasUuids;

    protected $fillable = [
        'reference',
        'type',
        'status',
        'assigned_to',
        'locale',
        'source_page',
        'campaign_source',
        'campaign_medium',
        'campaign_name',
        'contact_name',
        'organization_name',
        'email',
        'phone',
        'service_code',
        'property_type',
        'urgency',
        'estimated_quantity',
        'message',
        'payload',
        'correlation_id',
        'ip_hash',
        'submitted_at',
        'last_contacted_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'estimated_quantity' => 'integer',
            'submitted_at' => 'immutable_datetime',
            'last_contacted_at' => 'immutable_datetime',
        ];
    }

    /** @return HasMany<ConsentRecord, $this> */
    public function consents(): HasMany
    {
        return $this->hasMany(ConsentRecord::class);
    }

    /** @return HasMany<RfqAttachment, $this> */
    public function attachments(): HasMany
    {
        return $this->hasMany(RfqAttachment::class);
    }

    /** @return HasMany<RfqNote, $this> */
    public function notes(): HasMany
    {
        return $this->hasMany(RfqNote::class)->latest();
    }

    /** @return BelongsTo<User, $this> */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
