<?php

return [
    // Your SaaS base (the domain under which org subdomains live)
    'base_domain'     => env('TENANCY_BASE_DOMAIN', 'visitorsuite.in'),

    // Central/marketing domains where tenancy should NOT resolve
    'central_domains' => array_filter(array_map('trim', explode(',', env('TENANCY_CENTRAL_DOMAINS', 'visitorsuite.in,www.visitorsuite.in')))),

    // Subdomains you don’t want to treat as tenants
    'reserved'        => array_filter(array_map('trim', explode(',', env('TENANCY_RESERVED_SUBS', 'www,app,admin')))),
];
