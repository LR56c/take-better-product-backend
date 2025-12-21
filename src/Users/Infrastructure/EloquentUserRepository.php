<?php

namespace Src\Users\Infrastructure;

use Illuminate\Database\Eloquent\Builder;
use Src\Shared\Domain\Criteria\Criteria;
use Src\Shared\Domain\SearchResult;
use Src\Shared\Domain\ValueObjects\ValidUUID;
use Src\Shared\Infrastructure\Eloquent\CursorPaginator;
use Src\Users\Domain\User;
use Src\Users\Domain\UserRepository;

class EloquentUserRepository implements UserRepository
{
    public function find(ValidUUID $id): ?User
    {
        return User::find($id->value());
    }

    public function search(Criteria $criteria): SearchResult
    {
        $query = User::query();

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

    public function save(User $user): void
    {
        $user->save();
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
