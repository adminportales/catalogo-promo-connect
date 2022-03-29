<?php

namespace Database\Factories;

use App\Models\ProductAttribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductAttributeFactory extends Factory
{
    protected $model = ProductAttribute::class;

    public function definition()
    {
        return [
			'product_id' => $this->faker->name,
			'attribute' => $this->faker->name,
			'slug' => $this->faker->name,
			'value' => $this->faker->name,
        ];
    }
}
