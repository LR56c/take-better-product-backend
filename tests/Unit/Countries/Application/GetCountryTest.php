<?php

namespace Tests\Unit\Countries\Application;

use App\Models\Country;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Src\Countries\Application\GetCountry;
use Src\Countries\Domain\Exceptions\CountryNotFound;
use Tests\TestCase;

class GetCountryTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_gets_a_country_by_id()
    {
        // Arrange
        $useCase = $this->app->make(GetCountry::class);
        $country = Country::factory()->create();

        // Act
        $foundCountry = $useCase->execute($country->id);

        // Assert
        $this->assertEquals($country->id, $foundCountry->id);
    }

    public function test_it_throws_exception_if_country_not_found()
    {
        $useCase = $this->app->make(GetCountry::class);
        $this->expectException(CountryNotFound::class);
        $useCase->execute('550e8400-e29b-41d4-a716-446655440000');
    }
}
