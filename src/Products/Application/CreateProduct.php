<?php

namespace Src\Products\Application;

use Illuminate\Support\Facades\DB;
use Src\Products\Domain\Product;
use Src\Products\Domain\ProductRepository;

class CreateProduct
{
    public function __construct(
        private readonly ProductRepository $repository
    ) {}

    public function execute(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            $product = new Product();

            // Extract relations data
            $images = $data['images'] ?? [];
            $price = $data['price'];

            // Clean data for product model
            unset($data['images']);

            $product->fill($data);
            $this->repository->save($product);

            // Handle Images
            if (!empty($images)) {
                $product->images()->createMany($images);
            }

            // Handle Initial Price History
            $product->priceHistories()->create([
                'price' => $price,
                'recorded_at' => now(),
            ]);

            return $product->load(['images', 'priceHistories']);
        });
    }
}
