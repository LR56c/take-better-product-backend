<?php

namespace Src\Categories\Domain;

use Src\Shared\Domain\ValueObjects\ValidUUID;
use Src\Shared\Domain\Criteria\Criteria;
use Src\Shared\Domain\SearchResult;

interface CategoryRepository
{
    public function find(ValidUUID $id): ?Category;
    public function search(Criteria $criteria): SearchResult;
    public function save(Category $category): void;
    public function delete(ValidUUID $id): void;
}
