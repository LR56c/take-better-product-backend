<?php

namespace Src\Stores\Domain;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Src\Countries\Domain\Country;
use Src\Categories\Domain\Category;
use Src\Products\Domain\Product;

class Store extends Model
{
    use HasUuids;

    protected $table = 'stores';

    protected $fillable = [
        'country_id',
        'name',
        'url',
        'thumbnail',
        'type',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'store_categories')
                    ->withPivot('url', 'is_active')
                    ->withTimestamps();
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
