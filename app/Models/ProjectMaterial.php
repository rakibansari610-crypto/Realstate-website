<?php

// app/Models/ProjectMaterial.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class ProjectMaterial extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'org_id','project_id','title','type','file_path','url','tags','is_public','uploaded_by','created_by','updated_by'
    ];

    protected $casts = ['tags' => 'array', 'is_public' => 'boolean'];

    public function project() { return $this->belongsTo(Project::class); }

    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path ? Storage::disk('public')->url($this->file_path) : null;
    }
}
