<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SponsorAsset extends Model
{
    public $incrementing = true;
    protected $keyType = 'int';
    protected $table = 'sponsor_activations';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    protected $fillable = ['sponsorshipId','eventId','sponsorId','activationType','title','description','scheduledDate','actualDate','status','impressions','interactions','leads','cost','notes','photos'];
}
