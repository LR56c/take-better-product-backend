<?php

namespace Src\Countries\Domain;

use Src\Shared\Domain\ValueObjects\ValidUUID;
use Src\Shared\Domain\Criteria\Criteria;
use Src\Shared\Domain\SearchResult;

interface CountryRepository
{
    public function find(ValidUUID $id): ?Country;
    public function search(Criteria $criteria): SearchResult;
    public function save(Country $country): void;
    public function delete(ValidUUID $id): void;
}
