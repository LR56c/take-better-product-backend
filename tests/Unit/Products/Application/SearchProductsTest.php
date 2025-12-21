<?php

namespace Tests\Unit\Products\Application;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Src\Products\Application\SearchProducts;
use Src\Shared\Domain\Criteria\Criteria;
use Tests\TestCase;

class SearchProductsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_searches_products_with_criteria()
    {
        // Arrange
        $useCase = $this->app->make(SearchProducts::class);
        Product::factory()->create(['title' => 'iPhone 15']);
        Product::factory()->create(['title' => 'Samsung S24']);

        $criteria = new Criteria(
            filters: ['title' => 'iPhone 15'],
            limit: 10
        );

        // Act
        $result = $useCase->execute($criteria);

        // Assert
        $this->assertCount(1, $result->items());
        $this->assertEquals('iPhone 15', $result->items()->first()->title);
    }
}
