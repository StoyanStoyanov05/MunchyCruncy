<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Recipe>
 */
class RecipeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id, // Create a user automatically if not provided
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(3),
            'instructions' => $this->faker->paragraphs(3, true),
            'image_url' => 'https://placehold.co/200x200', // Use the static placeholder URL
        ];
    }
}
