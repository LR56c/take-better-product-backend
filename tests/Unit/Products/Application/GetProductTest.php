<?php

namespace Tests\Unit\Products\Application;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Src\Products\Application\GetProduct;
use Src\Products\Domain\Exceptions\ProductNotFound;
use Tests\TestCase;

class GetProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_gets_a_product_by_id()
    {
        // Arrange
        $useCase = $this->app->make(GetProduct::class);
        $product = Product::factory()->create();

        // Act
        $foundProduct = $useCase->execute($product->id);

        // Assert
        $this->assertEquals($product->id, $foundProduct->id);
    }

    public function test_it_throws_exception_if_product_not_found()
    {
        $useCase = $this->app->make(GetProduct::class);
        $this->expectException(ProductNotFound::class);
        $useCase->execute('550e8400-e29b-41d4-a716-446655440000');
    }
}
