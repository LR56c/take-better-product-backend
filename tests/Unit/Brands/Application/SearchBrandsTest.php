<?php

namespace Tests\Unit\Brands\Application;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Src\Brands\Application\SearchBrands;
use Src\Shared\Domain\Criteria\Criteria;
use App\Models\Brand;
use Tests\TestCase;

class SearchBrandsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_searches_brands_with_criteria()
    {
        // Arrange
        $useCase = $this->app->make(SearchBrands::class);
        Brand::factory()->create(['name' => 'Nike']);
        Brand::factory()->create(['name' => 'Adidas']);
        Brand::factory()->create(['name' => 'Puma']);

        $criteria = new Criteria(
            filters: ['name' => 'Nike'],
            limit: 10
        );

        // Act
        $result = $useCase->execute($criteria);

        // Assert
        $this->assertCount(1, $result->items());
        $this->assertEquals('Nike', $result->items()->first()->name);
        $this->assertEquals(1, $result->total());
    }
}
