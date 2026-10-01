<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;
use App\Models\Project;
use App\Models\User;
use App\Models\Country;

class Developer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'org_id','name','slug','contact_person_name','email','phone','website','logo_path','address','city','state','zip','country','active','created_by','updated_by'
    ];

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo_path ? Storage::disk('public')->url($this->logo_path) : null;
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

        public function countries()
    {
        return $this->belongsToMany(Country::class, 'country_developer');
    }
}
