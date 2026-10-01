<?php

namespace App\Http\Controllers;

use App\Http\Requests\LeadRequest;
use App\Http\Requests\PartnerRequest;
use App\Mail\OtpMail;
use App\Models\ContactInquiry;
use App\Models\Developer;
use App\Models\Lead;
use App\Models\Partner;
use App\Models\Project;
use App\Models\ProjectMaterial;
use App\Models\ProjectUnit;
use App\Models\Unit;
use App\Models\User;
use App\Models\LeadOffer;
use App\Models\LeadTask;
use App\Models\ProjectAmenities;
use App\Models\ProjectMedia;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    public function login(Request $request)
    {

        $error = 0;
        $msg = 'success';
        $data = [];
        try {

            $user = User::where('email', $request->email)->first();
            if ($user) {
                if (Hash::check($request->password, $user->password)) {
                    $token = $user->generateToken();
                    $roleName = $user->getRoleNames()->first();
                    $data = [
                        'user' => $user,
                        'role' => $roleName,
                        'token' => $token,
                    ];
                } else {
                    $error = 1;
                    $msg = 'Email or password is wrong.';
                }
            } else {
                $error = 1;
                $msg = 'User does not exist.';
            }
        } catch (\Exception $e) {
            $error = 1;
            $msg = 'Error! '.$e->getMessage();
        }

        return response()->json([
            'error' => $error,
            'message' => $msg,
            'data' => $data,
        ]);
    }

    public function forgetPassword(Request $request)
    {
        $error = 0;
        $msg = 'success';
        $data = [];

        DB::beginTransaction();

        try {
            // Validate email
            $validatedData = $request->validate([
                'email' => 'required|email',
            ]);

            // Check if user exists
            $user = User::where('email', $request->email)->first();
            if (! $user) {
                throw new \Exception('Email not found');
            }

            // Generate OTP
            $otp = rand(100000, 999999);
            $user->otp = $otp;
            $user->otp_expires_at = now()->addMinutes(10);
            $user->save();

            // Send OTP email immediately
            $userName = $user->name ?? null;
            Mail::to($user->email)->send(new OtpMail($otp, $userName));

            DB::commit();

            $msg = 'OTP sent to your email';
            $data = ['email' => $user->email];
        } catch (\Exception $e) {
            DB::rollBack();
            $error = 1;
            $msg = 'Error! '.$e->getMessage();
        }

        return response()->json([
            'error' => $error,
            'message' => $msg,
            'data' => $data,
        ]);
    }

    public function resend_otp(Request $request)
    {
        $error = 0;
        $msg = 'success';
        $data = [];

        DB::beginTransaction();

        try {
            $user = User::where('email', $request->email)->first();
            if (! $user) {
                return response()->json([
                    'error' => 1,
                    'message' => 'Email not found. Please register first.',
                    'data' => [],
                ], 400);
            }

            $otp = rand(100000, 999999);
            $user->otp = $otp;
            $user->otp_expires_at = Carbon::now()->addMinutes(10);
            $user->save();
            $userName = $user->name ?? null;
            Mail::to($user->email)->send(new OtpMail($otp, $userName));

            DB::commit();

            $data = [
                'user' => $user,
            ];

            $msg = ' OTP sent to your email.';

        } catch (\Exception $e) {
            DB::rollBack();
            $error = 1;
            $msg = 'Error! '.$e->getMessage();
        }

        return response()->json([
            'error' => $error,
            'message' => $msg,
            'data' => $data,
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $error = 0;
        $msg = 'success';
        $data = [];

        DB::beginTransaction();

        try {
            $validatedData = $request->validate([
                'email' => 'required|email',
                'otp' => 'required',
            ]);

            $user = User::where('email', $request->email)
                ->where('otp', $request->otp)
                ->first();

            if (! $user) {
                throw new \Exception('Invalid OTP');
            }

            if (Carbon::now()->greaterThan($user->otp_expires_at)) {
                throw new \Exception('OTP expired');
            }
            $user->otp = null;
            $user->otp_expires_at = null;
            $user->email_verified_at = Carbon::now();
            $user->save();

            DB::commit();

            $msg = 'OTP verified successfully';
            $data = ['email' => $user->email];
        } catch (\Exception $e) {
            DB::rollBack();
            $error = 1;
            $msg = 'Error! '.$e->getMessage();
        }

        return response()->json([
            'error' => $error,
            'message' => $msg,
            'data' => $data,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $error = 0;
        $msg = 'success';
        $data = [];
        DB::beginTransaction();
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|confirmed|min:6',
            ]);

            $user = User::where('email', $request->email)->first();

            if (! $user) {
                throw new \Exception('User not found');
            }

            $user->password = Hash::make($request->password);
            $user->save();

            DB::commit();
            $msg = 'Password changed successfully';
            $data = ['email' => $user->email];
        } catch (\Exception $e) {
            DB::rollBack();
            $error = 1;
            $msg = 'Error! '.$e->getMessage();
        }

        return response()->json([
            'error' => $error,
            'message' => $msg,
            'data' => $data,
        ]);
    }

    public function orgDashboardApi()
    {
        $error = 0;
        $msg = 'success';
        $data = [];

        try {
            $user = Auth::user();
            // Visibility scope: Admin sees all; others see only theirs
            $leadScope = function ($q) use ($user) {
                if (! $user->hasRole('super admin')) {
                    $q->where(function ($qq) use ($user) {
                        $qq->where('assigned_to', $user->id)
                            ->orWhere('created_by', $user->id);
                    });
                }
            };

            // --- KPIs ---
            $developersCount = Developer::count();
            $projectsCount = Project::count();
            $leadsTotal = Lead::where($leadScope)->count();
            $leadsBooked = Lead::where($leadScope)->where('status', 'booked')->count();
            $partnersCount = Partner::count();
            $unitsTotal = ProjectUnit::count();
            $unitsBooked = ProjectUnit::whereRaw('LOWER(status) = ?', ['booked'])->count();
            $unitsAvailable = max(0, $unitsTotal - $unitsBooked);

            // --- Lead status pie ---
            $statusRows = Lead::where($leadScope)
                ->select('status', DB::raw('COUNT(*) as cnt'))
                ->groupBy('status')->get();

            $leadStatus = [
                'labels' => $statusRows->pluck('status')
                    ->map(fn ($s) => ucwords(str_replace('_', ' ', $s)))
                    ->values(),
                'series' => $statusRows->pluck('cnt')->values(),
            ];

            // --- Lead trend (12 weeks) ---
            $start = Carbon::now()->startOfWeek()->subWeeks(11);
            $trendRows = Lead::where($leadScope)
                ->where('created_at', '>=', $start)
                ->select(DB::raw('YEARWEEK(created_at, 3) as yw'), DB::raw('COUNT(*) as cnt'))
                ->groupBy('yw')->orderBy('yw')->get()->keyBy('yw');

            $trendLabels = [];
            $trendSeries = [];
            for ($i = 0; $i < 12; $i++) {
                $wkStart = (clone $start)->addWeeks($i);
                $yw = (int) $wkStart->format('oW');
                $trendLabels[] = $wkStart->format('d M');
                $trendSeries[] = (int) ($trendRows[$yw]->cnt ?? 0);
            }
            $leadTrend = ['labels' => $trendLabels, 'series' => $trendSeries];

            // --- Project inventory stacked ---
            $projectInv = Project::withCount([
                'units as total_units',
                'units as booked_units' => function ($q) {
                    $q->whereRaw('LOWER(status) = ?', ['booked']);
                },
            ])->orderBy('name')->get();

            $projectInventory = [
                'labels' => $projectInv->pluck('name'),
                'available' => $projectInv->map(fn ($p) => max(0, ($p->total_units - $p->booked_units))),
                'booked' => $projectInv->pluck('booked_units'),
            ];

            // --- Top partners ---
            $topPartners = DB::table('partners as p')
                ->leftJoin('users as u', 'u.id', '=', 'p.user_id')
                ->leftJoin('leads as l', function ($j) use ($user) {
                    $j->on('l.assigned_to', '=', 'u.id');
                    if (! $user->hasRole('super admin')) {
                        $j->where(function ($qq) use ($user) {
                            $qq->where('l.assigned_to', $user->id)
                                ->orWhere('l.created_by', $user->id);
                        });
                    }
                })
                ->groupBy('p.id', 'p.name')
                ->select('p.name', DB::raw('COUNT(l.id) as leads'))
                ->orderByDesc('leads')->limit(5)->get();

            // --- Upcoming follow-ups (next 7 days) ---
            $followUps = Lead::where($leadScope)
                ->whereNotNull('follow_up_at')
                ->whereBetween('follow_up_at', [now(), now()->addDays(7)])
                ->with('assignee:id,name')
                ->orderBy('follow_up_at')->limit(8)->get();

            // --- Recent leads ---
            $recentLeads = Lead::where($leadScope)
                ->latest()->with(['assignee:id,name', 'project:id,name'])
                ->limit(10)->get();

            // Conversion (last 30 days)
            $since = now()->subDays(30);
            $recentTotal = Lead::where($leadScope)->where('created_at', '>=', $since)->count();
            $recentBooked = Lead::where($leadScope)->where('status', 'booked')->where('created_at', '>=', $since)->count();
            $conversion30 = $recentTotal ? round(($recentBooked / $recentTotal) * 100, 1) : 0;

            // Optional: greeting & palette
            $greeting = $this->greeting($user->name);
            $palette = ['#2563eb', '#22c55e', '#E1A123', '#ef4444', '#7c3aed', '#0ea5e9', '#14b8a6', '#a78bfa', '#f97316', '#06b6d4'];

            
            $coverSub = DB::table('project_media')
                ->select('project_id', DB::raw('MAX(url) as cover_image'))
                ->where('collection', 'cover') // keep if you only want cover images
                ->groupBy('project_id');

            $bedroomSub = DB::table('project_typical_units')
                ->select(
                    'project_id',
                    DB::raw('GROUP_CONCAT(DISTINCT bedrooms ORDER BY bedrooms) as bedrooms')
                )
                ->groupBy('project_id');

            $latestProjects = Project::query()
                ->leftJoinSub($coverSub, 'cv', function ($join) {
                    $join->on('cv.project_id', '=', 'projects.id');
                })
                ->leftJoinSub($bedroomSub, 'bd', function ($join) {
                    $join->on('bd.project_id', '=', 'projects.id');
                })
                ->select('projects.*', 'cv.cover_image', 'bd.bedrooms')
                ->orderByDesc('projects.created_at')
                ->limit(5)
                ->get();

            $launchedProjects = Project::query()
                ->leftJoinSub($coverSub, 'cv', function ($join) {
                    $join->on('cv.project_id', '=', 'projects.id');
                })
                ->leftJoinSub($bedroomSub, 'bd', function ($join) {
                    $join->on('bd.project_id', '=', 'projects.id');
                })
                ->select('projects.*', 'cv.cover_image', 'bd.bedrooms')
                ->orderByDesc('projects.created_at')
                ->where('status', 'launched')
                ->limit(5)
                ->get();
                // dd($latestProjects);
            // Prepare structured response
            $data = [
                'kpis' => [
                    'developers_count' => $developersCount,
                    'projects_count' => $projectsCount,
                    'leads_total' => $leadsTotal,
                    'leads_booked' => $leadsBooked,
                    'partners_count' => $partnersCount,
                    'units_total' => $unitsTotal,
                    'units_booked' => $unitsBooked,
                    'units_available' => $unitsAvailable,
                    'conversion_30d' => $conversion30,
                ],
                'lead_status' => $leadStatus,
                'lead_trend' => $leadTrend,
                'project_inventory' => $projectInventory,
                'top_partners' => $topPartners,
                'upcoming_followups' => $followUps,
                'recent_leads' => $recentLeads,
                'greeting' => $greeting,
                'palette' => $palette,
                'latest_projects' => $latestProjects,
                'launchedProjects'=> $launchedProjects

            ];

        } catch (\Exception $e) {
            $error = 1;
            $msg = 'Error! '.$e->getMessage();
        }

        return response()->json([
            'error' => $error,
            'message' => $msg,
            'data' => $data,
        ]);
    }

    private function greeting(?string $name): string
    {
        $h = (int) now()->format('H');
        $hello = $h < 12 ? 'Good morning' : ($h < 17 ? 'Good afternoon' : 'Good evening');

        return trim($hello.' '.($name ?? ''));
    }

    public function channelPartner_register(PartnerRequest $request)
    {
        $error = 0;
        $msg = 'success';
        $data = [];
        DB::beginTransaction();
        try {
            $validatedData = $request->validated();
            $allowedTypes = ['channel_partner', 'property consultant', 'freelancer'];
            if (! in_array($validatedData['type'], $allowedTypes)) {
                return response()->json([
                    'error' => 1,
                    'message' => 'Invalid type value. Allowed types are: channel_partner, property consultant, freelancer',
                    'data' => [],
                ], 422);
            }
        $validatedData['rm_id'] = $request->rm_id ?? null;

            if (! is_dir(public_path('media/partners'))) {
                @mkdir(public_path('media/partners'), 0755, true);
            }
            foreach (['pan_file' => 'pan_path', 'gst_file' => 'gst_path', 'rera_file' => 'rera_path'] as $input => $field) {
                if (! empty($request->$input)) {
                    $file = $request->$input;
                    $name = md5(time().$input).'.'.$file->getClientOriginalExtension();
                    $file->move(public_path('media/partners'), $name);
                    $validatedData[$field] = 'media/partners/'.$name;
                }
            }
            $validatedData['approved'] = (bool) ($validatedData['approved'] ?? true);
            $validatedData['active'] = (bool) ($validatedData['active'] ?? true);

            $emailreq = trim((string) ($validatedData['email'] ?? ''));
            $phonereq = $this->normalizePhone($validatedData['phone'] ?? null);
            $validateemail = Partner::where('email', $emailreq)->first();
            $validatephone = Partner::where('phone', $phonereq)->first();
            if ($validateemail || $validatephone) {
                $error = 1;
                if ($validateemail) {
                    return response()->json([
                        'error' => $error,
                        'message' => 'This email already exsit!',
                        'data' => $data,
                    ]);
                } else {
                    return response()->json([
                        'error' => $error,
                        'message' => 'This phone already exsit!',
                        'data' => $data,
                    ]);
                }
            } else {
                $partner = Partner::create($validatedData);
            }

            $email = trim((string) ($validatedData['email'] ?? ''));
            $phone = $this->normalizePhone($validatedData['phone'] ?? null);
            $validateemail_user = User::where('email', $email)->first();
            $validatephone_user = User::where('phone', $phone)->first();

            if ($validateemail_user || $validatephone_user) {
                $error = 1;

                return response()->json([
                    'error' => $error,
                    'message' => 'This User already exsit!',
                    'data' => $data,
                ]);
            } else {
                $user = null;
                if ($email) {

                    $user = User::where('email', $email)->first();
                }
                if (! $user && $phone) {
                    $user = User::where('phone', $phone)->first();
                }
                if (! $user) {
                    $user = User::create([
                        'name' => $validatedData['name'],
                        'email' => $email ?: null,
                        'phone' => $phone ?: null,
                        'password' => Hash::make($validatedData['password']),
                    ]);
                } else {
                    $user->name = $user->name !== $validatedData['name'] ? $validatedData['name'] : $user->name;
                    $user->email = $user->email ?: $email;
                    $user->phone = $user->phone ?: $phone;
                    $user->save();
                }
            }

            // $user->syncRoles([$this->roleFor($partner->type)]);
            $roleName = $this->roleFor($partner->type);
            $role = \Spatie\Permission\Models\Role::where('name', $roleName)
                ->where('guard_name', 'web')
                ->first();

            if ($role) {
                $user->syncRoles([$role->name]);
            }
            $partner->update(['user_id' => $user->id]);

            $msg = 'Partner created successfully';
            $data = [
                'partner' => $partner,
                'user' => $user,
            ];
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            $error = 1;
            $msg = 'Error! '.$e->getMessage();
        }

        return response()->json([
            'error' => $error,
            'message' => $msg,
            'data' => $data,
        ]);
    }

    private function normalizePhone(?string $raw): ?string
    {
        if (! $raw) {
            return null;
        }
        $raw = trim($raw);

        return preg_replace('/(?!^\+)\D+/', '', $raw);
    }

    private function roleFor(string $type): string
    {
        return match ($type) {
            'channel_partner' => 'channel partner',
            'broker' => 'broker',
            'agent' => 'agent',
            default => 'channel partner',
        };
    }

    public function getUserProfile()
    {
        $error = 0;
        $msg = 'success';
        $data = [];

        try {

            $authUser = Auth::user();
            $userId = $authUser->id;

            $partner = Partner::where('user_id', $userId)->first();

            $data = [
                'user' => $authUser,
                'partner' => $partner,
            ];

            $msg = 'Profile fetched successfully';

        } catch (\Exception $e) {

            $error = 1;
            $msg = 'Error! '.$e->getMessage();
        }

        return response()->json([
            'error' => $error,
            'message' => $msg,
            'data' => $data,
        ]);
    }

    public function updatePartnerandUserProfile(Request $request)
    {
        $error = 0;
        $msg = 'success';
        $data = [];

        try {
            $authUser = Auth::user();
            $userId = $authUser->id;

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:20',
                'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
            ]);

            $authUser->name = $validated['name'];
            $authUser->email = $validated['email'] ?? $authUser->email;
            $authUser->phone = $validated['phone'] ?? $authUser->phone;
            if ($request->hasFile('image')) {
                $file = $request->image;
                $filename = md5(time()).'.'.$file->getClientOriginalExtension();
                $file->move(public_path('media/partners/profile'), $filename);

                $authUser->image = 'media/partners/profile/'.$filename;
            }
            $authUser->save();

            $partner = Partner::where('user_id', $userId)->first();

            if ($partner) {

                $partner->name = $validated['name'];
                $partner->email = $validated['email'] ?? $partner->email;
                $partner->phone = $validated['phone'] ?? $partner->phone;

                $partner->save();
            }

            $msg = 'Profile updated successfully';
            $data = [
                'user' => $authUser,
                'partner' => $partner,
            ];
            // dd($data);

        } catch (\Exception $e) {
            $error = 1;
            $msg = 'Error! '.$e->getMessage();
        }

        return response()->json([
            'error' => $error,
            'message' => $msg,
            'data' => $data,
        ]);
    }

    public function partner_index(Request $request)
    {
        $error = 0;
        $msg = 'success';
        $data = [];

        try {
            $user = Auth::user();
            $query = Partner::when($user->hasRole('channel partner'), function ($q) use ($user) {
                $q->where('parent_id', $user->id);
            })
                ->when($request->filled('type'), fn ($q) => $q->where('type', $request->type))
                ->with('parent:id,name')
                ->orderBy('type')
                ->orderBy('name');
            $partners = $query->get([
                'id',
                'name',
                'email',
                'phone',
                'type',
                'parent_id',
                'approved',
                'active',
            ]);

            $msg = 'Partners fetched successfully';
            $data = ['partners' => $partners];
        } catch (\Exception $e) {
            $error = 1;
            $msg = 'Error! '.$e->getMessage();
        }

        return response()->json([
            'error' => $error,
            'message' => $msg,
            'data' => $data,
        ]);
    }

    // public function project_index(Request $request)
    // {
    //     $error = 0;
    //     $msg = 'success';
    //     $data = [];

    //     try {
    //         $projects = Project::query()
    //             ->when($request->filled('search'), function ($q) use ($request) {
    //                 $search = $request->search;
    //                 $q->where(function ($query) use ($search) {
    //                     $query->where('name', 'like', "%{$search}%")
    //                         ->orWhere('location', 'like', "%{$search}%")
    //                         ->orWhere('min_price', 'like', "%{$search}%")
    //                         ->orWhere('project_type', 'like', "%{$search}%")
    //                         ->orWhere('city', 'like', "%{$search}%")
    //                         ->orWhere('status', 'like', "%{$search}%");
    //                 });
    //             })
    //             ->with([
    //                 'developer:id,name',
    //                 'category:id,category_name',
    //                 'subCategory:id,sub_category_name',
    //                 'creator:id,name',
    //             ])
    //             ->orderByDesc('id')
    //             ->get();

    //         $msg = 'Projects fetched successfully';
    //         $data = $projects;

    //     } catch (\Exception $e) {
    //         $error = 1;
    //         $msg = 'Error! '.$e->getMessage();
    //     }

    //     return response()->json([
    //         'error' => $error,
    //         'message' => $msg,
    //         'data' => $data,
    //     ]);
    // }


    public function project_index(Request $request)
    {
        try {
            $projects = Project::query()
                ->leftJoin('project_media as pm', function ($join) {
                    $join->on('pm.project_id', '=', 'projects.id')
                        ->where('pm.collection', 'cover');
                })
                ->when($request->filled('search'), function ($q) use ($request) {
                    $search = $request->search;
                    $q->where(function ($query) use ($search) {
                        $query->where('projects.name', 'like', "%{$search}%")
                            ->orWhere('projects.location', 'like', "%{$search}%")
                            ->orWhere('projects.min_price', 'like', "%{$search}%")
                            ->orWhere('projects.project_type', 'like', "%{$search}%")
                            ->orWhere('projects.city', 'like', "%{$search}%")
                            ->orWhere('projects.status', 'like', "%{$search}%");
                    });
                })->when($request->filled('type'), function ($q) use ($request) {
                    $q->where('project_type', $request->type);
                })  
                ->with([
                    'developer:id,name',
                    'category:id,category_name',
                    'subCategory:id,sub_category_name',
                    'creator:id,name',
                ])
                ->select(
                    'projects.*',
                    'pm.url as cover_image'
                )
                ->orderByDesc('projects.id')
                ->paginate(10);

            return response()->json([
                'error' => 0,
                'message' => 'Projects fetched successfully',
                'data' => $projects,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 1,
                'message' => $e->getMessage(),
                'data' => [],
            ]);
        }
    }


 public function developer_index(Request $request)
    {
        $error = 0;
        $msg = 'success';
        $data = [];

        try {
            $devloper = Developer::query()
                ->when($request->filled('search'), function ($q) use ($request) {
                    $search = $request->search;
                    $q->where(function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%") ;
                    });
                })
                ->orderByDesc('id')
                ->paginate(10);  

            $msg = 'Developer fetched successfully';
            $data = $devloper;

        } catch (\Exception $e) {
            $error = 1;
            $msg = 'Error! ' . $e->getMessage();
        }

        return response()->json([
            'error'   => $error,
            'message' => $msg,
            'data'    => $data,
        ]);
    }



    // public function developer_project_index(Request $request)
    // {
    //     try {

    //         if (!$request->filled('developer_id')) {
    //             return response()->json([
    //                 'error' => 1,
    //                 'message' => 'developer_id is required',
    //                 'data' => []
    //             ], 422);
    //         }

    //         $projects = Project::query()
    //             ->where('developer_id', $request->developer_id)
    //             ->leftJoin('project_media as pm', function ($join) {
    //                 $join->on('pm.project_id', '=', 'projects.id')
    //                     ->where('pm.collection', 'cover');
    //             }) 
    //             ->leftJoin('project_typical_units as ptu', 'ptu.project_id', '=', 'projects.id')
    //             ->when($request->filled('search'), function ($q) use ($request) {
    //                 $q->where('title', 'like', '%' . $request->search . '%');
    //             })
    //             ->select(
    //                 'projects.*',
    //                 DB::raw('MAX(pm.url) as cover_image'),
    //                 DB::raw('GROUP_CONCAT(DISTINCT ptu.bedrooms ORDER BY ptu.bedrooms) as bedrooms')
    //             )
    //             ->groupBy('projects.id')
    //             ->orderByDesc('id')
    //             ->paginate(10);

    //         return response()->json([
    //             'error' => 0,
    //             'message' => 'Projects fetched successfully',
    //             'data' => $projects
    //         ]);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'error' => 1,
    //             'message' => $e->getMessage(),
    //             'data' => []
    //         ]);
    //     }
    // }
    
