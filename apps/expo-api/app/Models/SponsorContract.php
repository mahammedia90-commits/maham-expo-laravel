<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SponsorContract extends Model
{
    public $incrementing = true;
    protected $keyType = 'int';
    protected $table = 'contracts';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = null;
    protected $fillable = ['contractNumber','bookingId','userId','type','templateId','content','totalAmount','status','signedAt','expiresAt','fileUrl','terms','signatureUrl','signatureData'];

    public function user(): BelongsTo { return $this->belongsTo(User::class, 'userId'); }
    
    protected static function booted() {
        static::addGlobalScope('sponsor', fn($q) => $q->where('type', 'sponsorship'));
    }
}
