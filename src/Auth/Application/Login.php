<?php

namespace Src\Auth\Application;

use Src\Auth\Domain\AuthRepository;

class Login
{
    public function __construct(
        private readonly AuthRepository $repository
    ) {}

    public function execute(string $email, string $password): array
    {
        return $this->repository->login($email, $password);
    }
}
