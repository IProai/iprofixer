<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class OrganizationGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'notes',
        'created_by',
        'updated_by',
    ];

    /** @return HasMany<Organization, $this> */
    public function organizations(): HasMany
    {
        return $this->hasMany(Organization::class, 'group_id');
    }

    /** @return BelongsTo<User, $this> */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
