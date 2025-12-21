<?php

namespace Src\Stores\Domain\Exceptions;

use Src\Shared\Domain\Exceptions\DomainError;
use Src\Shared\Domain\ValueObjects\ValidUUID;

class StoreNotFound extends DomainError
{
    public function __construct(private readonly ValidUUID $id)
    {
        parent::__construct();
    }

    public function errorCode(): string
    {
        return 'store_not_found';
    }

    public function errorMessage(): string
    {
        return sprintf('The store <%s> was not found', $this->id->value());
    }
}
