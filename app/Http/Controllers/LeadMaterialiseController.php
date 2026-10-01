<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\LeadMaterialiseRequest;
use App\Models\Lead;
use App\Models\Project;
use App\Models\ProjectUnit;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class LeadMaterialiseController extends Controller
{
    public function create(Lead $lead)
    {
        $this->authorizeAdmin($lead);

        $orgId    = Auth::user()->org_id;
        $projects = Project::where('org_id',$orgId)->orderBy('name')->get(['id','name']);
        $units    = $lead->project_id
                   ? ProjectUnit::where('project_id',$lead->project_id)->orderBy('unit_no')->get(['id','unit_no'])
                   : collect();

        return view('org.leads.materialise', compact('lead','projects','units'));
    }

    public function store(LeadMaterialiseRequest $request, Lead $lead)
    {
        $this->authorizeAdmin($lead);

        $orgId = Auth::user()->org_id;
        $data = $request->validated();

        // verify the unit belongs to the project (if sent)
        if (!empty($data['project_unit_id'])) {
            ProjectUnit::where('id',$data['project_unit_id'])
                ->where('project_id',$data['project_id'])
                ->firstOrFail();
        }

        DB::transaction(function() use ($lead, $orgId, $data) {
            $customer = Customer::updateOrCreate(
                ['lead_id' => $lead->id],
                [
                    'org_id'         => $orgId,
                    'project_id'     => $data['project_id'],
                    'project_unit_id'=> $data['project_unit_id'] ?? null,
                    'name'           => $lead->name,
                    'email'          => $lead->email,
                    'phone'          => $lead->phone,
                ]
            );

            $lead->update([
                'project_id'      => $data['project_id'],
                'project_unit_id' => $data['project_unit_id'] ?? null,
                'status'          => 'booked',
                'is_customer'     => true,
                'customer_id'     => $customer->id,
                'materialized_at' => now(),
            ]);
        });

        return redirect()->route('org.leads.edit',$lead)->with('success','Lead materialised to customer.');
    }

    protected function authorizeAdmin(Lead $lead): void
    {
        abort_unless($lead->org_id === Auth::user()->org_id, 403);
        abort_unless(Auth::user()->hasRole('org admin'), 403);
    }
}
