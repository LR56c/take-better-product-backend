<?php

namespace Src\Products\Infrastructure;

use Exception;
use Gemini\Client;
use Illuminate\Support\Facades\Log;
use Src\Products\Domain\ProductAiRepository;

class GeminiProductAiRepository implements ProductAiRepository
{
    private ?Client $client = null;

    public function __construct()
    {
        $apiKey = config('services.gemini.api_key');

        if (!empty($apiKey)) {
            $this->client = \Gemini::client($apiKey);
        }
    }

    public function generateEmbedding(string $text): ?array
    {
        if ($this->client === null) {
            Log::warning('Gemini API Key not configured');

            return null;
        }

        try {
            $response = $this->client->embeddingModel('models/text-embedding-004')
                ->embedContent($text);

            return $response->embedding->values;

        } catch (Exception $e) {
            Log::error('Gemini Connection Error', ['error' => $e->getMessage()]);

            return null;
        }
    }
}
