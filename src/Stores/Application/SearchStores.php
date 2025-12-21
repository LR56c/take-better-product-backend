<?php

namespace Src\Stores\Application;

use Src\Stores\Domain\StoreRepository;
use Src\Shared\Domain\Criteria\Criteria;
use Src\Shared\Domain\SearchResult;

class SearchStores
{
    public function __construct(
        private readonly StoreRepository $repository
    ) {}

    public function execute(Criteria $criteria): SearchResult
    {
        return $this->repository->search($criteria);
    }
}
