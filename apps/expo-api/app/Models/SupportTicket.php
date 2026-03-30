<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SupportTicket extends Model
{
    public $incrementing = true;
    protected $keyType = 'int';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    protected $fillable = ['customerId','subject','category','priority','status','assignedTo','slaDeadline','description'];
}
