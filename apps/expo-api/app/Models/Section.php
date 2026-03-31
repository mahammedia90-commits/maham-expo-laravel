<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Section extends Model {
    public $incrementing = true;
    protected $keyType = 'int';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    protected $fillable = ['eventId','name','nameAr','type','capacity','color'];
}
