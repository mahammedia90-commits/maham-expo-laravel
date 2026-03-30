<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Sponsor extends Model
{
    public $incrementing = true;
    protected $keyType = 'int';
    protected $table = 'sponsorships';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = null;
    protected $fillable = ['userId', 'eventId', 'packageId', 'status', 'amount', 'benefits', 'contractId'];
    protected $casts = ['benefits' => 'array', 'amount' => 'decimal:2'];
    public function event(): BelongsTo { return $this->belongsTo(Event::class, 'eventId'); }
    public function package(): BelongsTo { return $this->belongsTo(SponsorPackage::class, 'packageId'); }
}
