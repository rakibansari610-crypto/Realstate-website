<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Project;
use App\Models\ProjectTypicalUnits;
use App\Models\Taxonomy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TypicalUnitsController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|integer',
            'type_name' => 'required|string',
            'room_type' => 'required|string',
            'other_room_type' => 'nullable|string',
            'bedrooms' => 'required|decimal:0,1',
            'currency_code' => 'required|string|max:5',
            'from_price' => 'nullable|numeric',
            'to_price' => 'nullable|numeric',
            'from_size_sqft' => 'nullable|numeric',
            'to_size_sqft' => 'nullable|numeric',
            'from_size_m2' => 'nullable|numeric',
            'to_size_m2' => 'nullable|numeric',
            'sort_order' => 'nullable|integer',
        ]);

        DB::beginTransaction();

        try {
            if ($request->filled('typical_id')) {

                $typical_id = decrypt($request->typical_id);
                $unit = ProjectTypicalUnits::findOrFail($typical_id);
            } else {
                $unit = new ProjectTypicalUnits;
            }

            $unit->project_id = $request->project_id;
            $unit->type_name = $request->type_name;
            $unit->type_name = $request->type_name;
            $unit->room_type = $request->room_type;
            $unit->bedrooms = $request->bedrooms;
            $unit->currency_code = $request->currency_code;
            $unit->from_price = $request->from_price ?? 0;
            $unit->to_price = $request->to_price ?? 0;
            $unit->from_size_sqft = $request->from_size_sqft;
            $unit->to_size_sqft = $request->to_size_sqft;
            $unit->from_size_m2 = $request->from_size_m2;
            $unit->to_size_m2 = $request->to_size_m2;
            $unit->sort_order = $request->sort_order ?? 0;

            $unit->save();
            $prices = ProjectTypicalUnits::where('project_id', $request->project_id)
                ->selectRaw('
        MIN(NULLIF(from_price, 0)) as min_from,
        MAX(NULLIF(to_price, 0)) as max_to
    ')
                ->first();

            // Fallback handling
            $minPrice = $prices->min_from ?? $request->from_price;
            $maxPrice = $prices->max_to ?? $request->to_price;

            // Update project table
            DB::table('projects')
                ->where('id', $request->project_id)
                ->update([
                    'min_price' => $minPrice,
                    'max_price' => $maxPrice,
                    'updated_at' => now(),
                ]);
            DB::commit();

            return redirect()->back()->with('success', $request->filled('id') ? 'FloorPlan updated successfully' : 'FloorPlan added successfully');
        } catch (\Exception $e) {
            dd($e->getMessage());
            DB::rollBack();

            return redirect()->back()->withInput()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function get(Request $request)
    {
        $columns = [
            0 => 'id',
            1 => 'type_name',
            2 => 'room_type',
            3 => 'bedrooms',
            4 => 'currency_code',
            5 => 'from_price',
            6 => 'to_price',
            7 => 'from_size_sqft',
            8 => 'to_size_sqft',
            9 => 'from_size_m2',
            10 => 'to_size_m2',
        ];

        $query = ProjectTypicalUnits::query()

            ->leftJoin(
                'taxonomies',
                'taxonomies.id',
                '=',
                'project_typical_units.type_name'
            )

            ->where(
                'project_typical_units.project_id',
                $request->project_id
            )

            ->select(
                'project_typical_units.*',
                'taxonomies.name as type_text'
            );

        $totalData = $query->count();
        $totalFiltered = $totalData;

        // 🔍 Search
        if (! empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $query->where(function ($q) use ($search) {
                $q->where('bedrooms', 'LIKE', "%{$search}%")
                    ->orWhere('currency_code', 'LIKE', "%{$search}%");
            });

            $totalFiltered = $query->count();
        }

        // 🔢 Pagination & ordering
        $limit = $request->input('length', 10);
        $start = $request->input('start', 0);
        $orderColumn = $columns[$request->input('order.0.column', 0)];
        $orderDir = $request->input('order.0.dir', 'asc');

        $typicals = $query->offset($start)
            ->limit($limit)
            ->orderBy($orderColumn, $orderDir)
            ->get();

        // 📦 Data formatting
        $data = [];
        $sn = $request->start + 1;
        foreach ($typicals as $t) {

            $action = '
                <a style="font-size:18px; "href="'.route('org.typical.units.edit', encrypt($t->id)).'" 
                class="text-primary me-2" title="Edit">
                    <i class="las la-edit"></i>
                </a>

                <form action="'.route('org.typical.units.destroy', encrypt($t->id)).'" 
                    method="POST" 
                    class="d-inline"
                    onsubmit="return confirm(\'Delete this floor plan?\');">
                    '.csrf_field().method_field('DELETE').'
                    <button type="submit"  style="font-size:18px; "
                            class="btn btn-link text-danger p-0 m-0" 
                            title="Delete">
                        <i class="las la-trash"></i>
                    </button>
                </form>

                ';

            $data[] = [
                'id' => $sn++,

                'type_name' => $t->type_text,
                'room_type' => $t->room_type,
                'bedrooms' => $t->bedrooms,
                'currency_code' => $t->currency_code,
                'from_price' => rtrim(rtrim($t->from_price, '0'), '.'),
                'to_price' => rtrim(rtrim($t->to_price, '0'), '.'),
                'from_size_sqft' => $t->from_size_sqft,
                'to_size_sqft' => $t->to_size_sqft,
                'from_size_m2' => $t->from_size_m2,
                'to_size_m2' => $t->to_size_m2,
                'action' => $action,
            ];
        }

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data' => $data,
        ]);
    }

    public function edit($id)
    {
        $typical_id = decrypt($id);

        $typical = ProjectTypicalUnits::find($typical_id);
        $project = Project::where('id', $typical->project_id)->first();
        $countries = Country::where('country_status', 1)
            ->orderBy('country_name')
            ->get(['id', 'country_name', 'country_currency']);

        $types = Taxonomy::where('module', 'unit_type')
            ->whereNull('parent_id')
            ->get();

        return view('org.projects.floor_plan.edit', compact('typical', 'project', 'countries', 'types'));
    }

    public function destroy($typical)
    {
        $id = decrypt($typical);

        $unit = ProjectTypicalUnits::findOrFail($id);
        $unit->delete();

        return redirect()->back()->with('success', 'Floor Plan deleted.');
    }

    public function getRoomTypes(Request $request)
    {
        return Taxonomy::where('module', 'unit_type')
            ->where('parent_id', $request->type_id)
            ->get();
    }
}
