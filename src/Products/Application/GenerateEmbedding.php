<?php

namespace Src\Products\Application;

use Illuminate\Support\Facades\Log;
use Src\Products\Domain\ProductAiRepository;

class GenerateEmbedding
{
    public function __construct(
        private readonly ProductAiRepository $aiRepository
    ) {}

    public function execute(string $text): ?array
    {
        Log::info('[GenerateEmbedding] Generating vector for text:', ['text' => $text]);

        $vector = $this->aiRepository->generateEmbedding($text);

        if ($vector) {
            Log::info('[GenerateEmbedding] Vector generated successfully.');
        } else {
            Log::warning('[GenerateEmbedding] Failed to generate vector.');
        }

        return $vector;
    }
}
