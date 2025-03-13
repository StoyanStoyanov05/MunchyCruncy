<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Recipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'instructions',
        'image_url',
    ];

    // Define relationship with User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Many-to-Many relationship with Ingredients
    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class, 'recipe_ingredients')
            ->using(RecipeIngredient::class)
            ->withPivot('quantity'); // Include extra pivot fields if needed
    }

    // Recipe.php
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }
}
