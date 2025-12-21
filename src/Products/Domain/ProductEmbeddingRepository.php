<?php

namespace Src\Products\Domain;

interface ProductEmbeddingRepository
{
    public function save(ProductEmbedding $embedding): void;

    /**
     * @param  float[]  $vector
     * @return array<int, string> List of product IDs ordered by similarity
     */
    public function searchSimilar(array $vector, int $limit): array;
}
