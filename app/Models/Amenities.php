<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Amenities extends Model
{
     protected $table = 'amenities'; // agar table name different hai to change karna

    protected $fillable = [
        'name',
        'reelly_id',
        'icon_url',
        'is_active',
    ];
}
