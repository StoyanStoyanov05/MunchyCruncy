<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserIngredientResource;
use App\Models\UserIngredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserIngredientController extends Controller
{
    // GET: api/v1/users/{user_id}/ingredients
    public function index($user_id)
    {
        // Get all user ingredients
        $userIngredients = UserIngredient::where('user_id', $user_id)->get();
        return UserIngredientResource::collection($userIngredients);
    }

    // POST: api/v1/users/{user_id}/ingredients
    public function store(Request $request, $user_id)
    {
        // Validation rules
        $validator = Validator::make($request->all(), [
            'ingredient_id' => 'required|exists:ingredients,id',
            'quantity' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create a new user ingredient
        $userIngredient = UserIngredient::create([
            'user_id' => $user_id,
            'ingredient_id' => $request->ingredient_id,
            'quantity' => $request->quantity,
        ]);

        return new UserIngredientResource($userIngredient);
    }

    // GET: api/v1/users/{user_id}/ingredients/{id}
    public function show($user_id, $id)
    {
        // Find a specific user ingredient
        $userIngredient = UserIngredient::where('user_id', $user_id)->find($id);

        if (!$userIngredient) {
            return response()->json(['message' => 'User Ingredient not found for this user'], 404);
        }

        return new UserIngredientResource($userIngredient);
    }

    // PUT: api/v1/users/{user_id}/ingredients/{id}
    public function update(Request $request, $user_id, $id)
    {
        // Find the user ingredient
        $userIngredient = UserIngredient::where('user_id', $user_id)->find($id);

        if (!$userIngredient) {
            return response()->json(['message' => 'User Ingredient not found for this user'], 404);
        }

        // Validation rules
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Update the user ingredient
        $userIngredient->update([
            'quantity' => $request->quantity,
        ]);

        return new UserIngredientResource($userIngredient);
    }

    // DELETE: api/v1/users/{user_id}/ingredients/{id}
    public function destroy($user_id, $id)
    {
        // Find the user ingredient
        $userIngredient = UserIngredient::where('user_id', $user_id)->find($id);

        if (!$userIngredient) {
            return response()->json(['message' => 'User Ingredient not found for this user'], 404);
        }

        // Delete the user ingredient
        $userIngredient->delete();

        return response()->json(['message' => 'User Ingredient deleted successfully'], 200);
    }
}
