<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Lead extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    protected $fillable = [
        'form_submission_id',
        'organization_id',
        'property_id',
        'contact_id',
        'assigned_to',
        'status',
        'source',
        'source_detail',
        'service_code',
        'property_type',
        'urgency',
        'estimated_quantity',
        'budget_indication',
        'qualification_notes',
        'disqualification_reason',
        'qualified_at',
        'disqualified_at',
        'converted_at',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'estimated_quantity' => 'integer',
            'qualified_at' => 'immutable_datetime',
            'disqualified_at' => 'immutable_datetime',
            'converted_at' => 'immutable_datetime',
        ];
    }

    /** @return BelongsTo<FormSubmission, $this> */
    public function formSubmission(): BelongsTo
    {
        return $this->belongsTo(FormSubmission::class);
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

    /** @return HasOne<Opportunity, $this> */
    public function opportunity(): HasOne
    {
        return $this->hasOne(Opportunity::class);
    }

    public function isNew(): bool
    {
        return $this->status === 'new';
    }

    public function isQualified(): bool
    {
        return $this->status === 'qualified';
    }

    public function isConverted(): bool
    {
        return $this->status === 'converted';
    }

    public function isDisqualified(): bool
    {
        return $this->status === 'disqualified';
    }
}
