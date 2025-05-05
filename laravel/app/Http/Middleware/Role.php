<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Role
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, \Closure $next, ...$roles): Response
    {
        $user = Auth::user();

        // Ensure the user is authenticated
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Check if the user has any of the specified roles
        if (!$user->hasAnyRole($roles)) {
            return response()->json(['message' => 'You do not have the required permissions'], 401);
        }

        return $next($request);
    }
}
