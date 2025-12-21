<?php

namespace Src\Products\Domain;

interface ProductAiRepository
{
    /**
     * @param string $text
     * @return float[]|null
     */
    public function generateEmbedding(string $text): ?array;
}
