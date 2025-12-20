<?php

namespace Src\Stores\Infrastructure;

use Illuminate\Database\Eloquent\Builder;
use Src\Shared\Domain\Criteria\Criteria;
use Src\Shared\Domain\SearchResult;
use Src\Shared\Domain\ValueObjects\ValidUUID;
use Src\Shared\Infrastructure\Eloquent\CursorPaginator;
use Src\Stores\Domain\Store;
use Src\Stores\Domain\StoreRepository;

class EloquentStoreRepository implements StoreRepository
{
    public function find(ValidUUID $id): ?Store
    {
        return Store::find($id->value());
    }

    public function search(Criteria $criteria): SearchResult
    {
        $query = Store::query();

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

    public function save(Store $store): void
    {
        $store->save();
    }

    public function delete(ValidUUID $id): void
    {
        Store::destroy($id->value());
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
