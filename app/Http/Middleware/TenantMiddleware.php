<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\Tenant;
use Illuminate\Support\Facades\Config;

class TenantMiddleware
{
    public function handle($request, Closure $next)
    {
        $host = $request->getHost();

        $tenant = Tenant::where('custom_domain', $host)
                ->first();

        if (!$tenant) {
            abort(403, 'Shop not found.');
        }

        // Dynamically configure the tenant's DB connection
        Config::set('database.connections.tenant', [
            'driver' => 'mysql',
            'host' => env('DB_TENANT_HOST', '127.0.0.1'), // fallback to env or use a default
            'database' => $tenant->database_name,
            'username' => $tenant->database_username,
            'password' => $tenant->database_password,
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ]);

        // Set default connection for this request
        DB::setDefaultConnection('tenant');

        // Optionally, share the tenant instance globally
        app()->instance('currentTenant', $tenant);

        return $next($request);
    }
}