<?php

namespace Src\Brands\Domain;

use Illuminate\Database\Eloquent\Collection;
use Src\Shared\Domain\Criteria\Criteria;
use Src\Shared\Domain\ValueObjects\ValidUUID;

interface BrandRepository
{
    public function find(ValidUUID $id): ?Brand;
    public function search(Criteria $criteria): Collection;
    public function save(Brand $brand): void;
    public function delete(ValidUUID $id): void;
}
