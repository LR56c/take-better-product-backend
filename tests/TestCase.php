<?php

namespace Tests;

use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  string|null  $guard  If a string is provided, it's used as the permission/role for the JWT.
     * @return $this
     */
    public function actingAs(Authenticatable $user, $guard = null)
    {
        if (is_string($guard)) {
            $secret = config('services.supabase.jwt_secret');

            $payload = [
                'sub' => $user->getAuthIdentifier(),
                'email' => $user->email,
                'user_metadata' => [
                    'permission' => $guard,
                ],
                'iat' => time(),
                'exp' => time() + 3600,
            ];

            $token = JWT::encode($payload, $secret, 'HS256');

            $this->withHeader('Authorization', 'Bearer '.$token);
        }

        return parent::actingAs($user);
    }
}
