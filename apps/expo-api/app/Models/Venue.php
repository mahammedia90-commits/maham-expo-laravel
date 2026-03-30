<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{
    public $incrementing = true;
    protected $keyType = 'int';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = null;
    protected $fillable = ['name', 'nameAr', 'city', 'address', 'capacity', 'facilities', 'mapImage', 'lat', 'lng'];
}
