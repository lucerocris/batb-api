<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => null, // Set in seeder
            'type' => fake()->randomElement(['shipping', 'billing', 'both']),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'company' => fake()->company(),
            'address_line_1' => fake()->streetAddress(),
            'address_line_2' => fake()->secondaryAddress(),
            'city' => fake()->city(),
            'state_province' => fake()->state(),
            'postal_code' => fake()->postcode(),
            'country_code' => 'PH',
            'phone' => fake()->phoneNumber(),
            'is_default_shipping' => fake()->boolean(),
            'is_default_billing' => fake()->boolean(),
            'is_active' => true,
            'is_validated' => fake()->boolean(),
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
