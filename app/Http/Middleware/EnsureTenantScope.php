<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Firewall;
use App\Models\Company;

class EnsureTenantScope
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // Global admins can see everything
        if ($user && $user->role === 'admin') {
            return $next($request);
        }

        // For non-admin users, restrict access to their company's resources
        if ($user && $user->company_id) {
            // If accessing a firewall, ensure it belongs to their company
            $firewallParam = $request->route('firewall');
            if ($firewallParam) {
                // Handle both model-bound instances and IDs
                $firewall = $firewallParam instanceof Firewall
                    ? $firewallParam
                    : Firewall::find($firewallParam);

                if (!$firewall || $firewall->company_id !== $user->company_id) {
                    file_put_contents('/tmp/debug_middleware.log', "EnsureTenantScope: Firewall access denied\n", FILE_APPEND);
                    abort(403, 'Unauthorized access to this firewall.');
                }
            }

            // If accessing a company, ensure it's their company
            $companyParam = $request->route('company');
            if ($companyParam) {
                $companyId = $companyParam instanceof Company
                    ? $companyParam->id
                    : $companyParam;

                if ($companyId != $user->company_id) {
                    file_put_contents('/tmp/debug_middleware.log', "EnsureTenantScope: Company access denied\n", FILE_APPEND);
                    abort(403, 'Unauthorized access to this company.');
                }
            }
        }

        file_put_contents('/tmp/debug_middleware.log', "EnsureTenantScope: Fallthrough (Access Granted)\n", FILE_APPEND);
        return $next($request);
    }
}
