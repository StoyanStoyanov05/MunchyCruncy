<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ShoppingListItemResource;
use App\Models\ShoppingListItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShoppingListItemController extends Controller
{
    // GET: api/v1/shopping-lists/{user_id}/{list_id}/items
    public function index($user_id, $list_id)
    {
        // Retrieve all items for a specific shopping list
        $items = ShoppingListItem::where(
            'shopping_list_id',
            $list_id
        )
            ->with('ingredient') //Ensure the ingredient relationship is loaded
            ->get();

        // Return the items as a collection of resources
        return ShoppingListItemResource::collection($items);
    }

    // POST: api/v1/shopping-lists/{user_id}/{list_id}/items
    public function store(Request $request, $user_id, $list_id)
    {
        $validator = Validator::make($request->all(), [
            'ingredient_id' => 'required|exists:ingredients,id',
            'purchased' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create a new shopping list item for the specified shopping list
        $item = ShoppingListItem::create([
            'shopping_list_id' => $list_id,
            'ingredient_id' => $request->ingredient_id,
            'purchased' => $request->purchased ?? false,
        ]);

        return new ShoppingListItemResource($item);
    }

    // PUT: api/v1/shopping-lists/{user_id}/{list_id}/items/{id}
    public function update(Request $request, $user_id, $list_id, $id)
    {
        $item = ShoppingListItem::where('shopping_list_id', $list_id)->find($id);

        if (!$item) {
            return response()->json(['message' => 'Item not found in this shopping list'], 404);
        }

        $validator = Validator::make($request->all(), [
            'purchased' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $item->update([
            'purchased' => $request->purchased,
        ]);

        return new ShoppingListItemResource($item);
    }

    // DELETE: api/v1/shopping-lists/{user_id}/{list_id}/items/{id}
    public function destroy($user_id, $list_id, $id)
    {
        $item = ShoppingListItem::where('shopping_list_id', $list_id)->find($id);

        if (!$item) {
            return response()->json(['message' => 'Item not found in this shopping list'], 404);
        }

        $item->delete();

        return response()->json(['message' => 'Item deleted successfully'], 200);
    }
}
