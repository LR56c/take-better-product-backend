<?php

namespace Src\Shared\Domain\Criteria;

class Criteria
{
    public function __construct(
        private readonly array $filters = [],
        private readonly ?string $orderBy = 'created_at',
        private readonly string $orderType = 'DESC',
        private readonly int $limit = 10,
        private readonly ?string $cursor = null
    ) {}

    public function filters(): array
    {
        return $this->filters;
    }

    public function orderBy(): ?string
    {
        return $this->orderBy;
    }

    public function orderType(): string
    {
        return $this->orderType;
    }

    public function limit(): int
    {
        return $this->limit;
    }

    public function cursor(): ?string
    {
        return $this->cursor;
    }
}
