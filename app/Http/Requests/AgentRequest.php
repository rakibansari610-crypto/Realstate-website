<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class AgentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
     public function rules(): array
    {
        $agentId = $this->route('id'); // update case me id milegi

        return [
 
            'name'      => ['required','string','max:255'],
            'email'     => [
                'nullable',
                'email',
                Rule::unique('agents','email')->ignore($agentId)
            ],
            'phone'     => ['nullable','string','max:20'], 
            'designation' => ['nullable','string','max:255'],

            // Address
            'address' => ['nullable','string'],
            'city'    => ['nullable','string','max:100'],
            'state'   => ['nullable','string','max:100'],
            'pincode' => ['nullable','string','max:10'],
            'country' => ['nullable','integer','max:10'],

            // Image
            'profile_image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120' // 2MB
            ],

            // Social Links
            'facebook' => ['nullable','url','max:255'],
            'linkedin' => ['nullable','url','max:255'],
            'twitter'  => ['nullable','url','max:255'], 

            
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Please select agent type.',
            'name.required' => 'Agent name is required.',
            'email.unique'  => 'This email is already registered.',
            'profile_image.image' => 'Profile image must be a valid image file.',
            'profile_image.max'   => 'Profile image must not exceed 2MB.',
            'default_commission.numeric' => 'Commission must be numeric.',
        ];
    }
}
