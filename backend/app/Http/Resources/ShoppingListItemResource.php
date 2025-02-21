<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShoppingListItemResource extends JsonResource
{
    public function toArray(Request $request)
    {
        return [
            'id' => $this->id,
            'ingredient' => new IngredientResource($this->whenLoaded('ingredient')),
            'purchased' => $this->purchased,
        ];
    }
}
