<?php

namespace Database\Seeders;

use App\Models\ShoppingListItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShoppingListItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ShoppingListItem::factory(500)->create(); // Example to create 50 shopping list items

    }
}
