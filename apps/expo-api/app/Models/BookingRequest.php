<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class BookingRequest extends Model
{
    public $incrementing = true;
    protected $keyType = 'int';
    protected $table = 'bookings';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    protected $fillable = ['orderNumber','userId','unitId','eventId','status','totalPrice','vatAmount','services','approvedBy','approvedAt','rejectionReason','notes'];
    protected $casts = ['services' => 'array', 'totalPrice' => 'decimal:2', 'vatAmount' => 'decimal:2'];
}
