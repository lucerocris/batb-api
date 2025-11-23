<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Sample Philippine locations for realism
        $philippines = [
            [
                'region' => 'NCR',
                'province' => 'Metro Manila',
                'city' => 'Quezon City',
                'barangay' => 'Batasan Hills',
                'postal_code' => '1126',
            ],
            [
                'region' => 'Region IV-A',
                'province' => 'Cavite',
                'city' => 'Imus',
                'barangay' => 'Bucandala',
                'postal_code' => '4103',
            ],
            [
                'region' => 'Region VII',
                'province' => 'Cebu',
                'city' => 'Cebu City',
                'barangay' => 'Lahug',
                'postal_code' => '6000',
            ],
            [
                'region' => 'Region XI',
                'province' => 'Davao del Sur',
                'city' => 'Davao City',
                'barangay' => 'Matina Crossing',
                'postal_code' => '8000',
            ],
        ];

        $place = fake()->randomElement($philippines);

        // Generate realistic PH mobile number
        $phone = '+63' . fake()->numberBetween(9000000000, 9999999999);

        return [
            'id' => (string) Str::uuid(),
            'user_id' => null, // Set in seeder
            'order_number' => 'ORD-' . str_pad(fake()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'fulfillment_status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => fake()->randomElement(['gcash', 'bank_transfer']),
            'payment_due_date' => now()->addDays(3),
            'payment_sent_date' => null,
            'payment_verified_date' => null,
            'payment_verified_by' => null,
            'idempotency_key' => Str::uuid()->toString(),
            'expires_at' => now()->addDays(7),
            'payment_reference' => null,
            'subtotal' => fake()->randomFloat(2, 100, 5000),
            'tax_amount' => 0,
            'shipping_amount' => 0,
            'discount_amount' => 0,
            'total_amount' => fake()->randomFloat(2, 100, 5000),
            'refunded_amount' => 0,
            'currency' => 'PHP',
            'email' => fake()->safeEmail(),

            // ✅ Matches Address type + phone
            'shipping_address' => [
                'first_name' => fake()->firstName(),
                'last_name' => fake()->lastName(),
                'address_line_1' => fake()->streetAddress(),
                'address_line_2' => fake()->optional()->secondaryAddress(),
                'city' => $place['city'],
                'province' => $place['province'],
                'postal_code' => $place['postal_code'],
                'barangay' => $place['barangay'],
                'region' => $place['region'],
                'country_code' => 'PH',
                'phone' => $phone,
            ],

            'billing_address' => [
                'first_name' => fake()->firstName(),
                'last_name' => fake()->lastName(),
                'address_line_1' => fake()->streetAddress(),
                'address_line_2' => fake()->optional()->secondaryAddress(),
                'city' => $place['city'],
                'province' => $place['province'],
                'postal_code' => $place['postal_code'],
                'barangay' => $place['barangay'],
                'region' => $place['region'],
                'country_code' => 'PH',
                'phone' => $phone,
            ],

            'admin_notes' => fake()->sentence(),
            'customer_notes' => fake()->sentence(),
            'reminder_sent_count' => 0,
            'last_reminder_sent' => null,
            'order_date' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
