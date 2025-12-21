<?php

namespace Src\Brands\Application;

use Src\Brands\Domain\BrandRepository;
use Src\Shared\Domain\Criteria\Criteria;
use Src\Shared\Domain\SearchResult;

class SearchBrands
{
    public function __construct(
        private readonly BrandRepository $repository
    ) {}

    public function execute(Criteria $criteria): SearchResult
    {
        return $this->repository->search($criteria);
    }
}
