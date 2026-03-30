<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class City extends Model {
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['name','nameAr','region','latitude','longitude','isActive'];
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = null;
}
