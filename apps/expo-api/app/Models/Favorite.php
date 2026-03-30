<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Favorite extends Model
{
    public $incrementing = true;
    protected $keyType = 'int';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    public $timestamps = false;
    protected $fillable = ['userId', 'eventId', 'spaceId'];
}
