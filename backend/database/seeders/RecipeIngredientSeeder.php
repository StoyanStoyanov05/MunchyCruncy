<?php

namespace Database\Seeders;

use App\Models\RecipeIngredient;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RecipeIngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 10 recipes with random user IDs between 1 and 10
        RecipeIngredient::factory(100)->create();
    }
}
