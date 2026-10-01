<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use GeoIP;

class SetCountrySession
{
   public function handle($request, Closure $next)
    {
        // Do NOT override if user selected manually
        if (!session()->has('country_manual')) {

            if (!session()->has('country')) {

                $ip = $request->ip();

                // Localhost fallback
                if ($ip === '127.0.0.1' || $ip === '::1') {
                    session(['country' => 'India']);
                } else {
                    $location = geoip($ip);
                    $countryCode = $location->iso_code;

                    session([
                        'country' => $countryCode === 'IN' ? 'India' : 'UAE'
                    ]);
                }
            }
        }

        return $next($request);
    }

}
