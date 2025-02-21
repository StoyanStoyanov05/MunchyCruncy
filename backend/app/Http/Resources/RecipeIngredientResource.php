<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecipeIngredientResource extends JsonResource
{
    public function toArray(Request $request)
    {
        return [
            'id' => $this->id,
            // 'recipeId' => $this->recipe_id,
            // 'ingredientIds' => $this->ingredients->pluck('id'), // Get array of ingredient IDs
            'recipe' => new RecipeResource($this->whenLoaded('recipe')),
            'ingredients' => IngredientResource::collection($this->whenLoaded('ingredients')), // Return multiple ingredients
        ];
    }
}
