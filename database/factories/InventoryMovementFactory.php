<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */


class InventoryMovementFactory extends Factory
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
            'product_variant_id' => null,
            'order_id' => null,
            'user_id' => null,
            'type' => fake()->randomElement(['restock', 'creation', 'lost', 'damaged', 'other', 'correction']),
            'initial_quantity' => fake()->numberBetween(1, 20),
            'quantity' => fake()->numberBetween(1,40),
            'notes' => fake()->sentence(),
            'reference' => strtoupper(Str::random(8)),
            'meta_data' => json_encode([]),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
