<?php

namespace Tests\Unit\Products\Application;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Src\Products\Application\GenerateEmbedding;
use Src\Products\Application\SearchSimilarProducts;
use Src\Products\Domain\ProductEmbeddingRepository;
use Src\Products\Domain\ProductRepository;
use Tests\TestCase;

class SearchSimilarProductsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_searches_for_similar_products_successfully()
    {
        // Arrange
        $queryText = 'red lamp';
        $vector = array_fill(0, 768, 0.1);

        $product1 = Product::factory()->make(['id' => 'uuid-1', 'title' => 'Red Lamp']);
        $product2 = Product::factory()->make(['id' => 'uuid-2', 'title' => 'Reddish Lamp']);

        $generateEmbeddingMock = Mockery::mock(GenerateEmbedding::class);
        $generateEmbeddingMock->shouldReceive('execute')->with($queryText)->andReturn($vector);

        $embeddingRepoMock = Mockery::mock(ProductEmbeddingRepository::class);
        $embeddingRepoMock->shouldReceive('searchSimilar')->with($vector, 10)->andReturn([
            ['product_id' => 'uuid-1', 'similarity' => 0.98],
            ['product_id' => 'uuid-2', 'similarity' => 0.95],
        ]);

        $productRepoMock = Mockery::mock(ProductRepository::class);
        $productRepoMock->shouldReceive('findByIds')
            ->with(Mockery::type('array'))
            ->andReturn(new Collection([$product2, $product1]));

        $this->app->instance(GenerateEmbedding::class, $generateEmbeddingMock);
        $this->app->instance(ProductEmbeddingRepository::class, $embeddingRepoMock);
        $this->app->instance(ProductRepository::class, $productRepoMock);

        $useCase = $this->app->make(SearchSimilarProducts::class);

        // Act
        $result = $useCase->execute($queryText);

        // Assert
        $this->assertCount(2, $result->items());
        $this->assertEquals('uuid-1', $result->items()[0]->id);
        $this->assertEquals('uuid-2', $result->items()[1]->id);
    }
}
