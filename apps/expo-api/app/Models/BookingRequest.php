<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class BookingRequest extends Model
{
    public $incrementing = true;
    protected $keyType = 'int';
    protected $table = 'booking_requests';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    protected $fillable = ['exhibitionId', 'unitId', 'merchantName', 'merchantCompany', 'merchantPhone', 'merchantEmail', 'activityType', 'requestedAmount', 'status', 'notes', 'investorId'];
}
