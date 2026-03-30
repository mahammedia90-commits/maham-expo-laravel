<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Faq extends Model {
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['question','question_ar','answer','answer_ar','category','is_active','sort_order'];
    public function scopeActive($q) { return $q->where('is_active', true); }
}
