<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Blog;
use App\Models\Country;
use App\Models\Developer;
use App\Models\Project;
use App\Models\Support;
use App\Models\Taxonomy;
use App\Models\Testimonial;
use App\Models\Unit;
use App\Services\ReellyClient;
use App\Services\SeoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected $seoService;

    public function __construct(SeoService $seoService)
    {
        $this->seoService = $seoService;
    }

    public function news()
    {
        $country_code = session('country_code') ?? null;
        $countries = Country::where('country_code', $country_code)->first();
        if (! $countries) {
            $countries = Country::where('country_code', 'AED')->first();
        }
        $news = Blog::where('status', 'published')
            ->where('country_id', $countries->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('news', compact('news'));
    }

    public function news_search(Request $request)
    {
        $country_code = session('country_code') ?? null;
        $countries = Country::where('country_code', $country_code)->first();
        if (! $countries) {
            $countries = Country::where('country_code', 'AED')->first();
        }

        $searchTerm = $request->get('search');

        $news = Blog::where('status', 'published')
            ->where('country_id', $countries->id)
            ->where(function ($query) use ($searchTerm) {
                $query->where('title', 'like', "%{$searchTerm}%")
                    ->orWhere('category', 'like', "%{$searchTerm}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('partials.news_ajax', compact('news'))->render();
    }

    public function landing()
    {
        $seo = $this->seoService->get('home');
        $country_code = session('country_code') ?? null;
        $countries = Country::where('country_code', $country_code)->first();
        if (! $countries) {
            $countries = Country::where('country_code', 'aed')->first();
        }
        $units = Unit::with(['project', 'developer', 'unitType', 'country'])
            ->where('status', 1)->where('country_id', $countries->id)
            ->where('is_for_sale', 1)
            ->latest()
            ->take(8)
            ->get();

        $topUnits = Unit::with(['developer', 'unitType', 'country'])
            ->where('status', 1)->where('country_id', $countries->id)
            ->where('is_for_sale', 1)
            ->orderByRaw('CAST(REPLACE(REPLACE(selling_price, "$", ""), ",", "") AS DECIMAL(10,2)) DESC')
            ->take(4)
            ->get();

        $unitTypes = Taxonomy::where('module', 'unit_type')
            ->orderBy('priority')
            ->get();

        $amenities = Taxonomy::where('module', 'amenities')
            ->orderBy('priority')
            ->get();
        $agent = Agent::where('is_active', 1)->orderBy('sort_order', 'asc')->limit(3)->get();
        $blogs = Blog::where('status', 'published')->where('country_id', $countries->id)->orderBy('created_at', 'desc')->limit(3)->get();
        $testimonial = Testimonial::where('is_active', 1)->orderBy('sort_order', 'asc')->get();
        $latestProject = DB::table('projects')
            ->leftJoin('project_media', 'projects.id', '=', 'project_media.project_id')
            ->leftJoin('project_typical_units', 'projects.id', '=', 'project_typical_units.project_id')
            ->where('projects.sale_status', 'on_sale')
            ->where('projects.country_id', $countries->id)
            ->where('projects.featured', '1')
            ->select(
                'projects.id',
                'projects.name',
                'projects.location',
                'projects.city',
                'projects.min_price',
                'projects.sale_status',
                'projects.slug',

                DB::raw('MIN(project_media.url) as media_url'),
                DB::raw('GROUP_CONCAT(DISTINCT project_typical_units.bedrooms ORDER BY project_typical_units.bedrooms ASC) as bedrooms')
            )
            ->groupBy(
                'projects.id',
                'projects.name',
                'projects.location',
                'projects.city',
                'projects.min_price',
                'projects.sale_status'
            )
            ->orderBy('projects.id', 'desc')
            ->limit(4)
            ->get();

        $projectTypes = Project::select('project_type', DB::raw('COUNT(*) as total'))
            ->groupBy('project_type')
            ->pluck('total', 'project_type')
            ->toArray();
        $apartmentCount = Project::where('project_type', 'apartment')->count();
        $townhouseCount = Project::where('project_type', 'townhouse')->count();
        $villaCount = Project::where('project_type', 'villa')->count();

        // dd($latestProject);
        // dd($latestProject->toSql(), $latestProject->getBindings());

        // ->get();

        return view('welcome', compact(
            'units',
            'topUnits',
            'unitTypes',
            'amenities',
            'agent',
            'testimonial',
            'blogs',
            'latestProject',
            'projectTypes',
            'apartmentCount',
            'townhouseCount',
            'villaCount', 'seo'
        ));
    }

    public function buyProperties(Request $request)
    {
        $seo = $this->seoService->get('buy');

        return $this->properties_type($request, $country = null, $type = 'buy', $seo);
    }

    public function rentProperties(Request $request)
    {
        $seo = $this->seoService->get('rent');

        return $this->properties_type($request, $country = null, $type = 'rent', $seo);
    }

    public function properties_type(Request $request, $country = null, $type = null, $seo = null)
    {
        $bestImages = DB::table('unit_images as ui')
            ->select(
                'ui.unit_id',
                'ui.image_url',
                DB::raw('ROW_NUMBER() OVER (
                PARTITION BY ui.unit_id
                ORDER BY ui.is_marketing_image DESC, ui.sort_order ASC, ui.id ASC
            ) as rn')
            );

        $query = Unit::query()
            ->leftJoin('projects', 'projects.id', '=', 'units.project_id')
            ->leftJoin('developers', 'developers.id', '=', 'units.developer_id')
            ->leftJoinSub($bestImages, 'best_image', function ($join) {
                $join->on('best_image.unit_id', '=', 'units.id')
                    ->where('best_image.rn', '=', 1);
            })
            ->where('units.is_active', 1)
            ->select([
                'units.*',
                'projects.name as project_name',
                'developers.name as developer_name',
                'best_image.image_url as unit_image',
            ]);

        if ($type === 'rent') {
            $query->where('units.unit_for', 'For Rent');
        } elseif ($type === 'buy') {
            $query->where('units.unit_for', 'For Sale');
        } elseif (! empty($type)) {
            $taxonomyId = Taxonomy::where('slug', $type)->value('id');

            if ($taxonomyId) {
                $query->where('units.sub_type', $taxonomyId);
            }
        }

        if ($request->filled('developer_id')) {
            $query->where('units.developer_id', $request->developer_id);
        }

        if ($request->filled('locality')) {
            $query->where('units.locality', 'like', "%{$request->locality}%");
        }

        if ($request->filled('keyword')) {
            $query->where('units.property_name', 'like', "%{$request->keyword}%");
        }

        if ($request->filled('city')) {
            $query->where('units.city_name', 'like', "%{$request->city}%");
        }

        if ($request->filled('unit_type')) {
            $query->where('units.unit_type', $request->unit_type);
        }

        if ($request->filled('bedrooms')) {
            $query->where('units.bedrooms', $request->bedrooms);
        }

        if ($request->filled('bathrooms')) {
            $query->where('units.no_of_bathrooms', $request->bathrooms);
        }

        if ($request->filled('min_price')) {
            if ($type === 'rent') {
                $query->where('units.rent_price', '>=', $request->min_price);
            } else {
                $query->whereRaw(
                    "CAST(REPLACE(units.selling_price, ',', '') AS DECIMAL(15,2)) >= ?",
                    [$request->min_price]
                );
            }
        }

        if ($request->filled('max_price')) {
            if ($type === 'rent') {
                $query->where('units.rent_price', '<=', $request->max_price);
            } else {
                $query->whereRaw(
                    "CAST(REPLACE(units.selling_price, ',', '') AS DECIMAL(15,2)) <= ?",
                    [$request->max_price]
                );
            }
        }

        $country_code = session('country_code') ?? null;
        $countries = Country::where('country_code', $country_code)->first();

        if (! $countries) {
            $countries = Country::where('country_code', 'ae')->first();
        }

        $query->where('units.country_id', $countries->id);

        $units = $query->latest('units.id')->paginate(12);

        if ($request->ajax()) {
            return view('properties.properties_card', compact('units'))->render();
        }

        $developer = Developer::where('country', $countries->id)->get();

        $latestUnits = Unit::query()
            ->leftJoinSub($bestImages, 'best_image', function ($join) {
                $join->on('best_image.unit_id', '=', 'units.id')
                    ->where('best_image.rn', '=', 1);
            })
            ->whereIn('units.status', [1, 'Vacant'])
            ->where('units.is_active', 1)
            ->where('units.country_id', $countries->id)
            ->when($type === 'rent', function ($q) {
                $q->where('units.unit_for', 'For Rent');
            })
            ->when($type === 'buy', function ($q) {
                $q->where('units.unit_for', 'For Sale');
            })
            ->select([
                'units.*',
                'best_image.image_url as unit_image',
            ])
            ->latest('units.id')
            ->take(4)
            ->get();

        $unitTypes = Taxonomy::where('module', 'unit_type')->get();

        return view('properties', compact('units', 'latestUnits', 'unitTypes', 'type', 'developer', 'seo'));
    }

    public function properties(Request $request)
    {

        $type = $request->purpose ?? null;

        // Country
        $country_code = session('country_code');
        $country = Country::where('country_code', $country_code)->first();
        if (! $country) {
            abort(404);
        }

        $developer = Developer::where('country', $country->id)->get();

        $query = Unit::with(['project', 'developer', 'unitType', 'country'])
            ->where('status', 1)
            ->where('country_id', $country->id)

            // RENT FILTER
            ->when($type === 'rent', function ($q) {
                $q->where('is_for_rent', 1);
            })

            // SALE FILTER
            ->when($type === 'sale', function ($q) {
                $q->where('is_for_sale', 1);
            });

        // 🔎 Keyword
        if ($request->filled('keyword')) {
            $query->where('property_name', 'like', "%{$request->keyword}%");
        }

        // 🔎 City
        if ($request->filled('city')) {
            $query->where('city_name', 'like', "%{$request->city}%");
        }

        // 🔎 Unit Type
        if ($request->filled('unit_type')) {
            $query->where('unit_type', $request->unit_type);
        }

        // 🔎 Bedrooms
        if ($request->filled('bedrooms')) {
            $query->where('bedrooms', $request->bedrooms);
        }

        // 🔎 Bathrooms
        if ($request->filled('bathrooms')) {
            $query->where('no_of_bathrooms', $request->bathrooms);
        }

        // 💰 Min Price
        if ($request->filled('min_price')) {
            $query->whereRaw(
                "CAST(REPLACE(selling_price, ',', '') AS DECIMAL(12,2)) >= ?",
                [$request->min_price]
            );
        }

        // 💰 Max Price
        if ($request->filled('max_price')) {
            $query->whereRaw(
                "CAST(REPLACE(selling_price, ',', '') AS DECIMAL(12,2)) <= ?",
                [$request->max_price]
            );
        }

        $units = $query->latest()->paginate(12);

        if ($request->ajax()) {
            return view('properties.properties_card', compact('units'))->render();
        }

        $latestUnits = Unit::where('status', 1)
            ->where('country_id', $country->id)
            ->latest()
            ->take(4)
            ->get();

        $unitTypes = Taxonomy::where('module', 'unit_type')->get();

        $type = $request->purpose ?? 'buy';
        return view('properties', compact('units', 'latestUnits', 'unitTypes', 'developer', 'type'));
    }

    public function propertyDetails(Request $request)
    {
        $slug = $request->slug;

        $unit = Unit::with([
            'project',
            'developer',
            'unitType',
            'country',
            'fittings',
            'unitImages' => function ($q) {
                $q->orderBy('is_marketing_image', 'desc')
                    ->orderBy('sort_order', 'asc')
                    ->orderBy('id', 'asc');
            },
        ])->where('slug', $slug)->firstOrFail();

        $id = $unit->id;

        $amenityIds = array_filter(
            array_map('trim', explode(',', $unit->building_amenities ?? ''))
        );

        $amenities = Taxonomy::whereIn('id', $amenityIds)
            ->where('module', 'amenities')
            ->get();

        $country_code = session('country_code');
        $countries = Country::where('country_code', $country_code)->first();

        if (! $countries) {
            $countries = Country::where('country_code', 'ae')->first();
        }

        $latestUnits = Unit::whereIn('status', [1, 'Vacant'])
            ->where('is_featured', 1)
            ->where('is_active', 1)
            ->where('country_id', $countries->id)
            ->latest()
            ->take(4)
            ->get();

        return view('properties-details', compact('unit', 'latestUnits', 'amenities', 'id'));
    }

    public function reellyProjects(Request $request, ReellyClient $reelly)
    {
        $params = array_filter([
            'page' => $request->get('page'),
            'perPage' => $request->get('perPage'),
            'countryId' => $request->get('countryId'),
            'regionId' => $request->get('regionId'),
            'districtId' => $request->get('districtId'),
            'status' => $request->get('status'),
        ]);

        $data = $reelly->getProjects($params);

        return view('projects', ['data' => $data]);

    }

    public function showProject(int $id, \App\Services\ReellyClient $reelly)
    {
        $project = $reelly->getProject($id);

        return view('project_show', compact('project'));
    }

    public function projectUnits(int $id, Request $request, \App\Services\ReellyClient $reelly)
    {
        $limit = (int) $request->get('limit', 20);
        $offset = (int) $request->get('offset', 0);

        $project = $reelly->getProject($id);
        $data = $reelly->getProjectUnits($id, ['limit' => $limit, 'offset' => $offset]);

        $projectId = $id;

        return view('project_units', compact('project', 'data', 'projectId'));
    }

    public function show(string $id, ReellyClient $reelly)
    {
        $data = $reelly->getProperty($id);

        return response()->json($data);
    }

    public function publicPass($token)
    {
        // Public view of the pass by token (no login), scoped to current host/org
        $orgId = Auth::user()->org_id ?? \App\Tenancy\Tenant::id(); // if no login, Tenant middleware still set
        $visit = \App\Models\Visit::with(['visitor', 'site', 'host', 'org'])
            ->where('org_id', $orgId)
            ->where('qr_token', $token)
            ->firstOrFail();

        $validUntil = $visit->scheduled_end ?? $visit->scheduled_start?->copy()->addHours(4) ?? now()->endOfDay();

        // Reuse the same pass Blade; hide internal buttons with a flag
        $publicView = true;

        return view('org.visits.pass', compact('visit', 'validUntil', 'publicView'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // return view('home');
        $user = auth()->user();
        if (! $user) {
            return redirect('/login');
        }
        if ($user->hasRole('super admin')) {
            return redirect('/su/dashboard');
        } elseif ($user->hasRole('org admin')) {
            return redirect('/su/dashboard');
        } elseif ($user->hasRole('data entry')) {
            return redirect('/org/dashboard');
        } elseif ($user->hasRole('employee')) {
            return redirect('/employee/dashboard');
        } elseif ($user->hasRole('channel partner')) {
            return redirect('/cp/dashboard');

        } elseif ($user->hasRole('property consultant')) {
            return redirect('/consultant/dashboard');

        } elseif ($user->hasRole('freelancer')) {
            return redirect('/freelancer/dashboard');

        } elseif ($user->hasRole('rm')) {
            return redirect('/rm/dashboard');

        } else {
            return redirect('/su/dashboard');
        }
    }

    public function privacyPolicy()
    {
        $privacy_policy = DB::table('pages')->where('type', 'privacy_policy')->value('description');

        return view('privacy-policy', compact('privacy_policy'));
    }

    public function termsCondtions()
    {
        $terms_conditions = DB::table('pages')->where('type', 'terms_conditions')->value('description');

        return view('terms-conditions', compact('terms_conditions'));
    }

    public function support()
    {
        return view('support');
    }

    public function supportStore(Request $request)
    {
        try {
            DB::beginTransaction();

            $objSupport = new Support;
            $objSupport->name = $request->name;
            $objSupport->email = $request->email;
            $objSupport->phone_no = $request->phone_no;
            $objSupport->message = $request->message;
            $objSupport->save();

            DB::commit();

            return redirect()->back()->with('success', 'Message sent successfully!');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Error! '.$e->getMessage());
        }
    }

    public function all_agents(Request $request)
    {
        $query = Agent::query();
        if ($request->filled('search_deve')) {
            $search = $request->search_deve;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('state', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }
        $country_code = session('country_code');
        if ($country_code == 'in') {
            $country_code = 'IN';
        } else {
            $country_code = 'AED';
        }
        $countries = Country::where('country_code', $country_code)->first();
        $query->where('country', $countries->id);
        $data = $query->orderBy('id', 'desc')->paginate(20);

        return view('agents', compact('data', 'countries'));
    }

    public function blog_details(Request $request)
    {
        $country_code = session('country_code') ?? null;
        $countries = Country::where('country_code', $country_code)->first();

        $slug = $request->slug;
        $blog = Blog::where('slug', $slug)->first();
        if (! $blog) {
            return redirect()->back();
        }
        if ($request->latest_serach) {
            $latestBlogs = Blog::where('id', '!=', $blog->id)->where('country_id', $countries->id)->where('title', 'like', "%{$request->latest_serach}%")->get();
        } else {

            $latestBlogs = Blog::where('id', '!=', $blog->id)->where('country_id', $countries->id)->latest()->take(5)->get();
        }
        $relatedBlogs = Blog::where('id', '!=', $blog->id)->where('country_id', $countries->id)->latest()->take(3)->get();

        return view('news-details', compact('blog', 'latestBlogs', 'relatedBlogs'));
    }

    public function contactUs(Request $request)
    {
        return view('contact-us');
    }

    public function aboutUs(Request $request)
    {
        return view('about-us');
    }

    public function servicePage(Request $request)
    {
        return view('services');
    }
}
