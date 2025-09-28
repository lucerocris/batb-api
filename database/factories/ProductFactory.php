<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
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
        static $index = 0;
        
        $names = [
            'Lilac Hue', 'Rosy Lure', 'Vanilla Plush', 'Morose', 'Vanilla White',
            'Cherry Red', 'White Bow', 'Gold Crescent', 'Pink Sakura', 'Purple Seashell',
            'Ocean Drift', 'Golden Weave', 'Pearl Mist', 'Silken Chain', 'Twilight Thread'
        ];

        $name = $names[$index];
        $index++;

        return [
            'id' => (string) Str::uuid(),
            'category_id' => null, // Set in seeder
            'name' => $name,
            'slug' => Str::slug(fake()->words(3, true)),
            'description' => fake()->paragraph(),
            'short_description' => fake()->sentence(),
            'sku' => strtoupper(Str::random(8)),
            'base_price' => fake()->randomFloat(2, 100, 5000),
            'sale_price' => null,
            'cost_price' => fake()->randomFloat(2, 50, 2000),
            'stock_quantity' => fake()->numberBetween(1, 10),
            'reserved_quantity' => 0,
            'low_stock_threshold' => 5,
            'track_inventory' => true,
            'allow_backorder' => false,
            'type' => fake()->randomElement(['premium', 'classic']),
            'image_path' => 'products/' . fake()->randomElement(['category1.jpg', 'category2.jpg', 'category3.jpg', 'category4.jpg', 'category5.jpg']),
            'is_active' => true,
            'is_featured' => fake()->boolean(),
            'available_from' => now(),
            'purchase_count' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
