<?php

namespace Src\Products\Domain;

use Src\Shared\Domain\ValueObjects\ValidUUID;
use Src\Shared\Domain\Criteria\Criteria;
use Src\Shared\Domain\SearchResult;

interface ProductRepository
{
    public function find(ValidUUID $id): ?Product;
    public function findByExternalId(ValidUUID $storeId, string $externalId): ?Product;
    public function search(Criteria $criteria): SearchResult;
    public function save(Product $product): void;
    public function delete(ValidUUID $id): void;
}
