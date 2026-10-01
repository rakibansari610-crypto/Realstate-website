<?php

namespace App\Http\Controllers;

use App\Models\AdminUiSetting;
use App\Models\Developer;
use App\Models\Lead;
use App\Models\Partner;
use App\Models\Project;
use App\Models\ProjectUnit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function su_dashboard()
    {

        $user = Auth::user();
        return view('su.dashboard.index');
    }

    public function org_dashboard()
    {

        $user  = Auth::user();
        // $orgId = $user->org_id;

        // Visibility scope: Admin sees all; others see only theirs
        $leadScope = function ($q) use ($user) {
            if (!$user->hasRole('org admin')) {
                $q->where(function ($qq) use ($user) {
                    $qq->where('assigned_to', $user->id)
                        ->orWhere('created_by', $user->id);
                });
            }
        };

        // --- KPIs ---
        // $developersCount = Developer::where('org_id', $orgId)->count();
        // $projectsCount   = Project::where('org_id', $orgId)->count();

        $developersCount = Developer::count();
        $projectsCount   = Project::count();
        // $leadsTotal = Lead::where('org_id', $orgId)->where($leadScope)->count();
        // $leadsBooked = Lead::where('org_id', $orgId)->where($leadScope)->where('status', 'booked')->count();
        // $partnersCount = Partner::where('org_id', $orgId)->count();
        // // Units (optional: requires ProjectUnit with status)
        // $unitsTotal = ProjectUnit::whereHas('project', fn($q) => $q->where('org_id', $orgId))->count();
        // $unitsBooked = ProjectUnit::whereHas('project', fn($q) => $q->where('org_id', $orgId))->whereRaw('LOWER(status) = ?', ['booked'])->count();
         $leadsTotal = Lead::where($leadScope)->count();
        $leadsBooked = Lead::where($leadScope)->where('status', 'booked')->count();
        $partnersCount = Partner::count();
        // Units (optional: requires ProjectUnit with status)
        $unitsTotal = ProjectUnit::count();
        $unitsBooked = ProjectUnit::whereRaw('LOWER(status) = ?', ['booked'])->count();
        $unitsAvailable = max(0, $unitsTotal - $unitsBooked);

        // --- Lead status pie ---
        // $statusRows = Lead::where('org_id', $orgId)->where($leadScope)
        $statusRows = Lead::where($leadScope)
            ->select('status', DB::raw('COUNT(*) as cnt'))
            ->groupBy('status')->get();

        $leadStatus = [
            'labels' => $statusRows->pluck('status')->map(fn($s) => ucwords(str_replace('_', ' ', $s)))->values(),
            'series' => $statusRows->pluck('cnt')->values(),
        ];

        // --- Lead trend (12 weeks) ---
        $start = Carbon::now()->startOfWeek()->subWeeks(11);
        // $trendRows = Lead::where('org_id', $orgId)->where($leadScope)
        $trendRows = Lead::where($leadScope)
            ->where('created_at', '>=', $start)
            ->select(DB::raw('YEARWEEK(created_at, 3) as yw'), DB::raw('COUNT(*) as cnt'))
            ->groupBy('yw')->orderBy('yw')->get()->keyBy('yw');

        $trendLabels = [];
        $trendSeries = [];
        for ($i = 0; $i < 12; $i++) {
            $wkStart = (clone $start)->addWeeks($i);
            $yw = (int)$wkStart->format('oW'); // ISO year-week
            $trendLabels[] = $wkStart->format('d M');
            $trendSeries[] = (int)($trendRows[$yw]->cnt ?? 0);
        }
        $leadTrend = ['labels' => $trendLabels, 'series' => $trendSeries];

        // --- Project inventory stacked (available vs booked) ---
        // $projectInv = Project::where('org_id', $orgId)->withCount([
        $projectInv = Project::withCount([
            'units as total_units',
            'units as booked_units' => function ($q) {
                $q->whereRaw('LOWER(status) = ?', ['booked']);
            }
        ])->orderBy('name')->get();

        $inv = [
            'labels'   => $projectInv->pluck('name'),
            'available' => $projectInv->map(fn($p) => max(0, ($p->total_units - $p->booked_units))),
            'booked'   => $projectInv->pluck('booked_units'),
        ];

        // --- Top partners (by leads assigned) ---
        $topPartners = DB::table('partners as p')
            ->leftJoin('users as u', function ($j) {
                $j->on('u.id', '=', 'p.user_id');
            })
            ->leftJoin('leads as l', function ($j) use ($user) {
                $j->on('l.assigned_to', '=', 'u.id');
                if (!$user->hasRole('super admin')) {
                    $j->where(function ($qq) use ($user) {
                        $qq->where('l.assigned_to', $user->id)
                            ->orWhere('l.created_by', $user->id);
                    });
                }
            })
            // ->where('p.org_id', $orgId)
            ->groupBy('p.id', 'p.name')
            ->select('p.name', DB::raw('COUNT(l.id) as leads'))
            ->orderByDesc('leads')->limit(5)->get();

        // --- Upcoming follow-ups (next 7 days) ---
        // $followUps = Lead::where('org_id', $orgId)->where($leadScope)
        $followUps = Lead::where($leadScope)
            ->whereNotNull('follow_up_at')
            ->whereBetween('follow_up_at', [now(), now()->addDays(7)])
            ->with('assignee:id,name')
            ->orderBy('follow_up_at')->limit(8)->get();

        // --- Recent leads ---
        // $recentLeads = Lead::where('org_id', $orgId)->where($leadScope)
        $recentLeads = Lead::where($leadScope)
            ->latest()->with(['assignee:id,name', 'project:id,name'])
            ->limit(10)->get();

        // Conversion (last 30 days)
        $since = now()->subDays(30);
        // $recentTotal  = Lead::where('org_id', $orgId)->where($leadScope)->where('created_at', '>=', $since)->count();
        $recentTotal  = Lead::where($leadScope)->where('created_at', '>=', $since)->count();
        $recentBooked = Lead::where($leadScope)->where('status', 'booked')->where('created_at', '>=', $since)->count();
        // $recentBooked = Lead::where('org_id', $orgId)->where($leadScope)->where('status', 'booked')->where('created_at', '>=', $since)->count();
        $conversion30 = $recentTotal ? round(($recentBooked / $recentTotal) * 100, 1) : 0;

        // 👇 ADD
        $greeting = $this->greeting(Auth::user()->name);
        $palette  = ["#2563eb", "#22c55e", "#E1A123", "#ef4444", "#7c3aed", "#0ea5e9", "#14b8a6", "#a78bfa", "#f97316", "#06b6d4"];

        return view('org.dashboard.index', compact(
            'developersCount',
            'projectsCount',
            'leadsTotal',
            'leadsBooked',
            'partnersCount',
            'unitsTotal',
            'unitsBooked',
            'unitsAvailable',
            'leadStatus',
            'leadTrend',
            'inv',
            'topPartners',
            'followUps',
            'recentLeads',
            'conversion30',
            'greeting',
            'palette'   // 👈 pass
        ));
    }



    // public function org_dashboard()
    // {

    //     $user  = Auth::user();
    //     $orgId = $user->org_id;

    //     // Visibility scope: Admin sees all; others see only theirs
    //     $leadScope = function ($q) use ($user) {
    //         if (!$user->hasRole('org admin')) {
    //             $q->where(function ($qq) use ($user) {
    //                 $qq->where('assigned_to', $user->id)
    //                     ->orWhere('created_by', $user->id);
    //             });
    //         }
    //     };

    //     // --- KPIs ---
    //     $developersCount = Developer::where('org_id', $orgId)->count();
    //     $projectsCount   = Project::where('org_id', $orgId)->count();

    //     $leadsTotal = Lead::where('org_id', $orgId)
    //         ->where($leadScope)->count();

    //     $leadsBooked = Lead::where('org_id', $orgId)
    //         ->where($leadScope)->where('status', 'booked')->count();

    //     $partnersCount = Partner::where('org_id', $orgId)->count();

    //     // Units (optional: requires ProjectUnit with status)
    //     $unitsTotal = ProjectUnit::whereHas('project', fn($q) => $q->where('org_id', $orgId))->count();
    //     $unitsBooked = ProjectUnit::whereHas('project', fn($q) => $q->where('org_id', $orgId))
    //         ->whereRaw('LOWER(status) = ?', ['booked'])->count();
    //     $unitsAvailable = max(0, $unitsTotal - $unitsBooked);

    //     // --- Lead status pie ---
    //     $statusRows = Lead::where('org_id', $orgId)->where($leadScope)
    //         ->select('status', DB::raw('COUNT(*) as cnt'))
    //         ->groupBy('status')->get();

    //     $leadStatus = [
    //         'labels' => $statusRows->pluck('status')->map(fn($s) => ucwords(str_replace('_', ' ', $s)))->values(),
    //         'series' => $statusRows->pluck('cnt')->values(),
    //     ];

    //     // --- Lead trend (12 weeks) ---
    //     $start = Carbon::now()->startOfWeek()->subWeeks(11);
    //     $trendRows = Lead::where('org_id', $orgId)->where($leadScope)
    //         ->where('created_at', '>=', $start)
    //         ->select(DB::raw('YEARWEEK(created_at, 3) as yw'), DB::raw('COUNT(*) as cnt'))
    //         ->groupBy('yw')->orderBy('yw')->get()->keyBy('yw');

    //     $trendLabels = [];
    //     $trendSeries = [];
    //     for ($i = 0; $i < 12; $i++) {
    //         $wkStart = (clone $start)->addWeeks($i);
    //         $yw = (int)$wkStart->format('oW'); // ISO year-week
    //         $trendLabels[] = $wkStart->format('d M');
    //         $trendSeries[] = (int)($trendRows[$yw]->cnt ?? 0);
    //     }
    //     $leadTrend = ['labels' => $trendLabels, 'series' => $trendSeries];

    //     // --- Project inventory stacked (available vs booked) ---
    //     $projectInv = Project::where('org_id', $orgId)->withCount([
    //         'units as total_units',
    //         'units as booked_units' => function ($q) {
    //             $q->whereRaw('LOWER(status) = ?', ['booked']);
    //         }
    //     ])->orderBy('name')->get();

    //     $inv = [
    //         'labels'   => $projectInv->pluck('name'),
    //         'available' => $projectInv->map(fn($p) => max(0, ($p->total_units - $p->booked_units))),
    //         'booked'   => $projectInv->pluck('booked_units'),
    //     ];

    //     // --- Top partners (by leads assigned) ---
    //     $topPartners = DB::table('partners as p')
    //         ->leftJoin('users as u', function ($j) {
    //             $j->on('u.id', '=', 'p.user_id');
    //         })
    //         ->leftJoin('leads as l', function ($j) use ($orgId, $user) {
    //             $j->on('l.assigned_to', '=', 'u.id')->where('l.org_id', '=', $orgId);
    //             if (!$user->hasRole('org admin')) {
    //                 $j->where(function ($qq) use ($user) {
    //                     $qq->where('l.assigned_to', $user->id)
    //                         ->orWhere('l.created_by', $user->id);
    //                 });
    //             }
    //         })
    //         ->where('p.org_id', $orgId)
    //         ->groupBy('p.id', 'p.name')
    //         ->select('p.name', DB::raw('COUNT(l.id) as leads'))
    //         ->orderByDesc('leads')->limit(5)->get();

    //     // --- Upcoming follow-ups (next 7 days) ---
    //     $followUps = Lead::where('org_id', $orgId)->where($leadScope)
    //         ->whereNotNull('follow_up_at')
    //         ->whereBetween('follow_up_at', [now(), now()->addDays(7)])
    //         ->with('assignee:id,name')
    //         ->orderBy('follow_up_at')->limit(8)->get();

    //     // --- Recent leads ---
    //     $recentLeads = Lead::where('org_id', $orgId)->where($leadScope)
    //         ->latest()->with(['assignee:id,name', 'project:id,name'])
    //         ->limit(10)->get();

    //     // Conversion (last 30 days)
    //     $since = now()->subDays(30);
    //     $recentTotal  = Lead::where('org_id', $orgId)->where($leadScope)->where('created_at', '>=', $since)->count();
    //     $recentBooked = Lead::where('org_id', $orgId)->where($leadScope)->where('status', 'booked')->where('created_at', '>=', $since)->count();
    //     $conversion30 = $recentTotal ? round(($recentBooked / $recentTotal) * 100, 1) : 0;

    //     // 👇 ADD
    //     $greeting = $this->greeting(Auth::user()->name);
    //     $palette  = ["#2563eb", "#22c55e", "#E1A123", "#ef4444", "#7c3aed", "#0ea5e9", "#14b8a6", "#a78bfa", "#f97316", "#06b6d4"];

    //     return view('org.dashboard.index', compact(
    //         'developersCount',
    //         'projectsCount',
    //         'leadsTotal',
    //         'leadsBooked',
    //         'partnersCount',
    //         'unitsTotal',
    //         'unitsBooked',
    //         'unitsAvailable',
    //         'leadStatus',
    //         'leadTrend',
    //         'inv',
    //         'topPartners',
    //         'followUps',
    //         'recentLeads',
    //         'conversion30',
    //         'greeting',
    //         'palette'   // 👈 pass
    //     ));
    // }



    private function greeting(?string $name): string
    {
        $h = (int) now()->format('H');
        $hello = $h < 12 ? 'Good morning' : ($h < 17 ? 'Good afternoon' : 'Good evening');
        return trim($hello . ' ' . ($name ?? ''));
    }
    public function employee_dashboard()
    {

        $user = Auth::user();
        return view('employee.dashboard.index');
    }
    public function receptionist_dashboard()
    {
        $user = Auth::user();
        // $orgId = $user->org_id;

        // KPI counters
        $todayVisits = \DB::table('visits')
            // ->where('org_id', $orgId)
            ->whereDate('created_at', today())
            ->count();

        $newRequests = \DB::table('visits')
            // ->where('org_id', $orgId)
            ->where('status', 'pending')
            ->count();

        $approvedVisits = \DB::table('visits')
            // ->where('org_id', $orgId)
            ->where('status', 'approved')
            ->count();

        // Recent activity (last 5 visits joined with visitor info)
        $recent = \DB::table('visits as v')
            ->join('visitors as vs', 'vs.id', '=', 'v.visitor_id')
            ->select('vs.name as visitor_name', 'v.status', 'v.purpose', 'v.created_at')
            // ->where('v.org_id', $orgId)
            ->orderByDesc('v.created_at')
            ->limit(3)
            ->get()
            ->map(fn($r) => [
                'text' => "{$r->visitor_name} visit for {$r->purpose} ({$r->status})",

                'time' => $r->created_at
                    ? \Carbon\Carbon::parse($r->created_at)->format('d M g:i A')
                    : '',
            ]);

        // Pending approvals (last 5)
        $approvals = \DB::table('visits as v')
            ->join('visitors as vs', 'vs.id', '=', 'v.visitor_id')
            ->select('vs.name as visitor_name', 'v.purpose', 'v.scheduled_start')
            // ->where('v.org_id', $orgId)
            ->where('v.status', 'pending')
            ->orderBy('v.created_at', 'desc')
            ->limit(3)
            ->get()
            ->map(fn($a) => [
                'text' => "{$a->visitor_name} • {$a->purpose}",
                'time' => $a->scheduled_start
                    ? \Carbon\Carbon::parse($a->scheduled_start)->format('d M g:i A')
                    : '',
            ]);

        return view('receptionist.dashboard.index', compact(
            'todayVisits',
            'newRequests',
            'approvedVisits',
            'recent',
            'approvals'
        ));
    }



    public function ui_management(Request $request)
    {
        $get_data = AdminUiSetting::first();
        return view('admin.ui_management', compact('get_data'));
    }

    public function management_store(Request $request)
    {

        $logoPath = null;
        $logoutImgPath = null;
        $uiSettings = AdminUiSetting::first();

        if ($uiSettings) {
            if ($request->hasFile('admin_logo')) {
                $logo = $request->file('admin_logo')->store('logos', 'public');
            } else {
                $logo = $uiSettings->admin_logo;
            }

            if ($request->hasFile('admin_logout_i')) {
                $logout_i = $request->file('admin_logout_i')->store('icons', 'public');
            } else {
                $logout_i = $uiSettings->admin_logout_i;
            }


            $uiSettings->admin_logo = $logo;

            $uiSettings->admin_logout_i = $logout_i;
            $uiSettings->admin_h_color = $request->admin_h_color;
            $uiSettings->login_bg_color = $request->login_bg_color;
            $uiSettings->sidebar_border_color = $request->sidebar_border_color;
            $uiSettings->admin_b_color = $request->admin_b_color;
            $uiSettings->admin_bt_color = $request->admin_bt_color;
            $uiSettings->admin_t_color = $request->admin_t_color;
            $uiSettings->admin_f_color = $request->admin_f_color;
            $uiSettings->admin_c_color = $request->admin_c_color;
            $uiSettings->admin_l_c_h_color = $request->admin_l_c_h_color;
            $uiSettings->admin_l_bt_color = $request->admin_l_bt_color;
            $uiSettings->admin_logo_size = $request->admin_logo_size;
            $uiSettings->admin_user_sidebar_color = $request->admin_user_sidebar_color;
            $uiSettings->admin_header_text_color = $request->admin_header_text_color;
            $uiSettings->admin_button_text_color = $request->admin_button_text_color;
            $uiSettings->admin_card_background_color = $request->admin_card_background_color;
            $uiSettings->admin_card_top_t_color = $request->admin_card_top_t_color;
            $uiSettings->admin_modal_header_background_color = $request->admin_modal_header_background_color;
            $uiSettings->admin_table_header_bg_color = $request->admin_table_header_bg_color;
            $uiSettings->admin_table_header_text_color = $request->admin_table_header_text_color;
            $uiSettings->admin_table_row_odd_bg_color = $request->admin_table_row_odd_bg_color;
            $uiSettings->admin_table_row_even_bg_color = $request->admin_table_row_even_bg_color;
            $uiSettings->admin_table_row_hover_bg_color = $request->admin_table_row_hover_bg_color;

            $uiSettings->save();
            return redirect()->back()->with('success', 'UI settings Updated successfully!');
        } else {
            $logo = null;
            if ($request->hasFile('admin_logo')) {
                $logo = $request->file('admin_logo')->store('logos', 'public');
            }

            $logout_i = null;
            if ($request->hasFile('admin_logout_i')) {
                $logout_i = $request->file('admin_logout_i')->store('icons', 'public');
            }




            $data = new AdminUiSetting();

            $data->admin_logo = $logo;
            $data->admin_logout_i = $logout_i;
            $data->admin_h_color = $request->admin_h_color;
            $uiSettings->login_bg_color = $request->login_bg_color;
            $uiSettings->sidebar_border_color = $request->sidebar_border_color;
            $data->admin_b_color = $request->admin_b_color;
            $data->admin_bt_color = $request->admin_bt_color;
            $data->admin_t_color = $request->admin_t_color;
            $data->admin_f_color = $request->admin_f_color;
            $data->admin_c_color = $request->admin_c_color;
            $data->admin_l_c_h_color = $request->admin_l_c_h_color;
            $data->admin_l_bt_color = $request->admin_l_bt_color;
            $data->admin_logo_size = $request->admin_logo_size;
            $data->admin_user_sidebar_color = $request->admin_user_sidebar_color;
            $data->admin_header_text_color = $request->admin_header_text_color;
            $data->admin_button_text_color = $request->admin_button_text_color;
            $data->admin_card_background_color = $request->admin_card_background_color;
            $data->admin_card_top_t_color = $request->admin_card_top_t_color;
            $data->admin_modal_header_background_color = $request->admin_modal_header_background_color;
            $data->admin_table_header_bg_color = $request->admin_table_header_bg_color;
            $data->admin_table_header_text_color = $request->admin_table_header_text_color;
            $data->admin_table_row_odd_bg_color = $request->admin_table_row_odd_bg_color;
            $data->admin_table_row_even_bg_color = $request->admin_table_row_even_bg_color;
            $data->admin_table_row_hover_bg_color = $request->admin_table_row_hover_bg_color;


            $data->save();
            return redirect()->back()->with('success', 'UI settings Seved successfully!');
        }
    }

    public function rm_dashboard()
    {
        
    }



}
