<?php

namespace Src\Shared\Domain;

use Illuminate\Database\Eloquent\Collection;

class SearchResult
{
    public function __construct(
        private readonly Collection $items,
        private readonly int $total
    ) {}

    public function items(): Collection
    {
        return $this->items;
    }

    public function total(): int
    {
        return $this->total;
    }
}
