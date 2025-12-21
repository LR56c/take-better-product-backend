<?php

namespace Src\Users\Domain;

use Src\Shared\Domain\Criteria\Criteria;
use Src\Shared\Domain\SearchResult;
use Src\Shared\Domain\ValueObjects\ValidUUID;

interface UserRepository
{
    public function find(ValidUUID $id): ?User;

    public function search(Criteria $criteria): SearchResult;

    public function save(User $user): void;
}
