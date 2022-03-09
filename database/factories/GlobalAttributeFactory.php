<?php

namespace Database\Factories;

use App\Models\GlobalAttribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class GlobalAttributeFactory extends Factory
{
    protected $model = GlobalAttribute::class;

    public function definition()
    {
        return [
			'attribute' => $this->faker->name,
			'value' => $this->faker->name,
        ];
    }
}
