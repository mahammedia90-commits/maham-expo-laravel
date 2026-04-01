<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractSignature extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'contract_id',
        'party_id',
        'version_number',
        'signature_type',
        'signature_data',
        'signature_hash',
        'verification_method',
        'verification_code',
        'verified_at',
        'ip_address',
        'user_agent',
        'device_fingerprint',
        'geo_location',
        'legal_timestamp',
        'signed_document_url',
        'certificate_url',
        'created_at',
    ];

    protected $casts = [
        'geo_location' => 'array',
        'verified_at' => 'datetime',
        'legal_timestamp' => 'datetime',
        'created_at' => 'datetime',
    ];

    // ──────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function party(): BelongsTo
    {
        return $this->belongsTo(ContractParty::class, 'party_id');
    }
}
