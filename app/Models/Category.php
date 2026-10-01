<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'category_name','priority','status','icon'
    ];
    public function projects()
    {
        return $this->hasMany(Project::class, 'category_id');
    }
}
