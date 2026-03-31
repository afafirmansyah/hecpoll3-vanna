<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Check if user has access restrictions
        $userAccess = $user->userAccess;
        
        if ($userAccess && ($userAccess->station_ids || $userAccess->terminal_ids)) {
            // User has access restrictions - check if they have any access
            if (empty($userAccess->station_ids) && empty($userAccess->terminal_ids)) {
                abort(403, 'No access granted to any stations or terminals');
            }
        }

        return $next($request);
    }
}