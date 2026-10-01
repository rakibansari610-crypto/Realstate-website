<?php

// app/Models/ProjectUnit.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectUnit extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'org_id','project_id','unit_no','tower','floor','bhk','carpet_area','builtup_area',
        'super_area','price','status','facing','remarks'
    ];

    public function project() { return $this->belongsTo(Project::class); }
}
