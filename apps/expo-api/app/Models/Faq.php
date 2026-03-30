<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    public $incrementing = true;
    protected $keyType = 'string';
    protected $fillable = ['question', 'question_ar', 'answer', 'answer_ar', 'category', 'sort_order', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];
    
    public function scopeActive($q) { return $q->where('is_active', true); }
}
