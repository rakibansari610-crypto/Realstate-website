<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
class SubCategoryController extends Controller
{
    public function index(Request $request)
{
    $sub_category = SubCategory::leftJoin('categories', 'sub_categories.category_id', '=', 'categories.id')
        ->select(
            'sub_categories.id',
            'sub_categories.sub_category_name',
            'sub_categories.icon',
            'sub_categories.priority',
            'sub_categories.status',
            'categories.category_name'
        )
        ->when($request->filled('q'), function ($query) use ($request) {
            $query->where('sub_categories.sub_category_name', 'like', '%' . $request->q . '%')
                  ->orWhere('categories.category_name', 'like', '%' . $request->q . '%');
        })
        ->orderBy('sub_categories.priority', 'asc')
        ->paginate(20)
        ->withQueryString();

    return view('org.sub_category.index', compact('sub_category'));
}



   public function getSubCategories(Request $request)
    {
        $columns = [
            0 => 'sub_categories.id',
            1 => 'categories.category_name',
            2 => 'sub_categories.sub_category_name',
            3 => 'sub_categories.icon',
            4 => 'sub_categories.priority',
            5 => 'sub_categories.status',
            6 => 'sub_categories.id'
        ];

        $totalData = SubCategory::count();
        $totalFiltered = $totalData;

        $limit = $request->input('length') ?? 10;
        $start = $request->input('start') ?? 0;
        $orderColumnIndex = $request->input('order.0.column') ?? 0;
        $orderColumn = $columns[$orderColumnIndex];
        $orderDir = $request->input('order.0.dir') ?? 'asc';
        $searchValue = $request->input('search.value');

        $query = SubCategory::leftJoin('categories', 'sub_categories.category_id', '=', 'categories.id')
            ->select(
                'sub_categories.id',
                'sub_categories.sub_category_name',
                'sub_categories.icon',
                'sub_categories.priority',
                'sub_categories.status',
                'categories.category_name'
            );

        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('sub_categories.sub_category_name', 'like', "%{$searchValue}%")
                    ->orWhere('categories.category_name', 'like', "%{$searchValue}%")
                    ->orWhere('sub_categories.priority', 'like', "%{$searchValue}%")
                    ->orWhere('sub_categories.status', 'like', "%{$searchValue}%");
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
            $nestedData = [];
            $nestedData['sn'] = $sn;
            $nestedData['category_name'] = $record->category_name;
            $nestedData['sub_category_name'] = $record->sub_category_name;
            $nestedData['icon'] = $record->icon;
            $nestedData['priority'] = $record->priority;
            $nestedData['status'] = $record->status == 1 
                ? '<span class="badge bg-success">Active</span>' 
                : '<span class="badge bg-danger">Deactive</span>';
            $nestedData['action'] = '
                <a href="'.route('org.sub_category.edit',$record->id).'" class="btn btn-sm btn-warning">Edit</a>
                <form method="POST" action="'.route('org.sub_category.destroy', $record->id).'" style="display:inline-block;">
                    '.csrf_field().method_field('DELETE').'
                    <button class="btn btn-sm btn-danger" onclick="return confirm(\'Delete?\')">Delete</button>
                </form>';

            $data[] = $nestedData;
            $sn++;
        }

        return response()->json([
            "draw" => intval($request->input('draw')),
            "iTotalRecords" => intval($totalData),
            "iTotalDisplayRecords" => intval($totalFiltered),
            "aaData" => $data
        ]);
    }



    public function create()
    {   
        $category = Category::where('status',1)->orderby('category_name')->get();
        return view('org.sub_category.create',compact('category'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sub_category_name' => 'required|string|max:255',
        ]);
 
        $category = new SubCategory(); 
        $category->sub_category_name = $request->sub_category_name;
        $category->category_id = $request->category_id;
        $category->icon = $request->icon;
        $category->priority = $request->priority;
        $category->status = $request->active; 
        $category->save();  
        return redirect()
            ->route('org.sub_category.index')
            ->with('success', 'Category created successfully.');
    }


    public function edit(Request $request)
    {
        try { 
            $id = $request->id;
            
            $sub_category = SubCategory::find($id);
            if (!$sub_category) {
                
                return redirect()
                    ->route('org.sub_category.index')
                    ->with('error', 'Sub Category not found.');
            } 
            $category = Category::where('status',1)->get();
            return view('org.sub_category.edit', compact('category','sub_category'));
        } catch (\Exception $e) { 
            return redirect()
                ->route('org.sub_category.index')
                ->with('error', 'Something went wrong. Please try again.');
        }
    }


    public function update(Request $request)
    {
        $request->validate([
            'sub_category_name' => 'required|string|max:255',
        ]);
 
        $category = SubCategory::find($request->id); 
        if (!$category) {
            return redirect()
                ->route('org.category.index')
                ->with('error', 'Sub Category not found.');
        } 
        $category->sub_category_name = $request->sub_category_name;
        $category->category_id = $request->category_id;
        $category->icon = $request->icon;
        $category->priority = $request->priority;
        $category->status = $request->active; 
        $category->update(); 
        return redirect()
            ->route('org.sub_category.index')
            ->with('success', 'Sub Category created successfully.');
    }


    public function destroy(Request $request)
    {
        try { 
            $id = $request->id; 
            $category = SubCategory::find($id);
            if (!$category) {
                return redirect()
                    ->route('org.sub_category.index')
                    ->with('error', ' Sub Category not found.');
            } 
            $category->delete();
            return redirect()
                ->route('org.sub_category.index')
                ->with('success', 'Sub Category deleted successfully.');
        } catch (\Exception $e) {  
            return redirect()
                ->route('org.sub_category.index')
                ->with('error', 'Something went wrong. Please try again.');
        }
    }




}
