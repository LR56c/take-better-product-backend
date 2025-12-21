<?php

namespace Src\Products\Domain;

use Src\Products\Domain\ProductEmbedding;

interface ProductEmbeddingRepository
{
    public function save(ProductEmbedding $embedding): void;

    /**
     * @param float[] $vector
     * @param int $limit
     * @return array<int, string> List of product IDs ordered by similarity
     */
    public function searchSimilar(array $vector, int $limit): array;
}
