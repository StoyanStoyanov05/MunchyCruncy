<?php

namespace Database\Seeders;

use App\Models\ShoppingList;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ShoppingListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ShoppingList::factory(100)->create();
    }
}
