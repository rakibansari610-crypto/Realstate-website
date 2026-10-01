<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoMeta extends Model
{
    protected $table = 'seo_metas'; 

    protected $fillable = [
        'page_type',
        'slug',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'country_id',
        'schema_json',
        'og_title',
        'og_description',
        'og_image',
        'og_image_alt',
        'twitter_title',
        'twitter_description',
        'twitter_image',
        'twitter_image_alt',
    ];
}
