<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectUnitRequest;
use App\Models\Project;
use App\Models\ProjectUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectUnitController extends Controller
{
    public function index(Project $project)
    {
        // $this->authorizeOrg($project);

        $units = $project->units()->orderBy('tower')->orderBy('floor')->orderBy('unit_no')
            ->paginate(25)->withQueryString();

        return view('org.projects.units.index', compact('project','units'));
    }

    public function create(Project $project)
    {
        // $this->authorizeOrg($project);
        return view('org.projects.units.form', ['project'=>$project,'unit'=>new ProjectUnit()]);
    }

    public function store(ProjectUnitRequest $request, Project $project)
    {
         
         // $this->authorizeOrg($project);
        $data = $request->validated() ; 
        // $data = $request->validated() + ['org_id'=>Auth::user()->org_id];
        $project->units()->create($data);
        return redirect()->route('org.projects.units.index',$project)->with('success','Unit added.');
    }

    public function edit(Project $project, ProjectUnit $unit)
    {
        // $this->authorizeOrg($project);
        abort_unless($unit->project_id === $project->id, 404);
        return view('org.projects.units.form', compact('project','unit'));
    }

    public function update(ProjectUnitRequest $request, Project $project, ProjectUnit $unit)
    { 
        // $this->authorizeOrg($project);
        abort_unless($unit->project_id === $project->id, 404);
        $unit->update($request->validated());
        return back()->with('success','Unit updated.');
    }

    public function destroy(Project $project, ProjectUnit $unit)
    {
        // $this->authorizeOrg($project);
        abort_unless($unit->project_id === $project->id, 404);
        $unit->delete();
        return redirect()->route('org.projects.units.index',$project)->with('success','Unit removed.');
    }

    protected function authorizeOrg(Project $project): void
    {
        abort_unless($project->org_id === Auth::user()->org_id, 403);
    }
}
