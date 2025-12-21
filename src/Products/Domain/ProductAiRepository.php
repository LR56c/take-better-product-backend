<?php

namespace Src\Products\Domain;

interface ProductAiRepository
{
    /**
     * @return float[]|null
     */
    public function generateEmbedding(string $text): ?array;
}
