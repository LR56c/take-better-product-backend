<?php

namespace Src\Categories\Infrastructure;

use Illuminate\Database\Eloquent\Builder;
use Src\Categories\Domain\Category;
use Src\Categories\Domain\CategoryRepository;
use Src\Shared\Domain\Criteria\Criteria;
use Src\Shared\Domain\SearchResult;
use Src\Shared\Domain\ValueObjects\ValidUUID;
use Src\Shared\Infrastructure\Eloquent\CursorPaginator;

class EloquentCategoryRepository implements CategoryRepository
{
    public function find(ValidUUID $id): ?Category
    {
        return Category::find($id->value());
    }

    public function search(Criteria $criteria): SearchResult
    {
        $query = Category::query();

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

    public function save(Category $category): void
    {
        $category->save();
    }

    public function delete(ValidUUID $id): void
    {
        Category::destroy($id->value());
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
