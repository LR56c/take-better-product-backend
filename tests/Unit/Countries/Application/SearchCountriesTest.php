<?php

namespace Tests\Unit\Countries\Application;

use App\Models\Country;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Src\Countries\Application\SearchCountries;
use Src\Shared\Domain\Criteria\Criteria;
use Tests\TestCase;

class SearchCountriesTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_searches_countries_with_criteria()
    {
        // Arrange
        $useCase = $this->app->make(SearchCountries::class);
        Country::factory()->create(['name' => 'Chile']);
        Country::factory()->create(['name' => 'Argentina']);

        $criteria = new Criteria(
            filters: ['name' => 'Chile'],
            limit: 10
        );

        // Act
        $result = $useCase->execute($criteria);

        // Assert
        $this->assertCount(1, $result->items());
        $this->assertEquals('Chile', $result->items()->first()->name);
    }
}
