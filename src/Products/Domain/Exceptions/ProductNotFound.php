<?php

namespace Src\Products\Domain\Exceptions;

use Src\Shared\Domain\Exceptions\DomainError;
use Src\Shared\Domain\ValueObjects\ValidUUID;

class ProductNotFound extends DomainError
{
    public function __construct(private readonly ValidUUID $id)
    {
        parent::__construct();
    }

    public function errorCode(): string
    {
        return 'product_not_found';
    }

    public function errorMessage(): string
    {
        return sprintf('The product <%s> was not found', $this->id->value());
    }
}
