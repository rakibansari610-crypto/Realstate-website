<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitFitting extends Model
{
    protected $fillable = [
        'unit_id',
        'source',
        'source_fixture_id',
        'title',
        'name',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
