<?php

// app/Http/Controllers/Org/DeveloperController.php

namespace App\Http\Controllers;

use App\Http\Requests\DeveloperRequest;
use App\Models\Country;
use App\Models\Developer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DeveloperController extends Controller
{
    private function applyDeveloperVisibility($query)
    {
        $user = auth()->user();

        if ($user->hasRole('data entry')) {
            $query->whereHas('countries', function ($q) use ($user) {
                $q->where('countries.id', $user->country_id);
            });
        }

        return $query;
    }

    public function index(Request $request)
    {
        // $orgId = Auth::user()->org_id;

        $q = Developer::with('creator')
            // ->where('org_id', $orgId)
            ->when(
                $request->filled('q'),
                fn ($qq) => $qq->where('name', 'like', '%'.$request->q.'%')
            )
            ->orderBy('name');
        $q = $this->applyDeveloperVisibility($q);

        $developers = $q->paginate(20)->withQueryString();

        return view('org.developers.index', compact('developers'));
    }

    public function get(Request $request)
    {
        $columns = [
            0 => 'id',
            1 => 'name',
            2 => 'website',
            3 => 'logo',
            4 => 'creator_name',
        ];

        $totalData = Developer::count();
        $totalFiltered = $totalData;

        $limit = $request->input('length', 10);
        $start = $request->input('start', 0);
        $orderColumnIndex = $request->input('order.0.column', 0);
        $orderColumn = $columns[$orderColumnIndex] ?? 'name';
        $orderDir = $request->input('order.0.dir', 'asc');
        $searchValue = $request->input('search.value');

        $query = Developer::with('creator');
        $query = $this->applyDeveloperVisibility($query);

        if (! empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('name', 'like', "%{$searchValue}%")
                    ->orWhere('website', 'like', "%{$searchValue}%");
            });
            $totalFiltered = $query->count();
        }

        $records = $query->offset($start)
            ->limit($limit)
            ->orderBy($orderColumn, $orderDir)
            ->get();

        $data = [];
        $sn = 1;
        foreach ($records as $record) {
            $row = [
                'sn' => $sn++,
                'name' => $record->name,
                'website' => '<a href="'.$record->website.'" target="_blank">'.$record->website.'</a>',
                'logo' => $record->logo_path ? '<img src="'.Storage::disk('public')->url($record->logo_path).'" style="height:36px">' : '-',
            ];

            if (auth()->user()->hasAnyRole(['org admin', 'admin'])) {
                $row['creator'] = $record->creator->name ?? '—';
            }

            $actions = '';
            if (! auth()->user()->hasAnyRole(['channel partner'])) {
                $actions .= '<a href="'.route('org.developers.edit', $record).'" class="btn btn-sm btn-outline-secondary">Edit</a> ';
            }
            $actions .= '<a href="'.route('org.developers.view', $record).'" class="btn btn-sm btn-outline-success">View</a> ';
            if (! auth()->user()->hasAnyRole(['data entry', 'channel partner'])) {
                $actions .= '
                    <form action="'.route('org.developers.destroy', $record).'" method="POST" class="d-inline" onsubmit="return confirm(\'Delete this developer?\');">
                        '.csrf_field().method_field('DELETE').'
                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                    </form>';
            }

            $row['actions'] = $actions;
            $data[] = $row;
        }

        return response()->json([
            'draw' => intval($request->input('draw', 1)),
            'recordsTotal' => intval($totalData),
            'recordsFiltered' => intval($totalFiltered),
            'data' => $data,
        ]);
    }

    public function create()
    {
        $countries = Country::where('country_status', 1)
            ->orderBy('country_name')
            ->get();

        return view('org.developers.form', ['developer' => new Developer, 'countries' => $countries]);
    }

    public function store(DeveloperRequest $request)
    {
        try {
            $data = $request->validated();

            $data['slug'] = Str::slug($data['name']);
            $data['updated_by'] = Auth::id();
            $data['created_by'] = Auth::id();
            $data['country'] = $request->countries;

            if ($request->hasFile('logo')) {
                $filename = Str::uuid().'.'.$request->file('logo')->extension();
                $data['logo_path'] = $request->file('logo')
                    ->storeAs('developers', $filename, 'public');
            }

            $dev = Developer::create($data);
            // dd($dev, $request->countries);
            // if ($request->countries) {
            //     $dev->countries()->sync($request->countries);
            // }
            // dd($dev);
            // $dev->countries()->sync($request->countries);

            return redirect()->route('org.developers.edit', $dev)->with('success', 'Developer created successfully.');
        } catch (\Exception $e) {
            // dd($e->getMessage());
            return redirect()->back()->with('error', 'Failed to create developer: '.$e->getMessage());
        }
    }

    public function edit(Developer $developer)
    {
        // $this->authorizeOrg($developer);
        $countries = Country::where('country_status', 1)
            ->orderBy('country_name')
            ->get();
        $isView = request()->routeIs('org.developers.view');

        return view('org.developers.form', compact('developer', 'isView', 'countries'));
    }

    public function update(DeveloperRequest $request, Developer $developer)
    {
        // $this->authorizeOrg($developer);
        $userId = Auth::user()->id;
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);
        $data['updated_by'] = Auth::id();
        $data['country'] = $request->countries;

        if ($request->hasFile('logo')) {
            $filename = Str::uuid().'.'.$request->file('logo')->extension();
            $path = $request->file('logo')
                ->storeAs("orgs/{$userId}/developers", $filename, 'public');

            // delete old file
            if ($developer->logo_path) {
                Storage::disk('public')->delete($developer->logo_path);
            }
            $data['logo_path'] = $path;
        }

        $developer->update($data);
        // $developer->countries()->sync($request->countries);

        return back()->with('success', 'Developer updated.');
    }

    public function destroy(Developer $developer)
    {
        // $this->authorizeOrg($developer);

        if ($developer->logo_path) {
            Storage::disk('public')->delete($developer->logo_path);
        }
        $developer->delete();

        return redirect()->route('org.developers.index')->with('success', 'Developer deleted.');
    }

    protected function authorizeOrg(Developer $developer): void
    {
        abort_unless($developer->org_id === Auth::user()->org_id, 403);
    }

    public function developer(Request $request)
    {
        $query = Developer::query();

        if ($request->filled('search_deve')) {
            $search = $request->search_deve;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $country_code = session('country_code');
        if ($country_code == 'in') {
            $country_code = 'IN';
        } else {
            $country_code = 'ae';
        }

        $countries = Country::where('country_code', $country_code)->first();

        $query->where('country', $countries->id);

        $page = (int) $request->get('page', 1);

        if ($page < 1) {
            abort(404);
        }

        $data = $query->orderBy('id', 'desc')
            ->paginate(20)
            ->withQueryString();

        if ($data->lastPage() > 0 && $page > $data->lastPage()) {
            abort(404);
        }

        return view('developers', compact('data', 'countries'));
    }
}
