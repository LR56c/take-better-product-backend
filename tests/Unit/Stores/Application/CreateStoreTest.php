<?php

namespace Tests\Unit\Stores\Application;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Src\Stores\Application\CreateStore;
use App\Models\Country;
use Tests\TestCase;

class CreateStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_store_successfully()
    {
        // Arrange
        $useCase = $this->app->make(CreateStore::class);
        $country = Country::factory()->create();
        $data = [
            'country_id' => $country->id,
            'name' => 'Test Store',
            'type' => 'supermarket',
        ];

        // Act
        $store = $useCase->execute($data);

        // Assert
        $this->assertDatabaseHas('stores', [
            'id' => $store->id,
            'name' => 'Test Store',
        ]);
    }
}
