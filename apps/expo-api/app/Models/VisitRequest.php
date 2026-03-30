<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisitRequest extends Model
{
    public $incrementing = true;
    protected $keyType = 'int';
    protected $table = 'bookings';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    protected $fillable = ['orderNumber','userId','unitId','eventId','status','totalPrice','vatAmount','services','approvedBy','approvedAt','rejectionReason','notes'];
    protected $casts = ['services' => 'array'];
    
    public function event(): BelongsTo { return $this->belongsTo(Event::class, 'eventId'); }
}
