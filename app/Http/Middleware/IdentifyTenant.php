<?php

namespace App\Http\Middleware;

use App\Tenancy\TenantManager;
use Closure;
use Illuminate\Http\Request;

class IdentifyTenant
{
    public function __construct(private TenantManager $tenants) {}

    public function handle(Request $request, Closure $next)
    {
        $tenant = $this->tenants->resolveFromHost($request->getHost());
        $this->tenants->configure($tenant);

        return $next($request);
    }
}
