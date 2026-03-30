<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Service extends Model {
    public $incrementing = true;
    protected $keyType = 'int';
    protected $table = 'services';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = null;
    protected $fillable = ['name','nameAr','description','price','category','isActive'];
}
