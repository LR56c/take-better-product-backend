<?php

namespace Src\Products\Infrastructure;

use Src\Products\Domain\ProductRepository;
use Src\Products\Domain\Product;
use Src\Shared\Domain\ValueObjects\ValidUUID;
use Src\Shared\Domain\Criteria\Criteria;
use Src\Shared\Domain\SearchResult;
use Src\Shared\Infrastructure\Eloquent\CursorPaginator;
use Illuminate\Database\Eloquent\Builder;

class EloquentProductRepository implements ProductRepository
{
    public function find(ValidUUID $id): ?Product
    {
        return Product::find($id->value());
    }

    public function findByExternalId(ValidUUID $storeId, string $externalId): ?Product
    {
        return Product::where('store_id', $storeId->value())
                      ->where('external_id', $externalId)
                      ->first();
    }

    public function search(Criteria $criteria): SearchResult
    {
        $query = Product::query();

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

    public function save(Product $product): void
    {
        $product->save();
    }

    public function delete(ValidUUID $id): void
    {
        Product::destroy($id->value());
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
