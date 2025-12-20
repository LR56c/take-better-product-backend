<?php

namespace Src\Categories\Domain\Exceptions;

use Src\Shared\Domain\ValueObjects\ValidUUID;
use Src\Shared\Domain\Exceptions\DomainError;

class CategoryNotFound extends DomainError
{
    public function __construct(private readonly ValidUUID $id)
    {
        parent::__construct();
    }

    public function errorCode(): string
    {
        return 'category_not_found';
    }

    public function errorMessage(): string
    {
        return sprintf('The category <%s> was not found', $this->id->value());
    }
}
