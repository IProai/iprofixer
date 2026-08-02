<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Opportunity extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    protected $fillable = [
        'lead_id',
        'organization_id',
        'property_id',
        'contact_id',
        'assigned_to',
        'title',
        'stage',
        'probability',
        'service_code',
        'estimated_value',
        'currency_code',
        'expected_close_date',
        'next_action',
        'next_action_due_at',
        'loss_reason',
        'loss_notes',
        'notes',
        'won_at',
        'lost_at',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'probability' => 'integer',
            'estimated_value' => 'decimal:2',
            'expected_close_date' => 'date',
            'next_action_due_at' => 'immutable_datetime',
            'won_at' => 'immutable_datetime',
            'lost_at' => 'immutable_datetime',
        ];
    }

    /** @return BelongsTo<Lead, $this> */
    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    /** @return BelongsTo<Organization, $this> */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /** @return BelongsTo<Property, $this> */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /** @return BelongsTo<Contact, $this> */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    /** @return BelongsTo<User, $this> */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function isOpen(): bool
    {
        return ! in_array($this->stage, ['won', 'lost'], true);
    }

    public function isWon(): bool
    {
        return $this->stage === 'won';
    }

    public function isLost(): bool
    {
        return $this->stage === 'lost';
    }

    public static function validStages(): array
    {
        return ['discovery', 'assessment', 'proposal', 'negotiation', 'won', 'lost'];
    }

    public static function lossReasons(): array
    {
        return [
            'price',
            'competitor',
            'budget_frozen',
            'no_decision',
            'lost_contact',
            'scope_mismatch',
            'other',
        ];
    }
}
