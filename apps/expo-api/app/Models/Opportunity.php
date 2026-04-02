<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Opportunity extends Model
{
    use SoftDeletes;

    protected $table = 'opportunities';

    protected $fillable = [
        'title',
        'description',
        'type',
        'status',
        'value',
        'event_id',
        'assigned_to',
        'deadline',
    ];

    protected $casts = [
        'deadline' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'value' => 'decimal:2',
    ];
}
