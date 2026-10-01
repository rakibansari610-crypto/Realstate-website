<?php
// app/Http/Requests/LeadMaterialiseRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LeadMaterialiseRequest extends FormRequest
{
    public function authorize(): bool { return true; } // admin-only route guards this

    public function rules(): array
    {
        return [
            'project_id' => ['required','exists:projects,id'],
            'project_unit_id' => ['nullable','exists:project_units,id'],
        ];
    }
}
