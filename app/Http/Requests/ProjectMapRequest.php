<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectMapRequest extends FormRequest
{
    /**
     * Authorize request
     */
    public function authorize(): bool
    {
        return true; // Important: false hoga to validation chalegi hi nahi
    }

    /**
     * Validation Rules
     */
    public function rules(): array
    {
        return [
            'project_id' => 'required|integer',
            'name'       => 'required|string|max:255',
            'distance'   => 'nullable|numeric|min:0',
            'time'       => 'nullable|integer|min:0',
            'sort_order' => 'nullable|integer|min:0',
        ];
    }

    /**
     * Custom Error Messages
     */
    public function messages(): array
    {
        return [
            'project_id.required' => 'Project select karna zaroori hai.',
            'project_id.integer'  => 'Project ID valid nahi hai.',

            'name.required' => 'Location name required hai.',
            'name.string'   => 'Name text format me hona chahiye.',
            'name.max'      => 'Name 255 characters se zyada nahi ho sakta.',

            'distance.numeric' => 'Distance number me hona chahiye.',
            'distance.min'     => 'Distance negative nahi ho sakta.',

            'time.integer' => 'Time integer me hona chahiye.',
            'time.min'     => 'Time negative nahi ho sakta.',

            'sort_order.integer' => 'Sort order integer me hona chahiye.',
            'sort_order.min'     => 'Sort order negative nahi ho sakta.',
        ];
    }

    /**
     * Custom Attribute Names (Optional but Professional)
     */
    public function attributes(): array
    {
        return [
            'project_id' => 'Project',
            'name'       => 'Location Name',
            'distance'   => 'Distance',
            'time'       => 'Time',
            'sort_order' => 'Sort Order',
        ];
    }
}
