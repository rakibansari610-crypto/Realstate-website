<?php

namespace App\Models;

use App\Models\Scopes\OrgScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Site extends Model
{
    use HasFactory;

    protected $fillable = ['org_id','name','code','timezone','address','settings'];
    protected $casts = ['address'=>'array','settings'=>'array'];

    protected static function booted() {
        static::addGlobalScope(new OrgScope);
        static::creating(function ($m) {
            if (blank($m->org_id) && ($orgId = \App\Tenancy\Tenant::id())) $m->org_id = $orgId;
        });
    }

    public function org() { return $this->belongsTo(Organization::class, 'org_id'); }
    public function devices() { return $this->hasMany(Device::class, 'site_id'); }
}

