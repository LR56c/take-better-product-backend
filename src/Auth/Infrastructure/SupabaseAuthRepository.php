<?php

namespace Src\Auth\Infrastructure;

use PHPSupabase\Service;
use Src\Auth\Domain\AuthRepository;

class SupabaseAuthRepository implements AuthRepository
{
    private Service $supabase;

    public function __construct()
    {
        $this->supabase = new Service(
            env('SUPABASE_KEY'),
            env('SUPABASE_URL')
        );
    }

    public function login(string $email, string $password): array
    {
        $auth = $this->supabase->createAuth();
        $auth->signInWithEmailAndPassword($email, $password);

        return (array) $auth->data();
    }

    public function register(string $email, string $password): array
    {
        $auth = $this->supabase->createAuth();
        $auth->createUserWithEmailAndPassword($email, $password);

        return (array) $auth->data();
    }

    public function update(string $jwt, array $attributes): array
    {
        $auth = $this->supabase->createAuth();

        $email = $attributes['email'] ?? null;
        $password = $attributes['password'] ?? null;
        $metaData = $attributes['data'] ?? [];

        $auth->updateUser($jwt, $email, $password, $metaData);

        return (array) $auth->data();
    }
}
