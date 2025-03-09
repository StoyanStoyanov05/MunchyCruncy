<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Recipe;
use App\Models\Ingredient;

class RecipeIngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all recipe and ingredient IDs
        $recipeIds = Recipe::pluck('id')->toArray();
        $ingredientIds = Ingredient::pluck('id')->toArray();

        // Ensure there are recipes and ingredients before attaching
        if (empty($recipeIds) || empty($ingredientIds)) {
            return;
        }

        // Loop through recipes and assign random ingredients
        foreach ($recipeIds as $recipeId) {
            // Select 2 to 5 random ingredients for each recipe
            $randomIngredients = collect($ingredientIds)->random(rand(2, 5));

            foreach ($randomIngredients as $ingredientId) {
                Recipe::find($recipeId)->ingredients()->attach($ingredientId, [
                    'quantity' => rand(1, 10), // Example additional pivot field
                    // 'created_at' => now(),
                    // 'updated_at' => now(),
                ]);
            }
        }   
    }
}