<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ShoppingListResource;
use App\Models\ShoppingList;
use App\Models\ShoppingListItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


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
            ->with('items.ingredient') // Eager load items and their ingredients
            ->findOrFail($id);

        return new ShoppingListResource($shoppingList);
    }

    // POST: api/v1/shopping-lists/{user_id}
    public function store(Request $request, $user_id)
    {
        // Validation rules
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'items' => 'required|array', // Ensure items array is present
            'items.*.id' => 'required|exists:ingredients,id', // Validate each ingredient_id
            'items.*.purchased' => 'boolean', // Validate purchased field
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Start a database transaction to ensure data consistency
        DB::beginTransaction();

        try {
            // Create a new shopping list with the provided user_id
            $shoppingList = ShoppingList::create([
                'name' => $request->name,
                'user_id' => $user_id,
            ]);

            // Add ingredients to the shopping list
            foreach ($request->items as $item) {
                ShoppingListItem::create([
                    'shopping_list_id' => $shoppingList->id,
                    'ingredient_id' => $item['id'],
                    'purchased' => $item['purchased'] ?? false,
                ]);
            }

            DB::commit(); // Commit the transaction

            return new ShoppingListResource($shoppingList);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback the transaction in case of an error
            return response()->json(['message' => 'Failed to create shopping list', 'error' => $e->getMessage()], 500);
        }
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
            'items' => 'required|array', // Ensure items array is present
            'items.*.id' => 'required|exists:ingredients,id', // Validate each ingredient_id
            'items.*.purchased' => 'boolean', // Validate purchased field
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Start a database transaction to ensure data consistency
        DB::beginTransaction();

        try {
            // Update the shopping list name
            $shoppingList->update([
                'name' => $request->name,
            ]);

            // Delete all existing shopping list items for this list
            ShoppingListItem::where('shopping_list_id', $id)->delete();

            // Add new items
            foreach ($request->items as $item) {
                ShoppingListItem::create([
                    'shopping_list_id' => $id,
                    'ingredient_id' => $item['id'],
                    'purchased' => $item['purchased'] ?? false,
                ]);
            }

            DB::commit(); // Commit the transaction

            return new ShoppingListResource($shoppingList);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback the transaction in case of an error
            return response()->json(['message' => 'Failed to update shopping list', 'error' => $e->getMessage()], 500);
        }
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