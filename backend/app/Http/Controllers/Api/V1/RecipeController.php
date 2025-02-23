<?php

// TODO: Filter by ingredients
// https://youtu.be/YGqCZjdgJJk?t=2415

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\RecipeResource;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RecipeController extends Controller
{
    // GET: api/v1/recipes
    public function index()
    {
        // Fetch all recipes and return as a collection
        return RecipeResource::collection(Recipe::paginate());
    }

    // GET: api/v1/recipes
    public function index()
    {
        return RecipeResource::collection(Recipe::paginate());
    }
    
    // GET: api/v1/recipes/{id}
    public function show($id)
    {
        $recipe = Recipe::find($id);

        if (!$recipe) {
            return response()->json(['message' => 'Recipe not found'], 404);
        }

        return new RecipeResource($recipe);
    }

    // POST: api/v1/recipes
    public function store(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'instructions' => 'required|string',
            'image_url' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create a new recipe
        $recipe = Recipe::create($request->all());

        return new RecipeResource($recipe);
    }

    // PUT/PATCH: api/v1/recipes/{id}
    public function update(Request $request, $id)
    {
        $recipe = Recipe::find($id);

        if (!$recipe) {
            return response()->json(['message' => 'Recipe not found'], 404);
        }

        // Validate the request data
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'instructions' => 'sometimes|string',
            'image_url' => 'nullable|string|max:200',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Update the recipe fields if provided
        $recipe->update($request->only(['title', 'description', 'instructions', 'image_url']));

        return new RecipeResource($recipe);
    }

    // DELETE: api/v1/recipes/{id}
    public function destroy($id)
    {
        $recipe = Recipe::find($id);

        if (!$recipe) {
            return response()->json(['message' => 'Recipe not found'], 404);
        }

        $recipe->delete();

        return response()->json(['message' => 'Recipe deleted successfully'], 200);
    }
}
