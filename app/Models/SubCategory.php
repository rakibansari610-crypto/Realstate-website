<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    protected $fillable = [
       'category_id', 'sub_category_name','priority','status','icon'
    ];

    public function projects()
    {
        return $this->hasMany(Project::class, 'sub_category_id');
    }
}
