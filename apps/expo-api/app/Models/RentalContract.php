<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class RentalContract extends Model
{
    public $incrementing = true;
    protected $keyType = 'int';
    protected $table = 'contracts';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = null;
    protected $fillable = ['contractNumber','bookingId','totalAmount','status'];
}
