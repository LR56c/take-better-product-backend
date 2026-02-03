<?php

namespace Src\Products\Application;

use Illuminate\Support\Facades\DB;
use Src\Products\Domain\Product;
use Src\Products\Domain\ProductEmbedding;
use Src\Products\Domain\ProductEmbeddingRepository;
use Src\Products\Domain\ProductRepository;

class CreateProduct
{
    public function __construct(
        private readonly ProductRepository $repository,
        private readonly GenerateEmbedding $generateEmbedding,
        private readonly ProductEmbeddingRepository $embeddingRepository
    ) {}

    public function execute(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            $product = new Product;

            $images = $data['images'] ?? [];
            $price = $data['price'];

            unset($data['images']);

            $product->fill($data);
            $this->repository->save($product);

            $embeddingText = $product->title.' '.($product->description ?? '');
            $vector = $this->generateEmbedding->execute($embeddingText);

            if ($vector) {
                $embedding = new ProductEmbedding([
                    'product_id' => $product->id,
                    'vector' => $vector,
                ]);
                $this->embeddingRepository->save($embedding);
            }

            if (! empty($images)) {
                $this->saveImages($product, $images);
            }

            $product->priceHistories()->create([
                'price' => $price,
                'recorded_at' => now(),
            ]);

            return $product->load(['images', 'priceHistories', 'embedding']);
        });
    }

    private function saveImages(Product $product, array $images): void
    {
        $hasMain = false;
        foreach ($images as $image) {
            if (! empty($image['main'])) {
                $hasMain = true;
                break;
            }
        }

        if (! $hasMain && count($images) > 0) {
            $images[0]['main'] = true;
        }

        $product->images()->createMany($images);
    }
}
