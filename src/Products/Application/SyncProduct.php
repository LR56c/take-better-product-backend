<?php

namespace Src\Products\Application;

use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Src\Products\Domain\Product;
use Src\Products\Domain\ProductEmbedding;
use Src\Products\Domain\ProductEmbeddingRepository;
use Src\Products\Domain\ProductRepository;
use Src\Shared\Domain\ValueObjects\UUIDError;
use Src\Shared\Domain\ValueObjects\ValidUUID;

class SyncProduct
{
    public function __construct(
        private readonly ProductRepository $repository,
        private readonly GenerateEmbedding $generateEmbedding,
        private readonly ProductEmbeddingRepository $embeddingRepository
    ) {}

    public function execute(array $data): Product
    {
        $storeId = ValidUUID::from($data['store_id']);
        if ($storeId instanceof UUIDError) {
            throw new InvalidArgumentException(sprintf('The store id <%s> is invalid', $data['store_id']));
        }

        $existingProduct = $this->repository->findByExternalId($storeId, $data['external_id']);

        return DB::transaction(function () use ($existingProduct, $data) {
            $product = $existingProduct ?? new Product;

            $images = $data['images'] ?? [];
            $newPrice = (float) $data['price'];

            $shouldRecordHistory = ! $existingProduct || (abs((float) $product->price - $newPrice) > 0.001);

            unset($data['images']);

            $product->fill($data);
            $product->last_scraped_at = now();

            $this->repository->save($product);

            if (! $existingProduct) {
                $embeddingText = $product->title.' '.($product->description ?? '');
                $vector = $this->generateEmbedding->execute($embeddingText);

                if ($vector) {
                    $embedding = new ProductEmbedding([
                        'product_id' => $product->id,
                        'vector' => $vector,
                    ]);
                    $this->embeddingRepository->save($embedding);
                }
            }

            $product->images()->delete();
            if (! empty($images)) {
                $this->saveImages($product, $images);
            }

            if ($shouldRecordHistory) {
                $product->priceHistories()->create([
                    'price' => $newPrice,
                    'recorded_at' => now(),
                ]);
            }

            return $product->load(['store', 'brand', 'category', 'images', 'embedding']);
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
