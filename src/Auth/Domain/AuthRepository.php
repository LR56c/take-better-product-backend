<?php

namespace Src\Auth\Domain;

interface AuthRepository
{
    /**
     * @return array{access_token: string, refresh_token: string, user: object}
     */
    public function login(string $email, string $password): array;

    /**
     * @return array{user: object}
     */
    public function register(string $email, string $password): array;

    /**
     * @return array{user: object}
     */
    public function update(string $jwt, array $attributes): array;
}
