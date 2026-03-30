<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SponsorPayment extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
    public $incrementing = true;
    protected $keyType = 'int';
    protected $table = 'payments';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = null;
    protected $fillable = ['userId','amount','currency','method','status','paidAt','invoiceId'];
}
