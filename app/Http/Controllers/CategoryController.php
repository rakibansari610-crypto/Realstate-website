<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
class CategoryController extends Controller
{
     public function index(Request $request)
     {
        $category = Category::when($request->filled('q'), function ($query) use ($request) {
            $query->where('category_name', 'like', '%' . $request->q . '%');
        })
        ->orderBy('priority', 'asc')
        ->paginate(20)
        ->withQueryString();
        return view('org.category.index',compact('category'));
     }

    public function create()
    { 
        return view('org.category.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|max:255',
        ]);
 
        $category = new Category(); 
        $category->category_name = $request->category_name;
        $category->icon = $request->icon;
        $category->priority = $request->priority;
        $category->status = $request->active; 
        $category->save(); 
        return redirect()
            ->route('org.category.index')
            ->with('success', 'Category created successfully.');
    }


    public function edit(Request $request)
    {
        try { 
            $id = $request->id; 
            $category = Category::find($id);
            if (!$category) {
                return redirect()
                    ->route('org.category.index')
                    ->with('error', 'Category not found.');
            } 
            return view('org.category.edit', compact('category'));
        } catch (\Exception $e) { 
            return redirect()
                ->route('org.category.index')
                ->with('error', 'Something went wrong. Please try again.');
        }
    }


    public function update(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|max:255',
        ]);
 
        $category = Category::find($request->id); 
        if (!$category) {
            return redirect()
                ->route('org.category.index')
                ->with('error', 'Category not found.');
        } 
        $category->category_name = $request->category_name;
        $category->icon = $request->icon;
        $category->priority = $request->priority;
        $category->status = $request->active; 
        $category->update(); 
        return redirect()
            ->route('org.category.index')
            ->with('success', 'Category created successfully.');
    }


    public function destroy(Request $request)
    {
        try { 
            $id = $request->id; 
            $category = Category::find($id);
            if (!$category) {
                return redirect()
                    ->route('org.category.index')
                    ->with('error', 'Category not found.');
            } 
            $category->delete();
            return redirect()
                ->route('org.category.index')
                ->with('success', 'Category deleted successfully.');
        } catch (\Exception $e) { 
            return redirect()
                ->route('org.category.index')
                ->with('error', 'Something went wrong. Please try again.');
        }
    }




}
