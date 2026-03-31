<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Space extends Model
{
    public $incrementing = true;
    protected $keyType = 'int';
    protected $table = 'spaces';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = null;
    protected $fillable = ['venueId','eventId','name','nameAr','zone','type','size','price','status','positionX','positionY','width','height','amenities','hallId','floorNumber','occupantName','occupantId','bookingId','description','electricityIncluded','wifiIncluded'];
    protected $casts = ['price' => 'decimal:2', 'electricityIncluded' => 'boolean', 'wifiIncluded' => 'boolean'];
    
    public function event(): BelongsTo { return $this->belongsTo(Event::class, 'eventId'); }
    public function scopeAvailable($q) { return $q->where('status', 'available'); }
}
