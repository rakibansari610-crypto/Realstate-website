<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $table = 'testimonial'; 

    protected $fillable = [
        'name',
        'designation',
        'profile',
        'rating_number',
        'description',
        'is_active',
        'sort_order',
        'update_by'
    ];
}
