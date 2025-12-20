<?php

namespace Src\Products\Application;

use Src\Products\Domain\Contracts\ProductRepository;
use Src\Shared\Domain\Criteria\Criteria;
use Src\Shared\Domain\SearchResult;

class SearchProducts
{
    public function __construct(
        private readonly ProductRepository $repository
    ) {}

    public function execute(Criteria $criteria): SearchResult
    {
        return $this->repository->search($criteria);
    }
}
