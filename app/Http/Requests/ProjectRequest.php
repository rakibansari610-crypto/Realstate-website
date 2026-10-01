<?php
// app/Http/Requests/ProjectRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $orgId = $this->user()->org_id;
        $id    = $this->route('project')?->id;

        return [
            'developer_id' => ['required','exists:developers,id'],
            'name'         => [
                'required','string','max:200',
                Rule::unique('projects','name')->where(fn($q)=>$q->where('org_id',$orgId))->ignore($id)
            ],
            'rera_no'      => ['nullable','string','max:100'],
            'city'         => ['nullable','string','max:120'],
            'location'     => ['nullable','string','max:255'],
            'status'       => ['required', Rule::in(['planning','launched','under_construction','ready_to_move','completed'])],
            'sale_status'  => ['nullable','string'],
            'launch_date'  => ['nullable','date'],
            'completion_date'=> ['nullable','date','after_or_equal:launch_date'],
            'min_price'    => ['nullable','numeric'],
            'max_price'    => ['nullable','numeric'],
            'lat'          => ['nullable','numeric'],
            'lng'          => ['nullable','numeric'],
            'active'       => ['nullable','boolean'],
            'cover'        => ['nullable','image','mimes:jpg,jpeg,png,webp','max:4096'],
            'category_id' => 'nullable|integer',
            'sub_category_id' => 'nullable|integer',

            'payment_plan'          => ['nullable','string'],
            'project_type'          => ['nullable','string'],
            'area_size'          => ['nullable','string'],
            'handover_payment_plan'          => ['nullable','string'],
            'building_description'          => ['nullable','string'],
            'handover'          => ['nullable','string'],
            'created_by'  => ['nullable','integer'],
            'updated_by'  => ['nullable','integer'],
            'country_id' => ['required','exists:countries,id'],
            'meta_title'       => ['nullable','string','max:255'],
            'meta_description' => ['nullable','string','max:500'],
            'meta_keywords'    => ['nullable','string','max:255'],
            'pincode'          => ['nullable','string','max:20'],
            'locality'         => ['nullable','string','max:150'],
            'draft'            => ['nullable','string'],
        ];
    }
}
