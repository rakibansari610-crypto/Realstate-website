<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Http\Requests\LeadRequest;
use App\Models\Lead;
use App\Models\Project;
use App\Models\ProjectUnit;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;

class LeadController extends Controller
{
    // public function index(Request $request)
    // {
    //     $user  = Auth::user();
    //     // $orgId = $user->org_id;

    //     // $leads = Lead::where('org_id', $orgId)
    //     $parentPartnerId = null;
    //     if ($user->hasRole('property consultant')) {
    //         $parentPartnerId = DB::table('partners')
    //             ->where('user_id', $user->id)
    //             ->value('parent_id'); // assumed to be parent USER ID
    //     }

    //     // If user is Channel Partner → fetch all child Property Consultants' user_ids
    //     $childConsultantIds = [];
    //     if ($user->hasRole('channel partner')) {
    //         $childConsultantIds = DB::table('partners')
    //             ->where('parent_id', $user->id) // parent is current Channel Partner (user_id)
    //             ->pluck('user_id')              // property consultants' user ids
    //             ->filter()
    //             ->all();
    //     }

    //     $leads = Lead::when(!$user->hasRole('super admin'), function ($q) use ($user, $parentPartnerId, $childConsultantIds) {
    //         $q->where(function ($qq) use ($user, $parentPartnerId, $childConsultantIds) {
    //             // Always include the user's own leads
    //             $qq->where('assigned_to', $user->id)
    //                 ->orWhere('created_by', $user->id);

    //             // If Property Consultant → include parent Channel Partner's leads
    //             if (!is_null($parentPartnerId)) {
    //                 $qq->orWhere('assigned_to', $parentPartnerId)
    //                     ->orWhere('created_by', $parentPartnerId);
    //             }

    //             // If Channel Partner → include all associated Property Consultants' leads
    //             if (!empty($childConsultantIds)) {
    //                 $qq->orWhereIn('assigned_to', $childConsultantIds)
    //                     ->orWhereIn('created_by', $childConsultantIds);
    //             }
    //         });
    //     })
    //         ->when($request->filled('q'), function ($q) use ($request) {
    //             $s = trim($request->q);
    //             $q->where(function ($qq) use ($s) {
    //                 $qq->where('name', 'like', "%$s%")
    //                     ->orWhere('phone', 'like', "%$s%")
    //                     ->orWhere('email', 'like', "%$s%");
    //             });
    //         })
    //         ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
    //         ->when($request->filled('project_id'), fn($q) => $q->where('project_id', $request->project_id))
    //         ->when($request->filled('assigned_to'), fn($q) => $q->where('assigned_to', $request->assigned_to))
    //         ->with(['project:id,name', 'unit:id,unit_no', 'assignee:id,name'])
    //         ->latest()
    //         ->paginate(20)
    //         ->withQueryString();

    //     $projects  = Project::orderBy('name')->get(['id', 'name']);
    //     $assignees = User::orderBy('name')->get(['id', 'name']);

    //     //  $projects  = Project::where('org_id',$orgId)->orderBy('name')->get(['id','name']);
    //     // $assignees = User::where('org_id',$orgId)->orderBy('name')->get(['id','name']);

    //     return view('org.leads.index', compact('leads', 'projects', 'assignees'));
    // }


    public function index(Request $request)
    {
        $projects  = Project::orderBy('name')->get(['id', 'name']);
        $assignees = User::orderBy('name')->get(['id', 'name']);
       $leadsSources = Lead::select('source')->whereNotNull('source')->distinct()
        ->orderBy('source')->pluck('source');

        return view('org.leads.index', compact('projects', 'assignees','leadsSources'));
    }

