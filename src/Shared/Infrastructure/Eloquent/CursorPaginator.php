<?php

namespace Src\Shared\Infrastructure\Eloquent;

use Illuminate\Database\Eloquent\Builder;

class CursorPaginator
{
    public static function apply(Builder $query, ?string $cursorId, string $orderBy = 'created_at', string $orderType = 'desc'): void
    {
        $query->orderBy($orderBy, $orderType);

        if ($orderBy !== 'id') {
            $query->orderBy('id', 'asc');
        }

        if (! $cursorId) {
            return;
        }

        $modelClass = $query->getModel()::class;
        $cursorModel = $modelClass::find($cursorId);

        if (! $cursorModel) {
            return;
        }

        $operator = strtolower($orderType) === 'asc' ? '>' : '<';

        if ($orderBy === 'id') {
            $query->where('id', $operator, $cursorId);
        } else {
            $value = $cursorModel->$orderBy;

            $query->where(function ($q) use ($orderBy, $operator, $value, $cursorId) {
                $q->where($orderBy, $operator, $value)
                    ->orWhere(function ($subQ) use ($orderBy, $value, $cursorId) {
                        $subQ->where($orderBy, $value)
                            ->where('id', '>', $cursorId);
                    });
            });
        }
    }
}
