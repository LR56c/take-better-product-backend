<?php

namespace Tests\Unit\Categories\Application;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Src\Categories\Application\SearchCategories;
use Src\Shared\Domain\Criteria\Criteria;
use App\Models\Category;
use Tests\TestCase;

class SearchCategoriesTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_searches_categories_with_criteria()
    {
        // Arrange
        $useCase = $this->app->make(SearchCategories::class);
        Category::factory()->create(['name' => 'Electronics']);
        Category::factory()->create(['name' => 'Books']);

        $criteria = new Criteria(
            filters: ['name' => 'Electronics'],
            limit: 10
        );

        // Act
        $result = $useCase->execute($criteria);

        // Assert
        $this->assertCount(1, $result->items());
        $this->assertEquals('Electronics', $result->items()->first()->name);
    }
}
