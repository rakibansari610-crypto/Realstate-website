<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Organization extends Model
{
    use HasFactory;
   

    protected $fillable = ['theme', 'logo_path', 'login_bg_path', 'favicon_path','name','slug','subdomain','custom_domain','settings'];
    protected $casts = ['settings' => 'array','theme' => 'array'];

    public function sites() { return $this->hasMany(Site::class, 'org_id'); }
    public function users() { return $this->hasMany(User::class, 'org_id'); }
}
