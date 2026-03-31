<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Middleware for Admin portal requests.
 * When no Bearer token is present, tries to authenticate via:
 * 1. X-Admin-User-Id header (set by nginx from Node session)
 * 2. Cookie-based session passthrough
 * This allows admin pages to call Laravel API without requiring JWT.
 */
class AdminSessionMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // If already has bearer token, skip
        if ($request->bearerToken()) {
            return $next($request);
        }

        // Check for admin user ID header (set by nginx proxy)
        $adminUserId = $request->header('X-Admin-User-Id');
        if ($adminUserId) {
            $user = DB::table('users')->find($adminUserId);
            if ($user && in_array($user->role ?? '', ['admin', 'super_admin'])) {
                $request->merge([
                    'auth_user' => (array) $user,
                    'auth_user_id' => $user->id,
                    'auth_user_roles' => [$user->role],
                    'auth_user_permissions' => ['*'],
                ]);
                return $next($request);
            }
        }

        // Fallback: check if request comes from admin domain (trusted internal)
        $referer = $request->header('Referer', '');
        $origin = $request->header('Origin', '');
        $isAdmin = str_contains($referer, 'admin.mahamexpo.sa') || str_contains($origin, 'admin.mahamexpo.sa');
        
        if ($isAdmin) {
            // Grant admin access for admin-domain requests (secured by nginx)
            $admin = DB::table('users')->where('role', 'admin')->first()
                   ?? DB::table('users')->where('role', 'super_admin')->first();
            
            if ($admin) {
                $request->merge([
                    'auth_user' => (array) $admin,
                    'auth_user_id' => $admin->id,
                    'auth_user_roles' => [$admin->role ?? 'admin'],
                    'auth_user_permissions' => ['*'],
                ]);
                return $next($request);
            }
        }

        return $next($request);
    }
}
