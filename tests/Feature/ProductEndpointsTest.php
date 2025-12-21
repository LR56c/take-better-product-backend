<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Pgvector\Laravel\Vector;
use Src\Products\Application\GenerateEmbedding;
use Src\Products\Domain\ProductEmbedding; // Use the model
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
        $this->mock(GenerateEmbedding::class, function ($mock) {
            $mock->shouldReceive('execute')->andReturn(array_fill(0, 768, 0.1));
        });

        $store = Store::factory()->create();
        $brand = Brand::factory()->create();

        $productData = [
            'store_id' => $store->id,
            'brand_id' => $brand->id,
            'title' => 'New Awesome Product',
            'price' => 200,
            'url' => 'http://example.com/product',
            'external_id' => 'SKU-12345',
            'currency' => 'USD',
            'images' => [
                ['image_url' => 'http://example.com/image1.jpg', 'main' => true],
                ['image_url' => 'http://example.com/image2.jpg', 'main' => false],
            ],
        ];

        $response = $this->actingAsSupabaseUser($this->adminUser, 'admin')
            ->postJson('/api/products', $productData);

        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'New Awesome Product');

        $this->assertDatabaseHas('products', ['title' => 'New Awesome Product']);
        $this->assertDatabaseHas('product_images', ['main' => true]);
        $this->assertDatabaseHas('price_histories', ['price' => 200]);
    }

    public function test_it_can_sync_a_product_and_records_price_history()
    {
        $this->mock(GenerateEmbedding::class, function ($mock) {
            $mock->shouldReceive('execute')->andReturn(array_fill(0, 768, 0.1));
        });

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

        $response = $this->actingAsSupabaseUser($this->adminUser, 'admin')
            ->postJson('/api/products/sync', $syncData);

        $response->assertStatus(200)
            ->assertJsonPath('data.title', 'Synced Product Title');

        $this->assertDatabaseHas('products', ['price' => 150]);
        $this->assertDatabaseCount('price_histories', 2);
        $this->assertDatabaseHas('price_histories', ['price' => 150]);
    }

    public function test_it_can_search_similar_products()
    {
        $vectorArray = array_fill(0, 768, 0.1);

        $this->mock(GenerateEmbedding::class, function ($mock) use ($vectorArray) {
            $mock->shouldReceive('execute')
                ->with('red lamp')
                ->andReturn($vectorArray);
        });

        $product = Product::factory()->create(['title' => 'Red Lamp']);

        // Use the Domain Model to create the embedding
        // This ensures the cast (Vector::class) is applied correctly
        $embedding = new ProductEmbedding;
        $embedding->product_id = $product->id;
        $embedding->vector = new Vector($vectorArray);
        $embedding->save();

        $response = $this->postJson('/api/products/search-similar', [
            'query' => 'red lamp',
            'limit' => 5,
        ]);

        $response->assertStatus(200);

        $this->assertNotEmpty($response->json('data'), 'Search returned no results. DB Count: '.ProductEmbedding::count());

        $response->assertJsonStructure(['data' => [['id', 'title', 'price']]]);
        $this->assertEquals($product->id, $response->json('data.0.id'));
    }
}
