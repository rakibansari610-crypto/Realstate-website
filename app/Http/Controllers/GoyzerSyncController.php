<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class GoyzerSyncController extends Controller
{
    public function sync(Request $request): RedirectResponse
    {
        $type = $request->input('type', 'all');

        if (! in_array($type, ['sale', 'rent', 'all'], true)) {
            return back()->with('error', 'Invalid sync type.');
        }

        Artisan::call('goyzer:import', [
            '--type' => $type,
        ]);

        $output = Artisan::output();

        return back()->with('success', "Goyzer {$type} sync completed.")
            ->with('goyzer_output', $output);
    }
}
