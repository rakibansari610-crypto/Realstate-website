<?php
// app/Http/Requests/LeadAssignRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LeadAssignRequest extends FormRequest
{
    public function authorize(): bool { return true; } // admin-only route guards this

    public function rules(): array
    {
        return [
            'assigned_to' => ['nullable','exists:users,id'],
        ];
    }
}
