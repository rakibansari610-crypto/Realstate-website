<?php


namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\PartnerSelfRegisterRequest;
use App\Models\Partner;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class PartnerSelfRegisterController extends Controller
{
    public function show()
    {
        return view('public.partner_register');
    }

    public function store(PartnerSelfRegisterRequest $request)
    {
        // Resolve tenant/org id via your ResolveTenant middleware/context
        $orgId = app('current_org_id'); // Adjust to your tenant resolver

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email'=> $request->email,
            'password' => Hash::make($request->password),
            'org_id' => $orgId,
        ]);
        $user->assignRole(match($request->type){
            'channel_partner'=>'channel partner',
            'broker'=>'broker',
            'agent'=>'agent',
        });

        // Build partner data
        $data = $request->validated();
        $data['org_id'] = $orgId;
        $data['user_id']= $user->id;
        $data['approved'] = false; // admin must approve

        foreach (['pan_file'=>'pan_path','gst_file'=>'gst_path','rera_file'=>'rera_path'] as $input => $field) {
            if (!empty($request->$input)) {
                $value = $request->$input;
                $name  = md5(time().$input).'.'.$value->getClientOriginalExtension();
                $value->move(public_path('media/partners'), $name);
                $data[$field] = 'media/partners/'.$name;
            }
        }

        Partner::create($data);

        // TODO: notify admin for approval

        return redirect()->url('login')->with('success','Registration submitted. Admin will approve your account.');
    }
}
