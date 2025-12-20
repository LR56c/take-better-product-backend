<?php

namespace Src\Products\Domain;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Store;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Product extends Model
{
    use HasUuids;

    protected $table = 'products';

    protected $fillable = [
        'store_id',
        'brand_id',
        'category_id',
        'external_id',
        'url',
        'title',
        'description',
        'price',
        'currency',
        'additional_data',
        'last_scraped_at',
    ];

    protected $casts = [
        'additional_data' => 'array',
        'last_scraped_at' => 'datetime',
        'price' => 'decimal:2',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function embedding()
    {
        return $this->hasOne(ProductEmbedding::class);
    }

    public function priceHistories()
    {
        return $this->hasMany(PriceHistory::class);
    }
}
