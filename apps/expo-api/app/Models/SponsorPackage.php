<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SponsorPackage extends Model
{
    public $incrementing = true;
    protected $keyType = 'int';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = null;
    protected $fillable = ['name','nameAr','price','benefits','maxSponsors','eventId','tier'];
    protected $casts = ['benefits' => 'array', 'price' => 'decimal:2'];
}
