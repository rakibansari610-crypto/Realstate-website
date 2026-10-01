<?php

namespace App\Models;

use App\Models\Scopes\OrgScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Device extends Model
{
    use HasFactory;

    protected $fillable = ['org_id','site_id','name','type','auth_token','status','last_seen_at','meta'];
    protected $casts = ['last_seen_at'=>'datetime','meta'=>'array'];

    protected static function booted() {
        static::addGlobalScope(new OrgScope);
        static::creating(function ($m) {
            if (blank($m->org_id) && ($orgId = \App\Tenancy\Tenant::id())) $m->org_id = $orgId;
        });
    }

    public function org()  { return $this->belongsTo(Organization::class, 'org_id'); }
    public function site() { return $this->belongsTo(Site::class, 'site_id'); }
}

