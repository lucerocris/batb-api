<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => (string) Str::uuid(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'role' => fake()->randomElement(['admin', 'customer']),
            'phone_number' => fake()->phoneNumber(),
            'date_of_birth' => fake()->date(),
            'username' => fake()->userName(),
            'total_orders' => 0,
            'total_spent' => 0,
            'failed_login_attempts' => 0,
            'locked_until' => null,
            'deleted_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
            'image_path' => 'users/' . fake()->randomElement(['category1.jpg', 'category2.jpg', 'category3.jpg', 'category4.jpg', 'category5.jpg']),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
