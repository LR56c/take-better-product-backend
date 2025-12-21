<?php

namespace Tests\Unit\Stores\Application;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Src\Stores\Application\SearchStores;
use Src\Shared\Domain\Criteria\Criteria;
use App\Models\Store;
use Tests\TestCase;

class SearchStoresTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_searches_stores_with_criteria()
    {
        // Arrange
        $useCase = $this->app->make(SearchStores::class);
        Store::factory()->create(['name' => 'Jumbo']);
        Store::factory()->create(['name' => 'Lider']);

        $criteria = new Criteria(
            filters: ['name' => 'Jumbo'],
            limit: 10
        );

        // Act
        $result = $useCase->execute($criteria);

        // Assert
        $this->assertCount(1, $result->items());
        $this->assertEquals('Jumbo', $result->items()->first()->name);
    }
}
