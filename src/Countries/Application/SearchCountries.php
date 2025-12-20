<?php

namespace Src\Countries\Application;

use Src\Countries\Domain\CountryRepository;
use Src\Shared\Domain\Criteria\Criteria;
use Src\Shared\Domain\SearchResult;

class SearchCountries
{
    public function __construct(
        private readonly CountryRepository $repository
    ) {}

    public function execute(Criteria $criteria): SearchResult
    {
        return $this->repository->search($criteria);
    }
}
