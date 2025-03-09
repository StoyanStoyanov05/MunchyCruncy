<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class RecipeIngredient extends Pivot
{
    protected $table = 'recipe_ingredients'; // Explicitly define the table name if necessary

    protected $fillable = [
        'recipe_id',
        'ingredient_id',
        'quantity', // Example additional field
    ];
}