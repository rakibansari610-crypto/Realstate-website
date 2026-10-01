<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\PartnerRequest;
use App\Models\Partner;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Spatie\Permission\Traits\HasRoles;


class PartnerController extends Controller
{
    public function index(Request $request)
    {
        // $orgId = Auth::user()->org_id;

        $partners = Partner::when(auth()->user()->hasRole('channel partner'), function ($q) {
                // Show only partners whose parent is this channel partner
                $q->where('parent_id', auth()->id());
            })
            ->when($request->filled('q'), function ($q) use ($request) {
                $s = trim($request->q);
                $q->where(function ($qq) use ($s) {
                    $qq->where('name', 'like', "%$s%")
                        ->orWhere('email', 'like', "%$s%")
                        ->orWhere('phone', 'like', "%$s%");
                });
            })
            ->when($request->filled('type'), fn($q) => $q->where('type', $request->type))
            ->with('parent:id,name')
            ->orderBy('type')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('org.partners.index', compact('partners'));
    }


    public function get(Request $request)
    {

    
        $columns = [
            0 => 'id',        
            1 => 'name',
            2 => 'type',
            3 => 'phone',
            4 => 'email',
            5 => 'parent_id',
            6 => 'approved',
            7 => 'active',
        ];

        $totalData = Partner::count();

        $limit = $request->input('length', 10);
        $start = $request->input('start', 0);
        $orderColumnIndex = $request->input('order.0.column', 0);
        $orderColumn = $columns[$orderColumnIndex] ?? 'id';
        $orderDir = $request->input('order.0.dir', 'desc');

       $query = Partner::with('parent:id,name')
        ->when(auth()->user()->hasRole('channel partner'), function ($q) {
            $q->where('parent_id', auth()->id());
        })
        ->when(auth()->user()->hasRole('rm'), function ($q) {
            $q->where('rm_id', auth()->id());
        });


            
        // Custom filter: search input 'q'
        if ($request->filled('q')) {
            $s = trim($request->q);
            $query->where(function ($qq) use ($s) {
                $qq->where('name', 'like', "%$s%")
                ->orWhere('email', 'like', "%$s%")
                ->orWhere('type', 'like', "%$s%")
                ->orWhere('phone', 'like', "%$s%");
            });
        }

        // Type filter
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // DataTables search box
        if ($request->filled('search.value')) {
            $s = $request->input('search.value');
            $query->where(function ($qq) use ($s) {
                $qq->where('name', 'like', "%$s%")
                ->orWhere('email', 'like', "%$s%")
                ->orWhere('phone', 'like', "%$s%");
            });
        }

        $totalFiltered = $query->count();

        $partners = $query->offset($start)
            ->limit($limit)
            ->orderBy($orderColumn, $orderDir)
            ->get();

        $data = [];
        $sn = $start + 1;
        foreach ($partners as $p) {
            $nestedData['sn'] = $sn++;
            $nestedData['name'] = '<div class="fw-semibold">'.$p->name.'</div>';
            $nestedData['type'] = str_replace('_',' ',$p->type);
            $nestedData['phone'] = $p->phone;
            $nestedData['email'] = $p->email;
            $nestedData['parent'] = $p->parent?->name ?? '—';
            $nestedData['approved'] = $p->approved 
                ? '<span class="badge bg-success">Yes</span>' 
                : '<span class="badge bg-warning text-dark">No</span>';
            $nestedData['active'] = $p->active 
                ? '<span class="badge bg-success">Yes</span>' 
                : '<span class="badge bg-secondary">No</span>';

             $actions = '
                <a href="'.route('org.partners.edit', $p).'" class="btn btn-sm btn-outline-secondary">Edit</a>
            '; 
            if (!Auth::user()->hasRole('rm')) {
                $actions .= '
                    <a href="'.route('partner-reset-password', Crypt::encrypt($p->id)).'" class="btn btn-sm btn-outline-warning reset-password">Reset Password</a>
                    <a href="'.route('org.partners.commissions.index', $p).'" class="btn btn-sm btn-outline-info">Commissions</a>
                    <form action="'.route('org.partners.destroy', $p).'" method="post" class="d-inline" onsubmit="return confirm(\'Delete this partner?\')">
                        '.csrf_field().method_field('DELETE').'
                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                    </form>
                ';
            }

            $nestedData['actions'] = $actions;
            $data[] = $nestedData;
        }

        return response()->json([
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        ]);
    }




