<?php

namespace Tests\Unit\Stores\Application;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Src\Stores\Application\GetStore;
use Src\Stores\Domain\Exceptions\StoreNotFound;
use App\Models\Store;
use Tests\TestCase;

class GetStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_gets_a_store_by_id()
    {
        // Arrange
        $useCase = $this->app->make(GetStore::class);
        $store = Store::factory()->create();

        // Act
        $foundStore = $useCase->execute($store->id);

        // Assert
        $this->assertEquals($store->id, $foundStore->id);
    }

    public function test_it_throws_exception_if_store_not_found()
    {
        $useCase = $this->app->make(GetStore::class);
        $this->expectException(StoreNotFound::class);
        $useCase->execute('550e8400-e29b-41d4-a716-446655440000');
    }
}
