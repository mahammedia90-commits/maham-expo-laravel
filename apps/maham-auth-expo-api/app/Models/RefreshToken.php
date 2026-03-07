<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class RefreshToken extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'token',
        'device_name',
        'device_type',
        'ip_address',
        'expires_at',
        'last_used_at',
        'revoked',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'last_used_at' => 'datetime',
        'revoked' => 'boolean',
    ];

    protected $hidden = [
        'token',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isValid(): bool
    {
        return !$this->revoked && $this->expires_at->isFuture();
    }

    public function revoke(): void
    {
        $this->update(['revoked' => true]);
    }
}
