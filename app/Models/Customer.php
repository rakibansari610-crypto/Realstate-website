<?php

// app/Models/Customer.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $fillable = ['org_id','lead_id','project_id','project_unit_id','name','email','phone'];

    public function lead()    { return $this->hasOne(Lead::class, 'customer_id'); }
    public function project() { return $this->belongsTo(Project::class); }
    public function unit()    { return $this->belongsTo(ProjectUnit::class, 'project_unit_id'); }
}
