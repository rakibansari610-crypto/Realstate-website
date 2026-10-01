<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Site;
use App\Models\User;
use App\Tenancy\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use App\Models\Country;


class OrgUserController extends Controller
{
    /** Roles the current user is allowed to assign */
    private function allowedRolesForCurrent(): array
    {
        $me = Auth::user();
        if ($me->hasRole('org admin')) {
            return ['org admin'];
        }
        // site admin
        return ['employee'];
    }

    /** Site IDs the current user can assign */
    private function allowedSiteIdsForCurrent(): array
    {
        $me = Auth::user();
        if ($me->hasRole('org admin')) {
            return Site::where('org_id', $me->org_id)->pluck('id')->all();
        }
        // site admin: only their site (if any)
        return array_filter([$me->site_id]);
    }

    public function index(Request $req)
    {
        $me    = Auth::user();
        $orgId = $me->org_id;


        // $q = User::with('site:id,name')
        //     ->where('org_id', $orgId);
        $q = User::with('site:id,name')
        // ->where('org_id', $orgId)
        ->whereHas('roles', function($query) {
            $query->whereIn('name', ['org admin', 'data entry','rm']);
        });

        if ($s = $req->query('q')) {
            $q->where(function($qq) use ($s){
                $qq->where('name','like',"%{$s}%")
                   ->orWhere('email','like',"%{$s}%");
            });
        }

        // site admin sees only their site’s users
        if ($me->hasRole('site admin') && $me->site_id) {
            $q->where('site_id', $me->site_id);
        }

        $users = $q->orderBy('name')->paginate(15)->withQueryString();

        return view('org.users.index', compact('users'));
    }

    public function create()
    {

        $me        = Auth::user();
        $orgId     = $me->org_id;
        $roles = Role::whereIn('name', ['org admin','data entry','rm'])
             ->pluck('name')
             ->toArray();

        // $roles     = $this->allowedRolesForCurrent();
        $allowedIds= $this->allowedSiteIdsForCurrent();

        $sites = Site::where('org_id', $orgId)
            ->when($me->hasRole('site admin') && $me->site_id, fn($qq) => $qq->where('id', $me->site_id))
            ->orderBy('name')->get(['id','name']);
        $countries = Country::where('country_status', 1)
        ->orderBy('country_name')
        ->get(['id','country_name']);

        return view('org.users.create', compact('roles','sites','countries'));
    }

    public function store(Request $request)
    {
        $me        = Auth::user();
        // $orgId     = $me->org_id;
        // $roles     = $this->allowedRolesForCurrent();
        $roles = $request->role;
        $siteIds   = $this->allowedSiteIdsForCurrent();

        $data = $request->validate([
            'name'     => ['required','string','max:190'],
            'email'    => [
                'required','email','max:190',
                // Rule::unique('users','email')->where(fn($q) => $q->where('org_id',$orgId)),
                Rule::unique('users', 'email'),
            ],
            'role'     => ['required', Rule::in($roles)],
            'site_id'  => ['nullable', Rule::in($siteIds)],
            'password' => ['required','string','min:6'],
            'country_id' => ['required','exists:countries,id'],

        ]);

        // Enforce site admin cannot create org admin
        if ($me->hasRole('site admin') && $data['role'] === 'org admin') {
            abort(403, 'Not allowed to assign org admin');
        }

        if (!$me->hasAnyRole(['org admin', 'super admin'])) {
            $data['country_id'] = $me->country_id;
        }

        $user = User::create([
            // 'org_id'   => $orgId,
            'site_id'  => $data['site_id'] ?? ($me->hasRole('site admin') ? $me->site_id : null),
            'country_id' => $data['country_id'],
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $user->syncRoles([$data['role']]);

        return redirect()->route('org.users.index')->with('success','User created.');
    }

    public function edit($id)
    {
        $me    = Auth::user();
        $orgId = $me->org_id;

        $user = User::where('org_id', $orgId)->findOrFail($id);

        // site admin cannot edit users from other sites
        if ($me->hasRole('site admin') && $me->site_id && $user->site_id !== $me->site_id) {
            abort(403);
        }

        // $roles   = $this->allowedRolesForCurrent();
        $roles = Role::whereIn('name', ['org admin', 'data entry','rm'])
             ->pluck('name')
             ->toArray();
        $sites   = Site::where('org_id', $orgId)
            ->when($me->hasRole('site admin') && $me->site_id, fn($qq) => $qq->where('id', $me->site_id))
            ->orderBy('name')->get(['id','name']);
        $countries = Country::where('country_status', 1)
        ->orderBy('country_name')
        ->get(['id','country_name']);
        $currentRole = $user->roles()->pluck('name')->first();

        return view('org.users.edit', compact('user','roles','sites','currentRole','countries'));
    }

    public function update(Request $request, $id)
    {
        $me    = Auth::user();
        // $orgId = $me->org_id;

        // $user = User::where('org_id', $orgId)->findOrFail($id);
        $user = User::findOrFail($id);

        if ($me->hasRole('site admin') && $me->site_id && $user->site_id !== $me->site_id) {
            abort(403);
        }

        $roles = Role::whereIn('name', ['org admin', 'data entry'])
             ->pluck('name')
             ->toArray();
        // $roles   = $this->allowedRolesForCurrent();
        $siteIds = $this->allowedSiteIdsForCurrent();

        $data = $request->validate([
            'name'     => ['sometimes','required','string','max:190'],
            'email'    => [
                'sometimes','required','email','max:190',
                Rule::unique('users','email')
                    ->ignore($user->id),
            ],
            'role'     => ['required', Rule::in($roles)],
            'site_id'  => ['nullable', Rule::in($siteIds)],
            'country_id' => ['required','exists:countries,id'],
            'password' => ['nullable','string','min:6'],
        ]);

        if (!$me->hasAnyRole(['org admin', 'super admin'])) {
            $data['country_id'] = $me->country_id;
        }

        if (isset($data['name']))  $user->name  = $data['name'];
        if (isset($data['email'])) $user->email = $data['email'];
        if (isset($data['country_id']))  $user->country_id = $data['country_id'];


        // site selection rules
        if (array_key_exists('site_id', $data)) {
            $user->site_id = $data['site_id'] ?? ($me->hasRole('site admin') ? $me->site_id : null);
        } elseif ($me->hasRole('site admin')) {
            $user->site_id = $me->site_id; // force lock
        }

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        $user->syncRoles([$data['role']]);

        return redirect()->route('org.users.index')->with('success','User updated.');
    }

    public function destroy($id)
    {
        $me    = Auth::user();
        // $orgId = $me->org_id;

        $user = User::findOrFail($id);
        // $user = User::where('org_id', $orgId)->findOrFail($id);

        if ($me->hasRole('site admin') && $me->site_id && $user->site_id !== $me->site_id) {
            abort(403);
        }

        // prevent deleting self or higher role, if you want:
        if ($me->id === $user->id) {
            return back()->with('error','You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('org.users.index')->with('success','User deleted.');
    }
}
