<?php

namespace App\Http\Controllers;

use App\Models\Support;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function index()
    {
        // dd(123);
        return view('org.support.index');
    }

    public function get(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $rowperpage = $request->get('length');

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column'];
        $columnName = $columnName_arr[$columnIndex]['data'];
        $columnSortOrder = $order_arr[0]['dir'];
        $searchValue = $search_arr['value'];

        if ($columnName == 'sn' || $columnName == 'action') {
            $columnName = 'id';
        }

        $totalRecords = Support::count();

        $totalRecordswithFilter = Support::where(function ($q) use ($searchValue) {
            $q->where('name', 'like', "%$searchValue%")
                ->orWhere('email', 'like', "%$searchValue%")
                ->orWhere('phone_no', 'like', "%$searchValue%");
        })->count();

        $records = Support::where(function ($q) use ($searchValue) {
            $q->where('name', 'like', "%$searchValue%")
                ->orWhere('email', 'like', "%$searchValue%")
                ->orWhere('phone_no', 'like', "%$searchValue%");
        })
            ->orderBy($columnName, $columnSortOrder)
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = [];
        $i = $start + 1;

        foreach ($records as $record) {
            $action = '
                <a href="javascript:void(0)"
                  onclick="DeleteItems(\''.url('support').'\', \'delete\', \''.encrypt($record->id).'\')"
                  class="text-danger"
                  title="Delete">
                  <i class="ri-delete-bin-5-line"></i>
                </a>';

            $data_arr[] = [
                'sn' => $i++,
                'message' => $record->message,
                'email' => $record->email,
                'phone_no' => $record->phone_no,
                'name' => $record->name,
                'action' => $action,
            ];
        }

        return response()->json([
            'draw' => intval($draw),
            'iTotalRecords' => $totalRecords,
            'iTotalDisplayRecords' => $totalRecordswithFilter,
            'aaData' => $data_arr,
        ]);
    }

     public function destroy(Request $request)
    {
        $id = decrypt($request->id);
        $obj = Support::find($id);
        $obj->delete();
        return redirect('/support/index')->with('success', ' Data deleted');
    }
}
