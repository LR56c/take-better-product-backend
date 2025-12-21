<?php

namespace Tests\Unit\Products\Application;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Src\Products\Application\SyncProduct;
use App\Models\Store;
use App\Models\Product;
use Tests\TestCase;

class SyncProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_new_product_if_not_exists()
    {
        // Arrange
        $useCase = $this->app->make(SyncProduct::class);
        $store = Store::factory()->create();

        $data = [
            'store_id' => $store->id,
            'external_id' => 'EXT-123',
            'title' => 'New Product',
            'price' => 1000,
            'url' => 'http://example.com/new',
            'currency' => 'USD',
        ];

        // Act
        $product = $useCase->execute($data);

        // Assert
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'title' => 'New Product'
        ]);

        $this->assertDatabaseHas('price_histories', [
            'product_id' => $product->id,
            'price' => 1000
        ]);
    }

    public function test_it_updates_existing_product_and_records_history()
    {
        // Arrange
        $useCase = $this->app->make(SyncProduct::class);
        $store = Store::factory()->create();

        $existingProduct = Product::factory()->create([
            'store_id' => $store->id,
            'external_id' => 'EXT-123',
            'price' => 500,
        ]);

        $data = [
            'store_id' => $store->id,
            'external_id' => 'EXT-123',
            'title' => 'Updated Title',
            'price' => 1000, // Price changed
            'url' => $existingProduct->url,
            'currency' => 'USD',
        ];

        // Act
        $product = $useCase->execute($data);

        // Assert
        $this->assertEquals($existingProduct->id, $product->id);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'title' => 'Updated Title',
            'price' => 1000
        ]);

        // Should have 2 history records: initial (500) + new (1000)
        $this->assertDatabaseCount('price_histories', 2);
        $this->assertDatabaseHas('price_histories', [
            'product_id' => $product->id,
            'price' => 1000
        ]);
    }
}
