<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContractParty extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'contract_id',
        'party_type',
        'party_role',
        'user_id',
        'company_name',
        'company_name_ar',
        'contact_name',
        'contact_name_ar',
        'email',
        'phone',
        'national_id',
        'commercial_reg',
        'vat_number',
        'address',
        'address_ar',
        'is_signer',
        'signing_order',
        'has_signed',
        'signed_at',
        'signature_data',
        'signature_ip',
        'signature_device',
        'signing_token',
        'signing_token_expires_at',
        'metadata',
    ];

    protected $casts = [
        'is_signer' => 'boolean',
        'has_signed' => 'boolean',
        'signed_at' => 'datetime',
        'signing_token_expires_at' => 'datetime',
        'metadata' => 'array',
    ];

    // ──────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function signatures(): HasMany
    {
        return $this->hasMany(ContractSignature::class, 'party_id');
    }
}
