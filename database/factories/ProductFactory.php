<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Sku;
use App\Models\SubCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
            'status' => $this->faker->randomElement(['1', '0']),
            'sub_category_id'=>SubCategory::factory(),

        ];
    }

    public function configure()
    {
        return
            $this->afterCreating(function (Product $product) {
            SKU::factory()->create(['product_id' => $product->id]);

                $staticPhotoPath = public_path('frontImg.jpg');

                // Add the static photo to the product's media collection
                $product->addMedia($staticPhotoPath)
                    ->preservingOriginal()
                    ->toMediaCollection();

            })
        ;
    }
}
