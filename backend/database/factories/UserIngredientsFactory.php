<?php

namespace Database\Factories;

use App\Models\Ingredient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserIngredient>
 */
class UserIngredientsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id, // Random User ID
            'ingredient_id' => Ingredient::inRandomOrder()->first()->id, // Random Ingredient ID
            'quantity' => $this->faker->numberBetween(1, 500)
        ];
    }
}
