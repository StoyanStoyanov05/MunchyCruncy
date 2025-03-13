<?php

namespace App\Http\Resources;

use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecipeResource extends JsonResource
{
    public function toArray(Request $request)
    {
        return [
            'id' => $this->id,
            'userId' => $this->user_id,
            'title' => $this->title,
            'description' => $this->description,
            'instructions' => $this->instructions,
            'imageUrl' => $this->image_url,

            'ingredients' => IngredientResource::collection(
                $this->ingredients
            ), // Fetch all related ingredients

            'ratings' => RatingResource::collection(
                $this->ratings
            ), // Fetch all ratings for this recipe

            'averageRating' => round(
                (float) $this->ratings()->avg('rating'),
                2
            ), // Cast to float & round to 2 decimal places

        ];
    }
}
