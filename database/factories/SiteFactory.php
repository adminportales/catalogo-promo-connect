<?php

namespace Database\Factories;

use App\Models\Site;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SiteFactory extends Factory
{
    protected $model = Site::class;

    public function definition()
    {
        return [
			'name' => $this->faker->name,
			'woocommerce' => $this->faker->name,
			'url' => $this->faker->name,
			'consumer_key' => $this->faker->name,
			'consumer_secret' => $this->faker->name,
        ];
    }
}
