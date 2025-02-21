<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserIngredient extends Model
{
    /** @use HasFactory<\Database\Factories\UserIngredientsFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ingredient_id',
        'quantity'
    ];

    /**
     * Define relationship with User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Define relationship with Ingredient
     */
    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }
}
