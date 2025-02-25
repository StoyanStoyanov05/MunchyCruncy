<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\IngredientResource; // Import the IngredientResource
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class IngredientController extends Controller
{
    // GET: api/v1/ingredients
    public function index(Request $request)
    {
        // Check if a search term is provided, otherwise fetch all ingredients
        $searchTerm = $request->query('search', ''); // Get 'search' query parameter if provided, default to an empty string

        if ($searchTerm) {
            // If a search term is provided, filter the ingredients
            $ingredients = Ingredient::where('name', 'like', '%' . $searchTerm . '%')->paginate();
        } else {
            // Otherwise, fetch all ingredients
            $ingredients = Ingredient::paginate();
        }
        
        // Return the results as a collection wrapped in the IngredientResource
        return IngredientResource::collection($ingredients); 
    }

    // GET: api/v1/ingredients/{id}
    
    public function show($id)
    {
        // Find an ingredient by ID
        $ingredient = Ingredient::find($id);

        if ($ingredient) {
            // Return the ingredient wrapped in an IngredientResource
            return new IngredientResource($ingredient); // Use IngredientResource to format the single ingredient
        } else {
            return response()->json([
                'message' => 'Ingredient not found'
            ], 404);
        }
    }

    // POST: api/v1/ingredients
    public function store(Request $request)
    {
        // Validation rules
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:ingredients,name',
            'is_liquid' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create new ingredient
        $ingredient = Ingredient::create([
            'name' => $request->name,
            'is_liquid' => $request->has('is_liquid') ? $request->is_liquid : false,
        ]);

        // Return the created ingredient wrapped in an IngredientResource
        return new IngredientResource($ingredient);
    }

    // PUT: api/v1/ingredients/{id}
    public function update(Request $request, $id)
    {
        // Find the ingredient by ID
        $ingredient = Ingredient::find($id);

        if (!$ingredient) {
            return response()->json(['message' => 'Ingredient not found'], 404);
        }

        // Validation rules
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255|unique:ingredients,name,' . $id,
            'is_liquid' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Update ingredient
        $ingredient->update([
            'name' => $request->name ?? $ingredient->name,
            'is_liquid' => $request->has('is_liquid') ? $request->is_liquid : $ingredient->is_liquid,
        ]);

        // Return the updated ingredient wrapped in an IngredientResource
        return new IngredientResource($ingredient);
    }

    // DELETE: api/v1/ingredients/{id}
    public function destroy($id)
    {
        // Find the ingredient by ID
        $ingredient = Ingredient::find($id);

        if (!$ingredient) {
            return response()->json(['message' => 'Ingredient not found'], 404);
        }

        // Delete ingredient
        $ingredient->delete();

        return response()->json(['message' => 'Ingredient deleted successfully'], 200);
    }
}
