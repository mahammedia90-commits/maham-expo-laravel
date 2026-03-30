<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TicketReply extends Model
{
    public $incrementing = true;
    protected $keyType = 'int';
    protected $table = 'complaint_activities';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = null;
    protected $fillable = ['complaintId','action','note','performedBy'];
}
