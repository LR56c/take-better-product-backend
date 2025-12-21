<?php

namespace Src\Users\Domain\Exceptions;

use Src\Shared\Domain\ValueObjects\ValidUUID;
use Src\Shared\Domain\Exceptions\DomainError;

class UserNotFound extends DomainError
{
    public function __construct(private readonly ValidUUID $id)
    {
        parent::__construct();
    }

    public function errorCode(): string
    {
        return 'user_not_found';
    }

    public function errorMessage(): string
    {
        return sprintf('The user <%s> was not found', $this->id->value());
    }
}
