<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Invoice extends Model
{
    public $incrementing = true;
    protected $keyType = 'int';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = null;
    protected $fillable = ['invoiceNumber','paymentId','userId','amount','vatRate','vatAmount','totalAmount','zatcaUuid','zatcaHash','zatcaQr','status','pdfUrl','issuedAt'];
    protected $casts = ['amount' => 'decimal:2', 'vatAmount' => 'decimal:2', 'totalAmount' => 'decimal:2'];
}
