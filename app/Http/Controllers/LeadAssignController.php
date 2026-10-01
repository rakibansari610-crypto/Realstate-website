<?php


namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\LeadAssignRequest;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LeadAssignController extends Controller
{
    public function edit(Lead $lead)
    {
        $this->authorizeAdmin($lead);

        $assignables = User::where('org_id', Auth::user()->org_id)
            ->whereHas('roles', fn($q) => $q->whereIn('name', ['channel partner','broker','agent']))
            ->orderBy('name')
            ->get(['id','name']);

        return view('org.leads.assign', compact('lead','assignables'));
    }

    public function update(LeadAssignRequest $request, Lead $lead)
    {
        $this->authorizeAdmin($lead);

        $assignedTo = $request->assigned_to;
        if ($assignedTo) {
            // must belong to same org and be in allowed roles
            User::where('org_id', Auth::user()->org_id)
                ->where('id', $assignedTo)
                ->whereHas('roles', fn($q)=>$q->whereIn('name',['channel partner','broker','agent']))
                ->firstOrFail();
        }

        $lead->update(['assigned_to'=>$assignedTo]);

        return redirect()->route('org.leads.edit',$lead)->with('success','Lead assignment updated.');
    }

    protected function authorizeAdmin(Lead $lead): void
    {
        abort_unless($lead->org_id === Auth::user()->org_id, 403);
        abort_unless(Auth::user()->hasRole('org admin'), 403);
    }
}
