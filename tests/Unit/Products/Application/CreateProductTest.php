<?php

namespace Tests\Unit\Products\Application;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Src\Products\Application\CreateProduct;
use App\Models\Store;
use Src\Shared\Infrastructure\Gemini\GeminiService;
use Tests\TestCase;

class CreateProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_product_and_its_price_history_successfully()
    {
        // Mock Gemini Service
        $geminiMock = Mockery::mock(GeminiService::class);
        $geminiMock->shouldReceive('generateEmbedding')
            ->once()
            ->andReturn([0.1, 0.2, 0.3]); // Dummy vector

        $this->app->instance(GeminiService::class, $geminiMock);

        // Arrange
        $useCase = $this->app->make(CreateProduct::class);
        $store = Store::factory()->create();

        $data = [
            'store_id' => $store->id,
            'title' => 'Test Product',
            'price' => 1000,
            'url' => 'http://example.com/product',
            'external_id' => 'SKU-123',
            'currency' => 'USD',
            'images' => [],
        ];

        // Act
        $product = $useCase->execute($data);

        // Assert
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'title' => 'Test Product'
        ]);

        $this->assertDatabaseHas('price_histories', [
            'product_id' => $product->id,
            'price' => 1000
        ]);

        $this->assertDatabaseHas('product_embeddings', [
            'product_id' => $product->id,
        ]);
    }
}
