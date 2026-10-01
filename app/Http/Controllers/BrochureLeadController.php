<?php

namespace App\Http\Controllers;

use App\Models\BrochureLead;
use App\Models\Project;
use App\Models\ProjectMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Crypt;

class BrochureLeadController extends Controller
{
     public function brochureContactStore(Request $request)
    {
        $request->validate([
            'name'          => 'required',
            'email'         => 'required|email',
            'phone'         => 'required|digits:10',
            'project_media' => 'required',
            'project_id'    => 'required',
        ]);

        try {
            $downloadUrl = decrypt($request->project_media);
            $projectId   = decrypt($request->project_id);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid download link'
            ], 422);
        }

        // Save lead
        $lead = new BrochureLead();
        $lead->project_id = $projectId;
        $lead->name       = $request->name;
        $lead->email      = $request->email;
        $lead->phone      = $request->phone;
        $lead->language   = $request->language;
        $lead->save();

        return response()->json([
            'status' => 'success', 
            'download_url' => route('brochure.download', [
                'file' => encrypt($downloadUrl)
            ])
        ]);
    }

    // ===============================
    // FORCE DOWNLOAD (EXTERNAL FILE)
    // ===============================
    public function downloadBrochure(Request $request)
    {
        try {
            $url = decrypt($request->file);
        } catch (\Exception $e) {
            abort(403, 'Invalid file');
        }

        $response = Http::get($url);

        if (!$response->successful()) {
            abort(404, 'File not found');
        }

        $filename = basename(parse_url($url, PHP_URL_PATH));

        return response()->streamDownload(function () use ($response) {
            echo $response->body();
        }, $filename);
    }





    public function index(Request $request)
    {
        return view ('org.brochure.index');
    }


   public function get(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $rowperpage = $request->get('length');

        $columnIndex_arr = $request->get('order');
        $columnName_arr  = $request->get('columns');
        $order_arr       = $request->get('order');
        $search_arr      = $request->get('search');

        $columnIndex     = $columnIndex_arr[0]['column'];
        $columnName      = $columnName_arr[$columnIndex]['data'];
        $columnSortOrder = $order_arr[0]['dir'];
        $searchValue     = $search_arr['value'];

        if (in_array($columnName, ['sn', 'action'])) {
            $columnName = 'id';
        }

        $query = BrochureLead::query();

        // 🔍 Search only name, email, phone
        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('name', 'like', "%{$searchValue}%")
                ->orWhere('email', 'like', "%{$searchValue}%")
                ->orWhere('phone', 'like', "%{$searchValue}%");
            });
        }

        $totalRecords = BrochureLead::count();
        $totalRecordswithFilter = $query->count();

        $records = $query
            ->orderBy($columnName, $columnSortOrder)
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = [];
        $i = $start + 1;

        foreach ($records as $record) {

            $action = '
                <a href="javascript:void(0)"
                onclick="DeleteItems(\''.url('brochure/inquiries').'\', \'delete\', \''.encrypt($record->id).'\')"
                class="text-danger" title="Delete">
                <i class="ri-delete-bin-5-line"></i>
                </a>';

            $data_arr[] = [
                'sn'       => $i++,
                'name'     => $record->name,
                'email'    => $record->email,
                'phone' => $record->phone,
                'action'   => $action,
            ];
        }

        return response()->json([
            'draw' => intval($draw),
            'iTotalRecords' => $totalRecords,
            'iTotalDisplayRecords' => $totalRecordswithFilter,
            'aaData' => $data_arr,
        ]);
    }


    public function destroy($id)
    {
        try {
            $decryptedId = decrypt($id);
            $inquiry = BrochureLead::findOrFail($decryptedId);
            $inquiry->delete();

            return redirect()->back()->with('success', 'Inquiry deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete inquiry. ' . $e->getMessage());
        }
    }





}
