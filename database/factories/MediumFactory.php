<?php

namespace Database\Factories;

use App\Models\Medium;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MediumFactory extends Factory
{
    protected $model = Medium::class;

    public function definition()
    {
        return [
			'name' => $this->faker->name,
			'path' => $this->faker->name,
        ];
    }
}
