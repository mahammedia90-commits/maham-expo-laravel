<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Payment extends Model
{
    public $incrementing = true;
    protected $keyType = 'int';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = null;
    protected $fillable = ['userId','bookingId','amount','currency','method','gatewayRef','status','invoiceId','paidAt'];
    protected $casts = ['amount' => 'decimal:2'];
}
