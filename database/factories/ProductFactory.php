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
			'internal_sku' => $this->faker->name,
			'sku_parent' => $this->faker->name,
			'sku' => $this->faker->name,
			'name' => $this->faker->name,
			'price' => $this->faker->name,
			'description' => $this->faker->name,
			'stock' => $this->faker->name,
			'type_id' => $this->faker->name,
			'color_id' => $this->faker->name,
			'provider_id' => $this->faker->name,
        ];
    }
}
