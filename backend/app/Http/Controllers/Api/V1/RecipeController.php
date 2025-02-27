<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\RatingResource;
use App\Http\Resources\RecipeResource;
use App\Models\Rating;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RecipeController extends Controller
{    
    // GET: api/v1/recipes
    public function index()
    {
        return RecipeResource::collection(
            Recipe::with('ingredients')->paginate()
        );
    }

    // GET: api/v1/recipes/user/{user_id}
    public function recipesByUser($user_id)
    {
        $recipes = Recipe::where('user_id', $user_id)->paginate();
   
        if ($recipes->isEmpty()) {
            return response()->json(['message' => 'No recipes found for this user'], 404);
        }
   
        return RecipeResource::collection($recipes);
    }

    // GET: api/v1/recipes/{id}
    public function show($id)
    {
        $recipe = Recipe::with(
            ['ingredients', 'ratings']
        )->find($id);

        if (!$recipe) {
            return response()->json(['message' => 'Recipe not found'], 404);
        }

        return new RecipeResource($recipe);
    }

            
    // GET: api/v1/recipes/search?ingredients[]=tomato&ingredients[]=chicken
    public function searchByIngredientNames(Request $request)
    {
        $ingredientNames = $request->input('ingredients', []);
        if (empty($ingredientNames)) {
            return response()->json([
                'message' => 'No ingredients provided'
            ], 400);
        }
        $recipes = Recipe::whereHas('ingredients', function ($query) use ($ingredientNames) {
            $query->whereIn('name', $ingredientNames);
        }, '>=', count($ingredientNames))->with('ingredients')->get();
        if ($recipes->isEmpty()) {
            return response()->json([
                'message' => 'No recipes found'
            ], 404);
        }
        return RecipeResource::collection($recipes);
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
            'image_url' => 'nullable|string', // Base64 image URL
            'ingredients' => 'array',
            'ingredients.*' => 'integer|exists:ingredients,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        // Handle image upload if base64 provided
        $imageName = null;
        if ($request->has('image_url') && !empty($request->image_url)) {
            $imageName = $this->saveImageFromBase64($request->image_url);
        }

        // Create a new recipe
        $recipe = Recipe::create($request->except('ingredients', 'image_url') + ['image_url' => $imageName]);

        // Attach ingredients if provided
        if ($request->has('ingredients')) {
            $recipe->ingredients()->attach($request->ingredients);
        }

        return new RecipeResource($recipe);
    }

    // POST /api/v1/recipes/1/ratings/update-or-create
    public function updateOrCreateRating(
        Request $request,
        $recipe_id
    ) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'rating' => 'required|integer|min:1|max:5',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        // Find or create the rating by user and recipe
        $rating = Rating::updateOrCreate(
            [
                'user_id' => $request->user_id,
                'recipe_id' => $recipe_id,
            ],
            [
                'rating' => $request->rating,
            ]
        );

        return new RatingResource($rating);
    }

    // PUT/PATCH: api/v1/recipes/{id}
    public function update(Request $request, $id)
    {
        $recipe = Recipe::find($id);

        if (!$recipe) {
            return response()->json(['message' => 'Recipe not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'instructions' => 'sometimes|string',
            'image_url' => 'nullable|string',
            'ingredients' => 'array',
            'ingredients.*' => 'integer|exists:ingredients,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Handle image upload if base64 provided
        $imageName = $recipe->image_url;  // Keep the old image name by default
        if ($request->has('image_url') && !empty($request->image_url)) {
            $imageName = $this->saveImageFromBase64($request->image_url);
        }

        // Update the recipe fields if provided
        $recipe->update($request->only([
            'title',
             'description',
              'instructions'
        ]) + [
               'image_url' => $imageName
        ]);

        // Sync ingredients if provided
        if ($request->has('ingredients')) {
            $recipe->ingredients()->sync($request->ingredients);
        }

        return new RecipeResource($recipe);
    }

    // DELETE: api/v1/recipes/{id}
    public function destroy($id)
    {
        $recipe = Recipe::find($id);

        if (!$recipe) {
            return response()->json(['message' => 'Recipe not found'], 404);
        }

        // If the recipe has an image, delete the image from storage
        if ($recipe->image_url) {
            $this->deleteImage($recipe->image_url);
        }

        $recipe->delete();

        return response()->json(['message' => 'Recipe deleted successfully'], 200);
    }

    // DELETE: api/v1/recipes/{recipe_id}/ingredients/{ingredient_id}
    public function removeIngredient($recipe_id, $ingredient_id)
    {
        $recipe = Recipe::find($recipe_id);

        if (!$recipe) {
            return response()->json([
                'message' => 'Recipe not found'
            ], 404);
        }

        // Check if the ingredient exists in the pivot table
        if (!$recipe->ingredients()->where(
            'ingredient_id',
            $ingredient_id
        )->exists()) {
            return response()->json([
                'message' => 'Ingredient not found in this recipe'
            ], 404);
        }

        // Detach the ingredient from the recipe (removes from pivot table)
        $recipe->ingredients()->detach($ingredient_id);

        return response()->json([
            'message' => 'Ingredient removed from recipe successfully'
        ], 200);
    }

    // Helper function to save image from base64
    private function saveImageFromBase64($base64Image)
    {
        // Extract file extension
        $extension = explode(';', explode(
            '/',
            mime_content_type($base64Image)
        )[1])[0];
        $extension = $extension == 'jpeg' ? 'jpg' : $extension;  // Adjust for .jpeg

        // Generate a unique filename
        $imageName = Str::random(10) . '.' . $extension;

        // Decode the base64 string and save the image
        $imageData = base64_decode(preg_replace(
            '#^data:image/\w+;base64,#i',
            '',
            $base64Image
        ));
        $imagePath = public_path('images/' . $imageName);

        file_put_contents($imagePath, $imageData);

        return $imageName;
    }

    // Helper function to delete image
    private function deleteImage($imageName)
    {
        $imagePath = public_path('images/' . $imageName);
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
}
