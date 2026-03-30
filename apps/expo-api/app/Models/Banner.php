<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Banner extends Model {
    public $incrementing = true;
    protected $keyType = 'int';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = null;
    protected $fillable = ['title','titleAr','image','link','isActive','sortOrder'];
    protected $casts = ['isActive' => 'boolean'];
    public function scopeActive($q) { return $q->where('isActive', true); }
}
