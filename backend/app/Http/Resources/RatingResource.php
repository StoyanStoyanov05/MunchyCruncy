<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RatingResource extends JsonResource
{
    public function toArray(Request $request)
    {
        return [
            'id' => $this->id,
            'userId' => $this->user_id,
            'recipeId' => $this->recipe_id,
            'rating' => $this->rating,
        ];
    }
}