public function developer_project_index(Request $request)
{
    try {
        if (!$request->filled('developer_id')) {
            return response()->json([
                'error' => 1,
                'message' => 'developer_id is required',
                'data' => []
            ], 422);
        }

        $coverSub = DB::table('project_media')
            ->select('project_id', DB::raw('MAX(url) as cover_image'))
            ->where('collection', 'cover')
            ->groupBy('project_id');

        $bedroomSub = DB::table('project_typical_units')
            ->select(
                'project_id',
                DB::raw('GROUP_CONCAT(DISTINCT bedrooms ORDER BY bedrooms) as bedrooms')
            )
            ->groupBy('project_id');

        $projects = Project::query()
            ->where('projects.developer_id', $request->developer_id)
            ->leftJoinSub($coverSub, 'cv', function ($join) {
                $join->on('cv.project_id', '=', 'projects.id');
            })
            ->leftJoinSub($bedroomSub, 'bd', function ($join) {
                $join->on('bd.project_id', '=', 'projects.id');
            })
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('projects.title', 'like', '%' . $request->search . '%'); // or projects.name
            })
            ->select('projects.*', 'cv.cover_image', 'bd.bedrooms')
            ->orderByDesc('projects.id')
            ->paginate(10);

        return response()->json([
            'error' => 0,
            'message' => 'Projects fetched successfully',
            'data' => $projects
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 1,
            'message' => $e->getMessage(),
            'data' => []
        ]);
    }
}








    public function project_detail_index(Request $request)
    {
        $error = 0;
        $msg = 'success';
        $data = [];

        try {

            $project = Project::find($request->id);
             if (!$project) {
                return response()->json([
                    'error' => 1,
                    'message' => 'Project not found',
                    'data' => []
                ]);
            }
            $projectTypicalUnit = DB::table('project_typical_units')->where('project_id', $request->id)->get(); 
            $projectMedia = ProjectMedia::where('project_id', $request->id)->where('collection', 'cover')->select('url')->get();
            $amenities = DB::table('amenity_project')
                ->where('amenity_project.project_id', $request->id)
                ->leftJoin('amenities', 'amenity_project.amenity_id', '=', 'amenities.id')
                ->select('amenities.*')
                ->get(); 
            $materials = ProjectMaterial::where('project_id', $request->id)->select('project_id', 'file_path', 'type', 'title', 'url', 'tags')->get();
            $paymentplans = DB::table('payment_plans as pp')
            ->leftJoin('payment_plan_steps as pps', 'pps.payment_plan_id', '=', 'pp.id')
            ->where('pp.project_id', $request->id)
            ->select(
                'pp.id as payment_plan_id',
                'pps.name as step_name',
                'pps.percentage'
            )
            ->orderBy('pp.id')
            ->orderBy('pps.sort_order')
            ->get(); 
            $developer = Developer::find($project->developer_id);
            if (!$developer) {
                    $developer = 'Developer not found';
                }

            $msg = 'All project materials fetched successfully';
            $data = [
                'project' => $project,
                'materials' => $materials,
                'projectmedia'=> $projectMedia,
                'amenities' => $amenities,
                'floorplan' => $projectTypicalUnit,
                'paymentplans' => $paymentplans,
                'developer' => $developer,

            ];
        } catch (\Exception $e) {
            $error = 1;
            $msg = 'Error! '.$e->getMessage();
        }

        return response()->json([
            'error' => $error,
            'message' => $msg,
            'data' => $data,
        ]);
    }

    public function project_units_index(Request $request)
    {
        $error = 0;
        $msg = 'success';
        $data = [];

        try {

            $project = Project::find($request->id);
            if (! $project) {
                return response()->json([
                    'error' => 1,
                    'message' => 'Project not found',
                    'data' => [],
                ]);
            }
            $units = ProjectUnit::where('project_id', $request->id)->get();
            $msg = 'All project Units fetched successfully';
            $data = [
                'project' => $project->id,
                'units' => $units,
            ];
        } catch (\Exception $e) {
            $error = 1;
            $msg = 'Error! '.$e->getMessage();
        }

        return response()->json([
            'error' => $error,
            'message' => $msg,
            'data' => $data,
        ]);
    }

    public function property_index()
    {
        $error = 0;
        $msg = 'success';
        $data = [];

        try {
            $units = Unit::leftJoin('projects', 'units.project_id', '=', 'projects.id')
                ->leftJoin('developers', 'units.developer_id', '=', 'developers.id')
                ->leftJoin('taxonomies as ut', 'ut.id', '=', 'units.unit_type')
                ->leftJoin('taxonomies as st', 'st.id', '=', 'units.sub_type')
                ->leftJoin('users as creators', 'units.created_by', '=', 'creators.id')
                ->select(
                    'units.id',
                    'projects.name as project_name',
                    'developers.name as developer',
                    'units.property_name',
                    'ut.name as unit_type_name',
                    'st.name as sub_type_name',
                    'units.status',
                    'creators.name as created_by_name'
                )
                ->orderBy('units.id', 'desc')
                ->get();

            $msg = 'Property list fetched successfully';
            $data = [
                'units' => $units,
            ];
        } catch (\Exception $e) {
            $error = 1;
            $msg = 'Error! '.$e->getMessage();
        }

        return response()->json([
            'error' => $error,
            'message' => $msg,
            'data' => $data,
        ]);
    }

    public function leads_index(Request $request)
    {
        $user = Auth::user();
        $error = 0;
        $msg = 'success';
        $data = [];

        try {
            $search = $request->search;
            $leads = Lead::where('created_by', $user->id)
                ->when($search, function ($q, $search) {
                    $q->where(function ($query) use ($search) {
                        $query->where('status', 'like', "%{$search}%")
                            ->orWhere('source', 'like', "%{$search}%")
                            ->orWhere('pref_langugae', 'like', "%{$search}%")
                            ->orWhere('lead_no', $search)
                            ->orWhere('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
                })->get();
            $msg = 'Leads list fetched successfully';
            $data = [
                'leads' => $leads,
            ];
        } catch (\Exception $e) {
            $error = 1;
            $msg = 'Error! '.$e->getMessage();
        }

        return response()->json([
            'error' => $error,
            'message' => $msg,
            'data' => $data,
        ]);
    }

    public function leads_store(LeadRequest $request)
    {
        $error = 0;
        $msg = 'success';
        $data = [];

        try {
            $user = Auth::user();
            $validated = $request->validated();
            $validated['created_by'] = $user->id;
            $dup = Lead::where('name', $validated['name'])
                ->when(! empty($validated['phone']), fn ($q) => $q->where('phone', $validated['phone']))
                ->when(! empty($validated['email']), fn ($q) => $q->where('email', $validated['email']))
                ->exists();

            if ($dup) {
                return response()->json([
                    'error' => 1,
                    'message' => 'A similar lead already exists.',
                    'data' => [],
                ], 409);
            }
            $lastLeadNo = Lead::max('lead_no');
            $validated['lead_no'] = $lastLeadNo ? $lastLeadNo + 1 : 1001;

            $lead = Lead::create($validated);

            $msg = 'Lead created successfully';
            $data = ['lead' => $lead];

        } catch (\Exception $e) {
            $error = 1;
            $msg = 'Error! '.$e->getMessage();
        }

        return response()->json([
            'error' => $error,
            'message' => $msg,
            'data' => $data,
        ]);
    }

    public function leads_views(Request $request)
    {
        $error = 0;
        $msg = 'success';
        $data = [];

        try {
            $id = $request->id;
            $lead = Lead::find($id);
            $msg = 'Leads fetched successfully';
            $data = ['lead' => $lead];
        } catch (\Exception $e) {
            $error = 1;
            $msg = 'Error! '.$e->getMessage();
        }

        return response()->json([
            'error' => $error,
            'message' => $msg,
            'data' => $data,
        ]);
    }

    public function change_password(Request $request)
    {
        $error = 0;
        $msg = 'success';
        $data = [];

        try {
            $user = Auth::user();
            $validator = Validator::make($request->all(), [
                'old_password' => 'required',
                'new_password' => 'required|min:6|confirmed',
            ], [
                'old_password.required' => 'Please enter your old password.',
                'new_password.required' => 'Please enter a new password.',
                'new_password.min' => 'New password must be at least 6 characters.',
                'new_password.confirmed' => 'Confirm password does not match the new password.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 1,
                    'message' => 'Validation failed.',
                    'data' => $validator->errors(),
                ], 422);
            }
            if (! Hash::check($request->old_password, $user->password)) {
                return response()->json([
                    'error' => 1,
                    'message' => 'Old password is incorrect.',
                    'data' => [],
                ], 400);
            }

            $user->password = Hash::make($request->new_password);
            $user->save();

            $msg = 'Password changed successfully!';
            $data = ['user_id' => $user->id];

        } catch (\Exception $e) {
            $error = 1;
            $msg = 'Error! '.$e->getMessage();
        }

        return response()->json([
            'error' => $error,
            'message' => $msg,
            'data' => $data,
        ]);
    }

    public function getRelationManager(Request $request)
    {
        $error = 0;
        $msg = 'success';
        $data = [];

        try {
            $relationManagers = User::role('rm')->get();

            $msg = 'Relation Managers fetched successfully';
            $data = [
                'relation_managers' => $relationManagers,
            ];

        } catch (\Exception $e) {
            $error = 1;
            $msg = 'Error! '.$e->getMessage();
        }

        return response()->json([
            'error' => $error,
            'message' => $msg,
            'data' => $data,
        ]);
    }
    public function storeLeadTasks(Request $request)
    {
            $error = 0;
            $msg   = 'success';
            $data  = [];

            try {

                $data = $request->validate([
                    'task_type'               => 'required|string|max:255',
                    'note'                    => 'nullable|string',
                    'unit_ref'                => 'nullable|string|max:255',
                    'is_completed'            => 'nullable|boolean',
                    'schedule_follow_up_days' => 'nullable|integer',
                    'date'                    => 'nullable|date',
                    'time'                    => 'nullable',
                ]);

                $data['lead_id']      = $request->lead_id;
                $data['user_id']      = auth()->id();
                $data['is_completed'] = $request->boolean('is_completed', false);

                $task = LeadTask::create($data);

                $data = $task;

            } catch (\Exception $e) {
                $error = 1;
                $msg   = "Error! " . $e->getMessage();
            }

            return response()->json([
                'error'   => $error,
                'message' => $msg,
                'data'    => $data,
            ]);
    }
  public function getLeadTaskActivity(Request $request, Lead $lead)
    {
    $error = 0;
    $msg   = 'success';
    $data  = [];

    try {

        $tasks = $lead->tasks()
            ->leftJoin('units as u', 'lead_tasks.unit_ref', '=', 'u.id')
            ->latest()
            ->get([
                'lead_tasks.id',
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

        $data = $tasks;

    } catch (\Exception $e) {
        $error = 1;
        $msg   = "Error! " . $e->getMessage();
    }

    return response()->json([
        'error'   => $error,
        'message' => $msg,
        'data'    => $data,
    ]);
    }

public function storeLeadOffers(Request $request, Lead $lead)
{
    $error = 0;
    $msg   = 'success';
    $data  = [];

    try {

        $data = $request->validate([
            'unit_id'        => 'required|exists:units,id',
            'offered_amount' => 'required|numeric|min:0',
            'status'         => 'required|in:pending,accepted,rejected,closed',
            'offer_type'     => 'nullable|in:accepted,rejected',
            'reason'         => 'nullable|string|max:1000',
        ]);

        $data['lead_id']    = $lead->id;
        $data['created_by'] = auth()->id();

        $offer = LeadOffer::create($data);

        $data = $offer;

    } catch (\Exception $e) {
        $error = 1;
        $msg   = "Error! " . $e->getMessage();
    }

    return response()->json([
        'error'   => $error,
        'message' => $msg,
        'data'    => $data,
    ]);
}
public function getLeadOffers(Request $request, Lead $lead)
{
    $error = 0;
    $msg   = 'success';
    $data  = [];

    try {

        $offers = LeadOffer::where('lead_id', $lead->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $data = $offers;

    } catch (\Exception $e) {
        $error = 1;
        $msg   = "Error! " . $e->getMessage();
    }

    return response()->json([
        'error'   => $error,
        'message' => $msg,
        'data'    => $data,
    ]);
}
public function getUnitRef(Request $request)
{
    $error = 0;
    $msg   = 'success';
    $data  = [];

    try {
    $data = DB::table('units')
            ->select('id', 'ref_no', 'unit_number', 'property_name')
            ->orderBy('property_name')
            ->get();


    } catch (\Exception $e) {
        $error = 1;
        $msg   = "Error! " . $e->getMessage();
    }

    return response()->json([
        'error'   => $error,
        'message' => $msg,
        'data'    => $data,
    ]);
}



    public function contactStore(Request $request)
    {
        $error = 0;
        $msg   = 'success';
        $data  = [];

        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|max:255',
            'phone_no'  => 'nullable|digits_between:10,15',
            'message'   => 'required|string',
            'p_id'      => 'nullable|integer',
            'type'      => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error'   => 1,
                'message' => 'Validation Error',
                'data'    => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {

            $contact = new ContactInquiry();
            $contact->name     = $request->name;
            $contact->email    = $request->email;
            $contact->phone_no = $request->phone_no ?? null;
            $contact->message  = $request->message;
            $contact->p_id     = $request->p_id ?? null;
            $contact->type     = $request->type ?? null;
            $contact->save();

            DB::commit();

            $msg = 'Inquiry submitted successfully';

            $data = [
                'contact' => $contact
            ];

        } catch (\Exception $e) {

            DB::rollBack();

            $error = 1;
            $msg   = 'Error! ' . $e->getMessage();
        }

        return response()->json([
            'error'   => $error,
            'message' => $msg,
            'data'    => $data
        ]);
    }

    public function userDestroy()
    {
        try {

            $userId = auth()->user();
            if (!$userId) {
                return response()->json([
                    'error' => 1,
                    'message' => 'Unauthorized',
                    'data' => []
                ], 401);
            }
            $user = User::where('id', $userId->id)->first();
            if (!$user) {
                return response()->json([
                    'error' => 1,
                    'message' => 'User not found',
                    'data' => []
                ], 404);
            }
            // dd($user);
            $user->delete();
            return response()->json([
                'error' => 0,
                'message' => 'User deleted successfully',
                'data' => []
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 1,
                'message' => $e->getMessage(),
                'data' => []
            ]);
        }
    }





}
