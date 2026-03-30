<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    public $incrementing = true;
    protected $keyType = 'int';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = null;
    protected $fillable = ['name', 'nameAr', 'region', 'country', 'latitude', 'longitude', 'isActive'];
    protected $casts = ['isActive' => 'boolean', 'latitude' => 'decimal:8', 'longitude' => 'decimal:8'];
}
