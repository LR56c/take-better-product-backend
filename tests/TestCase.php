<?php

namespace Tests;

use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function actingAsSupabaseUser(User $user, string $permission = 'admin')
    {
        $secret = config('services.supabase.jwt_secret');

        if (! $secret) {
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
            'exp' => time() + 3600,
        ];

        $token = JWT::encode($payload, $secret, 'HS256');

        $this->withHeader('Authorization', 'Bearer '.$token);

        return $this;
    }
}
