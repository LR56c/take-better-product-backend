<?php

namespace Src\Stores\Domain;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Src\Categories\Domain\Category;

class StoreCategory extends Model
{
    use HasUuids;

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

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
