<?php

namespace App\Http\Controllers;

use App\Models\PaymentPlans;
use App\Models\PaymentPlanStep;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentPlanController extends Controller
{
    // public function store(Request $request)
    // {
    //     // dd($request->all()); 
    //     $request->validate([
    //         // payment_plans
    //         'plan_name'       => 'required|string|max:255',
    //         'duration_months' => 'nullable|integer',
    //         'is_handover'     => 'nullable|boolean',
    //         'months_after_handover' => 'nullable|integer',
    //         'eoi'             => 'nullable|numeric',
    //         'sort_order'      => 'nullable|integer',

    //         // steps
    //         'step_name'     => 'required|array',
    //         'step_name.*'   => 'required|string',
    //         'stage_type'    => 'required|array',
    //         'stage_type.*'  => 'required|string',
    //         'percentage'    => 'nullable|array',
    //         'start'         => 'nullable|array',
    //         'end'           => 'nullable|array',
    //         'fixed_amount'  => 'nullable|array',
    //         'step_sort_order' => 'nullable|array',
    //     ]);

    //     DB::beginTransaction();

    //     try {

    //         /* ======================
    //         SAVE PAYMENT PLAN
    //         ====================== */
    //         $plan = new PaymentPlans();
    //         $plan->name                 = $request->plan_name;
    //         $plan->project_id                 = $request->project_id;
    //         $plan->duration_months       = $request->duration_months ?? 0;
    //         $plan->is_handover           = $request->is_handover ?? 0;
    //         $plan->months_after_handover = $request->months_after_handover;
    //         $plan->eoi                   = $request->eoi;
    //         $plan->sort_order            = $request->sort_order ?? 0;
    //         $plan->save();

    //         /* ======================
    //         SAVE PLAN STEPS
    //         ====================== */
    //         foreach ($request->step_name as $i => $name) {

    //             $step = new PaymentPlanStep();
    //             $step->payment_plan_id = $plan->id;
    //             $step->name            = $name;
    //             $step->percentage      = $request->percentage[$i] ?? 0;
    //             $step->stage_type      = $request->stage_type[$i];
    //             $step->start           = $request->start[$i] ?? null;
    //             $step->end             = $request->end[$i] ?? null;
    //             $step->fixed_amount    = $request->fixed_amount[$i] ?? 0;
    //             $step->sort_order      = $request->step_sort_order[$i] ?? $i;
    //             $step->save();
    //         }

    //         DB::commit();

    //         return redirect()->back()->with('success', 'Payment Plan saved successfully.');

    //     } catch (\Exception $e) {
    //         dd($e->getMessage());
    //         DB::rollBack();
    //         return redirect()->back()
    //             ->withInput()
    //             ->with('error', $e->getMessage());
    //     }
    // }

    public function storePaymentPlan(Request $request)
    {
        $request->validate([
            'project_id'       => 'required|integer',
            'plan_name'        => 'required|string|max:255',
            'duration_months'  => 'nullable|integer',
            'is_handover'      => 'nullable|boolean',
            'months_after_handover' => 'nullable|integer',
            'eoi'              => 'nullable|string',
            'sort_order'       => 'nullable|integer',
        ]);

        // $plan = new PaymentPlans();
         $plan = !empty($request->payment_plan_id)
        ? PaymentPlans::findOrFail(decrypt($request->payment_plan_id))
        : new PaymentPlans();
        $plan->project_id           = $request->project_id;
        $plan->name                 = $request->plan_name;
        $plan->duration_months      = $request->duration_months;
        $plan->is_handover          = $request->is_handover ?? 0;
        $plan->months_after_handover= $request->months_after_handover;
        $plan->eoi                  = $request->eoi;
        $plan->sort_order           = $request->sort_order ?? 0;
        $plan->save();

       return redirect()->back()->with('success', 'Payment Plan saved. Now add steps.');
    }


    public function getPaymentPlan(Request $request)
    {
        $columns = [
            0 => 'id',
            1 => 'name',
            2 => 'duration_months',
            3 => 'is_handover',
            4 => 'months_after_handover',
            5 => 'eoi',
        ];

        $query = PaymentPlans::where('project_id', $request->project_id);

        $totalData = $query->count();
        $totalFiltered = $totalData;

        // 🔍 Search (sirf relevant fields)
        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');

            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                ->orWhere('eoi', 'LIKE', "%{$search}%");
            });

            $totalFiltered = $query->count();
        }

        // 🔢 Pagination
        $limit = $request->input('length', 10);
        $start = $request->input('start', 0);

        // 🔢 Ordering
        $orderIndex  = $request->input('order.0.column', 1);
        $orderColumn = $columns[$orderIndex] ?? 'name';
        $orderDir    = $request->input('order.0.dir', 'asc');

        $plans = $query->offset($start)
            ->limit($limit)
            ->orderBy($orderColumn, $orderDir)
            ->get();

        // 📦 Format Data
        $data = [];
        $sn = $start + 1;

        foreach ($plans as $plan) {

            $handover = $plan->is_handover
                ? '<span class="badge bg-success">Yes</span>'
                : '<span class="badge bg-secondary">No</span>';

            $action = '
                <a href="'.route('org.payment-plan.edit', encrypt($plan->id)).'"
                class="text-primary me-2" title="Edit">
                    <i class="las la-edit"></i>
                </a>

                <form action="'.route('org.payment-plan.destroy', encrypt($plan->id)).'"
                    method="POST"
                    class="d-inline"
                    onsubmit="return confirm(\'Delete this payment plan?\');">
                    '.csrf_field().method_field('DELETE').'
                    <button type="submit" class="btn btn-link text-danger p-0">
                        <i class="las la-trash"></i>
                    </button>
                </form>
            ';

            $data[] = [
                'id'                     => $sn++,
                'name'                   => e($plan->name),
                'duration_months'        => $plan->duration_months ?? '-',
                'is_handover'            => $handover,
                'months_after_handover'  => $plan->months_after_handover ?? '-',
                'eoi'                    => $plan->eoi ?? '-',
                'action'                 => $action,
            ];
        }

        return response()->json([
            "draw"            => intval($request->draw),
            "recordsTotal"    => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data"            => $data,
        ]);
    }


     public function editPlan($id)
    {
        $planId = decrypt($id);

        $plan = PaymentPlans::find($planId);  
        if (!$plan) {
            return redirect()->back()->with('error', 'Payment Plan not found.');
        }
        $project = Project::where('id', $plan->project_id)->first();
        return view('org.projects.payment_plan.edit',compact('plan','project'));
    }



    public function destroyPaymentPlan($payment)
    {
        $id = decrypt($payment);

        $pm = PaymentPlans::findOrFail($id);
        $pm->delete();

        return redirect()->back()->with('success', 'Payment Plan deleted.');
    }

    public function createStepPlan($id)
    {
        $steps = collect();
        $planId = decrypt($id);
        $plan = PaymentPlans::find($planId);  
        if (!$plan) {
            return redirect()->back()->with('error', 'Payment Plan not found.');
        }
        $project = Project::where('id', $plan->project_id)->first();
        return view('org.projects.payment_plan.payment_plan_step_create',compact('plan','project','steps'));

    }

    public function storePlanStep(Request $request)
    {
        $request->validate([
            'payment_plan_id'      => 'required',
            
            // steps arrays
            'step_name'            => 'required|array|min:1',
            'step_name.*'          => 'required|string|max:255',

            'stage_type'           => 'required|array',
            'stage_type.*'         => 'required|in:on_booking,during_construction,on_handover,post_handover',

            'percentage'           => 'nullable|array',
            'percentage.*'         => 'nullable|numeric|min:0|max:100',
        ]);

        try {

            $planId = decrypt($request->payment_plan_id);

            foreach ($request->step_name as $i => $name) {

                $step = new PaymentPlanStep();
                $step->payment_plan_id = $planId;
                $step->name            = $name;
                $step->percentage      = $request->percentage[$i] ?? 0;
                $step->stage_type      = $request->stage_type[$i];
                $step->start           = $request->start[$i] ?? null;
                $step->end             = $request->end[$i] ?? null;
                $step->fixed_amount    = $request->fixed_amount[$i] ?? 0;
                $step->sort_order      = $request->step_sort_order[$i] ?? $i;
                $step->save();
            }

            return redirect()->back()->with('success', 'Payment Plan Steps saved successfully.');

        } catch (\Exception $e) {

            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function getPlanStep(Request $request)
    {
        $columns = [
            0 => 'id',
            1 => 'name',
            2 => 'percentage',
            3 => 'stage_type',
            4 => 'start',
            5 => 'end',
        ];

        $query = PaymentPlanStep::where('payment_plan_id', $request->plan_id);

        $totalData = $query->count();
        $totalFiltered = $totalData;

        // 🔍 Search (sirf relevant fields)
        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');

            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                ->orWhere('stage_type', 'LIKE', "%{$search}%");
            });

            $totalFiltered = $query->count();
        }

        // 🔢 Pagination
        $limit = $request->input('length', 10);
        $start = $request->input('start', 0);

        // 🔢 Ordering
        $orderIndex  = $request->input('order.0.column', 1);
        $orderColumn = $columns[$orderIndex] ?? 'name';
        $orderDir    = $request->input('order.0.dir', 'asc');

        $plans = $query->offset($start)
            ->limit($limit)
            ->orderBy($orderColumn, $orderDir)
            ->get();

        // 📦 Format Data
        $data = [];
        $sn = $start + 1;

        foreach ($plans as $plan) {

           

            $action = '
                <a href="'.route('org.payment-plan.step.edit', encrypt($plan->id)).'"
                class="text-primary me-2" title="Edit">
                    <i class="las la-edit"></i>
                </a>

                <form action="'.route('org.payment-plan.step.destroy', encrypt($plan->id)).'"
                    method="POST"
                    class="d-inline"
                    onsubmit="return confirm(\'Delete this payment plan?\');">
                    '.csrf_field().method_field('DELETE').'
                    <button type="submit" class="btn btn-link text-danger p-0">
                        <i class="las la-trash"></i>
                    </button>
                </form>
            ';

            $data[] = [
                'id'                     => $sn++,
                'name'                   => e($plan->name),
                'percentage'        => $plan->percentage ?? '-',
                'stage_type'            => $plan->stage_type,
                'start'  => $plan->start ?? '-',
                'end'                    => $plan->end ?? '-',
                'action'                 => $action,
            ];
        }

        return response()->json([
            "draw"            => intval($request->draw),
            "recordsTotal"    => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data"            => $data,
        ]);
    }

    public function editPlanStep($id)
    {
        $planId = decrypt($id);
        $steps = PaymentPlanStep::find($planId);  
        if (!$steps) {
            return redirect()->back()->with('error', 'Payment Plan not found.');
        }
        $plan = PaymentPlans::find($steps->payment_plan_id);  
        if (!$plan) {
            return redirect()->back()->with('error', 'Payment Plan not found.');
        }
        $project = Project::where('id', $plan->project_id)->first();
         
        return view('org.projects.payment_plan.payment_plan_step_edit',compact('plan','project','steps'));   
    }


    public function updatePlanStep(Request $request, $encryptedPlanId)
    {
        $request->validate([
            'step_id'    => 'required|integer',
            'step_name'  => 'required|string',
            'stage_type' => 'required|string',
            'percentage' => 'nullable|numeric',
        ]);

        try {

            // ✅ Correct plan id
            $planId = decrypt($encryptedPlanId);

            // ✅ Step fetch by step_id
            $step = PaymentPlanStep::find($request->step_id);

            if (!$step) {
                return redirect()->back()->with('error', 'Payment Plan step not found.');
            }

            // ✅ Update step correctly
            $step->payment_plan_id = $planId;   // parent plan id
            $step->name            = $request->step_name;
            $step->percentage      = $request->percentage ?? 0;
            $step->stage_type      = $request->stage_type;
            $step->start           = $request->start;
            $step->end             = $request->end;
            $step->fixed_amount    = $request->fixed_amount ?? 0;
            $step->sort_order      = $request->step_sort_order;

            $step->save();

            return redirect()->back()->with('success', 'Payment Plan Step updated successfully.');

        } catch (\Exception $e) {

            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }


    public function destroyPlanStep($paymentPlanStep)
    {
        $id = decrypt($paymentPlanStep);

        $pm = PaymentPlanStep::findOrFail($id);
        $pm->delete();

        return redirect()->back()->with('success', 'Payment Plan Step deleted.');
    }



}
