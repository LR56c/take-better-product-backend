<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_authenticated_user_data()
    {
        // Arrange
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'role' => 'admin',
        ]);

        // Act
        $response = $this->actingAsSupabaseUser($user, 'admin')
            ->getJson('/api/user');

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'id' => $user->id,
                'email' => 'test@example.com',
                'role' => 'admin',
            ]);
    }

    public function test_it_returns_unauthorized_if_no_token_is_provided()
    {
        $response = $this->getJson('/api/user');

        $response->assertStatus(401);
    }

    public function test_admin_can_list_users()
    {
        // Arrange
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory()->count(5)->create();

        // Act
        $response = $this->actingAsSupabaseUser($admin, 'admin')
            ->getJson('/api/users');

        // Assert
        $response->assertStatus(200)
            ->assertJsonCount(6, 'data'); // 5 created + 1 admin
    }

    public function test_regular_user_cannot_list_users()
    {
        // Arrange
        $user = User::factory()->create(['role' => 'user']);

        // Act
        $response = $this->actingAsSupabaseUser($user, 'user')
            ->getJson('/api/users');

        // Assert
        $response->assertStatus(403); // Forbidden
    }
}
