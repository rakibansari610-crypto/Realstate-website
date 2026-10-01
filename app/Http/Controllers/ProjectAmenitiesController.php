<?php

namespace App\Http\Controllers;

use App\Models\AmenityProject;
use Illuminate\Http\Request; 
use App\Models\ProjectAmenities;
use App\Models\SubCategory;
use App\Models\Taxonomy;

class ProjectAmenitiesController extends Controller
{
   public function storeAmenities(Request $request)
    { 
        $request->validate([
            'project_id' => 'required|integer',
            'amenities_name' => 'required|integer',
            'id'         => 'nullable|integer'
        ]); 
        if ($request->id) {

            $amenity = AmenityProject::find($request->id);

            if (!$amenity) {
                return redirect()->back()->with('error', 'Amenity not found.');
            }

            $amenity->project_id = $request->project_id;
            $amenity->amenity_id = $request->amenities_name; 
            $amenity->save();

            return redirect()->back()->with('success', 'Amenity updated successfully.');
        }
 
        $exists = AmenityProject::where('project_id', $request->project_id)
                    ->where('amenity_id', $request->amenities_name)
                    ->first();

        if ($exists) {
            return redirect()->back()->with('error', 'Amenity already added for this project.');
        }

        $amenity = new AmenityProject();
        $amenity->project_id = $request->project_id;
        $amenity->amenity_id = $request->amenities_name; 
        $amenity->save();

        return redirect()->back()->with('success', 'Amenity saved successfully.');
    }


     



    public function destroy($id)
    {
        $id = decrypt($id); 

        $pr = AmenityProject::findOrFail($id); 
        $pr->delete();

        return redirect()->back()->with('success', 'Amenities deleted successfully.');
    }



    // public function storeAmenities(Request $request)
    // {
    //     $request->validate([
    //         'project_id' => 'required|integer',
    //         'sub_category_ids' => 'nullable|array',
    //     ]);

    //     $projectId = $request->project_id;
    //     $newSubCategories = $request->sub_category_ids ?? [];   
    //     $amenity = ProjectAmenities::where('project_id', $projectId)->first();

    //     if ($amenity) { 
    //         $oldSubCategories = $amenity->sub_category_id 
    //             ? explode(',', $amenity->sub_category_id)
    //             : []; 
    //         $merged = array_unique(array_merge($oldSubCategories, $newSubCategories)); 
    //         $final = $newSubCategories; 
    //         $amenity->sub_category_id = implode(',', $final);
    //         $amenity->save();

    //     } else { 
    //         ProjectAmenities::create([
    //             'project_id' => $projectId,
    //             'sub_category_id' => implode(',', $newSubCategories),
    //             'status' => 1,
    //         ]);
    //     }
    //     return redirect()->back()->with('success', 'Amenities saved successfully.');
    // }


   public function getSubCategories(Request $request)
    {
        $columns = [
            0 => 'id',
            1 => 'amenity_id',
        ];

        $limit  = $request->input('length', 10);
        $start  = $request->input('start', 0);
        $orderColumnIndex = $request->input('order.0.column', 0);
        $orderColumn = $columns[$orderColumnIndex] ?? 'id';
        $orderDir = $request->input('order.0.dir', 'asc');
        $searchValue = $request->input('search.value');

        // 🔹 Base Query
       $query = AmenityProject::leftJoin('amenities', 'amenities.id', '=', 'amenity_project.amenity_id')
            ->select('amenities.name', 'amenity_project.id as ap_id')
            ->where('amenity_project.project_id', $request->project_id);


        // 🔹 Search
        if (!empty($searchValue)) {
            $query->where('amenities.name', 'like', "%{$searchValue}%");
        }

        $totalData = AmenityProject::count();
        $totalFiltered = $query->count();

        // 🔹 Order + Pagination
        $categories = $query->orderBy($orderColumn, $orderDir)->offset($start)->limit($limit)->get();

        $data = [];
        $sn = $start + 1;
         

        foreach ($categories as $row) {
            $action = '
                <form action="'.route('org.projects.amenities.destroy', encrypt($row->ap_id)).'"
                    method="POST"
                    class="d-inline"
                    onsubmit="return confirm(\'Delete this Amenities ?\');">
                    '.csrf_field().method_field('DELETE').'
                    <button style="font-size:18px;" type="submit" class="btn btn-link text-danger p-0">
                        <i class="las la-trash"></i>
                    </button>
                </form>
            ';


            $data[] = [
                'sn' => $sn++,
                'amenity_id' => $row->name,
                 'action'     => $action,
            ];
        }

        return response()->json([
            "draw" => intval($request->input('draw')),
            "iTotalRecords" => $totalData,
            "iTotalDisplayRecords" => $totalFiltered,
            "aaData" => $data
        ]);
    }






}
