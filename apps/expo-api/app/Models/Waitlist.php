<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Waitlist extends Model
{
    protected $table = 'waitlist';

    protected $fillable = [
        'user_id',
        'event_id',
        'space_id',
        'position',
        'status',
        'notified_at',
    ];

    protected $casts = [
        'notified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
