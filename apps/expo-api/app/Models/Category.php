<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Category extends Model {
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['name','name_ar','icon','description','is_active','sort_order'];
}
