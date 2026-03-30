<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvestorProfile extends Model
{
    public $incrementing = true;
    protected $keyType = 'int';
    protected $table = 'investments';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = null;
    protected $fillable = ['userId', 'opportunityId', 'amount', 'roiExpected', 'roiActual', 'status', 'contractId'];
    
    public function user(): BelongsTo { return $this->belongsTo(User::class, 'userId'); }
}
