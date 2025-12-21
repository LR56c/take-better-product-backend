<?php

namespace Database\Factories;

use App\Models\Country;
use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;

class StoreFactory extends Factory
{
    protected $model = Store::class;

    public function definition()
    {
        return [
            'country_id' => Country::factory(),
            'name' => $this->faker->company,
            'url' => $this->faker->url,
            'type' => 'supermarket',
        ];
    }
}
