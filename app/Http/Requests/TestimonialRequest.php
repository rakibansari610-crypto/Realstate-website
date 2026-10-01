<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TestimonialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'          => 'nullable|string|max:255',
            'designation'   => 'nullable|string|max:255',
            'sort_order'    => 'nullable|integer|min:0',

            'rating_number' => 'required|integer|min:1|max:5',
            'description'   => 'required|string|min:10|max:1000',

            // 70x70 se badi image allowed nahi
            'profile' => 'nullable|image|mimes:jpeg,png,webp|max:512|dimensions:max_width=70,max_height=70',
        ];
    }

    public function messages(): array
    {
        return [
            'rating_number.required' => 'Rating is required.',
            'rating_number.integer'  => 'Rating must be a number.',
            'rating_number.min'      => 'Rating must be at least 1.',
            'rating_number.max'      => 'Rating cannot be greater than 5.',

            'description.required' => 'Description is required.',
            'description.min'      => 'Description must be at least 10 characters.',
            'description.max'      => 'Description cannot exceed 1000 characters.',

            'profile.image'      => 'Profile must be a valid image file.',
            'profile.mimes'      => 'Profile image must be jpeg, png, or webp format.',
            'profile.max'        => 'Profile image size must not exceed 512KB.',
            'profile.dimensions' => 'Profile image must not be larger than 70x70 pixels.',
        ];
    }
}
