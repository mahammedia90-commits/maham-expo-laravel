<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'full_name', 'company', 'phone', 'phone_whatsapp', 'email',
        'city', 'sector', 'lead_type', 'source', 'priority',
        'assigned_to', 'ai_score', 'status', 'next_action',
        'next_action_date', 'notes', 'last_contacted_at'
    ];

    protected $casts = [
        'last_contacted_at' => 'datetime',
        'next_action_date' => 'date',
    ];

    public function deals()
    {
        return $this->hasMany(Deal::class);
    }

    public function followUps()
    {
        return $this->hasMany(FollowUp::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
