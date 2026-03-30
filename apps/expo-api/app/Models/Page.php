<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    public $incrementing = true;
    protected $keyType = 'int';
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = null;
    protected $fillable = ['slug','titleAr','titleEn','contentAr','contentEn','status','seoTitle','seoDescription'];
    public function scopePublished($q) { return $q->where('status', 'published'); }
}
