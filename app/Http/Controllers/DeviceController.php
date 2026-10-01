<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;

use Illuminate\Support\Str;

class DeviceController extends Controller
{
    public function index(Request $req)
    {
        $q = Device::with('site:id,name');
        if ($siteId = $req->query('site_id')) $q->where('site_id', $siteId);
        if ($type = $req->query('type'))     $q->where('type', $type);
        return $q->orderByDesc('id')->paginate(15);
    }

    public function store(Request $req)
    {
        $data = $req->validate([
            'site_id' => 'required|exists:sites,id',
            'name'    => 'required|string|max:190',
            'type'    => 'required|in:KIOSK,SCANNER',
            'status'  => 'nullable|in:ACTIVE,INACTIVE',
            'meta'    => 'nullable|array',
        ]);
        $data['auth_token'] = Str::random(40); // generate device token
        $device = Device::create($data);
        return response()->json($device, 201);
    }

    public function show($id)
    {
        $device = Device::with('site:id,name')->findOrFail($id);
        return response()->json($device);
    }

    public function update(Request $req, $id)
    {
        $device = Device::findOrFail($id);
        $data = $req->validate([
            'site_id'    => 'sometimes|exists:sites,id',
            'name'       => 'sometimes|string|max:190',
            'type'       => 'sometimes|in:KIOSK,SCANNER',
            'status'     => 'sometimes|in:ACTIVE,INACTIVE',
            'meta'       => 'nullable|array',
            'rotate_key' => 'nullable|boolean', // if true, rotate token
        ]);
        if (!empty($data['rotate_key'])) {
            $data['auth_token'] = Str::random(40);
            unset($data['rotate_key']);
        }
        $device->update($data);
        return response()->json($device);
    }

    public function destroy($id)
    {
        $device = Device::findOrFail($id);
        $device->delete();
        return response()->json(['ok' => true]);
    }
}
