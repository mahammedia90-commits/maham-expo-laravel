<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    public $incrementing = true;
    protected $keyType = 'int';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = null;
    protected $fillable = ['userId', 'balance', 'currency'];
    protected $casts = ['balance' => 'decimal:2'];
}
