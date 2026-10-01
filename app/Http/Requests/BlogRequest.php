<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BlogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'country_id' => 'required',
            'title' => 'required|string|max:255',

            'author_name' => 'nullable|string|max:255',

            'category' => 'nullable|string|max:255',

            'featured_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            // 'detailed_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:8192',
            'detailed_image' => 'nullable|image|mimes:jpg,jpeg,png,webp',

            'short_description' => 'nullable|string',

            'content' => 'nullable|string',

            'publish_date' => 'nullable|date',

            'status' => 'required|in:draft,published',

            'sort_order' => 'nullable|integer|min:0',

            'is_active' => 'nullable|in:1',
            'is_auto_slug' => 'nullable|in:1',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Custom Error Messages
    |--------------------------------------------------------------------------
    */
    public function messages(): array
    {
        return [

            'title.required' => 'Blog title is required.',
            'title.max' => 'Blog title cannot exceed 255 characters.',

            'featured_image.image' => 'Featured image must be a valid image file.',
            'featured_image.mimes' => 'Featured image must be jpg, jpeg, png or webp format.',
            'featured_image.max' => 'Featured image size must not exceed 2MB.',

            'detailed_image.image' => 'Detailed image must be a valid image file.',
            'detailed_image.mimes' => 'Detailed image must be jpg, jpeg, png or webp format.',
            'detailed_image.max' => 'Detailed image size must not exceed 8MB.',

            'publish_date.date' => 'Publish date must be a valid date.',

            'status.required' => 'Please select blog status.',
            'status.in' => 'Invalid blog status selected.',

            'sort_order.integer' => 'Sort order must be a number.',
            'sort_order.min' => 'Sort order cannot be negative.',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Custom Attribute Names (Cleaner Field Names)
    |--------------------------------------------------------------------------
    */
    public function attributes(): array
    {
        return [
            'title' => 'Blog Title',
            'author_name' => 'Author Name',
            'category' => 'Category',
            'featured_image' => 'Featured Image',
            'short_description' => 'Short Description',
            'content' => 'Blog Content',
            'publish_date' => 'Publish Date',
            'sort_order' => 'Sort Order',
        ];
    }
}
