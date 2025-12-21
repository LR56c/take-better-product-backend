<?php

namespace Tests\Unit\Categories\Application;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Src\Categories\Application\CreateCategory;
use Tests\TestCase;

class CreateCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_category_successfully()
    {
        // Arrange
        $useCase = $this->app->make(CreateCategory::class);
        $data = [
            'name' => 'Electronics',
            'slug' => 'electronics',
        ];

        // Act
        $category = $useCase->execute($data);

        // Assert
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'slug' => 'electronics',
        ]);
    }
}
