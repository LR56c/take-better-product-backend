<?php

namespace Tests\Unit\Countries\Application;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Src\Countries\Application\UpdateCountry;
use App\Models\Country;
use Tests\TestCase;

class UpdateCountryTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_updates_a_country_successfully()
    {
        // Arrange
        $useCase = $this->app->make(UpdateCountry::class);
        $country = Country::factory()->create(['name' => 'Old Name']);

        // Act
        $updatedCountry = $useCase->execute($country->id, ['name' => 'New Name']);

        // Assert
        $this->assertEquals('New Name', $updatedCountry->name);
        $this->assertDatabaseHas('countries', ['id' => $country->id, 'name' => 'New Name']);
    }
}
