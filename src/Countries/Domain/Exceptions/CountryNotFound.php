<?php

namespace Src\Countries\Domain\Exceptions;

use Src\Shared\Domain\Exceptions\DomainError;
use Src\Shared\Domain\ValueObjects\ValidUUID;

class CountryNotFound extends DomainError
{
    public function __construct(private readonly ValidUUID $id)
    {
        parent::__construct();
    }

    public function errorCode(): string
    {
        return 'country_not_found';
    }

    public function errorMessage(): string
    {
        return sprintf('The country <%s> was not found', $this->id->value());
    }
}
