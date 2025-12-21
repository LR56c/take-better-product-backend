<?php

namespace Tests\Unit\Categories\Application;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Src\Categories\Application\GetCategory;
use Src\Categories\Domain\Exceptions\CategoryNotFound;
use App\Models\Category;
use Tests\TestCase;

class GetCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_gets_a_category_by_id()
    {
        // Arrange
        $useCase = $this->app->make(GetCategory::class);
        $category = Category::factory()->create();

        // Act
        $foundCategory = $useCase->execute($category->id);

        // Assert
        $this->assertEquals($category->id, $foundCategory->id);
    }

    public function test_it_throws_exception_if_category_not_found()
    {
        $useCase = $this->app->make(GetCategory::class);
        $this->expectException(CategoryNotFound::class);
        $useCase->execute('550e8400-e29b-41d4-a716-446655440000');
    }
}
