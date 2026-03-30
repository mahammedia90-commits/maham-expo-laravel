<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TicketReply extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
    public $incrementing = true;
    protected $keyType = 'int';
    protected $table = 'support_messages';
    const CREATED_AT = 'smCreatedAt';
    const UPDATED_AT = null;
    protected $fillable = ['smTicketId','smUserId','smMessage','isStaff','smAttachmentUrl'];
}
