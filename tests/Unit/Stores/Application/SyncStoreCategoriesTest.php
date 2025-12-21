<?php

namespace Tests\Unit\Stores\Application;

use App\Models\Category;
use App\Models\Store;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Src\Stores\Application\SyncStoreCategories;
use Tests\TestCase;

class SyncStoreCategoriesTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_syncs_categories_for_a_store()
    {
        // Arrange
        $useCase = $this->app->make(SyncStoreCategories::class);
        $store = Store::factory()->create();
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();

        $syncData = [
            ['category_id' => $category1->id, 'url' => 'http://a.com', 'is_active' => true],
            ['category_id' => $category2->id, 'url' => 'http://b.com', 'is_active' => false],
        ];

        // Act
        $useCase->execute($store->id, $syncData);

        // Assert
        $this->assertDatabaseHas('store_categories', [
            'store_id' => $store->id,
            'category_id' => $category1->id,
            'is_active' => true,
        ]);
        $this->assertDatabaseCount('store_categories', 2);
    }
}
