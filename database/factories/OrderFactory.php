<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
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
        $regions = [
            'NCR', 'Region I', 'Region II', 'Region III', 'Region IV-A', 'Region IV-B',
            'Region V', 'Region VI', 'Region VII', 'Region VIII', 'Region IX',
            'Region X', 'Region XI', 'Region XII', 'CAR', 'CARAGA', 'BARMM'
        ];

        return [
             'id' => (string) Str::uuid(),
            'user_id' => null, // Set in seeder
            'order_number' => 'ORD-' . str_pad(fake()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'status' => 'for_verification',
            'fulfillment_status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => fake()->randomElement(['gcash', 'bank_transfer']),
            'payment_due_date' => now()->addDays(3),
            'payment_sent_date' => null,
            'payment_verified_date' => null,
            'payment_verified_by' => null,
            'expires_at' => now()->addDays(7),
            'payment_instructions' => ['instructions' => 'Pay before due date'],
            'payment_reference' => null,
            'subtotal' => fake()->randomFloat(2, 100, 5000),
            'tax_amount' => 0,
            'shipping_amount' => 0,
            'discount_amount' => 0,
            'total_amount' => fake()->randomFloat(2, 100, 5000),
            'refunded_amount' => 0,
            'currency' => 'PHP',
            'email' => fake()->email(),
            'shipping_address' => [
                'firstName' => fake()->firstName(),
                'lastName' => fake()->lastName(),
                'phoneNumber' => fake()->phoneNumber(),
                'addressLine1' => fake()->streetAddress(),
                'postalCode' => fake()->postcode(),
                'city' => fake()->city(),
                'region' => fake()->randomElement($regions)
            ],
            'billing_address' => [
                'addressLine1' => fake()->streetAddress(),
                'city' => fake()->city(),
                'region' => fake()->randomElement($regions)
            ],
            'admin_notes' => fake()->sentence(),
            'customer_notes' => fake()->sentence(),
            'reminder_sent_count' => 0,
            'last_reminder_sent' => null,
            'order_date' => now(),
            'created_at' => now(),
            'updated_at' => now(),
            'tip' => fake()->randomFloat(7, 0, 1000),
        ];
    }
}
