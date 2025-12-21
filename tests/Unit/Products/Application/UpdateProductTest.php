<?php

namespace Tests\Unit\Products\Application;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Src\Products\Application\UpdateProduct;
use App\Models\Product;
use Tests\TestCase;

class UpdateProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_updates_a_product_successfully()
    {
        // Arrange
        $useCase = $this->app->make(UpdateProduct::class);
        $product = Product::factory()->create(['title' => 'Old Title']);

        // Act
        $updatedProduct = $useCase->execute($product->id, ['title' => 'New Title']);

        // Assert
        $this->assertEquals('New Title', $updatedProduct->title);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'title' => 'New Title']);
    }
}
