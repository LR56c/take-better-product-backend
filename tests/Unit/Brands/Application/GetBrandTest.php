<?php

namespace Tests\Unit\Brands\Application;

use App\Models\Brand;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Src\Brands\Application\GetBrand;
use Src\Brands\Domain\Exceptions\BrandNotFound;
use Tests\TestCase;

class GetBrandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_gets_a_brand_by_id()
    {
        // Arrange
        $useCase = $this->app->make(GetBrand::class);
        $brand = Brand::factory()->create();

        // Act
        $foundBrand = $useCase->execute($brand->id);

        // Assert
        $this->assertEquals($brand->id, $foundBrand->id);
    }

    public function test_it_throws_exception_if_brand_not_found()
    {
        // Arrange
        $useCase = $this->app->make(GetBrand::class);
        $nonExistentId = '550e8400-e29b-41d4-a716-446655440000';

        // Assert
        $this->expectException(BrandNotFound::class);

        // Act
        $useCase->execute($nonExistentId);
    }
}
