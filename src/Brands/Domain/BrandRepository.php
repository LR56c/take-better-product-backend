<?php

namespace Src\Brands\Domain;

use Src\Shared\Domain\ValueObjects\ValidUUID;
use Src\Shared\Domain\Criteria\Criteria;
use Src\Shared\Domain\SearchResult;

interface BrandRepository
{
    public function find(ValidUUID $id): ?Brand;
    public function search(Criteria $criteria): SearchResult;
    public function save(Brand $brand): void;
    public function delete(ValidUUID $id): void;
}
