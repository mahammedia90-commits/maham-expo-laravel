<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Performance extends Model
{
    protected $fillable = [
        'user_id', 'date', 'leads_assigned', 'leads_contacted',
        'followups_completed', 'meetings_held', 'proposals_sent',
        'deals_closed', 'revenue_generated', 'conversion_rate',
        'avg_deal_value', 'response_time_hours', 'daily_score'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
