<?php

namespace App\Http\Controllers;

use App\Http\Requests\BlogRequest;
use App\Models\Blog;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function index()
    {
        return view('org.blogs.index');
    }

    public function create()
    {
        return view('org.blogs.form', [
            'blog' => new Blog,
            'countries' => Country::orderBy('country_name')->get(),
        ]);
    }

    public function store(BlogRequest $request)
    {
        // dd($request->all());
        $data = $request->validated();

        $data['slug'] = Str::slug($request->title);
        $data['is_active'] = $request->has('is_active') ? 1 : 0;
        $data['update_by'] = Auth::id();

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')
                ->store('blogs', 'public');
        }
        if ($request->hasFile('detailed_image')) {
            $data['detailed_image'] = $request->file('detailed_image')
                ->store('blogs', 'public');
        }

        // ✅ PROCESS CONTENT IMAGES
        if (!empty($data['content'])) {
            $data['content'] = $this->processEditorImages($data['content']);
        }

        $data['meta_title'] = $data['meta_title'] ?? $data['title'];
        $data['meta_description'] = $data['meta_description'] ?? Str::limit(strip_tags($data['title'] ?? $data['title']), 160);
        $data['meta_keywords'] = $data['meta_keywords'] ?? Str::slug($data['title'], ',');
        Blog::create($data);

        return redirect()->route('org.blogs-index')
            ->with('success', 'Blog created successfully');
    }

    public function update(BlogRequest $request, $id)
    {

        $id = decrypt($id);
        $blog = Blog::find($id);
        if (! $blog) {
            return redirect()->back()->with('error', 'Blog not found.');
        }

        $data = $request->validated();

        $data['slug'] = Str::slug($request->title);
        $data['is_active'] = $request->has('is_active') ? 1 : 0;
        $data['update_by'] = Auth::id();
        $data['meta_title'] = $request->meta_title;
        $data['meta_description'] = $request->meta_description;
        $data['meta_keywords'] = $request->meta_keywords;
        $data['is_auto_slug'] = $request->has('autoSlug') ? 1 : 0;
        if ($data['is_auto_slug']) {
            $slug = Str::slug($request->title);

        } else {
            $slug = $request->blog_slug;
        }
        $exists = Blog::where('slug', $slug)->where('id', '!=', $blog->id)->exists();
        if ($exists) {
            return redirect()->back()->withInput()->withErrors(['blog_slug' => 'This slug already exists']);
        }
        $data['slug'] = $slug;

        if ($request->hasFile('featured_image')) {

            if ($blog->featured_image) {
                Storage::disk('public')->delete($blog->featured_image);
            }

            $data['featured_image'] = $request->file('featured_image')
                ->store('blogs', 'public');
        }

        if ($request->hasFile('detailed_image')) {

            if ($blog->detailed_image) {
                Storage::disk('public')->delete($blog->detailed_image);
            }

            $data['detailed_image'] = $request->file('detailed_image')
                ->store('blogs', 'public');
        }

        // OLD images
        $oldImages = $this->extractImagePaths($blog->content);

        // Process new content
        $data['content'] = $this->processEditorImages($data['content']);

        // NEW images
        $newImages = $this->extractImagePaths($data['content']);

        // Find deleted images
        $deletedImages = array_diff($oldImages, $newImages);

        // Delete them from storage
        foreach ($deletedImages as $img) {
            Storage::disk('public')->delete($img);
        }

        $blog->update($data);

        return redirect()->route('org.blogs-index')
            ->with('success', 'Blog updated successfully');
    }

    private function processEditorImages($content)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');

        libxml_use_internal_errors(true);

        $dom->loadHTML(
            mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'),
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );

        libxml_clear_errors();

        $images = $dom->getElementsByTagName('img');

        foreach ($images as $img) {
            $src = $img->getAttribute('src');

            if (Str::startsWith($src, 'data:image')) {
                preg_match('/data:image\/([a-zA-Z0-9\+\-\.]+);base64,/', $src, $type);
                $extension = $type[1] ?? 'png';

                if ($extension === 'svg+xml') {
                    $extension = 'svg';
                }

                $image = preg_replace('/^data:image\/[a-zA-Z0-9\+\-\.]+;base64,/', '', $src);
                $image = base64_decode($image);

                if ($image === false) {
                    continue;
                }

                $fileName = 'blogs/content/' . Str::random(20) . '.' . $extension;

                Storage::disk('public')->put($fileName, $image);

                $img->setAttribute('src', asset('storage/' . $fileName));
            }
        }

        return $dom->saveHTML();
    }

    private function extractImagePaths($content)
    {
        $dom = new \DOMDocument();

        libxml_use_internal_errors(true);
        $dom->loadHTML($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $images = $dom->getElementsByTagName('img');

        $paths = [];

        foreach ($images as $img) {
            $src = $img->getAttribute('src');

            // only take local storage images
            if (Str::contains($src, '/storage/blogs/content/')) {
                $paths[] = str_replace('/storage/', '', parse_url($src, PHP_URL_PATH));
            }
        }

        return $paths;
    }

    public function get(Request $request)
    {
        $columns = [
            0 => 'id',
            1 => 'country_id',
            2 => 'title',
            3 => 'category',
            4 => 'featured_image',
            5 => 'status',
        ];

        $query = Blog::with('country');

        $totalData = $query->count();

        $limit = $request->input('length', 10);
        $start = $request->input('start', 0);
        $orderColumnIndex = $request->input('order.0.column', 0);
        $orderColumn = $columns[$orderColumnIndex] ?? 'id';
        $orderDir = $request->input('order.0.dir', 'desc');

        // 🔍 Global Search
        if ($request->filled('search.value')) {
            $s = $request->input('search.value');
            $query->where(function ($qq) use ($s) {
                $qq->where('title', 'like', "%$s%")
                    ->orWhere('category', 'like', "%$s%")
                    ->orWhere('short_description', 'like', "%$s%")
                    ->orWhere('status', 'like', "%$s%");
            });
        }

        $totalFiltered = $query->count();

        $blogs = $query->offset($start)
            ->limit($limit)
            ->orderBy($orderColumn, $orderDir)
            ->get();

        $data = [];
        $sn = $start + 1;

        foreach ($blogs as $blog) {

            $featuredImage = $blog->featured_image
                ? '<img src="'.asset('storage/'.$blog->featured_image).'" width="50" height="50" class="rounded">'
                : '<span class="badge bg-secondary">No Image</span>';

            $detailedImage = $blog->detailed_image
                ? '<img src="'.asset('storage/'.$blog->detailed_image).'" width="50" height="50" class="rounded">'
                : '<span class="badge bg-secondary">No Image</span>';

            $statusBadge = $blog->status == 'published'
                ? '<span class="badge bg-success">Published</span>'
                : '<span class="badge bg-warning">Draft</span>';

            $actions = '
                <a style="font-size:18px;" href="'.route('org.blogs-edit', encrypt($blog->id)).'"
                    class="text-primary me-2" title="Edit">
                    <i class="las la-edit"></i>
                </a>

                <form action="'.route('org.blogs-destroy', encrypt($blog->id)).'"
                    method="POST"
                    class="d-inline"
                    onsubmit="return confirm(\'Delete this Blog?\');">
                    '.csrf_field().method_field('DELETE').'
                    <button style="font-size:18px;" type="submit" class="btn btn-link text-danger p-0">
                        <i class="las la-trash"></i>
                    </button>
                </form>
            ';

            $data[] = [
                'sn' => $sn++,
                'country' => $blog->country->country_name ?? '-',
                'title' => '<div class="fw-semibold">'.$blog->title.'</div>',
                'category' => $blog->category ?? '-',
                'featured_image' => $featuredImage,
                'detailed_image' => $detailedImage,
                'short_description' => \Str::limit($blog->short_description, 50),
                'status' => $statusBadge,
                'actions' => $actions,
            ];
        }

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => intval($totalData),
            'recordsFiltered' => intval($totalFiltered),
            'data' => $data,
        ]);
    }

    public function edit($id)
    {
        $blogId = decrypt($id);
        $blog = Blog::find($blogId);

        if (! $blog) {
            return redirect()->back()->with('error', 'Blog not found.');
        }
        $countries = Country::where('country_status', 1)
            ->orderBy('country_name')
            ->get(['id', 'country_name']);

        return view('org.blogs.form', compact('blog', 'countries'));
    }

    public function destroy($id)
    {
        $id = decrypt($id);
        $blog = Blog::find($id);
        if (! $blog) {
            return redirect()->back()->with('error', 'Blog not found.');
        }
        $blog->delete();

        return redirect()->back()->with('success', 'Blog deleted.');
    }

    public function search(Request $request)
    {
        $query = $request->q;

        if(!$query){
            return response()->json([]);
        }

        $slug = $request->slug;
        $blog = Blog::where('slug', $slug)->first();

        $country_code = session('country_code') ?? null;
        $countries = Country::where('country_code', $country_code)->first();

        $blogs = Blog::where('id', '!=', $blog->id)
            ->where('country_id', $countries->id)
            ->where('title', 'like', "%{$query}%")
            ->limit(5)
            ->get();
        $blogs->map(function($item){
            if (!empty($item->featured_image) && \Storage::disk('public')->exists($item->featured_image)) {
                $item->image_url = asset('storage/'.$item->featured_image);
            } else {
                $item->image_url = asset('assets/images/defalt/dummy_property_images.png');
            }
            return $item;
        });

        return response()->json($blogs);
    }






}
