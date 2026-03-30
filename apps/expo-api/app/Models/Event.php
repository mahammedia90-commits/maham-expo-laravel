<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    public $incrementing = true;
    protected $keyType = 'int';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    protected $fillable = ['name','nameAr','description','descriptionAr','type','startDate','endDate','venueId','status','coverImage','expectedVisitors','rating','isOfficial','createdBy'];
    protected $casts = ['isOfficial' => 'boolean', 'startDate' => 'datetime', 'endDate' => 'datetime'];
    
    public function sections(): HasMany { return $this->hasMany(Section::class, 'eventId'); }
    public function units(): HasMany { return $this->hasMany(Space::class, 'eventId'); }
    public function sponsorPackages(): HasMany { return $this->hasMany(SponsorPackage::class, 'eventId'); }
    
    public function scopePublished($q) { return $q->whereIn('status', ['active','upcoming']); }
    public function scopeFeatured($q) { return $q->where('isOfficial', true); }
    public function scopeSearch($q, $s) { return $q->where('name','like',"%$s%")->orWhere('nameAr','like',"%$s%"); }
}
