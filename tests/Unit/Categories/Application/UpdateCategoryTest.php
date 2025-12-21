<?php

namespace Tests\Unit\Categories\Application;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Src\Categories\Application\UpdateCategory;
use Tests\TestCase;

class UpdateCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_updates_a_category_successfully()
    {
        // Arrange
        $useCase = $this->app->make(UpdateCategory::class);
        $category = Category::factory()->create(['name' => 'Old Name']);

        // Act
        $updatedCategory = $useCase->execute($category->id, ['name' => 'New Name']);

        // Assert
        $this->assertEquals('New Name', $updatedCategory->name);
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'New Name']);
    }
}
