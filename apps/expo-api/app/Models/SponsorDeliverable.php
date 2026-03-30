<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SponsorDeliverable extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
    public $incrementing = true;
    protected $keyType = 'int';
    protected $table = 'sponsor_package_items';
    public $timestamps = false;
}
