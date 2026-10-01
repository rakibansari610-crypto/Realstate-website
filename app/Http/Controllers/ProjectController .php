<?php

namespace App\Http\Controllers;

use App\Services\LeadRatService;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    protected $leadRatService;

    public function __construct(LeadRatService $leadRatService)
    {
        $this->leadRatService = $leadRatService;
    }

    /**
     * Show project list page — matches your existing route:
     * https://vserverealestate.com/in/project-lists
     */
    public function index(Request $request)
    {
        $filters = [
            'PageNumber' => $request->get('page', 1),
            'PageSize'   => 12, // adjust as needed
        ];

        // Optional: allow search from a search box on your page
        if ($request->filled('search')) {
            $filters['Search'] = $request->get('search');
        }

        try {
            $projectData = $this->leadRatService->getAllProjects($filters);
        } catch (\Exception $e) {
            // Fail gracefully — show empty list + error message instead of crashing the page
            return view('projects.index', [
                'projects'   => [],
                'totalCount' => 0,
                'error'      => 'Unable to load projects right now. Please try again shortly.',
            ]);
        }

        return view('projects.index', [
            'projects'   => $projectData['items'] ?? [],
            'totalCount' => $projectData['totalCount'] ?? 0,
            'error'      => null,
        ]);
    }
}