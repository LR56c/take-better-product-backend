<?php

namespace Src\Categories\Domain;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Src\Products\Domain\Product;
use Src\Stores\Domain\Store;
use Src\Stores\Domain\StoreCategory;

class Category extends Model
{
    use HasUuids;

    protected $table = 'categories';

    protected $fillable = [
        'name',
        'slug',
        'parent_id',
    ];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id', 'id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id', 'id');
    }

    public function stores()
    {
        return $this->belongsToMany(Store::class, 'store_categories')
                    ->using(StoreCategory::class) // Use the pivot model logic
                    ->withPivot('url', 'is_active')
                    ->withTimestamps();
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
