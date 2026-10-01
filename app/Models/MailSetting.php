<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MailSetting extends Model
{
    //
    protected $guarded = []; 
    protected $casts = [
        'mail_password' => 'encrypted',
    ];

    public function org()
    {
        return $this->belongsTo(Organization::class, 'org_id');
    }
}
