<?php

namespace Src\Products\Domain;

use Illuminate\Database\Eloquent\Collection;
use Src\Shared\Domain\Criteria\Criteria;

interface ProductRepository
{
    public function find(string $id): ?Product;
    public function search(Criteria $criteria): Collection;
    public function save(Product $product): void;
    public function delete(string $id): void;
}
