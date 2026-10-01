<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Tenancy\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Illuminate\Support\Str;

class OrganizationController extends Controller
{
    public function index(Request $req)
    {


        $q = Organization::query()
            ->select('organizations.*')
            ->addSelect([
                'admin_name' => DB::table('users')
                    ->join('model_has_roles as mhr', 'mhr.model_id', '=', 'users.id')
                    ->join('roles as r', 'r.id', '=', 'mhr.role_id')
                    ->whereColumn('users.org_id', 'organizations.id')
                    ->where('mhr.model_type', User::class)
                    ->where('r.name', 'org admin')
                    ->orderBy('users.id')
                    ->limit(1)
                    ->select('users.name'),
                'admin_email' => DB::table('users')
                    ->join('model_has_roles as mhr', 'mhr.model_id', '=', 'users.id')
                    ->join('roles as r', 'r.id', '=', 'mhr.role_id')
                    ->whereColumn('users.org_id', 'organizations.id')
                    ->where('mhr.model_type', User::class)
                    ->where('r.name', 'org admin')
                    ->orderBy('users.id')
                    ->limit(1)
                    ->select('users.email'),
            ]);
        $orgs = $q->orderByDesc('id')->paginate(15)->withQueryString();
        return view('su.org.index', compact('orgs'));
    }


