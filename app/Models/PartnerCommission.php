<?php

// app/Models/PartnerCommission.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerCommission extends Model
{
    protected $fillable = [
        'org_id','partner_id','project_id','type','value','effective_from','effective_to','active'
    ];

    protected $casts = [
        'effective_from' => 'date',
        'effective_to'   => 'date',
        'active'         => 'boolean',
    ];

    public function partner(){ return $this->belongsTo(Partner::class); }
    public function project(){ return $this->belongsTo(Project::class); }
}

