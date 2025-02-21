<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\RecipeIngredientResource;
use App\Models\RecipeIngredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RecipeIngredientController extends Controller
{
    // GET: api/v1/recipe-ingredients
    public function index()
    {
        return RecipeIngredientResource::collection(
            RecipeIngredient::with(['recipe', 'ingredients'])->get()
        );
    }

    // GET: api/v1/recipe-ingredients/{id}
    public function show($id)
    {
        $recipeIngredient = RecipeIngredient::with(['recipe', 'ingredients'])->find($id);

        if (!$recipeIngredient) {
            return response()->json(['message' => 'Recipe-Ingredient relationship not found'], 404);
        }

        return new RecipeIngredientResource($recipeIngredient);
    }

    // POST: api/v1/recipe-ingredients (Now handles multiple ingredients)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'recipe_id' => 'required|exists:recipes,id',
            'ingredient_ids' => 'required|array',  // Ensure it's an array
            'ingredient_ids.*' => 'exists:ingredients,id', // Ensure each ID exists
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Attach multiple ingredients to a recipe
        $recipeIngredient = RecipeIngredient::where('recipe_id', $request->recipe_id)->firstOrCreate([
            'recipe_id' => $request->recipe_id,
        ]);

        // Sync (add/remove) multiple ingredients
        $recipeIngredient->ingredients()->sync($request->ingredient_ids);

        return new RecipeIngredientResource($recipeIngredient->load(['recipe', 'ingredients']));
    }

    // DELETE: api/v1/recipe-ingredients/{id}
    public function destroy($id)
    {
        $recipeIngredient = RecipeIngredient::find($id);

        if (!$recipeIngredient) {
            return response()->json(['message' => 'Recipe-Ingredient relationship not found'], 404);
        }

        $recipeIngredient->delete();

        return response()->json(['message' => 'Recipe-Ingredient relationship deleted successfully'], 200);
    }
}
