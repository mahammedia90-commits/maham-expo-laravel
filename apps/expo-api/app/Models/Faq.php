<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Faq extends Model
{
    public $incrementing = true;
    protected $keyType = 'int';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = null;
    protected $fillable = ['question','questionAr','answer','answerAr','category','sortOrder','isActive'];
    protected $casts = ['isActive' => 'boolean'];
}
