<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ShoppingListResource;
use App\Models\ShoppingList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShoppingListController extends Controller
{
    // GET: api/v1/shopping-lists/{user_id}
    public function index($user_id)
    {
        // Return all shopping lists for a specific user
        $shoppingLists = ShoppingList::where('user_id', $user_id)->get();
        return ShoppingListResource::collection($shoppingLists);
    }

    // GET: api/v1/shopping-lists/{user_id}/{id}
    public function show($user_id, $id)
    {
        // Find the shopping list by ID and user_id
        $shoppingList = ShoppingList::where('user_id', $user_id)
        ->with('items.ingredient') // Eager load the items and ingredients
        ->find($id); 

        return new ShoppingListResource($shoppingList);
    }

    // POST: api/v1/shopping-lists/{user_id}
    public function store(Request $request, $user_id)
    {
        // Validation rules
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create a new shopping list with the provided user_id
        $shoppingList = ShoppingList::create([
            'name' => $request->name,
            'user_id' => $user_id,
        ]);

        return new ShoppingListResource($shoppingList);
    }

    // PUT: api/v1/shopping-lists/{user_id}/{id}
    public function update(Request $request, $user_id, $id)
    {
        // Find the shopping list by ID and user_id
        $shoppingList = ShoppingList::where('user_id', $user_id)->find($id);

        if (!$shoppingList) {
            return response()->json(['message' => 'Shopping List not found for this user'], 404);
        }

        // Validation rules
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Update the shopping list
        $shoppingList->update([
            'name' => $request->name,
        ]);

        return new ShoppingListResource($shoppingList);
    }

    // DELETE: api/v1/shopping-lists/{user_id}/{id}
    public function destroy($user_id, $id)
    {
        // Find the shopping list by ID and user_id
        $shoppingList = ShoppingList::where('user_id', $user_id)->find($id);

        if (!$shoppingList) {
            return response()->json(['message' => 'Shopping List not found for this user'], 404);
        }

        // Delete the shopping list
        $shoppingList->delete();

        return response()->json(['message' => 'Shopping List deleted successfully'], 200);
    }
}
