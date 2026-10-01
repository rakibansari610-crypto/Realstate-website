<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class CountrySession
{
    public function handle(Request $request, Closure $next)
    {
        $country = $request->route('country');

        if ($country) {
            session(['country_code' => $country]);
 
            URL::defaults([
                'country' => $country
            ]);
        }

        return $next($request);
    }
}
