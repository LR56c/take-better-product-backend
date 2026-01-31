<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role  The required role/permission
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if (! $user || ! isset($user->jwt_claims)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $claims = $user->jwt_claims;

        $userMetadata = $claims->user_metadata ?? (object) [];
        $appMetadata = $claims->app_metadata ?? (object) [];

        $userPermission = $userMetadata->permission ?? $appMetadata->permission ?? null;

        if ($userPermission !== $role) {
            return response()->json(['error' => 'Forbidden: Insufficient permissions'], 403);
        }

        return $next($request);
    }
}
