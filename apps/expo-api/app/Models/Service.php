<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Service extends Model {
    public $incrementing = true;
    protected $keyType = 'int';
    protected $table = 'exhibitor_services';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = null;
    protected $fillable = ['category','name','nameAr','description','descriptionAr','price','unit','isPopular','deliveryDays','rating','status'];
    protected $casts = ['isPopular' => 'boolean', 'price' => 'decimal:2'];
}
