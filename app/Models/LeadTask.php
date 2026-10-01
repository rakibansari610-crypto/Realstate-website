<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadTask extends Model
{
    protected $fillable = [
        'lead_id',
        'task_type',
        'note',
        'unit_ref',
        'is_completed',
        'schedule_follow_up_days',
        'date',
        'time',
        'user_id',
        'lead_activity',
        'lead_status',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'date'         => 'date',
        'time'         => 'datetime:H:i',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }
}