<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\SeoMeta;
use Illuminate\Http\Request;

class SeoMetaController extends Controller
{
    public function index()
    {
        return view('org.seo_meta.index');
    }
    public function create()
    {
        $country = Country::all();
        return view('org.seo_meta.create',compact('country'));
    }
    
    
    // public function store(Request $request)
    // { 
    //     $request->validate([
    //         'page_type' => 'required',
    //         'slug' => 'nullable|string|max:255',
    //         'meta_title' => 'nullable|string|max:255',
    //         'meta_description' => 'nullable|string',
    //         'meta_keywords' => 'nullable|string',
    //     ]); 
    //     if ($request->page_type !== 'project') {
    //         $request->merge(['slug' => null]);
    //     } 
    //     if (is_null($request->slug)) {

    //         $exists = SeoMeta::where('page_type', $request->page_type)->where('country_id', $request->country_id)->whereNull('slug')->exists();
    //         if ($exists) {
    //             return back()->withErrors([
    //                 'page_type' => 'Template already exists for this page type'
    //             ])->withInput();
    //         }
    //     } 
    //     // if (!empty($request->slug)) {

    //     //     $slugExists = SeoMeta::where('slug', $request->slug)->exists();
    //     //     if ($slugExists) {
    //     //         return back()->withErrors([
    //     //             'slug' => 'SEO already exists for this project slug'
    //     //         ])->withInput();
    //     //     }
    //     // }
 
    //     SeoMeta::create([
    //         'page_type' => $request->page_type,
    //         'slug' => $request->slug,
    //         'meta_title' => $request->meta_title,
    //         'meta_description' => $request->meta_description,
    //         'meta_keywords' => $request->meta_keywords,
    //         'country_id' => $request->country_id,
    //     ]);

    //     return redirect()->route('org.seo-meta.index')->with('success', 'SEO Added Successfully');
    // }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'page_type' => 'required',
    //         'meta_title' => 'nullable|string|max:255',
    //         'meta_description' => 'nullable|string',
    //         'meta_keywords' => 'nullable|string',
    //         'country_id' => 'nullable|integer',
    //     ]);

    //     // 🔍 Duplicate check (same page_type + country)
    //     $exists = SeoMeta::where('page_type', $request->page_type)
    //         ->where('country_id', $request->country_id)
    //         ->exists();

    //     if ($exists) {
    //         return back()->withErrors([
    //             'page_type' => 'SEO already exists for this page type & country'
    //         ])->withInput();
    //     }

    //     // ✅ Store
    //     SeoMeta::create([
    //         'page_type' => $request->page_type,
    //         'meta_title' => $request->meta_title,
    //         'meta_description' => $request->meta_description,
    //         'meta_keywords' => $request->meta_keywords,
    //         'country_id' => $request->country_id,
    //     ]);

    //     return redirect()->route('org.seo-meta.index')
    //         ->with('success', 'SEO Added Successfully');
    // }



