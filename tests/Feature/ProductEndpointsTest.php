<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Store;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductEndpointsTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adminUser = User::factory()->create(['role' => 'admin']);
    }

    public function test_it_can_store_a_new_product()
    {
        // Arrange
        $store = Store::factory()->create();
        $brand = Brand::factory()->create();

        $productData = [
            'store_id' => $store->id,
            'brand_id' => $brand->id,
            'title' => 'New Awesome Product',
            'price' => 199.99,
            'url' => 'http://example.com/product',
            'external_id' => 'SKU-12345',
            'currency' => 'USD',
            'images' => [
                ['image_url' => 'http://example.com/image1.jpg', 'main' => true],
                ['image_url' => 'http://example.com/image2.jpg', 'main' => false],
            ]
        ];

        // Act
        $response = $this->actingAsSupabaseUser($this->adminUser, 'admin')
                         ->postJson('/api/products', $productData);

        // Assert
        $response->assertStatus(201)
                 ->assertJsonPath('data.title', 'New Awesome Product');

        $this->assertDatabaseHas('products', ['title' => 'New Awesome Product']);
        $this->assertDatabaseHas('product_images', ['main' => true]);
        $this->assertDatabaseHas('price_histories', ['price' => 199.99]);
    }

    public function test_it_can_sync_a_product_and_records_price_history()
    {
        // Arrange
        $store = Store::factory()->create();
        $product = Product::factory()->create([
            'store_id' => $store->id,
            'external_id' => 'SYNC-SKU-001',
            'price' => 100,
        ]);

        $syncData = [
            'store_id' => $store->id,
            'external_id' => 'SYNC-SKU-001',
            'title' => 'Synced Product Title',
            'price' => 150, // New price
            'url' => $product->url,
            'currency' => 'USD',
        ];

        // Act
        $response = $this->actingAsSupabaseUser($this->adminUser, 'admin')
                         ->postJson('/api/products/sync', $syncData);

        // Assert
        $response->assertStatus(200)
                 ->assertJsonPath('data.title', 'Synced Product Title');

        $this->assertDatabaseHas('products', ['price' => 150]);
        $this->assertDatabaseCount('price_histories', 2); // Initial + new one
        $this->assertDatabaseHas('price_histories', ['price' => 150]);
    }
}
