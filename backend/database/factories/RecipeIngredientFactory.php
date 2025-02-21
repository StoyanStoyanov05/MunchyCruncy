<?php

namespace Database\Factories;

use App\Models\Recipe;
use App\Models\Ingredient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RecipeIngredient>
 */
class RecipeIngredientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'recipe_id' => Recipe::inRandomOrder()->first()->id, // Create a recipe automatically if not provided
            'ingredient_id' => Ingredient::inRandomOrder()->first()->id, // Create an ingredient automatically if not provided
        ];
    }
}
