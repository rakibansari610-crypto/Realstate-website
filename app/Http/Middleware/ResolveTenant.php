<?php

namespace App\Http\Middleware;

use App\Models\Organization;
use App\Tenancy\Tenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ResolveTenant
{
    public function handle(Request $request, Closure $next)
    {
        $host     = $request->getHost();   // e.g. kgninfotech.localhost
        $base     = config('tenancy.base_domain');
        $centrals = config('tenancy.central_domains', []);
        $reserved = config('tenancy.reserved', []);

        // ----- Force URL & asset() root ONCE (include subfolder for local) -----
        $scheme   = $request->isSecure() ? 'https://' : 'http://';
        $basePath = rtrim($request->getBasePath(), '/');       // "/visitorsuite/public" or ""
        $root     = rtrim($scheme . $host . $basePath, '/');       // e.g. http://kgninfotech.localhost/visitorsuite/public

        URL::forceRootUrl($root);
        if ($request->isSecure()) {
            URL::forceScheme('https');
        }
        // make asset() generate from same root
        config(['app.asset_url' => $root]);

        // ----- Central domains: skip tenancy -----
        if (in_array($host, $centrals, true)) {
            Tenant::forget();
            return $next($request);
        }

        // ----- Resolve tenant by custom domain or subdomain -----
        $org = Organization::where('custom_domain', $host)->first();

        if (!$org && $base && Str::endsWith($host, '.' . $base)) {
            $sub = Str::before($host, '.' . $base);
            if ($sub !== '' && !in_array($sub, $reserved, true)) {
                $org = Organization::where('subdomain', $sub)->first();
            }
        }
        

        if (!$org) {
            throw new NotFoundHttpException('Organization not found for host: ' . $host);
        }

        Tenant::set($org);
        $defaults = [
            'colors' => [
                'primary'  => '#000',
                'header'   => '#ffffff',
                'login_bg' => '#1e2a78',
                'button'   => '#764ba2',
            ],
            'text'   => ['login_h1' => $org->name],
        ];
        $theme = array_replace_recursive($defaults, (array) $org->theme);
        view()->share('brand', $theme);
        view()->share('currentOrg', $org);

        // IMPORTANT: do NOT call forceRootUrl() again here.
        /*URL::defaults([
            'tenant' => $org->subdomain,   // or $org->custom_domain if that’s what you use
        ]);*/

        return $next($request);
    }
}
