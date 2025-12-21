<?php

namespace Src\Products\Domain;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

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
