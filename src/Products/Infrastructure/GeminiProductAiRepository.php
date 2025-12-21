<?php

namespace Src\Products\Infrastructure;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Src\Products\Domain\ProductAiRepository;

class GeminiProductAiRepository implements ProductAiRepository
{
    private string $apiKey;

    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/embedding-001:embedContent';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
    }

    public function generateEmbedding(string $text): ?array
    {
        if (empty($this->apiKey)) {
            Log::warning('Gemini API Key not configured');

            return null;
        }

        try {
            $response = Http::post("{$this->baseUrl}?key={$this->apiKey}", [
                'content' => [
                    'parts' => [
                        ['text' => $text],
                    ],
                ],
            ]);

            if ($response->failed()) {
                Log::error('Gemini API Error', ['body' => $response->body()]);

                return null;
            }

            $data = $response->json();

            return $data['embedding']['values'] ?? null;

        } catch (\Exception $e) {
            Log::error('Gemini Connection Error', ['error' => $e->getMessage()]);

            return null;
        }
    }
}
