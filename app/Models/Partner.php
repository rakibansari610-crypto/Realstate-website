<?php
// app/Models/Partner.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;


class Partner extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'org_id','parent_id','user_id','type','name','email','phone','alt_phone','address',
        'city','state','pincode','pan_no','gst_no','rera_no','pan_path','gst_path','rera_path',
        'bank_account_holder','bank_account_no','ifsc','bank_name','bank_branch','upi_id',
        'default_commission_type','default_commission','approved','active','country','rm_id','password'
    ];

    protected $casts = [
        'approved' => 'boolean',
        'active'   => 'boolean',
    ];

    public function setPasswordAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['password'] = Hash::make($value);
        }
    }

    public function parent()   { return $this->belongsTo(Partner::class, 'parent_id'); }
    public function children() { return $this->hasMany(Partner::class, 'parent_id'); }
    public function commissions(){ return $this->hasMany(PartnerCommission::class); }
    public function user()     { return $this->belongsTo(User::class); }
}
