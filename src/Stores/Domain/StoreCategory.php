<?php

namespace Src\Stores\Domain;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\Pivot;

class StoreCategory extends Pivot
{
    use HasUuids;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true; // Required for Pivot models with UUIDs

    protected $table = 'store_categories';

    protected $fillable = [
        'store_id',
        'category_id',
        'url',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
