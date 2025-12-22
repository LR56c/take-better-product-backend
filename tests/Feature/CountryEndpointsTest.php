<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CountryEndpointsTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adminUser = User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_can_create_country()
    {
        $data = [
            'name' => 'Chile',
            'code' => 'CHL',
            'currency' => 'CLP',
        ];

        $response = $this->actingAs($this->adminUser, 'admin')
                         ->postJson('/api/countries', $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('countries', ['code' => 'CHL']);
    }
}
