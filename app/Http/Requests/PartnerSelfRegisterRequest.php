<?php
// app/Http/Requests/PartnerSelfRegisterRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PartnerSelfRegisterRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'type'  => ['required', Rule::in(['channel_partner','broker','agent'])],
            'name'  => ['required','string','max:180'],
            'email' => ['required','email','max:180'],
            'phone' => ['required','string','max:40'],

            // Optional initial docs + bank
            'pan_no'  => ['nullable','string','max:20'],
            'gst_no'  => ['nullable','string','max:30'],
            'rera_no' => ['nullable','string','max:50'],
            'pan_file'  => ['nullable','file','mimes:jpg,jpeg,png,webp,pdf','max:4096'],
            'gst_file'  => ['nullable','file','mimes:jpg,jpeg,png,webp,pdf','max:4096'],
            'rera_file' => ['nullable','file','mimes:jpg,jpeg,png,webp,pdf','max:4096'],

            'password' => ['required','string','min:8','confirmed'],
        ];
    }
}
