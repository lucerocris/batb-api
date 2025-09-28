<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ProductVariantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => null, // Set in seeder
            'name' => fake()->word() . ' Variant',
            'sku' => strtoupper(Str::random(8)),
            'price_adjustment' => fake()->randomFloat(2, 0, 500),
            'stock_quantity' => fake()->numberBetween(0, 10),
            'reserved_quantity' => 0,
            'attributes' => ['color' => fake()->safeColorName()],
            'image_path' => 'variants/' . fake()->randomElement(['category1.jpg', 'category2.jpg', 'category3.jpg', 'category4.jpg', 'category5.jpg']),
            'is_active' => true,
            'sort_order' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