    public function create()
    {
        // $parents = Partner::where('org_id', Auth::user()->org_id)
        $parents = Partner::where('type', 'channel_partner')
        ->when(auth()->user()->hasRole('channel partner'), fn($q) => $q->where('user_id', auth()->id()))
        ->orderBy('name')
        ->get(['user_id', 'name']);
        $rmUsers = User::role('rm')->get();
        return view('org.partners.form', ['partner' => new Partner(), 'parents' => $parents,'rmUsers' => $rmUsers]);
    }

    public function store(PartnerRequest $request)
    {
        $userId = Auth::user();
        $data  = $request->validated();
        // $data['org_id'] = $orgId;

        if (!is_dir(public_path('media/partners'))) {
            @mkdir(public_path('media/partners'), 0755, true);
        }

        foreach (['pan_file' => 'pan_path', 'gst_file' => 'gst_path', 'rera_file' => 'rera_path'] as $input => $field) {
            if (!empty($request->$input)) {
                $value = $request->$input;
                $name  = md5(time() . $input) . '.' . $value->getClientOriginalExtension();
                $value->move(public_path('media/partners'), $name);
                $data[$field] = 'media/partners/' . $name;
            }
        }
        if (Auth::user()->hasRole('rm'))
        {
            $data['rm_id'] = $userId->id;
        } 
        
        $data['approved'] = (bool)($data['approved'] ?? true);
        $data['active']   = (bool)($data['active'] ?? true);

        $partner = Partner::create($data);

        // Create or link a User account (email or mobile)
        $email = trim((string)($data['email'] ?? ''));
        $phone = $this->normalizePhone($data['phone'] ?? null);

        if ($email || $phone) {
            // Prefer match by email first, then by phone
            $user = null;
            if ($email) {
                $user = User::where('email', $email)->first();
                // $user = User::where('org_id', $orgId)->where('email', $email)->first();
            }
            if (!$user && $phone) {
                $user = User::where('phone', $phone)->first();
                // $user = User::where('org_id', $orgId)->where('phone', $phone)->first();
            }

            if (!$user) { 
                $user = User::create([
                    // 'org_id'   => $orgId,
                    'name'     => $data['name'],
                    'email'    => $email ?: null,
                    'phone'    => $phone ?: null,
                    // 'password' => Hash::make('123456'), // temp password
                    'password' => Hash::make($data['password'])
                ]);
            } else {
                dd('else');
                // Sync name, and fill missing email/phone if available
                if ($user->name !== $data['name']) {
                    $user->name = $data['name'];
                }
                if ($email && !$user->email) {
                    $user->email = $email;
                }
                if ($phone && !$user->phone) {
                    $user->phone = $phone;
                }
                $user->save();
            }

            // Assign correct role & link to partner
            $user->syncRoles([$this->roleFor($partner->type)]);
            $partner->update(['user_id' => $user->id]);
        }

        return redirect()->route('org.partners.edit', $partner)->with('success', 'Partner created.');
    }

    public function edit(Partner $partner)
    {
        $this->ensureOrg($partner);
        $parents = Partner::where('type', 'channel_partner')
        // if channel partner, only themselves
        ->when(auth()->user()->hasRole('channel partner'), fn($q) => $q->where('user_id', auth()->id()))
        // don’t offer the same user if needed; if you truly want to exclude the *current* user record:
        ->where('user_id', '!=', $partner->user_id)
        ->orderBy('name')
        ->get(['user_id', 'name']);
         $rmUsers = User::role('rm')->get();
        return view('org.partners.form', compact('partner', 'parents','rmUsers'));
    }

