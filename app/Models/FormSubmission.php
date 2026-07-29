<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class FormSubmission extends Model
{
    use HasUuids;

    protected $fillable = [
        'type',
        'status',
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
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'estimated_quantity' => 'integer',
            'submitted_at' => 'immutable_datetime',
        ];
    }

    public function consents(): HasMany
    {
        return $this->hasMany(ConsentRecord::class);
    }
}
