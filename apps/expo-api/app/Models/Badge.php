<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    protected $fillable = [
        'name',
        'name_en',
        'description',
        'icon',
        'tier',
        'criteria',
        'auto_grant',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'auto_grant' => 'boolean',
    ];

    public function users()
    {
        return $this->belongsToMany(\App\Models\User::class, 'user_badges');
    }
}
