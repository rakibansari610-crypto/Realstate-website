<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectMedia extends Model
{
    protected $table = 'project_media';
    protected $fillable = [
        'project_id',
        'collection',
        'url',
        'mime',
        'size',
        'width',
        'height',
        'sort_order',
        'meta',
    ];
    protected $casts = [
        'meta' => 'array',
    ];
}
