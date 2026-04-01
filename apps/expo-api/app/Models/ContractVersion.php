<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractVersion extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'contract_id',
        'version_number',
        'content_snapshot',
        'content_html',
        'content_html_ar',
        'terms_snapshot',
        'change_summary',
        'change_summary_ar',
        'changed_fields',
        'diff_data',
        'pdf_url',
        'pdf_url_ar',
        'created_by',
        'created_at',
    ];

    protected $casts = [
        'content_snapshot' => 'array',
        'terms_snapshot' => 'array',
        'changed_fields' => 'array',
        'diff_data' => 'array',
        'created_at' => 'datetime',
    ];

    // ──────────────────────────────────────────
    // Relationships
    // ──────────────────────────────────────────

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
