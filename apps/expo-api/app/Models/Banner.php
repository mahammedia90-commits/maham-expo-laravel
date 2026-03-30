<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Banner extends Model {
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;
    protected $fillable = ['title','titleAr','image','link','isActive','sortOrder'];
    public function scopeActive($q) { return $q->where('isActive', true); }
}
