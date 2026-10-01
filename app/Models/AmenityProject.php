<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AmenityProject extends Model
{
    protected $table = 'amenity_project'; // agar table name different hai to change karna

    protected $fillable = [
        'project_id',
        'amenity_id',
        'reelly_project_amenity_id',
    ];
}
