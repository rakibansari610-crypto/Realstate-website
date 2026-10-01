<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectMaterialRequest;
use App\Models\Project;
use App\Models\ProjectMaterial;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProjectMaterialController extends Controller
{
    public function index(Project $project)
    { 
        // $this->authorizeOrg($project);
        $materials = $project->materials()->latest()->paginate(24);
        return view('org.projects.materials.index', compact('project','materials'));
    }

    public function store(ProjectMaterialRequest $request, Project $project)
    {
        // $this->authorizeOrg($project);
        $data = $request->validated();
        // $data['org_id'] = Auth::user()->org_id;
        $data['uploaded_by'] = Auth::id();
        $data['updated_by'] = Auth::id();
        $data['created_by'] = Auth::id();

        if (in_array($data['type'], ['image','pdf','doc']) && $request->hasFile('file')) {
            $filename = Str::uuid().'.'.$request->file('file')->extension();
            $data['file_path'] = $request->file('file')
                ->storeAs("orgs/projects/{$project->id}/materials", $filename, 'public');
        }

        $project->materials()->create($data);

        return back()->with('success','Material added.');
    }

    public function destroy(Project $project, ProjectMaterial $material)
    {
        // $this->authorizeOrg($project);
        abort_unless($material->project_id === $project->id, 404);

        if ($material->file_path) Storage::disk('public')->delete($material->file_path);
        $material->delete();
        return back()->with('success','Material deleted.');
    }

    protected function authorizeOrg(Project $project): void
    {
        abort_unless($project->org_id === Auth::user()->org_id, 403);
    }
}
