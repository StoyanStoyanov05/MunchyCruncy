<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserIngredientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'ingredient_id' => $this->ingredient_id,
            'ingredient' => new IngredientResource($this->whenLoaded('ingredient')), // Assuming IngredientResource exists
            'quantity' => $this->quantity
        ];
    }
}