    public function get(Request $request)
    {
        $user = Auth::user();
        $parentPartnerId = null;
        if ($user->hasRole('property consultant')) {
            $parentPartnerId = DB::table('partners')
                ->where('user_id', $user->id)
                ->value('parent_id');
        }

        $childConsultantIds = [];
        if ($user->hasRole('channel partner')) {
            $childConsultantIds = DB::table('partners')
                ->where('parent_id', $user->id)
                ->pluck('user_id')
                ->filter()
                ->all();
        }

        $columns = [
            0 => 'leads.id',
            1 => 'leads.name',
            2 => 'leads.phone',
            3 => 'projects.name',
            4 => 'leads.status',
            5 => 'leads.follow_up_at',
            6 => 'assignees.name',
        ];
        $searchValue = $request->input('search.value');

        $query = Lead::leftJoin('projects', 'projects.id', '=', 'leads.project_id')
            ->leftJoin('units', 'units.id', '=', 'leads.project_unit_id')
            ->leftJoin('users as assignees', 'assignees.id', '=', 'leads.assigned_to')
            ->orderBy('leads.id', 'desc')
            ->select(
                'leads.id',
                'leads.name',
                'leads.phone',
                'leads.email',
                'leads.status',
                'leads.source',
                'leads.follow_up_at',
                'projects.name as project_name',
                'units.unit_number',
                'assignees.name as assignee_name',
                'leads.created_by'
            );

        if ($user->hasRole('rm')) {

            $partnerIds = \DB::table('partners')
                ->where('rm_id', $user->id)
                ->pluck('user_id')
                ->toArray();

            if (!empty($partnerIds)) {
                $query->where(function ($qq) use ($user, $partnerIds) {
                    $qq->where('leads.created_by', $user->id)
                        ->orWhere('leads.assigned_to', $user->id)
                        ->orWhereIn('leads.created_by', $partnerIds);
                });
            } else {
                $query->where(function ($qq) use ($user) {
                    $qq->where('leads.created_by', $user->id)
                        ->orWhere('leads.assigned_to', $user->id);
                });
            }
        } else {
            $query->when(!$user->hasRole('super admin'), function ($q) use ($user, $parentPartnerId, $childConsultantIds) {
                $q->where(function ($qq) use ($user, $parentPartnerId, $childConsultantIds) {
                    $qq->where('leads.assigned_to', $user->id)
                        ->orWhere('leads.created_by', $user->id);

                    if (!is_null($parentPartnerId)) {
                        $qq->orWhere('leads.assigned_to', $parentPartnerId)
                            ->orWhere('leads.created_by', $parentPartnerId);
                    }

                    if (!empty($childConsultantIds)) {
                        $qq->orWhereIn('leads.assigned_to', $childConsultantIds)
                            ->orWhereIn('leads.created_by', $childConsultantIds);
                    }
                });
            });
        }




        if ($request->filled('status')) {
            $query->where('leads.status', $request->status);
        }
        if($request->filled('source')){
            $query->where('leads.source', $request->source);
        }
        if ($request->filled('project_id')) {
            $query->where('leads.project_id', $request->project_id);
        }
        if ($request->filled('assigned_to')) {
            $query->where('leads.assigned_to', $request->assigned_to);
        }
        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('leads.name', 'like', "%{$searchValue}%")
                    ->orWhere('leads.phone', 'like', "%{$searchValue}%")
                    ->orWhere('leads.email', 'like', "%{$searchValue}%")
                    ->orWhere('projects.name', 'like', "%{$searchValue}%")
                    ->orWhere('leads.source', 'like', "%{$searchValue}%")
                    ->orWhere('assignees.name', 'like', "%{$searchValue}%");
            });
        }

        $totalData = $query->count();
        $totalFiltered = $totalData;

        $limit = $request->input('length') ?? 10;
        $start = $request->input('start') ?? 0;
        $orderColumnIndex = $request->input('order.0.column', 0);
        $orderColumn = $columns[$orderColumnIndex] ?? 'leads.id';
        $orderDir = $request->input('order.0.dir', 'desc');

        $records = $query->offset($start)
            ->limit($limit)
            ->orderBy($orderColumn, $orderDir)
            ->get();

        $data = [];
        $sn = $start + 1;

        foreach ($records as $lead) {
            $statusBadge = '<span class="badge bg-secondary text-capitalize">'
                . str_replace('_', ' ', $lead->status) . '</span>';

            $followup = $lead->follow_up_at
                ? \Carbon\Carbon::parse($lead->follow_up_at)->format('d M Y H:i')
                : '—';
            $actions = '';
            if (!$user->hasAnyRole(['rm'])) {
                $actions = '<a href="' . route('org.leads.edit', $lead->id) . '" class="btn btn-sm btn-outline-secondary">Edit</a>';
            }
            if ($user->hasRole('org admin')) {
                $actions .= '
                    <a href="' . route('org.leads.assign', $lead->id) . '" class="btn btn-sm btn-outline-primary">Assign</a>
                    <a href="' . route('org.leads.materialise', $lead->id) . '" class="btn btn-sm btn-outline-success">Materialise</a>
                ';
            }

            if (!$user->hasAnyRole(['channel partner|rm'])) {
                $actions .= '
                    <form action="' . route('org.leads.destroy', $lead->id) . '" method="POST" class="d-inline" onsubmit="return confirm(\'Delete this lead?\')">
                        ' . csrf_field() . method_field('DELETE') . '
                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                    </form>';
            }
            if ($user->hasRole('rm')) {
                $actions = '<a href="' . route('org.leads.view', $lead->id) . '" class="btn btn-sm btn-outline-secondary">View</a>';
            }


            $data[] = [
                'sn' => $sn++,
                'lead' => '<div class="fw-semibold">' . e($lead->name) . '</div>',
                'contact' => '<div>' . e($lead->phone) . '</div><div class="text-muted small">' . e($lead->email) . '</div>',
                'project' => '<div>' . e($lead->project_name ?? '—') . '</div><div class="text-muted small">' . e($lead->unit_number ?? '') . '</div>',
                'status' => $statusBadge,
                'follow_up' => $followup,
                'source' => $lead->source,
                'assignee' => e($lead->assignee_name ?? '—'),
                'action' => $actions,
            ];
        }

        return response()->json([
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        ]);
    }



    public function create()
    {
        return view('org.leads.form', ['lead' => new Lead()]);
    }

    public function store(LeadRequest $request)
    {
        $user  = Auth::user();
        $data  = $request->validated();
        $data['created_by'] = $user->id;

        // Duplicate Guard
        $dup = Lead::where('name', $data['name'])
            ->when(!empty($data['phone']), fn($q) => $q->where('phone', $data['phone']))
            ->when(!empty($data['email']), fn($q) => $q->where('email', $data['email']))
            ->exists();

        if ($dup) {
            return back()->withErrors(['name' => 'A similar lead already exists.'])->withInput();
        }

        // Get next lead_no
        $lastLeadNo = Lead::max('lead_no');   // returns NULL if no rows exist
        $data['lead_no'] = $lastLeadNo ? $lastLeadNo + 1 : 1001;
        // start from 1001 if empty (based on your SQL dump starting point)

        // Insert Lead
        $lead = Lead::create($data);

        return redirect()->route('org.leads.edit', $lead)->with('success', 'Lead created.');
    }


    public function edit(Lead $lead)
    {
        // $this->authorizeLeadView($lead);
        $isView = request()->routeIs('org.leads.view');
        $disabled = '';
        $unit = Unit::where('status',1)->get();
        return view('org.leads.view', compact('lead', 'isView', 'disabled','unit'));
    }

    public function update(LeadRequest $request, Lead $lead)
    {
        // $this->authorizeLeadView($lead);
        $lead->update($request->validated());
        return back()->with('success', 'Lead updated.');
    }

    public function destroy(Lead $lead)
    {
        $this->authorizeLeadView($lead);
        $lead->delete();
        return redirect()->route('org.leads.index')->with('success', 'Lead deleted.');
    }

    // Ajax: units for project (all org users can load)
    public function unitsForProject(Project $project)
    {
        $this->authorizeProject($project);
        return response()->json(
            $project->units()->orderBy('unit_no')->get(['id', 'unit_no'])
        );
    }

    protected function authorizeLeadView(Lead $lead): void
    {
        $user = Auth::user();
        abort_unless($lead->org_id === $user->org_id, 403);

        if (!$user->hasRole('org admin')) {
            abort_unless(($lead->assigned_to === $user->id) || ($lead->created_by === $user->id), 403);
        }
    }

    protected function authorizeProject(Project $project): void
    {
        abort_unless($project->org_id === Auth::user()->org_id, 403);
    }
}
