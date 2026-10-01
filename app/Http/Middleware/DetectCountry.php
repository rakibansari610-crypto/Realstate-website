<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DetectCountry
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('country_code') || empty(session('country_code'))) {
            $ip = $request->ip();
            
            if ($ip === '::1' || $ip === '127.0.0.1') {
                $ip = '103.76.253.131';
            }
            
            $location = geoip()->getLocation($ip);
            $countryName = $location->country; 
            if ($countryName == 'India') {
                session(['country_code' => 'IN']);
                // $data = session('country_code'); 
            }else{
                session(['country_code' => 'AED']);
                // $data1 = session(['country_code']);
            } 
        }
        return $next($request);
    }
}