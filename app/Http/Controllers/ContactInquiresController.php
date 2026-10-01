<?php

namespace App\Http\Controllers;

use App\Models\ContactInquiry;
use App\Models\Project;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContactInquiresController extends Controller
{
     public function contactIndex($types)
    { 
        return view('org.enquiry.index', compact('types'));
    }

    public function contactStore(Request $request)
    { 
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|max:255',
            'phone_no'  => 'nullable|digits_between:10,15', 
            'message'   => 'required|string',
        ]);

        DB::beginTransaction();
        try {

            $contact = new ContactInquiry();
            $contact->name     = $validated['name'];
            $contact->email    = $validated['email'];
            $contact->phone_no = $validated['phone_no'] ?? null; 
            $contact->message  = $validated['message'];
            $contact->p_id     = $request->p_id ?? null ;
            $contact->type     = $request->type;
            $contact->save();

            DB::commit();

            return redirect()->back()->with('success', 'Inquiry submitted successfully.');

        } catch (\Exception $e) {

            DB::rollBack();
            dd($e->getMessage());
            return redirect()->back()->with(
                'error',
                'Something went wrong. Please try again. ' . $e->getMessage()
            );
        }
    }

    public function contactGet(Request $request)
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
        $types           = $request->types;  

        if (in_array($columnName, ['sn', 'action', 'detail'])) {
            $columnName = 'contact_inquiries.id';
        }

        $query = ContactInquiry::query();
 
        if (!empty($types)) {
            $query->where('type', $types);
        }
 
        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('name', 'like', "%{$searchValue}%")
                ->orWhere('email', 'like', "%{$searchValue}%")
                ->orWhere('phone_no', 'like', "%{$searchValue}%")
                ->orWhere('message', 'like', "%{$searchValue}%");
            });
        }

        $totalRecords = ContactInquiry::count();
        $totalRecordswithFilter = $query->count();

        $records = $query
            ->orderBy($columnName, $columnSortOrder)
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = [];
        $i = $start + 1;

        foreach ($records as $record) {

            $detail = '-'; 
            if ($record->type === 'project') {
                $project = Project::find($record->p_id);
                if ($project) {
                    $detail = $project->name;
                }
            } 
            elseif ($record->type === 'properties') {
                $unit = Unit::find($record->p_id);
                if ($unit) {
                    $detail = $unit->property_name;
                }
            }

            $action = '
                <a href="javascript:void(0)"
                onclick="DeleteItems(\''.url('contact/inquiries').'\', \'delete\', \''.encrypt($record->id).'\')"
                class="text-danger"
                title="Delete">
                <i class="ri-delete-bin-5-line"></i>
                </a>';

            $data_arr[] = [
                'sn'        => $i++,
                'name'      => $record->name,
                'email'     => $record->email,
                'phone_no'  => $record->phone_no,
                'message'   => $record->message,
                'detail'    => $detail,   
                'action'    => $action,
            ];
        }

        return response()->json([
            'draw' => intval($draw),
            'iTotalRecords' => $totalRecords,
            'iTotalDisplayRecords' => $totalRecordswithFilter,
            'aaData' => $data_arr,
        ]);
    }

    public function contactDestroy($id)
    {
        try {
            $decryptedId = decrypt($id);
            $inquiry = ContactInquiry::findOrFail($decryptedId);
            $inquiry->delete();

            return redirect()->back()->with('success', 'Inquiry deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete inquiry. ' . $e->getMessage());
        }
    }


    public function contactEnquiryStore(Request $request)
    { 
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|max:255',
            'phone_no'  => 'nullable|digits_between:10,15',
            'message'   => 'required|string',
            'timeline'  => 'nullable|string|max:255',
            'location'  => 'nullable|string|max:255',
        ],[
            'name.required'     => 'Please enter your name.',
            'email.required'    => 'Please enter your email.',
            'email.email'       => 'Please enter a valid email.',
            'phone_no.digits_between' => 'Phone number must be between 10 to 15 digits.',
            'message.required'  => 'Please enter your message.',
        ]); 
        DB::beginTransaction();

        try {

            $contact = new ContactInquiry();

            $contact->name      = $validated['name'];
            $contact->email     = $validated['email'];
            $contact->phone_no  = $validated['phone_no'] ?? null;
            $contact->message   = $validated['message'];
            $contact->timeline  = $validated['timeline'] ?? null;
            $contact->location  = $validated['location'] ?? null;
            $contact->type      = 'contact'; 
            $contact->save();

            DB::commit();

            return redirect()
                ->back()
                ->with('success', 'Inquiry submitted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }


    





}
