<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Rating extends Model
{
    public $incrementing = true;
    protected $keyType = 'int';
    protected $table = 'reviews';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = null;
    protected $fillable = ['userId', 'eventId', 'rating', 'comment', 'sentiment', 'status'];
}
