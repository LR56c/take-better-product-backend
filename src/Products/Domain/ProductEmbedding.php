<?php

namespace Src\Products\Domain;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Pgvector\Laravel\Vector;

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
        'vector' => Vector::class,
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
