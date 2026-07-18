<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Check if the authenticated user has the required permission.
     *
     * Usage in route: middleware('permission:bookings,add')
     * First param  = module key (matching Role::availableModules())
     * Second param = action: view | add | edit | delete
     */
    public function handle(Request $request, Closure $next, string $module, string $action = 'view'): Response
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!$user->canDo($module, $action)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized.'], 403);
            }
            abort(403, 'Anda tidak memiliki izin untuk melakukan tindakan ini.');
        }

        return $next($request);
    }
}
