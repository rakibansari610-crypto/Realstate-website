<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectRequest;
use App\Models\Amenities;
use App\Models\Category;
use App\Models\Country;
use App\Models\Developer;
use App\Models\Project;
use App\Models\ProjectAmenities;
use App\Models\ProjectMedia;
use App\Models\ProjectTypicalUnits;
use App\Models\SubCategory;
use App\Models\Taxonomy;
use App\Services\SeoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    protected $seoService;

    public function __construct(SeoService $seoService)
    {
        $this->seoService = $seoService;
    }

    private function applyProjectVisibility($query)
    {
        $user = auth()->user();

        if ($user->hasRole('data entry')) {
            $query->where('country_id', $user->country_id);
        }

        return $query;
    }

    public function index(Request $request)
    {
        // $orgId = Auth::user()->org_id;

        // $projects = Project::
        // // $projects = Project::where('org_id',$orgId)
        //     when($request->filled('q'), fn($q)=>$q->where('name','like','%'.$request->q.'%'))
        //     ->with('developer:id,name','category:id,category_name',
        //     'subcategory:id,sub_category_name')
        //     ->orderBy('id','desc')
        //     ->with('creator')
        //     ->paginate(20)->withQueryString();

        return view('org.projects.index');
    }

    public function get(Request $request)
    {

        $columns = [
            0 => 'id',
            1 => 'name',
            2 => 'developer_id',
            3 => 'min_price',
            4 => 'location',
            5 => 'creator_id',
            6 => 'status',
            7 => 'cover_image_path',
            8 => 'actions',
        ];

        $totalData = Project::count();
        $totalFiltered = $totalData;

        $limit = $request->input('length', 10);
        $start = $request->input('start', 0);
        $orderColumn = $columns[$request->input('order.0.column', 0)];
        $orderDir = $request->input('order.0.dir', 'desc');

        $query = Project::with('developer:id,name', 'category:id,category_name', 'subcategory:id,sub_category_name', 'creator:id,name');

        $query = $this->applyProjectVisibility($query);

        if (! empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhereHas('developer', fn ($d) => $d->where('name', 'LIKE', "%{$search}%"))
                    ->orWhere('location', 'LIKE', "%{$search}%")
                    ->orWhere('slug', 'LIKE', "%{$search}%")
                    ->orWhere('city', 'LIKE', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");
            });

            $totalFiltered = $query->count();
        }
        $projects = $query->offset($start)
            ->limit($limit)
            ->orderBy($orderColumn, $orderDir)
            ->get();

        $data = [];
        $sn = $start;
        foreach ($projects as $p) {
            $sn++;
            $actions = '';
            if (! auth()->user()->hasAnyRole(['channel partner'])) {
                $actions .= '<a href="'.route('org.projects.edit', $p).'" class="btn btn-sm btn-outline-secondary">Edit</a> ';
                $actions .= '<a href="'.route('org.projects.units.index', $p).'" class="btn btn-sm btn-outline-primary">Unit</a> ';
            }
            $actions .= '<a href="'.route('org.projects.view', $p).'" class="btn btn-sm btn-outline-success">View</a> ';
            $actions .= '<a href="'.route('org.projects.materials.index', $p).'" class="btn btn-sm btn-outline-info">Materials</a> ';
            if (! auth()->user()->hasAnyRole(['data entry', 'channel partner'])) {
                $actions .= '<form action="'.route('org.projects.destroy', $p).'" method="post" class="d-inline" onsubmit="return confirm(\'Delete this project?\')">'
                    .csrf_field().method_field('DELETE').
                    '<button class="btn btn-sm btn-outline-danger">Delete</button></form>';
            }

            $data[] = [
                $sn,
                $p->name,
                $p->developer?->name,
                rtrim(rtrim($p->min_price, '0'), '.').' - '.rtrim(rtrim($p->max_price, '0'), '.'),
                $p->location.' - '.$p->city,
                $p->creator?->name ?? '—',
                '<span class="badge bg-secondary text-capitalize">'.str_replace('_', ' ', $p->status).'</span>',
                $p->cover_image_path ? '<img src="'.Storage::disk('public')->url($p->cover_image_path).'" style="height:40px;border-radius:4px;">' : '',
                $actions,
            ];
        }
        $json_data = [
            'draw' => intval($request->input('draw')),
            'recordsTotal' => intval($totalData),
            'recordsFiltered' => intval($totalFiltered),
            'data' => $data,
        ];

        return response()->json($json_data);
    }

    public function create()
    {
        // $orgId = Auth::user()->org_id;
        $category = Category::where('status', 1)->get();
        $sub_category = SubCategory::where('status', 1)->get();
        $developers = Developer::orderBy('name')->get(['id', 'name']);
        $countries = Country::where('country_status', 1)
            ->orderBy('country_name')
            ->get(['id', 'country_name']);
        $types = Taxonomy::where('module', 'unit_type')
            ->whereNull('parent_id')
            ->get();
        $category_type = Taxonomy::where('module', 'unit_type')
            ->whereIn('name', ['Residential', 'Commercial'])
            ->select('name')
            ->distinct()
            ->get();

        // $developers = Developer::where('org_id',$orgId)->orderBy('name')->get(['id','name']);
        return view('org.projects.form', ['project' => new Project, 'developers' => $developers, 'sub_category' => $sub_category, 'category' => $category, 'countries' => $countries, 'types' => $types, 'category_type' => $category_type]);
    }

    public function store(ProjectRequest $request)
    {
        $orgId = Auth::user()->org_id;
        $data = $request->validated();
        $data['org_id'] = $orgId;
        $data['slug'] = Str::slug($data['name']);
        $data['updated_by'] = Auth::id();
        $data['created_by'] = Auth::id();
        $data['meta_title'] = $data['meta_title'] ?? $data['name'];
        $data['meta_description'] = $data['meta_description'] ?? Str::limit(strip_tags($data['description'] ?? $data['name']), 160);
        $data['meta_keywords'] = $data['meta_keywords'] ?? Str::slug($data['name'], ',');
        $data['featured'] = $request->has('featured') ? 1 : 0;
        $data['category_type'] = $request->category_type;
        $data['sub_category'] = $request->sub_category;
        $project = Project::create($data);

        // if ($request->hasFile('cover')) {
        //     $filename = Str::uuid() . '.' . $request->file('cover')->extension();
        //     $data['cover_image_path'] = $request->file('cover')
        //         ->storeAs("orgs/{$orgId}/projects/covers", $filename, 'public');
        // }
        if ($request->hasFile('cover')) {
            $file = $request->file('cover');
            $filename = Str::uuid().'.'.$file->extension();
            $path = $file->storeAs('projects/cover', $filename, 'public');
            // full URL
            $url = asset('storage/'.$path);
            // get image size
            [$width, $height] = getimagesize($file);
            // $project = Project::create($data);
            ProjectMedia::create([
                'project_id' => $project->id,
                'collection' => 'cover',
                'url' => $url,
                'mime' => $file->getMimeType(),
                'size' => $file->getSize(),
                'width' => $width,
                'height' => $height,
                'sort_order' => 1,
                'meta' => json_encode([
                    'url' => $url,
                    'metadata' => [
                        'mime' => $file->getMimeType(),
                        'size' => $file->getSize(),
                        'width' => $width,
                        'height' => $height,
                    ],
                ]),
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
        }

        return redirect()->route('org.projects.edit', $project->id)->with('success', 'Project created.');
    }

    public function edit(Project $project)
    {
        // $this->authorizeOrg($project);
        $orgId = Auth::user()->org_id;

        // $category = Category::where('status',1)->get();
        // $category = Taxonomy::where('module', 'amenities')->whereNull('parent_id')->get();
        // $sub_category = SubCategory::where('status', 1)->get();
        $amenities = Amenities::where('is_active', 1)->get();

        $developers = Developer::where('org_id', $orgId)->orderBy('name')->get(['id', 'name']);
        $countries = Country::where('country_status', 1)
            ->orderBy('country_name')
            ->get(['id', 'country_name', 'country_currency']);
        $types = Taxonomy::where('module', 'unit_type')
            ->whereNull('parent_id')
            ->get();
        $minPrice = ProjectTypicalUnits::where('project_id', $project->id)
            ->min('from_price');
        $maxPrice = ProjectTypicalUnits::where('project_id', $project->id)
            ->max('to_price');

        $isView = request()->routeIs('org.projects.view');
        $category_type = Taxonomy::where('module', 'unit_type')
            ->whereIn('name', ['Residential', 'Commercial'])
            ->select('name')
            ->distinct()
            ->get();

        return view('org.projects.form', compact('project', 'developers', 'isView', 'countries', 'amenities', 'types', 'maxPrice', 'minPrice', 'category_type'));
    }

    public function update(ProjectRequest $request, Project $project)
    {
        $data = $request->validated();

        // $data['slug'] = Str::slug($data['name']);
        $data['is_auto_slug'] = $request->has('autoSlug') ? 1 : 0;
        if ($data['is_auto_slug']) {
            $slug = Str::slug($data['name']);

        } else {
            $slug = $request->project_slug;
        }
        $exists = Project::where('slug', $slug)->where('id', '!=', $project->id)->exists();
        if ($exists) {
            return redirect()->back()->withInput()->withErrors(['blog_slug' => 'This slug already exists']);
        }

        if ($request->hasFile('cover')) {
            $file = $request->file('cover');
            $filename = Str::uuid().'.'.$file->extension();
            $path = $file->storeAs('projects/cover', $filename, 'public');
            // full URL
            $url = asset('storage/'.$path); 
            [$width, $height] = getimagesize($file); 
            ProjectMedia::create([
                'project_id' => $project->id,
                'collection' => 'cover',
                'url' => $url,
                'mime' => $file->getMimeType(),
                'size' => $file->getSize(),
                'width' => $width,
                'height' => $height,
                'sort_order' => 1,
                'meta' => json_encode([
                    'url' => $url,
                    'metadata' => [
                        'mime' => $file->getMimeType(),
                        'size' => $file->getSize(),
                        'width' => $width,
                        'height' => $height,
                    ],
                ]),
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
        }


        $data['slug'] = $slug;
        $data['updated_by'] = Auth::id();
        $data['featured'] = $request->has('featured') ? 1 : 0;
        $data['category_type'] = $request->category_type;
        $data['sub_category'] = $request->sub_category;
        $project->update($data);

        return back()->with('success', 'Project updated.');
    }

    // public function update(ProjectRequest $request, Project $project)
    // {
    //     // $this->authorizeOrg($project);

    //     $data = $request->validated();
    //     $data['slug'] = Str::slug($data['name']);
    //     $data['updated_by'] = Auth::id();

    //     // if ($request->hasFile('cover')) {
    //     //     $orgId = Auth::user()->org_id;
    //     //     $filename = Str::uuid() . '.' . $request->file('cover')->extension();
    //     //     $path = $request->file('cover')->storeAs("orgs/{$orgId}/projects/covers", $filename, 'public');
    //     //     if ($project->cover_image_path) Storage::disk('public')->delete($project->cover_image_path);
    //     //     $data['cover_image_path'] = $path;
    //     // }

    //     $project->update($data);
    //     return back()->with('success', 'Project updated.');
    // }

    public function destroy(Project $project)
    {
        // $this->authorizeOrg($project);
        if ($project->cover_image_path) {
            Storage::disk('public')->delete($project->cover_image_path);
        }
        $project->delete();

        return redirect()->route('org.projects.index')->with('success', 'Project deleted.');
    }

    protected function authorizeOrg(Project $project): void
    {
        abort_unless($project->org_id === Auth::user()->org_id, 403);
    }

    public function getSubCategories($category_id, $project_id)
    {
        $subcategories = Taxonomy::where('module', 'amenities')->where('parent_id', $category_id)->whereNotNull('parent_id')->get();
        $projectAmenities = ProjectAmenities::where('project_id', $project_id)->first();

        $selectedIds = [];
        if ($projectAmenities && ! empty($projectAmenities->sub_category_id)) {
            $selectedIds = explode(',', $projectAmenities->sub_category_id);
        }

        if ($subcategories->isEmpty()) {
            return '<p class="text-muted">No subcategories found for this category.</p>';
        }
        $html = '<div class="row">';
        foreach ($subcategories as $sub) {
            $checked = in_array($sub->id, $selectedIds) ? 'checked' : '';
            $html .= '
                <div class="col-md-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="sub_category_ids[]"
                            value="'.$sub->id.'" id="sub_'.$sub->id.'" '.$checked.'>
                        <label class="form-check-label" for="sub_'.$sub->id.'">
                            '.$sub->name.'
                        </label>
                    </div>
                </div>
            ';
        }
        $html .= '</div>';

        return $html;
    }

    public function project(Request $request)
    {
        $seo = $this->seoService->get('project_list');
        //    dd(session()->all());
        $country_code = session('country_code') ?? null;
        $country_auto = session('country_auto') ?? null;

        // dd($country_code,session()->all());
        $countries = Country::where('country_code', $country_code)->first();
        if (! $countries) {
            $countries = Country::where('country_code', 'AED')->first();
        }

        $slug = $request->slug;
        $d_data = null;
        if ($slug) {
            $d_data = Developer::where('slug', $slug)->first();
        }
        $perPage = 20;
        $query = Project::select('projects.*')
            // ->where('projects.sale_status', '!=', 'out_of_stock')
            ->where('country_id', $countries->id)
            ->where(function ($q) {
                $q->where('projects.sale_status', '!=', 'out_of_stock')
                    ->orWhereNull('projects.sale_status');
            });

        if ($d_data) {
            $query->where('projects.developer_id', $d_data->id);
        }
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;

            $query->where(function ($q) use ($keyword) {
                $q->where('projects.name', 'like', "%{$keyword}%")
                    ->orWhere('projects.building_description', 'like', "%{$keyword}%");
            });
        }

        // if ($request->filled('location')) {
        //     $query->where('projects.location', 'like', "%{$request->location}%");
        // }

        if ($request->filled('location')) {

            $locations = $request->location; // array

            $query->where(function ($q) use ($locations) {

                foreach ($locations as $location) {

                    $location = trim($location);

                    // explode locality | city
                    $parts = array_map('trim', explode('|', $location));

                    $locality = $parts[0] ?? null;
                    $city = $parts[1] ?? null;

                    $q->orWhere(function ($sub) use ($locality, $city) {

                        // BOTH locality and city exist
                        if ($locality && $city) {

                            $sub->where('projects.locality', 'LIKE', "%{$locality}%")
                                ->where('projects.city', 'LIKE', "%{$city}%");

                        }
                        // only locality
                        elseif ($locality) {

                            $sub->where('projects.locality', 'LIKE', "%{$locality}%")
                                ->orwhere('projects.city', 'LIKE', "%{$city}%");

                        }
                        // only city
                        elseif ($city) {

                            $sub->where('projects.locality', 'LIKE', "%{$locality}%")
                                ->orwhere('projects.city', 'LIKE', "%{$city}%");

                        }

                    });

                }

            });

        }

        $projects = $query
    ->selectRaw('projects.*, EXISTS(
        SELECT 1 FROM project_media pm 
        WHERE pm.project_id = projects.id 
        AND pm.collection IN ("cover", "lobby")
    ) as has_media')
    ->orderByDesc('has_media')
    ->orderByDesc('projects.id')
    ->paginate($perPage)
    ->withQueryString();
        if ($projects->currentPage() > $projects->lastPage() && $projects->total() > 0) {
            abort(404);
        }

        $projectIds = $projects->getCollection()->pluck('id')->all();

        // $mediaMap = DB::table('project_media')
        //     ->whereIn('project_id', $projectIds)
        //     ->select('project_id', DB::raw('MIN(url) as media_url'))
        //     ->groupBy('project_id')
        //     ->pluck('media_url', 'project_id');
        $mediaMap = DB::table('project_media')
        ->whereIn('project_id', $projectIds)
        ->whereIn('collection', ['cover', 'lobby'])
        ->select(
            'project_id',
            'url',
            DB::raw("
                CASE 
                    WHEN collection = 'cover' THEN 1
                    WHEN collection = 'lobby' THEN 2
                END as priority
            ")
        )
        ->orderBy('project_id')
        ->orderBy('priority')
        ->orderByDesc('id')
        ->get()
        ->groupBy('project_id')
        ->map(function ($items) {
            return $items->first()->url;
        });

        $unitsGrouped = DB::table('project_typical_units')
            ->whereIn('project_id', $projectIds)
            ->select(
                'id',
                'project_id',
                'bedrooms',
                'currency_code',
                'from_price',
                'to_price',
                'from_size_sqft',
                'to_size_sqft',
                'from_size_m2',
                'to_size_m2'
            )
            ->orderBy('bedrooms')
            ->get()
            ->groupBy('project_id');

        $result = $projects->getCollection()->map(function ($p) use ($mediaMap, $unitsGrouped) {
            return [
                'project' => $p,
                'media_url' => $mediaMap[$p->id] ?? null,
                'technical_units' => ($unitsGrouped[$p->id] ?? collect())->values(), // array like [0=>..,1=>..]
            ];
        })->values();

        return view('project_list', ['data' => $result, 'countries' => $countries, 'paginator' => $projects, 'dev_data' => $d_data, 'seo' => $seo]);
    }

    public function project_details(Request $request)
    {

        $data = Project::where('projects.slug', $request->slug)->leftJoin('developers', 'developers.id', '=', 'projects.developer_id')->select('projects.*', 'developers.name as developer_name')->first();
        abort_unless($data, 404);

        $seo = $this->seoService->get('project', [
            'project_name' => $data->name,
            'city' => $data->city ?? '',
            'developer' => $data->developer_name ?? '',
            'price' => $data->min_price ?? '',
            'location' => $data->location ?? '',
        ]);
        if (! $data) {
            return redirect()->back();
        }
        $amenities = DB::table('amenity_project')
            ->leftJoin('amenities', 'amenity_project.amenity_id', '=', 'amenities.id')
            ->select('amenities.*')
            ->where('amenity_project.project_id', $data->id)
            ->get();

        $plans = ProjectTypicalUnits::where('project_id', $data->id)->orderBy('sort_order', 'ASC')
            ->get();
        $media = DB::table('project_media')
            ->where('project_id', $data->id)
            ->whereIn('collection', [
                'cover',
                // 'architecture',
                // 'interior',
                'lobby',
                // 'general_plan',
            ])
            ->get();

        $map_point = DB::table('project_map_points')
            ->where('project_id', $data->id)
            ->orderBy('sort_order')
            ->get();
        $project_media = ProjectMedia::where('project_id', $data->id)->first();
        $payment_plans = DB::table('payment_plans')->where('project_id', $data->id)->first();
        $plans_step = null;
        if ($payment_plans) {
            $plans_step = DB::table('payment_plan_steps')->where('payment_plan_id', $payment_plans->id)->get();
        }
        $project_brochure = ProjectMedia::where('project_id', $data->id)
            ->where('collection', 'brochure')
            ->latest()
            ->first();
        return view('project_details', compact('data', 'amenities', 'map_point', 'media', 'payment_plans', 'plans_step', 'project_media', 'plans', 'project_brochure', 'seo'));
    }

    public function getCountriesDeveloper(Request $request)
    {
        $countries = $request->country_id;
        $developers = Developer::where('country', $countries)->select('id', 'name')->get();

        return response()->json($developers);
    }

    public function searchLocation(Request $request)
    {
        $keyword = trim($request->keyword);

        if (empty($keyword)) {
            return response()->json([]);
        }

        $countryCode = session('country_code');

        $code = ($countryCode === 'in' || $countryCode === null) ? 'IN' : 'AED';

        $country = Country::where('country_code', $code)->first();

        if (! $country) {
            return response()->json([]);
        }

        $locations = Project::where('country_id', $country->id)
            ->where('sale_status', '!=', 'out_of_stock')
            ->where(function ($query) use ($keyword) {
                $query->where('locality', 'LIKE', "%{$keyword}%")
                    ->orWhere('city', 'LIKE', "%{$keyword}%");
            })
            ->select('locality', 'city')
            ->distinct()
            ->limit(10)
            ->get();

        return response()->json($locations);
    }

    public function get_unit_types_amenities(Request $request)
    {
        $data = Taxonomy::where('name', $request->type)->first();

        if (! $data) {
            return response()->json([]);
        }

        $type = Taxonomy::where('parent_id', $data->id)
            ->select('id', 'name')
            ->get();
        if (! $type) {
            return response()->json([]);
        }

        return response()->json($type);
    }
}
