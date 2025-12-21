<?php

namespace Src\Products\Infrastructure;

use Src\Products\Domain\ProductEmbedding;
use Src\Products\Domain\ProductEmbeddingRepository;

class EloquentProductEmbeddingRepository implements ProductEmbeddingRepository
{
    public function save(ProductEmbedding $embedding): void
    {
        $embedding->save();
    }
}
