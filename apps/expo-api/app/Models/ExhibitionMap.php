<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExhibitionMap extends Model
{
    protected $table = 'exhibition_maps';

    protected $fillable = [
        'event_id',
        'name',
        'layout_data',
        'total_spaces',
    ];

    protected $casts = [
        'layout_data' => 'json',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
