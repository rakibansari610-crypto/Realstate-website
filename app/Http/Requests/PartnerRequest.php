<?Php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PartnerRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id    = $this->route('partner')?->id;
        $type  = $this->input('type');

        return [
            'type'  => ['required', Rule::in(['channel_partner','property consultant','freelancer'])],
            'name'  => ['required','string','max:180'],
            'email' => ['nullable','email','max:180'],
            'phone' => ['nullable','string','max:40'],
            // 'password' => ['required', 'string', 'min:6'],
             'password' => $id
            ? ['nullable', 'string', 'min:6', 'confirmed']
            : ['required', 'string', 'min:6', 'confirmed'],

            'password_confirmation' => $id ? ['nullable'] : ['required'],


            // Address
            'address' => ['nullable','string'],
            'city'    => ['nullable','string','max:120'],
            'state'   => ['nullable','string','max:120'],
            'pincode' => ['nullable','string','max:20'],
            'country' => ['nullable','string','max:20'],

            // Docs
            'pan_no'  => ['nullable','string','max:20'],
            'gst_no'  => ['nullable','string','max:30'],
            'rera_no' => ['nullable','string','max:50'],
            'pan_file'  => ['nullable','file','mimes:jpg,jpeg,png,webp,pdf','max:4096'],
            'gst_file'  => ['nullable','file','mimes:jpg,jpeg,png,webp,pdf','max:4096'],
            'rera_file' => ['nullable','file','mimes:jpg,jpeg,png,webp,pdf','max:4096'],

            // Bank
            'bank_account_holder' => ['nullable','string','max:180'],
            'bank_account_no'     => ['nullable','string','max:60'],
            'ifsc'                => ['nullable','string','max:20'],
            'bank_name'           => ['nullable','string','max:180'],
            'bank_branch'         => ['nullable','string','max:180'],
            'upi_id'              => ['nullable','string','max:100'],

            // Default commission
            'default_commission_type' => ['nullable', Rule::in(['percent','flat'])],
            'default_commission'      => ['nullable','numeric','gte:0'],

            'approved' => ['nullable','boolean'],
            'active'   => ['nullable','boolean'],
            'parent_id'=> ['nullable','exists:partners,user_id'],
            'rm_id' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }
}
