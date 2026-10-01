<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentPlans extends Model
{
     protected $table = 'payment_plans';

        protected $fillable = [
            'project_id',
            'reelly_id',
            'name',
            'duration_months',
            'is_handover',
            'months_after_handover',
            'eoi',
            'source_created_at',
            'source_modified_at',
            'sort_order',
        ];
    }
