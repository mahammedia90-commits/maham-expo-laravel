<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Banner extends Model
{
    public $incrementing = true;
    protected $keyType = 'string';
    protected $fillable = ['title','title_ar','image','link_url','position','is_active','start_date','end_date','sort_order'];
    protected $casts = ['is_active' => 'boolean'];
    public function scopeActive($q) { return $q->where('is_active', true); }
}
