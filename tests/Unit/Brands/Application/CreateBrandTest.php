<?php

namespace Tests\Unit\Brands\Application;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Src\Brands\Application\CreateBrand;
use Tests\TestCase;

class CreateBrandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_brand_successfully()
    {
        // Arrange
        $useCase = $this->app->make(CreateBrand::class);
        $brandName = 'Nike';

        // Act
        $brand = $useCase->execute($brandName);

        // Assert
        $this->assertDatabaseHas('brands', [
            'id' => $brand->id,
            'name' => 'Nike',
        ]);
    }
}
