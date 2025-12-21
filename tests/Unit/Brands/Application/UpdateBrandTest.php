<?php

namespace Tests\Unit\Brands\Application;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Src\Brands\Application\UpdateBrand;
use App\Models\Brand;
use Tests\TestCase;

class UpdateBrandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_updates_a_brand_successfully()
    {
        // Arrange
        $useCase = $this->app->make(UpdateBrand::class);
        $brand = Brand::factory()->create(['name' => 'Old Name']);

        // Act
        $updatedBrand = $useCase->execute($brand->id, 'New Name');

        // Assert
        $this->assertEquals('New Name', $updatedBrand->name);
        $this->assertDatabaseHas('brands', [
            'id' => $brand->id,
            'name' => 'New Name',
        ]);
    }
}
