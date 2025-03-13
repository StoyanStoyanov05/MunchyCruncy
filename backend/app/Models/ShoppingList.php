<?php

namespace App\Models;

use App\Models\ShoppingListItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShoppingList extends Model
{
    /** @use HasFactory<\Database\Factories\ShoppingListFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
    ];

    /**
     * Define relationship with User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }

    /**
     * Define relationship with ShoppingListItems (if exists)
     */
    public function items(): HasMany
    {
        return $this->hasMany(related: ShoppingListItem::class);
    }

}
