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
        // Define an array of image URLs
        $images = [
            'image1.jpg',
            'image2.jpg',
            'image3.jpg',
        ];

        return [
            'user_id' => User::inRandomOrder()->first()->id, // Assign a random user
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(3),
            'instructions' => $this->faker->paragraphs(3, true),
            'image_url' => $this->faker->randomElement($images), // Randomly select an image
        ];
    }
}
