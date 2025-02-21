<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Recipe;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Rating>
 */
class RatingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id, // Random User ID from the database
            'recipe_id' => Recipe::inRandomOrder()->first()->id, // Random Recipe ID from the database
            'rating' => $this->faker->numberBetween(1, 5), // Random rating between 1 and 5
        ];
    }
}
