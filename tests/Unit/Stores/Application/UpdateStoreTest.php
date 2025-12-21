<?php

namespace Tests\Unit\Stores\Application;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Src\Stores\Application\UpdateStore;
use App\Models\Store;
use Tests\TestCase;

class UpdateStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_updates_a_store_successfully()
    {
        // Arrange
        $useCase = $this->app->make(UpdateStore::class);
        $store = Store::factory()->create(['name' => 'Old Name']);

        // Act
        $updatedStore = $useCase->execute($store->id, ['name' => 'New Name']);

        // Assert
        $this->assertEquals('New Name', $updatedStore->name);
        $this->assertDatabaseHas('stores', ['id' => $store->id, 'name' => 'New Name']);
    }
}
