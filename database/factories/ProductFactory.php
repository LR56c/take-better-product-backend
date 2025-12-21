<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'store_id' => Store::factory(),
            'brand_id' => Brand::factory(),
            'category_id' => Category::factory(),
            'external_id' => $this->faker->unique()->ean13,
            'url' => $this->faker->unique()->url,
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph,
            'price' => $this->faker->randomFloat(2, 1, 1000),
            'currency' => 'USD',
            'last_scraped_at' => now(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Product $product) {
            $product->priceHistories()->create([
                'price' => $product->price,
                'recorded_at' => now(),
            ]);
        });
    }
}