    public function update(PartnerRequest $request, Partner $partner)
    {
        $this->ensureOrg($partner);
        $data = $request->validated();

        if (!is_dir(public_path('media/partners'))) {
            @mkdir(public_path('media/partners'), 0755, true);
        }

        foreach (['pan_file' => 'pan_path', 'gst_file' => 'gst_path', 'rera_file' => 'rera_path'] as $input => $field) {
            if (!empty($request->$input)) {
                $value = $request->$input;
                $name  = md5(time() . $input) . '.' . $value->getClientOriginalExtension();
                $value->move(public_path('media/partners'), $name);
                $data[$field] = 'media/partners/' . $name;
            }
        }

        $partner->update($data);

        // $orgId = Auth::user()->org_id;
        $email = trim((string)($data['email'] ?? ''));
        $phone = $this->normalizePhone($data['phone'] ?? null);

        if ($email || $phone) {
            if ($partner->user_id) {
                $user = User::where('id', $partner->user_id)->first();
                // $user = User::where('id', $partner->user_id)->where('org_id', $orgId)->first();

                if ($user) {
                    // Try to resolve conflicts by re-linking to existing user with same email/phone
                    // 1) Email conflict → relink
                    if ($email && $user->email !== $email) {
                        $uByEmail = User::where('email', $email)->first();
                        // $uByEmail = User::where('org_id', $orgId)->where('email', $email)->first();
                        if ($uByEmail && $uByEmail->id !== $user->id) {
                            $partner->update(['user_id' => $uByEmail->id]);
                            $user = $uByEmail;
                        } else {
                            $user->email = $email; // safe to set
                        }
                    }
                    // 2) Phone conflict → relink (only if not already re-linked via email to another)
                    if ($phone && $user->phone !== $phone) {
                        $uByPhone = User::where('phone', $phone)->first();
                        // $uByPhone = User::where('org_id', $orgId)->where('phone', $phone)->first();
                        if ($uByPhone && $uByPhone->id !== $user->id) {
                            $partner->update(['user_id' => $uByPhone->id]);
                            $user = $uByPhone;
                        } else {
                            $user->phone = $phone; // safe to set
                        }
                    }

                    // Keep name in sync
                    if ($user->name !== ($data['name'] ?? $user->name)) {
                        $user->name = $data['name'];
                    }

                    // Ensure password is hashed if you ever reset it
                    $user->password = Hash::make('123456'); // (only if you intend to reset)

                    $user->save();
                } else {
                    // Stale link: find by email/phone or create
                    $user = null;
                    if ($email) $user = User::where('email', $email)->first();
                    // if ($email) $user = User::where('org_id',$orgId)->where('email',$email)->first();
                    if (!$user && $phone) $user = User::where('phone', $phone)->first();
                    // if (!$user && $phone) $user = User::where('org_id',$orgId)->where('phone',$phone)->first();
                    if (!$user) {
                        $user = User::create([
                            // 'org_id'   => $orgId,
                            'name'     => $data['name'] ?? $partner->name,
                            'email'    => $email ?: null,
                            'phone'    => $phone ?: null,
                            'password' => Hash::make(Str::random(12)),
                        ]);
                    }
                    $partner->update(['user_id' => $user->id]);
                }
            } else {
                // No link yet: find by email/phone or create
                $user = null;
                if ($email) $user = User::where('email', $email)->first();
                // if ($email) $user = User::where('org_id',$orgId)->where('email',$email)->first();
                if (!$user && $phone) $user = User::where('phone', $phone)->first();
                // if (!$user && $phone) $user = User::where('org_id',$orgId)->where('phone',$phone)->first();
                if (!$user) {
                    $user = User::create([
                        // 'org_id'   => $orgId,
                        'name'     => $data['name'] ?? $partner->name,
                        'email'    => $email ?: null,
                        'phone'    => $phone ?: null,
                        'password' => Hash::make(Str::random(12)),
                    ]);
                }
                $partner->update(['user_id' => $user->id]);
            }

            // Ensure correct role based on current partner type
            if (isset($user)) {
                $user->syncRoles([$this->roleFor($partner->type)]);
            }
        } else {
            // If you want to unlink when both email & phone are cleared:
            // $partner->update(['user_id' => null]);
        }

        // If type changed and no email/phone change triggered role sync
        if ($partner->user_id && empty($email) && empty($phone)) {
            if ($u = User::find($partner->user_id)) {
                $u->syncRoles([$this->roleFor($partner->type)]);
            }
        }

        return back()->with('success', 'Partner updated.');
    }

    private function roleFor(string $type): string
    {
        return match ($type) {
            'channel_partner' => 'channel partner',
            'property consultant'          => 'property consultant',
            'freelancer'           => 'freelancer',
            default           => 'channel partner',
        };
    }

    public function destroy(Partner $partner)
    {
        $this->ensureOrg($partner);
        $partner->delete();
        $user = User::find($partner->user_id);
        if ($user) {
            $user->delete();
        }
        return redirect()->route('org.partners.index')->with('success', 'Partner deleted.');
    }

    private function ensureOrg(Partner $partner): void
    {
        //abort_unless($partner->org_id === Auth::user()->org_id, 403);
    }

    /** Keep digits, allow leading + */
    private function normalizePhone(?string $raw): ?string
    {
        if (!$raw) return null;
        $raw = trim($raw);
        return preg_replace('/(?!^\+)\D+/', '', $raw);
    }
}
