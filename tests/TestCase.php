<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Firebase\JWT\JWT;
use Illuminate\Contracts\Auth\Authenticatable;

abstract class TestCase extends BaseTestCase
{
    /**
     * Set the currently logged in user for the application using a Supabase JWT.
     *
     * @param  \App\Models\User  $user
     * @param  string  $permission
     * @return $this
     */
    public function actingAsSupabaseUser(User $user, string $permission = 'admin')
    {
        $secret = config('services.supabase.jwt_secret');

        if (!$secret) {
            $secret = 'dummy-secret-for-testing';
            config(['services.supabase.jwt_secret' => $secret]);
        }

        $payload = [
            'sub' => $user->id,
            'email' => $user->email,
            'user_metadata' => [
                'permission' => $permission,
            ],
            'iat' => time(),
            'exp' => time() + 3600, // Token valid for 1 hour
        ];

        $token = JWT::encode($payload, $secret, 'HS256');

        $this->withHeader('Authorization', 'Bearer ' . $token);

        return $this;
    }
}
