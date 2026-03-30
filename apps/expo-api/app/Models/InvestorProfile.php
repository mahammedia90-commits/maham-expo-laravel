<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvestorProfile extends Model
{
    public $incrementing = true;
    protected $keyType = 'int';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    protected $fillable = ['userId', 'investorType', 'companyName', 'preferredSectors', 'investmentCapacity', 'totalInvested', 'verificationStatus', 'bio', 'phone', 'location'];
    
    public function user(): BelongsTo { return $this->belongsTo(User::class, 'userId'); }
}
