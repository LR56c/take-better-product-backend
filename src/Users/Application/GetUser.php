<?php

namespace Src\Users\Application;

use InvalidArgumentException;
use Src\Shared\Domain\ValueObjects\UUIDError;
use Src\Shared\Domain\ValueObjects\ValidUUID;
use Src\Users\Domain\Exceptions\UserNotFound;
use Src\Users\Domain\User;
use Src\Users\Domain\UserRepository;

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

        if ($user === null) {
            throw new UserNotFound($userId);
        }

        return $user;
    }
}
