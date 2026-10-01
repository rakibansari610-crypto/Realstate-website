<?php

namespace App\Http\Controllers;

use App\Models\Taxonomy;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TaxonomyController extends Controller
{
    public function index($module)
    {
        $taxonomies = Taxonomy::where('module', $module)
            ->whereNull('parent_id')
            ->with('children')
            ->orderBy('priority')
            ->get();

        return view('org.taxonomies.index', compact('taxonomies', 'module'));
    }

    public function get(Request $request, $module)
    {
        $columns = [
            0 => 'parent.name',
            1 => 'taxonomies.name',
            2 => 'taxonomies.priority',
            3 => 'taxonomies.status',
        ];

        $totalData = Taxonomy::where('module', $module)->count();
        $totalFiltered = $totalData;

        $limit = $request->input('length') ?? 10;
        $start = $request->input('start') ?? 0;
        $orderColumnIndex = $request->input('order.0.column', 0);
        $orderColumn = $columns[$orderColumnIndex] ?? 'taxonomies.id';
        $orderDir = $request->input('order.0.dir', 'desc');
        $searchValue = $request->input('search.value');

        $query = Taxonomy::leftJoin('taxonomies as parent', 'parent.id', '=', 'taxonomies.parent_id')
            ->select(
                'taxonomies.id',
                'taxonomies.name',
                'taxonomies.priority',
                'taxonomies.status',
                'parent.name as parent_name'
            )
            ->where('taxonomies.module', $module);

        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('taxonomies.name', 'like', "%{$searchValue}%")
                    ->orWhere('parent.name', 'like', "%{$searchValue}%")
                    ->orWhere('taxonomies.status', 'like', "%{$searchValue}%");
            });
            $totalFiltered = $query->count();
        }

        $records = $query->offset($start)
            ->limit($limit)
            ->orderBy($orderColumn, $orderDir)
            ->get();

        $data = [];
        foreach ($records as $record) {
            $data[] = [
                'parent' => $record->parent_name ?? '-',
                'name' => $record->name,
                'priority' => $record->priority,
                'status' => ucfirst($record->status),
                'action' => '
                    <a href="' . route('taxonomies.edit', [$module, $record->id]) . '" class="btn btn-sm btn-warning">Edit</a>
                    <form action="' . route('taxonomies.destroy', [$module, $record->id]) . '" method="POST" style="display:inline-block;" onsubmit="return confirm(\'Are you sure to delete this category?\');">
                        ' . csrf_field() . method_field('DELETE') . '
                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                    </form>'
            ];
        }

        return response()->json([
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        ]);
    }



    public function create($module)
    {
        $parenttaxonomies = Taxonomy::where('module', $module)
            ->whereNull('parent_id')
            ->get();

        return view('org.taxonomies.create', compact('parenttaxonomies', 'module'));
    }

    public function store(Request $request, $module)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|string',
            'priority' => 'nullable|integer',
            'status' => 'required|in:active,inactive',
            'parent_id' => 'nullable|exists:taxonomies,id',
        ]);

        try {
            $data = $request->only(['name', 'description', 'thumbnail', 'priority', 'status', 'parent_id']);
            $data['module'] = $module;
            $data['slug'] = Str::slug($data['name']);

            Taxonomy::create($data);

            return redirect()->route('taxonomies.index', $module)->with('success', 'Taxonomy added!');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Failed to add taxonomy: ' . $e->getMessage()]);
        }
    }

    public function edit($module, $category)
    {
        // Retrieve the Taxonomy model instance by id or throw 404 if not found
        $taxonomy = Taxonomy::findOrFail($category);

        if ($taxonomy->module != $module) {
            abort(404);
        }

        $parenttaxonomies = Taxonomy::where('module', $module)
            ->whereNull('parent_id')
            ->where('id', '!=', $taxonomy->id)
            ->get();

        // Pass the taxonomy instance as 'taxonomy' (or another clear variable name)
        return view('org.taxonomies.edit', [
            'taxonomy' => $taxonomy,
            'parenttaxonomies' => $parenttaxonomies,
            'module' => $module,
        ]);
    }


    public function update(Request $request, $module, $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'priority' => 'nullable|integer',
            'status' => 'required|in:active,inactive',
            'parent_id' => 'nullable|exists:taxonomies,id',
        ]);

        $data['slug'] = Str::slug($data['name']);
        $data['module'] = $module;

        try {
            $taxonomy = Taxonomy::find($category);
            $taxonomy->name = $data['name'];
            $taxonomy->slug = Str::slug($data['name']);
            $taxonomy->priority = $data['priority'];
            $taxonomy->module = $module;
            $taxonomy->status = $data['status'];
            $taxonomy->parent_id = $data['parent_id'];
            $updated = $taxonomy->update(); 
            return redirect()->route('taxonomies.index', $module)->with('success', 'Taxonomy updated!');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Failed to update taxonomy: ' . $e->getMessage()]);
        }
    }


    public function destroy($module,  $category)
    {
        $taxonomy = Taxonomy::findOrFail($category);
        if ($taxonomy->module != $module) {
            abort(404);
        }
        $taxonomy->delete();
        return redirect()->route('taxonomies.index', $module)->with('success', 'Taxonomy deleted!');
    }
}
