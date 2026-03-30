<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class UserActivity extends Model
{
    public $incrementing = true;
    protected $keyType = 'int';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = null;
    protected $table = 'activity_log';
    protected $fillable = ['userId','action','details','entityType','entityId'];
}
