<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentPlanStep extends Model
{
    protected $table = 'payment_plan_steps';

    protected $fillable = [
        'payment_plan_id',
        'reelly_id',
        'parent_id',
        'name',
        'percentage',
        'stage_type',
        'start',
        'end',
        'notes',
        'fixed_amount',
        'sort_order',
    ];
}
