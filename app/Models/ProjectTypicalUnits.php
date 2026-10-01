<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectTypicalUnits extends Model
{
    protected $table = 'project_typical_units'; // agar table ka naam yahi hai

    protected $fillable = [
        'project_id',
        'bedrooms',
        'room_type',
        'type_name',
        'currency_code',
        'from_price',
        'to_price',
        'from_size_sqft',
        'to_size_sqft',
        'from_size_m2',
        'to_size_m2',
        'sort_order',
    ];
}
