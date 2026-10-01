<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Developer;

class Country extends Model
{

    protected $table = 'countries';

    protected $fillable = [
        'country_name',
        'country_currency',
        'country_lang',
        'country_code',
        'country_iso',
        'country_isd',
        'country_status',
        'country_default',
        'country_priority',
    ];

        public function developers()
    {
        return $this->belongsToMany(Developer::class, 'country_developer');
    }
}
