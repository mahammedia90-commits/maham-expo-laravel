<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
    public $incrementing = true;
    protected $keyType = 'int';
    protected $table = 'exhibitor_services';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = null;
    protected $fillable = ['name','nameAr','description','price','category','isActive'];
}
