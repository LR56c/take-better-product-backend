<?php

namespace Src\Categories\Application;

use Src\Categories\Domain\CategoryRepository;
use Src\Shared\Domain\Criteria\Criteria;
use Src\Shared\Domain\SearchResult;

class SearchCategories
{
    public function __construct(
        private readonly CategoryRepository $repository
    ) {}

    public function execute(Criteria $criteria): SearchResult
    {
        return $this->repository->search($criteria);
    }
}
