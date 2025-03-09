<?php

namespace Database\Seeders;

use App\Models\Rating;
use App\Models\ShoppingList;
use App\Models\ShoppingListItem;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([
            UserSeeder::class,
            
            IngredientSeeder::class,

            RecipeSeeder::class,
            RecipeIngredientSeeder::class,

            RatingSeeder::class,

            ShoppingListSeeder::class,
            ShoppingListItemSeeder::class
        ]);
    }
}
