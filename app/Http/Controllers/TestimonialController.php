<?php

namespace App\Http\Controllers;

use App\Http\Requests\TestimonialRequest;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class TestimonialController extends Controller
{
    public function index()
    {
        return view('org.testimonial.index');
    }

    public function create()
    { 
        return view('org.testimonial.form');
    }

    


    public function store(TestimonialRequest $request)
    {
        $data = $request->validated();

         $id = $request->id; 
        $data['is_active'] = $request->has('is_active') ? 1 : 0; 
         if ($id) {
            $testimonial = Testimonial::find($id);

            if (!$testimonial) {
                return redirect()
                    ->back()
                    ->with('error', 'Testimonial not found.');
            }
        } else {
            $testimonial = new Testimonial();
        }
        if ($request->hasFile('profile')) { 
            if ($id && !empty($testimonial->profile)) {
                Storage::disk('public')->delete($testimonial->profile);
            }

            $path = $request->file('profile')
                            ->store('testimonials', 'public');

            $data['profile'] = $path;
        } 
        if (auth()->check()) {
            $data['update_by'] = auth()->id();
        }

        $testimonial->fill($data);
        $testimonial->save();

        return redirect()
            ->route('org.testimonial-index')
            ->with('success', $id 
                ? 'Testimonial Updated Successfully' 
                : 'Testimonial Created Successfully');
    }



    public function get(Request $request)
    {
        $columns = [
            0 => 'id',
            1 => 'profile',
            2 => 'name',
            3 => 'designation',
            4 => 'rating_number',
            5 => 'description',
        ];

        $query = Testimonial::query();

        $totalData = $query->count();

        $limit = $request->input('length', 10);
        $start = $request->input('start', 0);
        $orderColumnIndex = $request->input('order.0.column', 0);
        $orderColumn = $columns[$orderColumnIndex] ?? 'id';
        $orderDir = $request->input('order.0.dir', 'desc');

        // Global search
        if ($request->filled('search.value')) {
            $s = $request->input('search.value');
            $query->where(function ($qq) use ($s) {
                $qq->where('name', 'like', "%$s%")
                ->orWhere('designation', 'like', "%$s%")
                ->orWhere('rating_number', 'like', "%$s%")
                ->orWhere('description', 'like', "%$s%");
            });
        }

        $totalFiltered = $query->count();

        $testimonials = $query->offset($start)
            ->limit($limit)
            ->orderBy($orderColumn, $orderDir)
            ->get();

        $data = [];
        $sn = $start + 1;

        foreach ($testimonials as $testimonial) {

            $stars = '';
            for ($i = 1; $i <= 5; $i++) {
                if ($i <= $testimonial->rating_number) {
                    $stars .= '<i class="las la-star text-warning"></i>';
                } else {
                    $stars .= '<i class="las la-star text-muted"></i>';
                }
            }

            $ratingHtml = '<ul class="list-inline mb-0">'.$stars.'</ul>';

            $image = $testimonial->profile
                ? '<img src="'.asset('storage/'.$testimonial->profile).'" width="40" height="40" class="rounded-circle">'
                : '<span class="badge bg-secondary">No Image</span>';

            $actions = '
                <a style="font-size:18px;" href="'.route('org.testimonial-edit', encrypt($testimonial->id)).'"
                    class="text-primary me-2" title="Edit">
                    <i class="las la-edit"></i>
                </a>

                <form action="'.route('org.testimonial-destroy', encrypt($testimonial->id)).'"
                    method="POST"
                    class="d-inline"
                    onsubmit="return confirm(\'Delete this Testimonial?\');">
                    '.csrf_field().method_field('DELETE').'
                    <button style="font-size:18px;" type="submit" class="btn btn-link text-danger p-0">
                        <i class="las la-trash"></i>
                    </button>
                </form>
            ';

            $data[] = [
                'sn'             => $sn++,
                'profile'        => $image,
                'name'           => '<div class="fw-semibold">'.$testimonial->name.'</div>',
                'designation'    => $testimonial->designation,
                'rating_number'  => $ratingHtml,
                'description'    => \Str::limit($testimonial->description, 50),
                'actions'        => $actions,
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
        $agentId = decrypt($id);
        $testimonial = Testimonial::find($agentId);
        if (!$testimonial) {
            return redirect()->back()->with('error', 'Testimonial not found.');
        }
        return view('org.testimonial.form', compact('testimonial'));
    }

    public function destroy($id)
    {
        $id = decrypt($id); 
        $testimonial = Testimonial::find($id);
        if (!$testimonial) {
            return redirect()->back()->with('error', 'Testimonial not found.');
        }
        $testimonial->delete();

        return redirect()->back()->with('success', 'Testimonial deleted.');
    }
    

    public function destroyImage($id)
    {
        try {

            DB::beginTransaction();
            $id = decrypt($id);
            $testimonial = Testimonial::find($id);

            if(!$testimonial){
                return redirect()->back()->with('error','Record not found');
            }

            if(!empty($testimonial->profile)){ 
                Storage::disk('public')->delete($testimonial->profile); 
                $testimonial->profile = null;
                $testimonial->save();
            }

            DB::commit();

            return redirect()->back()->with('success','Image deleted successfully');

        } catch (\Exception $e) {

            DB::rollBack();

            return redirect()->back()->with('error','Something went wrong');
        }
    }



}
