<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Country;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreEndpointsTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;

    private User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adminUser = User::factory()->create(['role' => 'admin']);
        $this->regularUser = User::factory()->create(['role' => 'user']);
    }

    public function test_admin_can_create_store()
    {
        $country = Country::factory()->create();
        $storeData = [
            'country_id' => $country->id,
            'name' => 'New Test Store',
            'type' => 'supermarket',
        ];

        $response = $this->actingAsSupabaseUser($this->adminUser, 'admin')
            ->postJson('/api/stores', $storeData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('stores', ['name' => 'New Test Store']);
    }

    public function test_user_cannot_create_store()
    {
        $country = Country::factory()->create();
        $storeData = [
            'country_id' => $country->id,
            'name' => 'New Test Store',
            'type' => 'supermarket',
        ];

        $response = $this->actingAsSupabaseUser($this->regularUser, 'user')
            ->postJson('/api/stores', $storeData);

        $response->assertStatus(403); // Forbidden
    }

    public function test_admin_can_sync_categories_for_store()
    {
        $store = Store::factory()->create();
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();

        $syncData = [
            'categories' => [
                ['category_id' => $category1->id, 'url' => 'http://a.com', 'is_active' => true],
                ['category_id' => $category2->id, 'url' => 'http://b.com', 'is_active' => false],
            ],
        ];

        $response = $this->actingAsSupabaseUser($this->adminUser, 'admin')
            ->postJson("/api/stores/{$store->id}/categories", $syncData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('store_categories', [
            'store_id' => $store->id,
            'category_id' => $category1->id,
            'is_active' => true,
        ]);
        $this->assertDatabaseCount('store_categories', 2);
    }
}
