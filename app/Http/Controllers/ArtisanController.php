<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;

class ArtisanController extends Controller
{
     public function runCommand(Request $request)
    {
        // Validate that a 'command' parameter is provided in the URL
        $request->validate([
            'command' => 'required|string',
        ]);


        // Get the Artisan command from the URL
        $command = $request->input('command');


        // Run the Artisan command and capture the output
        $exitCode = Artisan::call($command);


        // Get the output of the command
        $output = Artisan::output();


        // Return the output as a response (could be JSON or plain text)
        return response()->json([
            'command' => $command,
            'exit_code' => $exitCode,
            'output' => $output,
        ]);
    }
}
