<?php

namespace Src\Brands\Domain;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Src\Products\Domain\Product;

class Brand extends Model
{
    use HasUuids;

    protected $table = 'brands';

    protected $fillable = [
        'name',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
