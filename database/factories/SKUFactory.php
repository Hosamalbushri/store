<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SKU>
 */
class SKUFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->ean13,
            'price' => $this->faker->numberBetween(1000, 99999),
            'quantity' => $this->faker->numberBetween(1, 1000),
            'product_id' => Product::factory(),
        ];
    }
}
