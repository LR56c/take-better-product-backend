<?php

namespace Src\Products\Application;

use Illuminate\Database\Eloquent\Collection;
use Src\Products\Domain\ProductEmbeddingRepository;
use Src\Products\Domain\ProductRepository;
use Src\Shared\Domain\SearchResult;

class SearchSimilarProducts
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly ProductEmbeddingRepository $embeddingRepository,
        private readonly GenerateEmbedding $generateEmbedding
    ) {}

    public function execute(string $queryText, int $limit = 10): SearchResult
    {
        $vector = $this->generateEmbedding->execute($queryText);

        if (! $vector) {
            return new SearchResult(new Collection, 0);
        }

        $similarResults = $this->embeddingRepository->searchSimilar($vector, $limit);

        if (empty($similarResults)) {
            return new SearchResult(new Collection, 0);
        }

        $productIds = array_column($similarResults, 'product_id');
        $orderMap = array_flip($productIds);

        $products = $this->productRepository->findByIds($productIds);

        $sortedProducts = $products->sortBy(function ($product) use ($orderMap) {
            return $orderMap[$product->id] ?? 999999;
        })->values();

        return new SearchResult($sortedProducts, $sortedProducts->count());
    }
}
