<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RentalRequest extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
    public $incrementing = true;
    protected $keyType = 'int';
    protected $table = 'booking_requests';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    protected $fillable = ['exhibitionId','unitId','merchantName','merchantCompany','merchantPhone','merchantEmail','activityType','requestedAmount','status','notes','investorId'];
    
    public function event(): BelongsTo { return $this->belongsTo(Event::class, 'exhibitionId'); }
    public function space(): BelongsTo { return $this->belongsTo(Space::class, 'unitId'); }
}
