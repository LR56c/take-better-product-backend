<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryEndpointsTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adminUser = User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_can_create_category()
    {
        $data = [
            'name' => 'Electronics',
            'slug' => 'electronics',
        ];

        $response = $this->actingAsSupabaseUser($this->adminUser, 'admin')
                         ->postJson('/api/categories', $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('categories', ['slug' => 'electronics']);
    }

    public function test_admin_can_update_category()
    {
        $category = Category::factory()->create(['name' => 'Old Name']);

        $response = $this->actingAsSupabaseUser($this->adminUser, 'admin')
                         ->putJson("/api/categories/{$category->id}", ['name' => 'New Name']);

        $response->assertStatus(200);
        $this->assertDatabaseHas('categories', ['name' => 'New Name']);
    }
}
