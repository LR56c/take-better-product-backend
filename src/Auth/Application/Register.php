<?php

namespace Src\Auth\Application;

use Src\Auth\Domain\AuthRepository;

class Register
{
    public function __construct(
        private readonly AuthRepository $repository
    ) {}

    public function execute(string $email, string $password): array
    {
        return $this->repository->register($email, $password);
    }
}
