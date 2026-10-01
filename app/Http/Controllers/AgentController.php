<?php

namespace App\Http\Controllers;

use App\Http\Requests\AgentRequest;
use App\Models\Agent;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class AgentController extends Controller
{
    public function index()
    {
        return view('org.agent.index');
    }

    public function create()
    { 
        $countries = Country::where('country_status',1)->get();
        return view('org.agent.form', compact('countries'));
    }

    


    public function store(AgentRequest $request, $id = null)
    { 
        $data = $request->validated(); 
        $data['active'] = $request->has('active') ? 1 : 0;

         
        $agent = $id ? Agent::findOrFail($id) : new Agent();
 
        if ($request->hasFile('profile_image')) { 
            if ($id && !empty($agent->profile_image)) {
                Storage::disk('public')->delete($agent->profile_image);
            }
 
            $path = $request->file('profile_image')
                ->store('agents', 'public');

            $data['image'] = $path;
        } 
        $agent->fill($data);
        $agent->save();

        return redirect()
            ->route('org.agent-index')
            ->with('success', $id ? 'Agent Updated Successfully' : 'Agent Created Successfully');
    }


    public function get(Request $request)
    {
        $columns = [
            0 => 'id',
            1 => 'image',
            2 => 'name',
            3 => 'phone',
            4 => 'email',
            5 => 'facebook',
            6 => 'twitter',
            7 => 'linkedin',
        ];

        $query = Agent::query();

        $totalData = $query->count();

        $limit = $request->input('length', 10);
        $start = $request->input('start', 0);
        $orderColumnIndex = $request->input('order.0.column', 0);
        $orderColumn = $columns[$orderColumnIndex] ?? 'id';
        $orderDir = $request->input('order.0.dir', 'desc');

        // Custom filter
        if ($request->filled('q')) {
            $s = trim($request->q);
            $query->where(function ($qq) use ($s) {
                $qq->where('name', 'like', "%$s%")
                ->orWhere('email', 'like', "%$s%")
                ->orWhere('phone', 'like', "%$s%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // DataTables search
        if ($request->filled('search.value')) {
            $s = $request->input('search.value');
            $query->where(function ($qq) use ($s) {
                $qq->where('name', 'like', "%$s%")
                ->orWhere('email', 'like', "%$s%")
                ->orWhere('phone', 'like', "%$s%");
            });
        }

        $totalFiltered = $query->count();

        $agents = $query->offset($start)
            ->limit($limit)
            ->orderBy($orderColumn, $orderDir)
            ->get();

        $data = [];
        $sn = $start + 1;

        foreach ($agents as $agent) {

            $image = $agent->image 
                ? '<img src="'.asset('storage/'.$agent->image).'" width="40" height="40" class="rounded-circle">'
                : '<span class="badge bg-secondary">No Image</span>';

            $actions = '
                     
                    <a style="font-size:18px;" href="'.route('org.agent-edit', encrypt($agent->id)).'"
                         class="text-primary me-2" title="Edit">
                        <i class="las la-edit"></i>
                    </a>

                    <form action="'.route('org.agent-destroy', encrypt($agent->id)).'"
                        method="POST"
                        class="d-inline"
                        onsubmit="return confirm(\'Delete this Media ?\');">
                        '.csrf_field().method_field('DELETE').'
                        <button style="font-size:18px;" type="submit" class="btn btn-link text-danger p-0">
                            <i class="las la-trash"></i>
                        </button>
                    </form>
                    
                ';


            $data[] = [
                'sn'       => $sn++,
                'image'    => $image,
                'name'     => '<div class="fw-semibold">'.$agent->name.'</div>',
                'phone'    => $agent->phone,
                'email'    => $agent->email,
                'facebook' => $agent->facebook ? '<a href="'.$agent->facebook.'" target="_blank"><i class="las la-eye"></i> </a>' 
                                : '—',
                'twitter'  => $agent->twitter ? '<a href="'.$agent->twitter.'" target="_blank"><i class="las la-eye"></i> </a>'  : '—',
                'linkedin' => $agent->linkedin ? '<a href="'.$agent->linkedin.'" target="_blank"><i class="las la-eye"></i> </a>'  : '—',
                'actions'  => $actions,
            ];
        }

        return response()->json([
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        ]);
    }

    public function edit($id)
    {
        $agentId = decrypt($id);
        $agent = Agent::findOrFail($agentId);
        $countries = Country::where('country_status',1)->get();
        return view('org.agent.form', compact('agent','countries'));
    }

    public function destroy($agent)
    {
        $id = decrypt($agent);

        $pr = Agent::findOrFail($id);
        $pr->delete();

        return redirect()->back()->with('success', 'Agent deleted.');
    }
    



}
