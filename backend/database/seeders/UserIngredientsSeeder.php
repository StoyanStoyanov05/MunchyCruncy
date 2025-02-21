<?php

namespace Database\Seeders;

use App\Models\UserIngredient;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserIngredientsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserIngredient::factory(200)->create();
    }
}
