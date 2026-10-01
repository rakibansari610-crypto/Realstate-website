<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectMaterial;
use App\Models\ProjectMedia;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProjectMediaController extends Controller
{
  



    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'project_id' => 'required|integer',
            'collection' => 'required|string|max:255',
            'file'       => 'nullable|file',
            'video_link' => 'nullable|url'
        ]);

        DB::beginTransaction();

        try {

            $media = null;

            if ($request->filled('id')) {
                $media = ProjectMedia::findOrFail($request->id);
            }

            $data = [];

            /* ---------------- FILE UPLOAD ---------------- */

            if ($request->hasFile('file')) {

                $file = $request->file('file');

                if ($media && $media->url) {
                    $oldPath = str_replace(asset('storage/'), '', $media->url);
                    Storage::disk('public')->delete($oldPath);
                }

                $filename = Str::uuid() . '.' . $file->extension();

                $path = $file->storeAs("projects/{$request->project_id}", $filename, 'public');

                $url = asset('storage/' . $path);

                $width = null;
                $height = null;

                if (str_contains($file->getMimeType(), 'image')) {
                    [$width, $height] = getimagesize($file->getRealPath());
                }

                $data = [
                    'project_id' => $request->project_id,
                    'collection' => $request->collection,
                    'url'        => $url,
                    'mime'       => $file->getMimeType(),
                    'size'       => $file->getSize(),
                    'width'      => $width,
                    'height'     => $height,
                    'sort_order' => $request->sort_order ?? 1,
                    'meta'       => [
                        'url' => $url,
                        'metadata' => [
                            'mime'   => $file->getMimeType(),
                            'size'   => $file->getSize(),
                            'width'  => $width,
                            'height' => $height,
                        ]
                    ],
                ];
            }

            /* ---------------- VIDEO LINK ---------------- */

            if ($request->collection === 'videos' && !empty($request->video_link)) {
                // dd('aman');
                $data = [
                    'project_id' => $request->project_id,
                    'collection' => 'videos',
                    'url'        => $request->video_link,
                    'mime'       => 'video/link',
                    'size'       => null,
                    'width'      => null,
                    'height'     => null,
                    'sort_order' => $request->sort_order ?? 1,
                    'meta'       => [
                        'url' => $request->video_link
                    ],
                ];
            }

            /* ---------------- ONLY COLLECTION UPDATE ---------------- */

            if (empty($data)) {

                if ($media) {

                    $data = [
                        'project_id' => $request->project_id,
                        'collection' => $request->collection,
                        'sort_order' => $request->sort_order ?? $media->sort_order,
                    ];

                } else {

                    throw new \Exception("Please upload a file or provide a video link.");

                }
            }

            /* ---------------- SAVE / UPDATE ---------------- */

            if ($media) {

                $media->update($data);
                $message = "Project media updated successfully.";

            } else {

                ProjectMedia::create($data);
                $message = "Project media added successfully.";
            }

            DB::commit();

            return back()->with('success', $message);

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }




   public function get(Request $request)
    {
        $columns = [
            0 => 'id',
            1 => 'collection',
            2 => 'url',
            3 => 'size',
            4 => 'width',
            5 => 'height', 
            7 => 'created_at',
        ];

        $query = ProjectMedia::where('project_id', $request->project_id);

        $totalData = $query->count();
        $totalFiltered = $totalData;
 
        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');

            $query->where(function ($q) use ($search) {
                $q->where('collection', 'LIKE', "%{$search}%") 
                ->orWhere('size', 'LIKE', "%{$search}%")
                ->orWhere('width', 'LIKE', "%{$search}%")
                ->orWhere('height', 'LIKE', "%{$search}%")
                ;
            });

            $totalFiltered = $query->count();
        }
 
        $limit = $request->input('length', 10);
        $start = $request->input('start', 0);
 
        $orderIndex  = $request->input('order.0.column', 7);
        $orderColumn = $columns[$orderIndex] ?? 'created_at';

        $allowedOrderColumns = ['id','title','type','created_at'];
        if (!in_array($orderColumn, $allowedOrderColumns)) {
            $orderColumn = 'created_at';
        }

        $orderDir = $request->input('order.0.dir', 'desc');

        $materials = $query->offset($start)
            ->limit($limit)
            ->orderBy($orderColumn, $orderDir)
            ->get(); 
        $data = [];
        $sn = $start + 1;

        foreach ($materials as $m) {
            if ($m->collection == 'videos') {
                $url = '<a href="'.$m->url.'" target="_blank">'.$m->url.'</a>';
            } else {
                $url = '<img src="'.$m->url.'" width="100" height="100"/>';
            }
              
            $action = '
                <a style="font-size:18px;" href="'.route('org.project-media.edit', encrypt($m->id)).'"
                class="text-primary me-2" title="Edit">
                    <i class="las la-edit"></i>
                </a>

                <form action="'.route('org.project-media.destroy', encrypt($m->id)).'"
                    method="POST"
                    class="d-inline"
                    onsubmit="return confirm(\'Delete this Media ?\');">
                    '.csrf_field().method_field('DELETE').'
                    <button style="font-size:18px;" type="submit" class="btn btn-link text-danger p-0">
                        <i class="las la-trash"></i>
                    </button>
                </form>
            ';

            $data[] = [
                'id'         => $sn++,
                'collection'      => ucwords(str_replace('_',' ',$m->collection)),
                 'url'        => $url,
                'size'      => $m->size,
                'height'        => $m->height,
                'width'         => $m->width, 
                'created_at' => optional($m->created_at)->timestamp,
                'action'     => $action,
            ];
        }

        return response()->json([
            "draw"            => intval($request->draw),
            "recordsTotal"    => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data"            => $data,
        ]);
    }




    public function edit($id)
    {
        $media_id = decrypt($id);

        $media = ProjectMedia::find($media_id);  
        if (!$media) {
            return redirect()->back()->with('error', 'Media not found.');
        }
        $project = Project::where('id', $media->project_id)->first();
        return view('org.projects.project_media.edit',compact('media','project'));
    }



    public function destroy($media)
    {
        $id = decrypt($media);

        $pr = ProjectMedia::findOrFail($id);
        $pr->delete();

        return redirect()->back()->with('success', 'Project Media deleted.');
    }

}
