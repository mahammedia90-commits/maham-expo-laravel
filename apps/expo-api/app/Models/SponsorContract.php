<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SponsorContract extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
    public $incrementing = true;
    protected $keyType = 'int';
    protected $table = 'contracts';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    protected $fillable = ['contractNumber','userId','type','content','totalAmount','status','signedAt','expiresAt','fileUrl','terms','eventId','titleAr','titleEn','pdfUrl'];

    public function user(): BelongsTo { return $this->belongsTo(User::class, 'userId'); }
    public function event(): BelongsTo { return $this->belongsTo(Event::class, 'eventId'); }
    
    protected static function booted() {
        static::addGlobalScope('sponsor', fn($q) => $q->where('type', 'sponsorship'));
    }
}
