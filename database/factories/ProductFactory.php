<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
			'sku' => $this->faker->name,
			'name' => $this->faker->name,
			'price' => $this->faker->name,
			'description' => $this->faker->name,
			'stock' => $this->faker->name,
			'type' => $this->faker->name,
			'color' => $this->faker->name,
			'image' => $this->faker->name,
			'offer' => $this->faker->name,
			'discount' => $this->faker->name,
			'provider_id' => $this->faker->name,
        ];
    }
}
