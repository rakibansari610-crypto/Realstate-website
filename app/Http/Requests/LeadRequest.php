<?php

// app/Http/Requests/LeadRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LeadRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        // $orgId = $this->user()->org_id;
        $id    = $this->route('lead')?->id;

        return [
            'name'          => ['required','string','max:180'],
            'email'         => ['nullable','email','max:180'],
            'phone'         => ['nullable','string','max:40'],
            'alt_phone'     => ['nullable','string','max:40'],
            'source'        => ['nullable','string','max:120'],
            'status'        => ['required', Rule::in(['new','contacted','site_visit','booked','lost'])],
            'preferred_bhk' => ['nullable','string','max:20'],
            'budget_min'    => ['nullable','numeric','gte:0'],
            'budget_max'    => ['nullable','numeric','gte:0'],
            
            'property_type' => ['nullable','string','max:100'],
            'property_build' => ['nullable','string','max:100'],
            'timeframe' => ['nullable','string','max:100'],
            'pref_langugae' => ['nullable','string','max:20'],
            'follow_up_at'  => ['nullable','date'],
            'notes'         => ['nullable','string'],

            // DO NOT allow project/unit/assigned_to here (admin handles separately)
        ];
    }
}
