<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $table = 'blogs';

    protected $fillable = [
        'country_id',
        'title',
        'slug',
        'author_name',
        'category',
        'featured_image',
        'detailed_image',
        'short_description',
        'content',
        'publish_date',
        'status',
        'is_active',
        'update_by',
        'sort_order',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'is_auto_slug',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
