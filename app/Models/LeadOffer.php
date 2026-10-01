<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadOffer extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'unit_id',
        'offered_amount',
        'status',
        'offer_type',
        'reason',
        'created_by'
    ];

    protected $casts = [
        'offered_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
