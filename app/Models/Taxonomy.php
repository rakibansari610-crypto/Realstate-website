<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Taxonomy extends Model
{
    //
    protected $fillable = [
        'name',
        'slug',
        'module',
        'description',
        'thumbnail',
        'priority',
        'status',
        'parent_id',
    ];

    /**
     * Parent taxonomy (category) relationship.
     */
    public function parent()
    {
        return $this->belongsTo(Taxonomy::class, 'parent_id');
    }

    /**
     * Child taxonomies (subcategories) relationship.
     */
    public function children()
    {
        return $this->hasMany(Taxonomy::class, 'parent_id');
    }
}
