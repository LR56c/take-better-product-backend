<?php

namespace Src\Brands\Infrastructure;

use Src\Brands\Domain\Brand;
use Src\Brands\Domain\BrandRepository;
use Src\Shared\Domain\ValueObjects\ValidUUID;
use Src\Shared\Domain\Criteria\Criteria;
use Src\Shared\Domain\SearchResult;
use Src\Shared\Infrastructure\Eloquent\CursorPaginator;
use Illuminate\Database\Eloquent\Builder;

class EloquentBrandRepository implements BrandRepository
{
    public function find(ValidUUID $id): ?Brand
    {
        return Brand::find($id->value());
    }

    public function search(Criteria $criteria): SearchResult
    {
        $query = Brand::query();

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

    public function save(Brand $brand): void
    {
        $brand->save();
    }

    public function delete(ValidUUID $id): void
    {
        Brand::destroy($id->value());
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
