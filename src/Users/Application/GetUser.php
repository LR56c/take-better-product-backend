<?php

namespace Src\Users\Application;

use Src\Users\Domain\User;
use Src\Users\Domain\UserRepository;
use Src\Users\Domain\Exceptions\UserNotFound;
use Src\Shared\Domain\ValueObjects\ValidUUID;
use Src\Shared\Domain\ValueObjects\UUIDError;
use InvalidArgumentException;

class GetUser
{
    public function __construct(
        private readonly UserRepository $repository
    ) {}

    public function execute(string $id): User
    {
        $userId = ValidUUID::from($id);

        if ($userId instanceof UUIDError) {
            throw new InvalidArgumentException(sprintf('The user id <%s> is invalid', $id));
        }

        $user = $this->repository->find($userId);

        if (null === $user) {
            throw new UserNotFound($userId);
        }

        return $user;
    }
}
