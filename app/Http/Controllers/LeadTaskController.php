<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadTaskController extends Controller
{
    public function index(Lead $lead)
    {
        $tasks = $lead->tasks()
            ->leftJoin('units as u', 'lead_tasks.unit_ref', '=', 'u.id')
            ->latest()
            ->get(['lead_tasks.id',
            'lead_tasks.task_type',
            'lead_tasks.note',
            'lead_tasks.unit_ref',
            'lead_tasks.is_completed',
            'lead_tasks.schedule_follow_up_days',
            'lead_tasks.date',
            'lead_tasks.time',
            'lead_tasks.created_at',
            'u.ref_no',
            'u.unit_number',
            'u.property_name'
            ]);

        return response()->json([
            'success' => true,
            'data'    => $tasks,
        ]);
    }

    public function store(Request $request, Lead $lead)
    {
        $data = $request->validate([
            'task_type'               => 'required|string|max:255',
            'note'                    => 'nullable|string',
            'unit_ref'                => 'nullable|string|max:255',
            'is_completed'            => 'nullable|boolean',
            'schedule_follow_up_days' => 'nullable|integer',
            'date'                    => 'nullable|date',
            'time'                    => 'nullable|date_format:H:i',
            // 'user_id'               => 'nullable|exists:users,id',
        ]);

        $data['lead_id']  = $lead->id;
        $data['user_id']  = Auth::id(); // who created the task
        $data['is_completed'] = $request->boolean('is_completed', false);

        $task = LeadTask::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Task created',
            'data'    => $task,
        ]);
    }
}
