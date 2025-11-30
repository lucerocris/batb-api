<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Product;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $unitPrice = fake()->randomFloat(2, 50, 1000);
        $quantity = fake()->numberBetween(1, 5);
        return [
            'order_id' => null, // Set in seeder
            'product_id' => null, // Set in seeder
            'product_name' => null,
            'product_sku' => null,
            'product_attributes' => json_encode(['size' => fake()->randomElement(['S', 'M', 'L'])]),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'line_total' => $unitPrice * $quantity,
            'discount_amount' => 0,
            'customization' => null,
            'customization_notes' => null,
            // 'fulfillment_status' => fake()->randomElement(['pending', 'processing', 'shipped', 'delivered', 'cancelled', 'returned']),
            // 'quantity_shipped' => 0,
            // 'quantity_returned' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
