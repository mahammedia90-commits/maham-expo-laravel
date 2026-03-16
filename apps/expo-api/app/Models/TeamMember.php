<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeamMember extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'owner_id',
        'owner_type',
        'member_type_id',
        'name',
        'email',
        'phone',
        'id_number',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $hidden = [
        'id_number',
    ];

    /* ======== Relationships ======== */

    public function memberType(): BelongsTo
    {
        return $this->belongsTo(MemberType::class);
    }

    /* ======== Scopes ======== */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForOwner($query, string $ownerId, string $ownerType)
    {
        return $query->where('owner_id', $ownerId)->where('owner_type', $ownerType);
    }

    public function scopeOfType($query, string $memberTypeId)
    {
        return $query->where('member_type_id', $memberTypeId);
    }
}
