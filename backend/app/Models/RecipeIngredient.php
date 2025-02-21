<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecipeIngredient extends Model
{
    use HasFactory;

    // Disable automatic timestamping
    public $timestamps = false;


    protected $fillable = [
        'recipe_id',
        'ingredient_id'
    ];

    // Define the relationship with Recipe
    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }

    public function ingredients()
    {
        return $this->belongsToMany(
            Ingredient::class,
            'recipe_ingredients',
            'recipe_id',
            'ingredient_id'
        );
    }
}
