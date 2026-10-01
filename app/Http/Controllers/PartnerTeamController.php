<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\PartnerRequest;
use App\Models\Partner;
use Illuminate\Support\Facades\Auth;

class PartnerTeamController extends Controller
{
    public function create()
    {
        // Only CP role
        abort_unless(Auth::user()->hasRole('channel partner'), 403);
        return view('org.partners.team_form', ['partner'=>new Partner()]);
    }

    public function store(PartnerRequest $request)
    {
        abort_unless(Auth::user()->hasRole('channel partner'), 403);

        $cp = Partner::where('org_id',Auth::user()->org_id)
            ->where('user_id',Auth::id())
            ->where('type','channel_partner')
            ->firstOrFail();

        $data = $request->validated();
        // Force type and parent
        $data['type']      = 'channel_partner';
        $data['org_id']    = Auth::user()->org_id;
        $data['parent_id'] = $cp->id;
        $data['approved']  = true; // CP adding to own team — you can choose to require admin approval

        foreach (['pan_file'=>'pan_path','gst_file'=>'gst_path','rera_file'=>'rera_path'] as $input => $field) {
            if (!empty($request->$input)) {
                $value = $request->$input;
                $name  = md5(time().$input).'.'.$value->getClientOriginalExtension();
                $value->move(public_path('media/partners'), $name);
                $data[$field] = 'media/partners/'.$name;
            }
        }

        Partner::create($data);

        return redirect()->route('org.partners.index')->with('success','Team Channel Partner added.');
    }
}
