<?php

namespace Src\Products\Domain;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ProductEmbedding extends Model
{
    use HasUuids;

    protected $table = 'product_embeddings';

    public $timestamps = true;

    protected $fillable = [
        'product_id',
        'vector',
    ];

    protected $casts = [
        // 'vector' => Vector::class, // Removed until package is installed
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
