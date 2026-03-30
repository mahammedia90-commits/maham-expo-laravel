<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SponsorDeliverable extends Model
{
    public $incrementing = true;
    protected $keyType = 'int';
    protected $table = 'sponsor_package_items';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = null;
    protected $fillable = ['packageId','itemType','title','description','value','quantity','status','deliveryStatus','deliveredAt','notes'];
}
