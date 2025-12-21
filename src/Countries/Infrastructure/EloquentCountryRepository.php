<?php

namespace Src\Countries\Infrastructure;

use Src\Countries\Domain\CountryRepository;
use Src\Countries\Domain\Country;
use Src\Shared\Domain\ValueObjects\ValidUUID;
use Src\Shared\Domain\Criteria\Criteria;
use Src\Shared\Domain\SearchResult;
use Src\Shared\Infrastructure\Eloquent\CursorPaginator;
use Illuminate\Database\Eloquent\Builder;

class EloquentCountryRepository implements CountryRepository
{
    public function find(ValidUUID $id): ?Country
    {
        return Country::find($id->value());
    }

    public function search(Criteria $criteria): SearchResult
    {
        $query = Country::query();

        $this->applyFilters($query, $criteria->filters());

        $total = $query->count();

        CursorPaginator::apply(
            $query,
            $criteria->cursor(),
            $criteria->orderBy() ?? 'created_at',
            $criteria->orderType()
        );

        $query->limit($criteria->limit());

        return new SearchResult($query->get(), $total);
    }

    public function save(Country $country): void
    {
        $country->save();
    }

    public function delete(ValidUUID $id): void
    {
        Country::destroy($id->value());
    }

    private function applyFilters(Builder $query, array $filters): void
    {
        foreach ($filters as $field => $value) {
            if (is_array($value)) {
                 $query->whereIn($field, $value);
            } else {
                 $query->where($field, $value);
            }
        }
    }
}
