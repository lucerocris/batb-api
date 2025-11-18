<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'slug' => Str::slug(fake()->words(2, true)),
            'description' => fake()->sentence(),
            'image_path' => 'categories/' . fake()->randomElement(['category1.jpg', 'category2.jpg', 'category3.jpg', 'category4.jpg', 'category5.jpg']),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
