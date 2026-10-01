<?php

// app/Http/Requests/DeveloperRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DeveloperRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $orgId = $this->user()->org_id;
        $id    = $this->route('developer')?->id; // null on create

        return [
            'name'    => [
                'required','string','max:150',
                Rule::unique('developers','name')
                    // ->where(fn($q) => $q->where('org_id', $orgId))
                    ->ignore($id),
            ],
            'contact_person_name'   => ['nullable','string','max:150'],
            'email'   => ['nullable','email','max:150'],
            'phone'   => ['nullable','string','max:30'],
            'website' => ['nullable'],
            'address' => ['nullable','string'],
            'city' => ['nullable','string'],
            'state' => ['nullable','string'],
            // 'country' => ['nullable','string'],
            'countries'   => ['required'],
            'countries.*' => ['exists:countries,id'],
            'zip' => ['nullable','string'],
            'active'  => ['nullable','boolean'],
            'created_by'  => ['nullable','integer'],
            'updated_by'  => ['nullable','integer'],
            'logo'    => ['nullable','image','mimes:jpg,jpeg,png,webp'],
        ];
    }
}
