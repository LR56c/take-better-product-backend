<?php

namespace Src\Products\Domain;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ProductImage extends Model
{
    use HasUuids;

    protected $table = 'product_images';

    protected $fillable = [
        'product_id',
        'image_url',
        'main',
    ];

    protected $casts = [
        'main' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
