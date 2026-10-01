<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectSearchIndex extends Model
{
    protected $table = 'project_search_indexes';

    protected $fillable = [
        'project_id', 'name', 'slug', 'city', 'location', 'status', 'min_price', 'max_price', 'currency', 'completion_date',
        'has_post_handover', 'booking_percent_min', 'months_after_handover', 'plan_ratio', 'payment_plan_summary',
        'map_points_summary', 'search_text', 'indexed_at',
    ];

    protected $casts = [
        'has_post_handover' => 'boolean',
        'completion_date' => 'date',
        'indexed_at' => 'datetime',
        'min_price' => 'decimal:2',
        'max_price' => 'decimal:2',
        'booking_percent_min' => 'decimal:2',
    ];
}
