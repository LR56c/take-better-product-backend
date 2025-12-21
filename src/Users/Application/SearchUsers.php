<?php

namespace Src\Users\Application;

use Src\Shared\Domain\Criteria\Criteria;
use Src\Shared\Domain\SearchResult;
use Src\Users\Domain\UserRepository;

class SearchUsers
{
    public function __construct(
        private readonly UserRepository $repository
    ) {}

    public function execute(Criteria $criteria): SearchResult
    {
        return $this->repository->search($criteria);
    }
}