    public function get(Request $request)
    {
        $columns = [
            0 => 'id',
            1 => 'name',
            2 => 'subdomain',
            3 => 'custom_domain',
            4 => 'created_at',
            5 => 'actions'
        ];

        $totalData = Organization::count();
        $totalFiltered = $totalData;

        $limit = $request->input('length', 10);
        $start = $request->input('start', 0);
        $orderColumn = $columns[$request->input('order.0.column', 0)];
        $orderDir = $request->input('order.0.dir', 'desc');

        $query = Organization::select('organizations.*')
            ->addSelect([
                'admin_name' => DB::table('users')
                    ->join('model_has_roles as mhr', 'mhr.model_id', '=', 'users.id')
                    ->join('roles as r', 'r.id', '=', 'mhr.role_id')
                    ->whereColumn('users.org_id', 'organizations.id')
                    ->where('mhr.model_type', User::class)
                    ->where('r.name', 'org admin')
                    ->orderBy('users.id')
                    ->limit(1)
                    ->select('users.name'),
                'admin_email' => DB::table('users')
                    ->join('model_has_roles as mhr', 'mhr.model_id', '=', 'users.id')
                    ->join('roles as r', 'r.id', '=', 'mhr.role_id')
                    ->whereColumn('users.org_id', 'organizations.id')
                    ->where('mhr.model_type', User::class)
                    ->where('r.name', 'org admin')
                    ->orderBy('users.id')
                    ->limit(1)
                    ->select('users.email'),
            ]);

        // Search
        if(!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $query->where(function($q) use ($search) {
                $q->where('name','LIKE',"%{$search}%")
                ->orWhere('slug','LIKE',"%{$search}%")
                ->orWhere('subdomain','LIKE',"%{$search}%")
                ->orWhere('custom_domain','LIKE',"%{$search}%");
            });

            $totalFiltered = $query->count();
        }

        $orgs = $query->offset($start)
                    ->limit($limit)
                    ->orderBy($orderColumn, $orderDir)
                    ->get();

        $data = [];
        foreach($orgs as $org) {
            $tenantBase = config('tenancy.base_domain');
            $tenantUrl = $org->custom_domain
                ? request()->getScheme() . '://' . $org->custom_domain
                : request()->getScheme() . '://' . $org->subdomain . '.' . $tenantBase;

            $actions = '<a class="btn btn-sm btn-outline-primary" href="'.route('su.org.edit',$org->id).'">Edit</a> ';
            $actions .= '<a class="btn btn-sm btn-outline-secondary" href="'.route('su.org.branding.edit',$org->id).'">Branding</a> ';
            $actions .= '<a class="btn btn-sm btn-light" target="_blank" rel="noopener" href="'.$tenantUrl.'/login">Open Tenant</a> ';
            $actions .= '<form action="'.route('su.org.destroy',$org->id).'" method="post" class="d-inline" onsubmit="return confirm(\'Delete this organization?\');">'
                .csrf_field().method_field('DELETE').
                '<button class="btn btn-sm btn-outline-danger">Delete</button></form>';

            $data[] = [
                $org->id,
                $org->name.'<br><small class="text-muted">'.$org->slug.'</small>',
                $org->subdomain,
                $org->custom_domain ?? '—',
                $org->created_at?->format('Y-m-d'),
                $actions
            ];
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
        $org = new Organization();
        return view('su.org.create', compact('org'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:190',
            'slug'          => 'nullable|string|max:190|unique:organizations,slug',
            'subdomain'     => 'required|string|regex:/^[a-z0-9-]+$/|max:63|unique:organizations,subdomain',
            'custom_domain' => 'nullable|string|max:190|unique:organizations,custom_domain',
            'settings'      => 'nullable|array',
            'admin_name'     => 'string|max:190|required',
            'admin_email'    => 'email|max:190|required',
            'admin_password' => 'string|min:6|required',
        ]);

        // Generate slug if none provided
        if (empty($data['slug'])) {
            $base = Str::slug($data['name']);
            $slug = $base;
            $i = 1;
            while (Organization::where('slug', $slug)->exists()) {
                $slug = "{$base}-{$i}";
                $i++;
            }
            $data['slug'] = $slug;
        }

        $org = Organization::create($data);

        $email = $data['admin_email']; 
        $exists = User::where('org_id', $org->id)->where('email', $email)->exists();
        if ($exists) {
            throw ValidationException::withMessages([
                'admin_email' => 'This email is already used in this organization.',
            ]);
        }

        $user = User::create([
            'org_id'   => $org->id,
            'site_id'  => null, // optional; can set later
            'name'     => $data['admin_name'],
            'email'    => $email,
            'password' => Hash::make($data['admin_password']),
        ]);

        $user->syncRoles(['org admin']);

        return redirect()->route('su.org.index')->with('success', 'Organization created.');
    }

    public function edit($id)
    {
        $org = Organization::query()
            ->select('organizations.*')
            ->addSelect([
                'admin_name' => DB::table('users')
                    ->join('model_has_roles as mhr', 'mhr.model_id', '=', 'users.id')
                    ->join('roles as r', 'r.id', '=', 'mhr.role_id')
                    ->whereColumn('users.org_id', 'organizations.id')
                    ->where('mhr.model_type', User::class)
                    ->where('r.name', 'org admin')
                    ->orderBy('users.id')     // pick the earliest; change as needed
                    ->limit(1)
                    ->select('users.name'),
                'admin_email' => DB::table('users')
                    ->join('model_has_roles as mhr', 'mhr.model_id', '=', 'users.id')
                    ->join('roles as r', 'r.id', '=', 'mhr.role_id')
                    ->whereColumn('users.org_id', 'organizations.id')
                    ->where('mhr.model_type', User::class)
                    ->where('r.name', 'org admin')
                    ->orderBy('users.id')
                    ->limit(1)
                    ->select('users.email'),
            ])
            ->findOrFail($id);
        return view('su.org.edit', compact('org'));
    }

    public function update(Request $request, $id)
    {
        $org = Organization::findOrFail($id);

        $data = $request->validate([
            'name'          => 'sometimes|required|string|max:190',
            'slug'          => 'sometimes|nullable|string|max:190|unique:organizations,slug,' . $org->id,
            'subdomain'     => 'sometimes|required|string|regex:/^[a-z0-9-]+$/|max:63|unique:organizations,subdomain,' . $org->id,
            'custom_domain' => 'sometimes|nullable|string|max:190|unique:organizations,custom_domain,' . $org->id,
            'settings'      => 'nullable|array',

            // create OR update admin
            'admin_name'     => 'nullable|string|max:190',
            'admin_email'    => 'nullable|email|max:190',
            'admin_password' => 'nullable|string|min:6',
        ]);

        DB::transaction(function () use ($org, $data) {
            // --- Org update (auto-slug if needed) ---
            if ((!isset($data['slug']) || $data['slug'] === '') && isset($data['name'])) {
                $base = Str::slug($data['name']);
                $slug = $base;
                $i = 1;
                while (Organization::where('slug', $slug)->where('id', '!=', $org->id)->exists()) {
                    $slug = "{$base}-{$i}";
                    $i++;
                }
                $data['slug'] = $slug;
            }
            $org->update($data);

            // --- Admin create-or-update (only if any admin field provided) ---
            $wantsAdmin =
                filled($data['admin_email'] ?? null) ||
                filled($data['admin_name'] ?? null) ||
                filled($data['admin_password'] ?? null);

            if ($wantsAdmin) {
                if (empty($data['admin_email'])) {
                    throw ValidationException::withMessages([
                        'admin_email' => 'Admin email is required to create/update an Org Admin.',
                    ]);
                }

                $email = $data['admin_email'];

                // Find or prepare a user scoped to this org + email
                $user = User::firstOrNew([
                    'org_id' => $org->id,
                    'email'  => $email,
                ]);

                $isNew = !$user->exists;

                // Name: update if provided or set default on create
                if (!empty($data['admin_name'])) {
                    $user->name = $data['admin_name'];
                } elseif ($isNew && empty($user->name)) {
                    $user->name = 'Org Admin';
                }

                // Password: only hash if provided; require on create
                if (!empty($data['admin_password'])) {
                    $user->password = Hash::make($data['admin_password']);
                } elseif ($isNew) {
                    throw ValidationException::withMessages([
                        'admin_password' => 'Password is required when creating a new Org Admin.',
                    ]);
                }

                // Ensure site is null by default on create (can be set later)
                if ($isNew && empty($user->site_id)) {
                    $user->site_id = null;
                }

                $user->save();

                // Ensure role
                if (!$user->hasRole('org admin')) {
                    $user->assignRole('org admin');
                }
            }
        });

        return redirect()->route('su.org.index')->with('success', 'Organization updated.');
    }


    public function destroy($id)
    {
        $org = Organization::findOrFail($id);
        $org->delete();
        return redirect()->route('su.org.index')->with('success', 'Organization deleted.');
    }
}
