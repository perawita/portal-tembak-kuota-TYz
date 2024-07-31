<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\MaintenanceSetting;

class CheckMaintenanceStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $pageName = $request->route()->getName();

        $maintenanceSetting = MaintenanceSetting::where('page_name', $pageName)->first();

        if ($maintenanceSetting && $maintenanceSetting->is_open === 0) {
            return redirect()->route('platform.maintenance');
        } else {
            return $next($request);
        }
    }
}
