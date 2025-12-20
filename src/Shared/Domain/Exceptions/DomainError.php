<?php

namespace Src\Shared\Domain\Exceptions;

use Exception;

abstract class DomainError extends Exception
{
    abstract public function errorCode(): string;

    abstract public function errorMessage(): string;

    public function __construct()
    {
        parent::__construct($this->errorMessage());
    }
}
