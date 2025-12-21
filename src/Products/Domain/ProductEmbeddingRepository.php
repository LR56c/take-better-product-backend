<?php

namespace Src\Products\Domain;

use Src\Products\Domain\ProductEmbedding;

interface ProductEmbeddingRepository
{
    public function save(ProductEmbedding $embedding): void;
}
