<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SponsorExposureTracking extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
    public $incrementing = true;
    protected $keyType = 'int';
    protected $table = 'brand_exposure_metrics';
    public $timestamps = false;
}
