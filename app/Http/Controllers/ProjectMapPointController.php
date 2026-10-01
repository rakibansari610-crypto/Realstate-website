<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectMapRequest;
use App\Models\Project;
use App\Models\ProjectMapPoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectMapPointController extends Controller
{
    public function store(ProjectMapRequest $request)
    {
        DB::beginTransaction();

        try {

            ProjectMapPoint::create([
                'project_id' => $request->project_id,
                'name'       => $request->name,
                'distance'   => $request->distance,
                'time'       => $request->time,
                'sort_order' => $request->sort_order ?? 0,
            ]);

            DB::commit();

            return back()->with('success', 'Map point added successfully.');

        } catch (\Exception $e) {

            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }


    public function update(ProjectMapRequest $request, $id)
    {
        DB::beginTransaction();
        $id = decrypt($id);
        try {

            $mapPoint = ProjectMapPoint::find($id);
            if (!$mapPoint) {
                return redirect()->back()->with('error', 'Project Map not found.');
            }

            $mapPoint->update([
                'project_id' => $request->project_id,
                'name'       => $request->name,
                'distance'   => $request->distance,
                'time'       => $request->time,
                'sort_order' => $request->sort_order ?? 0,
            ]);

            DB::commit();

            return back()->with('success', 'Map point updated successfully.');

        } catch (\Exception $e) {

            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }




    public function get(Request $request)
    {
        $columns = [
            0 => 'id',
            1 => 'name',
            2 => 'distance',
            3 => 'time',
            4 => 'sort_order',
        ];

        $query = ProjectMapPoint::where('project_id', $request->project_id);

        $totalData = $query->count();
        $totalFiltered = $totalData;

        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');

            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                ->orWhere('distance', 'LIKE', "%{$search}%")
                ->orWhere('time', 'LIKE', "%{$search}%");
            });

            $totalFiltered = $query->count();
        }

        $limit = $request->input('length', 10);
        $start = $request->input('start', 0);

        $orderIndex  = $request->input('order.0.column', 0);
        $orderColumn = $columns[$orderIndex] ?? 'id';

        $allowedOrderColumns = ['id','name','distance','time','sort_order'];
        if (!in_array($orderColumn, $allowedOrderColumns)) {
            $orderColumn = 'id';
        }

        $orderDir = $request->input('order.0.dir', 'asc');

        $projectMap = $query->offset($start)
            ->limit($limit)
            ->orderBy($orderColumn, $orderDir)
            ->get();

        $data = [];
        $sn = $start + 1;

        foreach ($projectMap as $m) {

            $action = '
                <a href="'.route('org.near-by-plan.edit', encrypt($m->id)).'"
                class="text-primary me-2">
                    <i class="las la-edit"></i>
                </a>

                <form action="'.route('org.near-by-plan.destroy', encrypt($m->id)).'"
                    method="POST"
                    class="d-inline"
                    onsubmit="return confirm(\'Delete this Record?\');">
                    '.csrf_field().method_field('DELETE').'
                    <button type="submit" class="btn btn-link text-danger p-0">
                        <i class="las la-trash"></i>
                    </button>
                </form>
            ';
            $hours = floor($m->time / 60);
                $minutes = $m->time % 60;

                $formattedTime = '';

                if ($hours > 0) {
                    $formattedTime .= $hours . ' Hr ';
                }

                if ($minutes > 0) {
                    $formattedTime .= $minutes . ' Min';
                }

              

            $data[] = [
                'id'         => $sn++,
                'name'       => ucwords($m->name),
                'distance'   => number_format($m->distance, 2).' Km',
                 'time' => $formattedTime,
                'sort_order' => $m->sort_order,
                'action'     => $action,
            ];
        }

        return response()->json([
            "draw"            => intval($request->draw),
            "recordsTotal"    => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data"            => $data,
        ]);
    }





    public function edit($id)
    {
        $media_id = decrypt($id);

        $map = ProjectMapPoint::find($media_id);  
        if (!$map) {
            return redirect()->back()->with('error', 'Project Map not found.');
        }
        $project = Project::where('id', $map->project_id)->first();
        return view('org.projects.project_map.edit',compact('map','project'));
    }



    public function destroy($id)
    {
        $id = decrypt($id);

        $mapPoint = ProjectMapPoint::find($id);
        if (!$mapPoint) {
            return redirect()->back()->with('error', 'Project Map not found.');
        }
        $mapPoint->delete();

        return redirect()->back()->with('success', 'Project Map  deleted.');
    }

}
