<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShoppingListItem extends Model
{
    /** @use HasFactory<\Database\Factories\ShoppingListItemFactory> */
    use HasFactory;

    protected $fillable = [
        'shopping_list_id',
        'ingredient_id',
        'purchased',
    ];

    /**
     * Define relationship with ShoppingList
     */
    public function shoppingList()
    {
        return $this->belongsTo(ShoppingList::class);
    }

    /**
     * Define relationship with Ingredient
     */
    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }
}
