<?php

namespace Src\Products\Domain;

use Illuminate\Database\Eloquent\Collection;
use Src\Shared\Domain\Criteria\Criteria;
use Src\Shared\Domain\SearchResult;
use Src\Shared\Domain\ValueObjects\ValidUUID;

interface ProductRepository
{
    public function find(ValidUUID $id): ?Product;

    public function findByIds(array $ids): Collection;

    public function findByExternalId(ValidUUID $storeId, string $externalId): ?Product;

    public function search(Criteria $criteria): SearchResult;

    public function save(Product $product): void;

    public function delete(ValidUUID $id): void;
}
