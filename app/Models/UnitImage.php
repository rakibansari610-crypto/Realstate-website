<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitImage extends Model
{
    protected $fillable = [
        'unit_id',
        'source',
        'source_unit_code',
        'image_type',
        'title',
        'is_marketing_image',
        'image_remarks',
        'image_url',
        'image_hash',
        'sort_order',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
