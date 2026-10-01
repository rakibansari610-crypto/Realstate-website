<?php
// app/Http/Requests/ProjectUnitRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectUnitRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'unit_no'     => ['required','string','max:100'],
            'tower'       => ['nullable','string','max:100'],
            'floor'       => ['nullable','string','max:50'],
            'bhk'         => ['nullable','string','max:20'],
            'carpet_area' => ['nullable','numeric'],
            'builtup_area'=> ['nullable','numeric'],
            'super_area'  => ['nullable','numeric'],
            'price'       => ['nullable','numeric'],
            'status'      => ['required','in:available,blocked,booked,sold'],
            'facing'      => ['nullable','string','max:50'],
            'remarks'     => ['nullable','string'],
        ];
    }
}
