<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectMapPoint extends Model
{
    protected $table = 'project_map_points';

    protected $fillable = [
        'project_id',
        'name',
        'distance',
        'time',
        'sort_order',
    ];

    public $timestamps = true; 
}
