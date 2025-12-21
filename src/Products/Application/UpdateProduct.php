<?php

namespace Src\Products\Application;

use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Src\Products\Domain\Exceptions\ProductNotFound;
use Src\Products\Domain\Product;
use Src\Products\Domain\ProductRepository;
use Src\Shared\Domain\ValueObjects\UUIDError;
use Src\Shared\Domain\ValueObjects\ValidUUID;

class UpdateProduct
{
    public function __construct(
        private readonly ProductRepository $repository
    ) {}

    public function execute(string $id, array $data): Product
    {
        $productId = ValidUUID::from($id);

        if ($productId instanceof UUIDError) {
            throw new InvalidArgumentException(sprintf('The product id <%s> is invalid', $id));
        }

        $product = $this->repository->find($productId);

        if ($product === null) {
            throw new ProductNotFound($productId);
        }

        return DB::transaction(function () use ($product, $data) {
            // Check if price changed to record history
            if (isset($data['price']) && (float) $data['price'] !== (float) $product->price) {
                $product->priceHistories()->create([
                    'price' => $data['price'],
                    'recorded_at' => now(),
                ]);
            }

            // Handle Images Update (Full replacement strategy for simplicity, or append)
            if (isset($data['images'])) {
                // Option A: Delete old and create new (simplest for sync)
                $product->images()->delete();
                $product->images()->createMany($data['images']);
            }

            // Filter nulls and update product
            $data = array_filter($data, fn ($value) => $value !== null);
            // Remove relations from data array before filling product
            unset($data['images']);

            $product->fill($data);
            $this->repository->save($product);

            return $product->load(['images', 'priceHistories']);
        });
    }
}
