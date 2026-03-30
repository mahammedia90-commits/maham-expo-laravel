<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Space extends Model
{
    public $incrementing = true;
    protected $keyType = 'int';
    protected $table = 'units';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = null;
    protected $fillable = ['eventId','name','locationCode','spaceType','areaSqm','priceTotal','status'];
    
    public function event(): BelongsTo { return $this->belongsTo(Event::class, 'eventId'); }
    public function scopeAvailable($q) { return $q->where('status', 'available'); }
}
