<?php

namespace Database\Factories;

use App\Models\Provider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProviderFactory extends Factory
{
    protected $model = Provider::class;

    public function definition()
    {
        return [
			'company' => $this->faker->name,
			'email' => $this->faker->name,
			'phone' => $this->faker->name,
			'contact' => $this->faker->name,
			'discount' => $this->faker->name,
        ];
    }
}
