<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Workflow extends Model
{
    protected $fillable = ['name', 'trigger_type', 'steps', 'is_active'];

    protected $casts = [
        'steps' => 'json',
        'is_active' => 'boolean',
    ];
}
