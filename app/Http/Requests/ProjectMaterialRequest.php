<?php
// app/Http/Requests/ProjectMaterialRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectMaterialRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'title' => ['required','string','max:200'],
            'type'  => ['required','in:image,pdf,doc,video,link'],
            'file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf,doc,docx', 'max:131072'],
            'url'   => ['nullable','url'],
            'is_public' => ['nullable','boolean'],
            'tags'  => ['nullable','array'],
            'tags.*'=> ['string','max:30'],
            'created_by'  => ['nullable','integer'],
            'updated_by'  => ['nullable','integer'],
        ];
    }
}
