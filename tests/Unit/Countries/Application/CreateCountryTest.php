<?php

namespace Tests\Unit\Countries\Application;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Src\Countries\Application\CreateCountry;
use Tests\TestCase;

class CreateCountryTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_country_successfully()
    {
        // Arrange
        $useCase = $this->app->make(CreateCountry::class);
        $data = [
            'name' => 'Chile',
            'code' => 'CHL',
            'currency' => 'CLP',
        ];

        // Act
        $country = $useCase->execute($data);

        // Assert
        $this->assertDatabaseHas('countries', [
            'id' => $country->id,
            'code' => 'CHL',
        ]);
    }
}
