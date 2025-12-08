<?php

namespace Database\Factories;

use Carbon\Carbon;
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
        $orderDate = Carbon::instance(fake()->dateTimeBetween('-12 months', 'now'))->startOfHour();
        $sequence = str_pad((string) fake()->numberBetween(1, 99999), 5, '0', STR_PAD_LEFT);
        $orderNumber = sprintf('ORD-%s-%s', $orderDate->format('ym'), $sequence);

        $paymentMethod = fake()->randomElement(['gcash', 'bank_transfer']);
        $paymentStatus = fake()->randomElement([
            'paid', 'paid', 'paid', 'paid', 'pending', 'pending', 'failed', 'refunded',
        ]);
        $fulfillmentStatus = match ($paymentStatus) {
            'paid' => fake()->randomElement(['fulfilled', 'shipped', 'delivered']),
            'pending' => 'pending',
            'refunded' => 'cancelled',
            'failed' => 'cancelled',
            default => 'pending',
        };

        $subtotal = fake()->randomFloat(2, 350, 3500);
        $shippingAmount = fake()->randomElement([0, 0, 80, 120, 150]);
        $discountAmount = fake()->optional(0.35, 0)->randomFloat(2, 50, $subtotal * 0.35);
        $taxAmount = round($subtotal * 0.12, 2);
        $totalAmount = max($subtotal + $taxAmount + $shippingAmount - $discountAmount, 0);

        $paymentSentDate = $paymentStatus === 'paid'
            ? (clone $orderDate)->addDays(fake()->numberBetween(0, 2))
            : null;
        $paymentVerifiedDate = $paymentStatus === 'paid'
            ? (clone $paymentSentDate)->addHours(fake()->numberBetween(2, 24))
            : null;
        $paymentDueDate = in_array($paymentStatus, ['pending', 'failed'], true)
            ? (clone $orderDate)->addDays(3)
            : null;
        $reminderSentCount = $paymentStatus === 'pending' ? fake()->numberBetween(0, 2) : 0;
        $lastReminderSent = $reminderSentCount > 0
            ? (clone $orderDate)->addDays(fake()->numberBetween(1, 5))
            : null;

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
        $phone = '+63' . fake()->numberBetween(9000000000, 9999999999);

        // Product images available in storage


        return [
            'id' => (string) Str::uuid(),
            'user_id' => null, // Overridden in seeder when needed
            'order_number' => $orderNumber,
            'fulfillment_status' => $fulfillmentStatus,
            'payment_status' => $paymentStatus,
            'payment_method' => $paymentMethod,
            'payment_due_date' => $paymentDueDate,
            'payment_sent_date' => $paymentSentDate,
            'payment_verified_date' => $paymentVerifiedDate,
            'payment_verified_by' => null,
            'idempotency_key' => Str::uuid()->toString(),
            'expires_at' => (clone $orderDate)->addDays(7),
            'payment_reference' => $paymentStatus === 'paid'
                ? 'PAY-' . strtoupper(Str::random(8))
                : null,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'shipping_amount' => $shippingAmount,
            'discount_amount' => $discountAmount,
            'total_amount' => $totalAmount,
            'refunded_amount' => $paymentStatus === 'refunded' ? $totalAmount : 0,
            'currency' => 'PHP',
            'email' => fake()->safeEmail(),
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
            'admin_notes' => fake()->optional()->sentence(),
            'customer_notes' => fake()->optional()->sentence(),
            'reminder_sent_count' => $reminderSentCount,
            'last_reminder_sent' => $lastReminderSent,
            'order_date' => $orderDate,
            'created_at' => $orderDate,
            'updated_at' => (clone $orderDate)->addHours(fake()->numberBetween(1, 72)),
        ];
    }
}
