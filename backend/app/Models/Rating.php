<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    /** @use HasFactory<\Database\Factories\RatingFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'recipe_id',
        'rating',
    ];

    /**
     * Define relationship with User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Define relationship with Recipe
     */
    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }
}