    public function store(Request $request)
    {
        $request->validate([
            'page_type' => 'required',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'country_id' => 'nullable|integer',

            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string',
            'og_image' => 'nullable|image',
            'og_image_alt' => 'nullable|string|max:255',

            'twitter_title' => 'nullable|string|max:255',
            'twitter_description' => 'nullable|string',
            'twitter_image' => 'nullable|image',
            'twitter_image_alt' => 'nullable|string|max:255',

            'schema_json' => 'nullable|string',
        ]);

        // 🔍 Duplicate check
        $exists = SeoMeta::where('page_type', $request->page_type)
            ->where('country_id', $request->country_id)
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'page_type' => 'SEO already exists for this page type & country'
            ])->withInput();
        }

        // ===============================
        // 🧠 CLEAN META VALUES
        // ===============================
        $metaTitle = trim($request->meta_title);
        $metaDesc  = trim($request->meta_description);
        $metaKeywords = trim($request->meta_keywords);

        // ===============================
        // 🔥 OG FROM META ONLY (NO STATIC)
        // ===============================
        $ogTitle = $request->og_title 
            ?: ($metaTitle ?: $metaKeywords);

        $ogDesc = $request->og_description 
            ?: ($metaDesc ?: $metaKeywords);

        // ===============================
        // 🔥 TWITTER FROM OG
        // ===============================
        $twitterTitle = $request->twitter_title ?: $ogTitle;
        $twitterDesc  = $request->twitter_description ?: $ogDesc;

        // ===============================
        // 📁 IMAGE UPLOAD
        // ===============================
        $ogImagePath = null;
        if ($request->hasFile('og_image')) {
            $file = $request->file('og_image');
            $name = time().'_og.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/seo'), $name);
            $ogImagePath = 'uploads/seo/'.$name;
        }

        $twitterImagePath = null;
        if ($request->hasFile('twitter_image')) {
            $file = $request->file('twitter_image');
            $name = time().'_tw.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/seo'), $name);
            $twitterImagePath = 'uploads/seo/'.$name;
        } 
        $ogAlt = $request->og_image_alt ?: $ogTitle;
        $twitterAlt = $request->twitter_image_alt ?: $twitterTitle; 
        SeoMeta::create([
            'page_type' => $request->page_type,
            'country_id' => $request->country_id,

            'meta_title' => $metaTitle,
            'meta_description' => $metaDesc,
            'meta_keywords' => $metaKeywords,

            'og_title' => $ogTitle,
            'og_description' => $ogDesc,
            'og_image' => $ogImagePath,
            'og_image_alt' => $ogAlt,

            'twitter_title' => $twitterTitle,
            'twitter_description' => $twitterDesc,
            'twitter_image' => $twitterImagePath,
            'twitter_image_alt' => $twitterAlt,

            'schema_json' => $request->schema_json,
        ]);

        return redirect()->route('org.seo-meta.index')
            ->with('success', 'SEO Added Successfully');
    }


    public function get(Request $request)
    {
        $columns = [
            0 => 'seo_metas.id',
            1 => 'countries.country_name',
            2 => 'seo_metas.page_type',
            3 => 'seo_metas.meta_title',
            4 => 'seo_metas.meta_description',
            5 => 'seo_metas.meta_keywords',
        ];

        $searchValue = $request->input('search.value');

        $query = SeoMeta::leftJoin('countries', 'countries.id', '=', 'seo_metas.country_id')
            ->select(
                'seo_metas.*',
                'countries.country_name'
            );
 
        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('seo_metas.page_type', 'like', "%{$searchValue}%")
                    ->orWhere('seo_metas.meta_title', 'like', "%{$searchValue}%")
                    ->orWhere('seo_metas.meta_description', 'like', "%{$searchValue}%")
                    ->orWhere('seo_metas.meta_keywords', 'like', "%{$searchValue}%")
                    ->orWhere('countries.country_name', 'like', "%{$searchValue}%");
            });
        }
 
        $totalData = $query->count();
        $totalFiltered = $totalData; 
        $limit = $request->input('length') ?? 10;
        $start = $request->input('start') ?? 0;
 
        $orderColumnIndex = $request->input('order.0.column', 0);
        $orderColumn = $columns[$orderColumnIndex] ?? 'seo_metas.id';
        $orderDir = $request->input('order.0.dir', 'desc');

        $records = $query->orderBy($orderColumn, $orderDir)
            ->offset($start)
            ->limit($limit)
            ->get();

        $data = [];
        $sn = $start + 1;

        foreach ($records as $row) {

             $actions = '
                <a style="font-size:18px;" href="'. route('org.seo-meta.edit', encrypt($row->id)).'"
                    class="text-primary me-2" title="Edit">
                    <i class="las la-edit"></i>
                </a>

                <form action="'.route('org.seo-meta.destroy', encrypt($row->id)).'"
                    method="POST"
                    class="d-inline"
                    onsubmit="return confirm(\'Delete this SeoMeta?\');">
                    '.csrf_field().method_field('DELETE').'
                    <button style="font-size:18px;" type="submit" class="btn btn-link text-danger p-0">
                        <i class="las la-trash"></i>
                    </button>
                </form>
            ';

            $data[] = [
                'sn' => $sn++, 
                'country_id' => e($row->country_name ?? '—'), 
                'page_type' => '<span class="badge bg-info text-dark">'
                    . ucwords($row->page_type) . '</span>', 
                'meta_title' => '<div style="max-width:200px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">'
                    . e($row->meta_title) . '</div>', 
                'meta_description' => '<div style="max-width:250px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">'
                    . e($row->meta_description) . '</div>', 
                'meta_keywords' => '<div style="max-width:200px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">'
                    . e($row->meta_keywords) . '</div>', 
                'action' => $actions,
            ];
        }

        return response()->json([
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        ]);
    }





    public function edit($id)
    {
        $seo = SeoMeta::findOrFail(decrypt($id));
        $country = Country::all();
        return view('org.seo_meta.edit', compact('seo','country'));
    }



    // public function update(Request $request)
    // {
    //     $request->validate([
    //         'page_type' => 'required',
    //         'meta_title' => 'nullable|string|max:255',
    //         'meta_description' => 'nullable|string',
    //         'meta_keywords' => 'nullable|string',
    //         'country_id' => 'nullable|integer',
    //     ]);
    //     $id = decrypt($request->input('id'));
    //     $seo = SeoMeta::findOrFail($id); 
    //     $exists = SeoMeta::where('page_type', $request->page_type)
    //         ->where('country_id', $request->country_id)
    //         ->where('id', '!=', $id)
    //         ->exists();

    //     if ($exists) {
    //         return back()->withErrors([
    //             'page_type' => 'SEO already exists for this page type & country'
    //         ])->withInput();
    //     }
 
    //     $seo->update([
    //         'page_type' => $request->page_type,
    //         'meta_title' => $request->meta_title,
    //         'meta_description' => $request->meta_description,
    //         'meta_keywords' => $request->meta_keywords,
    //         'country_id' => $request->country_id,
    //     ]);

    //     return redirect()->route('org.seo-meta.index')
    //         ->with('success', 'SEO Updated Successfully');
    // }


    public function update(Request $request)
    {
        $request->validate([
            'page_type' => 'required',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'country_id' => 'nullable|integer',

            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string',
            'og_image' => 'nullable|image',
            'og_image_alt' => 'nullable|string|max:255',

            'twitter_title' => 'nullable|string|max:255',
            'twitter_description' => 'nullable|string',
            'twitter_image' => 'nullable|image',
            'twitter_image_alt' => 'nullable|string|max:255',

            'schema_json' => 'nullable|string',
        ]);
        $id = decrypt($request->id);

        $seo = SeoMeta::findOrFail($id); 
        $exists = SeoMeta::where('page_type', $request->page_type)
            ->where('country_id', $request->country_id)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'page_type' => 'SEO already exists for this page type & country'
            ])->withInput();
        } 
        $metaTitle = trim($request->meta_title);
        $metaDesc  = trim($request->meta_description);
        $metaKeywords = trim($request->meta_keywords);  
        $ogTitle = $request->og_title ?: ($metaTitle ?: $metaKeywords);
        $ogDesc  = $request->og_description ?: ($metaDesc ?: $metaKeywords); 
        $twitterTitle = $request->twitter_title ?: $ogTitle;
        $twitterDesc  = $request->twitter_description ?: $ogDesc; 
        $ogImagePath = $seo->og_image;
        if ($request->hasFile('og_image')) {
            $file = $request->file('og_image');
            $name = time().'_og.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/seo'), $name);
            $ogImagePath = 'uploads/seo/'.$name;
        }

        $twitterImagePath = $seo->twitter_image;
        if ($request->hasFile('twitter_image')) {
            $file = $request->file('twitter_image');
            $name = time().'_tw.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads/seo'), $name);
            $twitterImagePath = 'uploads/seo/'.$name;
        } 
        $ogAlt = $request->og_image_alt ?: $ogTitle;
        $twitterAlt = $request->twitter_image_alt ?: $twitterTitle;  
        $seo->update([
            'page_type' => $request->page_type,
            'country_id' => $request->country_id,
            'meta_title' => $metaTitle,
            'meta_description' => $metaDesc,
            'meta_keywords' => $metaKeywords,
            'og_title' => $ogTitle,
            'og_description' => $ogDesc,
            'og_image' => $ogImagePath,
            'og_image_alt' => $ogAlt,
            'twitter_title' => $twitterTitle,
            'twitter_description' => $twitterDesc,
            'twitter_image' => $twitterImagePath,
            'twitter_image_alt' => $twitterAlt,
            'schema_json' => $request->schema_json,
        ]);

        return redirect()->route('org.seo-meta.index')
            ->with('success', 'SEO Updated Successfully');
    }

    public function destroy($id)
    {
        $id = decrypt($id); 
        $seo = SeoMeta::find($id);
        if (!$seo) {
            return redirect()->back()->with('error', 'SEO not found.');
        }
        $seo->delete();

        return redirect()->back()->with('success', 'SEO deleted.');
    }




}
