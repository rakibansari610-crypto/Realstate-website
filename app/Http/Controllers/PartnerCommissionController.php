<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Http\Requests\PartnerCommissionRequest;
use App\Models\Partner;
use App\Models\PartnerCommission;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;

class PartnerCommissionController extends Controller
{
    public function index(Partner $partner)
    {

        // $this->ensureOrg($partner);
        $orgId = Auth::user()->org_id;

        $commissions = $partner->commissions()->with('project:id,name')->latest()->paginate(20);
        $projects    = Project::where('org_id',$orgId)->orderBy('name')->get(['id','name']);

        return view('org.partners.commissions', compact('partner','commissions','projects'));
    }

    public function store(PartnerCommissionRequest $request, Partner $partner)
    {
        $this->ensureOrg($partner);
        $orgId = Auth::user()->org_id;

        $data = $request->validated();
        $data['org_id']    = $orgId;
        $data['partner_id']= $partner->id;

        PartnerCommission::updateOrCreate(
            ['org_id'=>$orgId,'partner_id'=>$partner->id,'project_id'=>$data['project_id']],
            $data
        );

        return back()->with('success','Commission saved.');
    }

    public function destroy(Partner $partner, PartnerCommission $commission)
    {
        $this->ensureOrg($partner);
        abort_unless($commission->partner_id === $partner->id, 404);
        $commission->delete();
        return back()->with('success','Commission removed.');
    }

    private function ensureOrg(Partner $partner): void
    {
        abort_unless($partner->org_id === Auth::user()->org_id, 403);
    }
}
