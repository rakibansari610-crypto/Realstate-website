<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SiteController extends Controller
{
    

    public function index(Request $req)
    {
        $user = Auth::user();
        
        $orgId = $user->org_id;
        $q = Site::with('org:id,name')->where('org_id', $orgId);

        // If tenant is resolved (optional), you can force by tenant first:
        // if (\App\Tenancy\Tenant::id()) {
        //     $q->where('org_id', \App\Tenancy\Tenant::id());
        // }

        if ($user && ($user->hasRole('org admin') || $user->hasRole('site admin'))) {
            // Org/Site admins: lock to their org
            $q->where('org_id', $user->org_id);
            $orgs = Organization::where('id', $user->org_id)->get(['id', 'name']);
        } else {
            // Super admin: optional filter by org_id
            if ($orgId = $req->query('org_id')) {
                $q->where('org_id', $orgId);
            }
            $orgs = Organization::orderBy('name')->get(['id', 'name']);
        }

        if ($s = $req->query('q')) {
            $q->where(function ($qq) use ($s) {
                $qq->where('name', 'like', "%{$s}%")
                    ->orWhere('code', 'like', "%{$s}%");
            });
        }

        $sites = $q->orderBy('name')->paginate(15)->withQueryString();

        return view('org.sites.index', compact('sites', 'orgs'));
    }


    public function create()
    {
        $user = Auth::user();
        $orgId = $user->org_id;
        $orgs = Organization::orderBy('name')->get(['id', 'name'])->where('id', $orgId);
        $site = new Site();
        return view('org.sites.create', compact('site', 'orgs'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'org_id'   => 'required|exists:organizations,id',
            'name'     => 'required|string|max:190',
            'code'     => 'nullable|string|max:50',
            'timezone' => 'nullable|string|max:60',
            'address'  => 'nullable',  // JSON text; parsed below if provided
            'settings' => 'nullable',  // JSON text; parsed below if provided
        ]);
        $data['address']  = $this->jsonOrNull($request->input('address'));
        $data['settings'] = $this->jsonOrNull($request->input('settings'));

        $site = Site::create($data);
        return redirect()->route('org.sites.index')->with('success', 'Site created.');
    }

    public function edit($id)
    {
         $user = Auth::user();
        $site = Site::findOrFail($id);
        $orgId = $user->org_id;
        $orgs = Organization::orderBy('name')->get(['id', 'name'])->where('id', $orgId);
        return view('org.sites.edit', compact('site', 'orgs'));
    }

    public function update(Request $request, $id)
    {
        $site = Site::findOrFail($id);
        $data = $request->validate([
            'org_id'   => 'required|exists:organizations,id',
            'name'     => 'required|string|max:190',
            'code'     => 'nullable|string|max:50',
            'timezone' => 'nullable|string|max:60',
            'address'  => 'nullable',
            'settings' => 'nullable',
        ]);
        $data['address']  = $this->jsonOrNull($request->input('address'));
        $data['settings'] = $this->jsonOrNull($request->input('settings'));

        $site->update($data);
        return redirect()->route('org.sites.index')->with('success', 'Site updated.');
    }

    public function destroy($id)
    {
        $site = Site::findOrFail($id);
        $site->delete();
        return redirect()->route('org.sites.index')->with('success', 'Site deleted.');
    }

    private function jsonOrNull(?string $raw)
    {
        if (!filled($raw)) return null;
        $trim = trim($raw);
        try {
            return json_decode($trim, true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
