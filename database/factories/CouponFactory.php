<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class CouponFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => (string) Str::uuid(),
            'code' => strtoupper(Str::random(8)),
            'name' => fake()->words(2, true),
            'description' => fake()->sentence(),
            'type' => fake()->randomElement(['percentage', 'fixed_amount', 'free_shipping']),
            'value' => fake()->randomFloat(2, 50, 500),
            'minimum_amount' => null,
            'maximum_discount' => null,
            'usage_limit' => null,
            'usage_limit_per_customer' => null,
            'used_count' => 0,
            'starts_at' => now(),
            'expires_at' => now()->addMonth(),
            'is_active' => true,
            'applicable_products' => json_encode([]),
            'applicable_categories' => json_encode([]),
            'first_order_only' => fake()->boolean(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
