<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessProfile extends Model
{
    public $incrementing = true;
    protected $keyType = 'int';
    protected $table = 'customers';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = null;
    protected $fillable = ['userId','type','companyName','crNumber','vatNumber','kycStatus','classification','score'];
    
    public function user(): BelongsTo { return $this->belongsTo(User::class, 'userId'); }
}
