<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FollowUp extends Model
{
    protected $table = 'follow_ups';
    protected $fillable = ['lead_id', 'deal_id', 'due_date', 'type', 'status', 'outcome', 'duration', 'next_action', 'next_action_date', 'assigned_to'];

    protected $casts = [
        'due_date' => 'date',
        'next_action_date' => 'date',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function deal()
    {
        return $this->belongsTo(Deal::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
