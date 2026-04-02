<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PipelineStage extends Model
{
    protected $fillable = ['name_ar', 'name_en', 'order', 'sla_hours', 'color'];

    public function deals()
    {
        return $this->hasMany(Deal::class, 'stage_id');
    }

    public function getDealsCountAttribute()
    {
        return $this->deals()->count();
    }

    public function getDealsValueAttribute()
    {
        return $this->deals()->sum('value');
    }
}
