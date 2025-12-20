<?php

namespace Src\Shared\Domain\ValueObjects;

use Illuminate\Support\Str;
use Stringable;

final readonly class ValidUUID implements Stringable
{
    private string $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function from(string $id): self|UUIDError
    {
        return Str::isUuid($id)
            ? new self($id)
            : UUIDError::InvalidFormat;
    }

    public static function create(): self
    {
        return new self(Str::uuid()->toString());
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(ValidUUID $other): bool
    {
        return $this->value === $other->value();
    }

    public function __toString(): string
    {
        return $this->value();
    }
}
