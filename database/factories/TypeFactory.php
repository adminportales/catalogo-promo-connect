<?php

namespace Database\Factories;

use App\Models\Type;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TypeFactory extends Factory
{
    protected $model = Type::class;

    public function definition()
    {
        return [
			'type' => $this->faker->name,
			'slug' => $this->faker->name,
        ];
    }
}
