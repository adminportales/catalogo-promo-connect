<?php

namespace Database\Factories;

use App\Models\DinamycPrice;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DinamycPriceFactory extends Factory
{
    protected $model = DinamycPrice::class;

    public function definition()
    {
        return [
			'type' => $this->faker->name,
			'provider_change' => $this->faker->name,
			'type_change' => $this->faker->name,
			'amount' => $this->faker->name,
			'product_id' => $this->faker->name,
			'site_id' => $this->faker->name,
        ];
    }
}
