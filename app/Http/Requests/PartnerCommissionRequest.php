<?php

// app/Http/Requests/PartnerCommissionRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PartnerCommissionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'project_id'     => ['required','exists:projects,id'],
            'type'           => ['required', Rule::in(['percent','flat'])],
            'value'          => ['required','numeric','gt:0'],
            'effective_from' => ['nullable','date'],
            'effective_to'   => ['nullable','date','after_or_equal:effective_from'],
            'active'         => ['nullable','boolean'],
        ];
    }
}
