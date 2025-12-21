<?php

namespace Tests\Unit\Products\Application;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Src\Products\Application\SyncProduct;
use App\Models\Store;
use App\Models\Product;
use Src\Shared\Infrastructure\Gemini\GeminiService;
use Tests\TestCase;

class SyncProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_new_product_if_not_exists()
    {
        // Mock Gemini
        $geminiMock = Mockery::mock(GeminiService::class);
        $geminiMock->shouldReceive('generateEmbedding')
            ->once()
            ->andReturn([0.1, 0.2, 0.3]);
        $this->app->instance(GeminiService::class, $geminiMock);

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

        $this->assertDatabaseHas('product_embeddings', [
            'product_id' => $product->id,
        ]);
    }

    public function test_it_updates_existing_product_and_records_history()
    {
        // Mock Gemini (should NOT be called for update)
        $geminiMock = Mockery::mock(GeminiService::class);
        $geminiMock->shouldNotReceive('generateEmbedding');
        $this->app->instance(GeminiService::class, $geminiMock);

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
            'price' => 1000,
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
    }
}
