<?php

namespace Src\Stores\Domain;

use Src\Shared\Domain\ValueObjects\ValidUUID;
use Src\Shared\Domain\Criteria\Criteria;
use Src\Shared\Domain\SearchResult;

interface StoreRepository
{
    public function find(ValidUUID $id): ?Store;
    public function search(Criteria $criteria): SearchResult;
    public function save(Store $store): void;
    public function delete(ValidUUID $id): void;
}
