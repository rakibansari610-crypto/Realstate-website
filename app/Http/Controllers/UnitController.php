<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Developer;
use App\Models\Project;
use App\Models\Taxonomy;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class UnitController extends Controller
{
    public function index(Request $request)
    {
        // $query = Unit::query();

        // if ($search = $request->query('q')) {
        //     $query->where('code', 'like', "%{$search}%")
        //         ->orWhere('property_name', 'like', "%{$search}%");
        // }

        // $units = $query->paginate(20);

        return view('org.units.index');
    }

    public function get(Request $request)
    {

        $columns = [
            0 => 'units.id',
            1 => 'projects.name',
            2 => 'units.property_name',
            3 => 'developers.name',
            4 => 'ut.name',
            5 => 'st.name',
            6 => 'creators.name',
            7 => 'units.id',
        ];

        $totalData = Unit::count();
        $totalFiltered = $totalData;
        $limit = $request->input('length') ?? 10;
        $start = $request->input('start') ?? 0;
        $searchValue = $request->input('search.value');
        $orderColumn = 'units.id';
        $orderDir = 'desc';
        if ($request->has('order.0.column')) {
            $orderColumnIndex = $request->input('order.0.column');
            $orderDir = $request->input('order.0.dir') ?? 'desc';
            $orderColumn = $columns[$orderColumnIndex] ?? 'units.id';
        }

        $query = Unit::leftJoin('projects', 'units.project_id', '=', 'projects.id')
            ->leftJoin('developers', 'units.developer_id', '=', 'developers.id')
            ->leftJoin('taxonomies as ut', 'ut.id', '=', 'units.unit_type')
            ->leftJoin('taxonomies as st', 'st.id', '=', 'units.sub_type')
            ->leftJoin('users as creators', 'units.created_by', '=', 'creators.id')
            ->select(
                'units.id',
                'projects.name as project_name',
                'developers.name as developer',
                'units.property_name',
                'ut.name as unit_type_name',
                'st.name as sub_type_name',
                'units.status',
                'units.unit_for',
                'creators.name as created_by_name',
                'units.is_active',
            );

        if (! empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('units.property_name', 'like', "%{$searchValue}%")
                    ->orWhere('projects.name', 'like', "%{$searchValue}%")
                    ->orWhere('developers.name', 'like', "%{$searchValue}%")
                    ->orWhere('ut.name', 'like', "%{$searchValue}%")
                    ->orWhere('creators.name', 'like', "%{$searchValue}%")
                    ->orWhere('st.name', 'like', "%{$searchValue}%");
            });
            $totalFiltered = $query->count();
        }

        $records = $query->offset($start)
            ->limit($limit)
            ->orderBy($orderColumn, $orderDir)
            ->get();

        $data = [];
        $sn = $start + 1;

        foreach ($records as $record) {
            $nestedData['sn'] = $sn;
            $nestedData['unit_for'] = $record->unit_for ?? 'N/A';
            $nestedData['project_name'] = $record->project_name ?? 'N/A';
            $nestedData['property_name'] = $record->property_name ?? 'N/A';
            $nestedData['developer'] = $record->developer ?? 'N/A';
            $nestedData['unit_type'] = $record->unit_type_name ?? '-';
            $nestedData['sub_type'] = $record->sub_type_name ?? '-';

            if (auth()->user()->hasAnyRole(['org admin', 'admin'])) {
                $nestedData['created_by'] = $record->created_by_name ?? '-';
            }

            $nestedData['is_active'] = $record->is_active == '1'
                ? '<span class="badge bg-success">Active</span>'
                : '<span class="badge bg-danger">Deactive</span>';

            $encryptedId = Crypt::encrypt($record->id);
            $user = Auth::user();
            $viewUrl = url('units/'.$encryptedId.'/view');
            $nestedData['action'] = '';
            if (! $user->hasRole('channel partner')) {
                $nestedData['action'] .= '<a href="'.route('units.edit', $encryptedId).'" class="btn btn-sm btn-outline-secondary">Edit</a> ';
            }

            $nestedData['action'] .= '<a href="'.route('units.view', $encryptedId).'" class="btn btn-sm btn-outline-success">View</a> ';

            if (! $user->hasAnyRole(['data entry', 'channel partner'])) {
                $nestedData['action'] .= '
                    <form action="'.route('units.destroy', $encryptedId).'" method="POST" class="d-inline">
                        '.csrf_field().method_field('DELETE').'
                        <button class="btn btn-sm btn-outline-danger" onclick="return confirm(\'Delete unit?\')">Delete</button>
                    </form>
                ';
            }

            $data[] = $nestedData;
            $sn++;
        }

        return response()->json([
            'draw' => intval($request->input('draw')),
            'iTotalRecords' => intval($totalData),
            'iTotalDisplayRecords' => intval($totalFiltered),
            'aaData' => $data,
        ]);
    }

    public function create(Project $project)
    {
        $developers = Developer::all();
        $projects = Project::all();
        $parking_type = Taxonomy::where('module', 'parking_type')->whereNull('parent_id')->get();
        $unit_type = Taxonomy::where('module', 'unit_type')->whereNull('parent_id')->get();
        $currency = Taxonomy::where('module', 'currency')->whereNull('parent_id')->get();
        $amenities = Taxonomy::where('module', 'amenities')->whereNotNull('parent_id')->get();
        $countries = Country::where('country_status', 1)
            ->orderBy('country_name')
            ->get(['id', 'country_name']);

        return view('org.units.create', compact('project', 'developers', 'projects', 'parking_type', 'unit_type', 'currency', 'amenities', 'countries'));
    }

    public function store(Request $request, Project $project)
    { 
        $data = $request->validate([
            'unit_for' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:255',
            'property_name' => 'required|string|max:255',
            'developer_id' => 'nullable|exists:developers,id',
            'project_id' => 'nullable|exists:projects,id',
            'plan_type' => 'nullable|string|max:255',
            'street' => 'nullable|string|max:255',
            'unit_number' => 'nullable|string|max:255',
            'owner' => 'nullable|string|max:255',
            'listing_agent' => 'nullable|string|max:255',
            'furnished' => 'nullable|string|max:255',
            'size' => 'nullable|string|max:255',
            'view' => 'nullable|string|max:255',
            'parking_spaces' => 'nullable|string|max:255',
            'parking_number' => 'nullable|string|max:255',
            'parking_type' => 'nullable|string|max:255',
            'unit_fitting' => 'nullable|string|max:255',
            'unit_type' => 'nullable|string|max:255',
            'sub_type' => 'nullable|string|max:255',
            'handover_date' => 'nullable|string|max:20',
            'listing_date' => 'nullable|string|max:20',
            'no_of_bathrooms' => 'nullable|string|max:255',
            'bedrooms' => 'nullable|string|max:255',
            'plot_area' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:10',
            'selling_price' => 'nullable|string|max:50',
            'currency_id' => 'nullable|string|max:10',
            'amenities' => 'nullable|array',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'country_id' => 'nullable|exists:countries,id',
            'city_name' => 'nullable|string|max:255',
            'property_overview' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'india_term' => 'nullable|string',
            'uae_term' => 'nullable|string',
            'property_type' => 'nullable|in:is_for_sale,is_for_rent',
            'meta_title' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'locality' => 'nullable|string',

        ]);

        if ($request->has('building_amenities') && is_array($request->building_amenities)) {
            $data['building_amenities'] = implode(',', $request->building_amenities);
        } else {
            $data['building_amenities'] = null;
        }
        // multiple images upload
        if ($request->hasFile('images')) {
            $paths = [];
            foreach ($request->file('images') as $image) {
                $paths[] = $image->store('units', 'public');
            }
            $data['images'] = $paths;
        }
        // $data['is_for_sale'] = $request->has('is_for_sale') ? 1 : 0;
        // $data['is_for_rent'] = $request->has('is_for_rent') ? 1 : 0;

        if ($request->property_type == 'is_for_sale') {
            $data['is_for_sale'] = 1;
            $data['is_for_rent'] = 0;
        } elseif ($request->property_type == 'is_for_rent') {
            $data['is_for_sale'] = 0;
            $data['is_for_rent'] = 1;
        }

        $data['rent_price'] = $request->rent_price;
        $data['rent_period'] = $request->rent_period;

        $data['latitude'] = $request->latitude;
        $data['longitude'] = $request->longitude;
        $data['is_featured'] = $request->has('is_featured') ? 1 : 0;

        $data['updated_by'] = Auth::id();
        $data['created_by'] = Auth::id();

        $data['india_term'] = $request->india_term;
        $data['uae_term'] = $request->uae_term;
        $data['meta_title'] = $data['meta_title'] ?? $data['property_name'];
        $data['meta_description'] = $data['meta_description'] ?? Str::limit(strip_tags($data['property_name'] ?? $data['property_name']), 160);
        $data['meta_keywords'] = $data['meta_keywords'] ?? Str::slug($data['property_name'], ',');
        $data['slug'] = Str::slug($data['property_name']);
        // $data['project_id'] = $data['project_id']; // enforce current project
        Unit::create($data);

        return redirect()->route('units.index', $project)->with('success', 'Unit created successfully.');
        // return redirect()->route('projects.units.index', $project)->with('success', 'Unit created successfully.');
    }

    public function edit(Project $project, $id)
    {
        $id = Crypt::decrypt($id);
        $unit = Unit::findOrFail($id);
        $developers = Developer::all();
        $projects = Project::all();

        $parking_type = Taxonomy::where('module', 'parking_type')->whereNull('parent_id')->get();
        $unit_type = Taxonomy::where('module', 'unit_type')->whereNull('parent_id')->get();
        $currency = Taxonomy::where('module', 'currency')->whereNull('parent_id')->get();
        $amenities = Taxonomy::where('module', 'amenities')->whereNotNull('parent_id')->get();
        $countries = Country::where('country_status', 1)
            ->orderBy('country_name')
            ->get(['id', 'country_name']);

        $isView = request()->routeIs('projects.units.view');
        $unit->load('unitImages');

        return view('org.units.edit', compact('project', 'unit', 'developers', 'projects', 'unit_type', 'parking_type', 'currency', 'amenities', 'isView', 'countries'));
    }

    public function update(Request $request, Project $project, Unit $unit)
    {
        // dd($request->all());
        try {
            $data = $request->validate([
                'unit_for' => 'nullable|string|max:255',
                'code' => 'nullable|string|max:255',
                'property_name' => 'nullable|string|max:255',
                'developer_id' => 'nullable|exists:developers,id',
                'project_id' => 'nullable|exists:projects,id',
                'plan_type' => 'nullable|string|max:255',
                'street' => 'nullable|string|max:255',
                'unit_number' => 'nullable|string|max:255',
                'owner' => 'nullable|string|max:255',
                'listing_agent' => 'nullable|string|max:255',
                'furnished' => 'nullable|string|max:255',
                'size' => 'nullable|string|max:255',
                'view' => 'nullable|string|max:255',
                'parking_spaces' => 'nullable|string|max:255',
                'parking_number' => 'nullable|string|max:255',
                'parking_type' => 'nullable|string|max:255',
                'unit_fitting' => 'nullable|string|max:255',
                'unit_type' => 'nullable|string|max:255',
                'sub_type' => 'nullable|string|max:255',
                'handover_date' => 'nullable|string|max:20',
                'listing_date' => 'nullable|string|max:20',
                'no_of_bathrooms' => 'nullable|string|max:255',
                'bedrooms' => 'nullable|string|max:255',
                'plot_area' => 'nullable|string|max:255',
                'status' => 'nullable|string|max:10',
                'selling_price' => 'nullable|string|max:50',
                'currency_id' => 'nullable|string|max:10',
                'building_amenities' => 'nullable|array',
                'images' => 'nullable|array',
                'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
                'country_id' => 'nullable|exists:countries,id',
                'city_name' => 'nullable|string|max:255',
                'property_overview' => 'nullable|string',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'india_term' => 'nullable|string',
                'uae_term' => 'nullable|string',
                'property_type' => 'nullable|in:is_for_sale,is_for_rent',
                'meta_title' => 'nullable|string',
                'meta_description' => 'nullable|string',
                'meta_keywords' => 'nullable|string',
                'is_auto_slug' => 'nullable|string',
                'locality' => 'nullable|string',
            ]);

            // Amenities
            $data['building_amenities'] = $request->has('building_amenities') && is_array($request->building_amenities)
                ? implode(',', $request->building_amenities)
                : null;

            $data['unit_for'] = $request->unit_for;
            $data['latitude'] = $request->latitude;
            $data['longitude'] = $request->longitude;

            $data['rent_price'] = $request->rent_price;
            $data['rent_period'] = $request->rent_period;
            $data['is_featured'] = $request->has('is_featured') ? 1 : 0;

            // Sale / Rent flags
            if ($request->property_type === 'is_for_sale') {
                $data['is_for_sale'] = 1;
                $data['is_for_rent'] = 0;
            } else {
                $data['is_for_sale'] = 0;
                $data['is_for_rent'] = 1;
            }

            $data['status'] = $request->has('status') ? 1 : 0;

            // Images (⚠️ still using old JSON column)
            if ($request->hasFile('images')) {
                $existingImages = $unit->images ?? [];
                $newImages = [];

                foreach ($request->file('images') as $image) {
                    $newImages[] = $image->store('units', 'public');
                }

                $data['images'] = array_merge($existingImages, $newImages);
            }

            $data['updated_by'] = Auth::id();
            $data['india_term'] = $request->india_term;
            $data['uae_term'] = $request->uae_term;
            $data['meta_title'] = $request->meta_title;
            $data['meta_description'] = $request->meta_description;
            $data['meta_keywords'] = $request->meta_keywords;

            $data['is_auto_slug'] = $request->has('autoSlug') ? 1 : 0;

            // Slug
            $slug = $data['is_auto_slug']
                ? Str::slug($data['property_name'])
                : $request->project_slug;

            $exists = Project::where('slug', $slug)
                ->where('id', '!=', $project->id)
                ->exists();

            if ($exists) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['slug' => 'This slug already exists']);
            }

            $data['slug'] = $slug;

            $unit->update($data);

            return redirect()
                ->route('units.index', $project)
                ->with('success', 'Unit updated successfully.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation error (handled separately)
            return redirect()->back()
                ->withInput()
                ->with('error', 'Something went wrong while updating the unit.'.$e->getMessage());

        } catch (\Throwable $e) {
            // Log full error

            return redirect()->back()
                ->withInput()
                ->with('error', 'Something went wrong while updating the unit.'.$e->getMessage());
        }
    }

    public function destroy(Project $project, $id)
    {
        $id = Crypt::decrypt($id);
        $unit = Unit::findOrFail($id);
        $unit->delete();

        return redirect()->route('units.index', $project)->with('success', 'Unit deleted successfully.');
    }

    public function getProjectsByDeveloper(Request $request)
    {
        $developer_id = $request->developer_id;

        $projects = Project::where('developer_id', $developer_id)->get(['id', 'name']);

        return response()->json($projects);
    }

    public function getSubTypes(Request $request)
    {
        $unit_type_id = $request->unit_type_id;
        $subTypes = Taxonomy::where('module', 'unit_type')
            ->where('parent_id', $unit_type_id)
            ->get(['id', 'name']);

        return response()->json($subTypes);
    }
}
