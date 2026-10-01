<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    // Global middleware (optional, unchanged)
    protected $middleware = [
        // \App\Http\Middleware\TrustProxies::class,
        // ...
    ];

    // Groups (web/api)
    protected $middlewareGroups = [
        'web' => [
           \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
             \App\Http\Middleware\SetCountrySession::class,
            // (Optional) run tenant on ALL web routes:
            // \App\Http\Middleware\ResolveTenant::class,
        ],

        'api' => [
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    // ✅ Middleware aliases (Laravel 10/11)
    protected $middlewareAliases = [
       
        'verified'   => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        // ... your other aliases

        // <-- add your alias here:
        'tenant'     => \App\Http\Middleware\ResolveTenant::class,
    ];
}
