<?php

// app/Models/Lead.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'org_id',
        'lead_no',
        'name',
        'email',
        'phone',
        'alt_phone',
        'source',
        'status',
        'created_by',
        'assigned_to',
        'follow_up_at',
        'project_id',
        'project_unit_id',
        'is_customer',
        'customer_id',
        'materialized_at',
        'preferred_bhk',
        'budget_min',
        'budget_max',
        'property_type',
        'property_build',
        'timeframe',
        'pref_langugae',
        'notes',
    ];

    protected $casts = [
        'follow_up_at'   => 'datetime',
        'materialized_at' => 'datetime',
        'is_customer'    => 'boolean',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    public function unit()
    {
        return $this->belongsTo(ProjectUnit::class, 'project_unit_id');
    }
    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
    public function tasks()
    {
        return $this->hasMany(LeadTask::class);
    }
}
