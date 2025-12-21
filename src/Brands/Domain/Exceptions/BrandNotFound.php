<?php

namespace Src\Brands\Domain\Exceptions;

use Src\Shared\Domain\Exceptions\DomainError;
use Src\Shared\Domain\ValueObjects\ValidUUID;

class BrandNotFound extends DomainError
{
    public function __construct(private readonly ValidUUID $id)
    {
        parent::__construct();
    }

    public function errorCode(): string
    {
        return 'brand_not_found';
    }

    public function errorMessage(): string
    {
        return sprintf('The brand <%s> was not found', $this->id->value());
    }
}
