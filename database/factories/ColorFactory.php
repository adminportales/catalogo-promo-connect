<?php

namespace Database\Factories;

use App\Models\Color;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ColorFactory extends Factory
{
    protected $model = Color::class;

    public function definition()
    {
        return [
			'color' => $this->faker->name,
			'slug' => $this->faker->name,
        ];
    }
}
