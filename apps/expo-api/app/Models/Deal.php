<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deal extends Model
{
    protected $fillable = [
        'lead_id', 'stage_id', 'value', 'close_probability',
        'assigned_to', 'days_in_stage', 'last_activity',
        'is_overdue', 'is_at_risk', 'notes'
    ];

    protected $casts = [
        'last_activity' => 'datetime',
        'is_overdue' => 'boolean',
        'is_at_risk' => 'boolean',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function stage()
    {
        return $this->belongsTo(PipelineStage::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function followUps()
    {
        return $this->hasMany(FollowUp::class);
    }
}
