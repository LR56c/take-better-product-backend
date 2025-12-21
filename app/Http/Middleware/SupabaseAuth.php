<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SupabaseAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (! $token) {
            return response()->json(['error' => 'Token not provided'], 401);
        }

        try {
            $secret = config('services.supabase.jwt_secret');

            if (! $secret) {
                throw new \Exception('Supabase JWT Secret not configured');
            }

            $decoded = JWT::decode($token, new Key($secret, 'HS256'));

            $userId = $decoded->sub;

            $user = User::find($userId);

            if (! $user) {
                return response()->json(['error' => 'User not found or not synced'], 401);
            }

            // Store JWT claims as an object
            $user->jwt_claims = $decoded;

            Auth::login($user);

        } catch (\Throwable $e) {
            return response()->json(['error' => 'Invalid token: '.$e->getMessage()], 401);
        }

        return $next($request);
    }
}
