<?php

namespace Src\Products\Application;

use Illuminate\Support\Facades\DB;
use Src\Products\Domain\Product;
use Src\Products\Domain\ProductRepository;
use Src\Shared\Domain\ValueObjects\ValidUUID;
use Src\Shared\Domain\ValueObjects\UUIDError;
use InvalidArgumentException;

class SyncProduct
{
    public function __construct(
        private readonly ProductRepository $repository
    ) {}

    public function execute(array $data): Product
    {
        // Validate Store ID
        $storeId = ValidUUID::from($data['store_id']);
        if ($storeId instanceof UUIDError) {
            throw new InvalidArgumentException(sprintf('The store id <%s> is invalid', $data['store_id']));
        }

        // Try to find existing product
        $existingProduct = $this->repository->findByExternalId($storeId, $data['external_id']);

        return DB::transaction(function () use ($existingProduct, $data) {
            $product = $existingProduct ?? new Product();

            // Extract relations
            $images = $data['images'] ?? [];
            $newPrice = (float) $data['price'];

            // Logic for Price History
            $shouldRecordHistory = false;
            if (!$existingProduct) {
                // New product: always record initial price
                $shouldRecordHistory = true;
            } elseif (abs((float)$product->price - $newPrice) > 0.001) { // Float comparison
                // Existing product: record only if price changed
                $shouldRecordHistory = true;
            }

            // Clean data
            unset($data['images']);

            // Fill/Update attributes
            $product->fill($data);
            $product->last_scraped_at = now(); // Always update scraped timestamp

            $this->repository->save($product);

            // Handle Images (Full Sync)
            if (!empty($images)) {
                $product->images()->delete(); // Remove old images
                $product->images()->createMany($images);
            }

            // Handle Price History
            if ($shouldRecordHistory) {
                $product->priceHistories()->create([
                    'price' => $newPrice,
                    'recorded_at' => now(),
                ]);
            }

            return $product->load(['store', 'brand', 'category', 'images']);
        });
    }
}
