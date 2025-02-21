<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\RatingResource;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RatingController extends Controller
{
    // GET: api/v1/recipes/{recipe_id}/ratings
    public function index($recipe_id)
    {
        $ratings = Rating::where('recipe_id', $recipe_id)->get();
        return RatingResource::collection($ratings);
    }

    // GET: api/v1/recipes/{recipe_id}/ratings/{id}
    public function show($recipe_id, $id)
    {
        $rating = Rating::where('recipe_id', $recipe_id)->find($id);

        if (!$rating) {
            return response()->json(['message' => 'Rating not found'], 404);
        }

        return new RatingResource($rating);
    }

    // POST: api/v1/recipes/{recipe_id}/ratings
    public function store(Request $request, $recipe_id)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $rating = Rating::create([
            'user_id' => $request->user_id,
            'recipe_id' => $recipe_id,
            'rating' => $request->rating,
        ]);

        return new RatingResource($rating);
    }

    // PUT: api/v1/recipes/{recipe_id}/ratings/{id}
    public function update(Request $request, $recipe_id, $id)
    {
        $rating = Rating::where('recipe_id', $recipe_id)->find($id);

        if (!$rating) {
            return response()->json(['message' => 'Rating not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $rating->update(['rating' => $request->rating]);

        return new RatingResource($rating);
    }

    // DELETE: api/v1/recipes/{recipe_id}/ratings/{id}
    public function destroy($recipe_id, $id)
    {
        $rating = Rating::where('recipe_id', $recipe_id)->find($id);

        if (!$rating) {
            return response()->json(['message' => 'Rating not found'], 404);
        }

        $rating->delete();

        return response()->json(['message' => 'Rating deleted successfully'], 200);
    }
}
