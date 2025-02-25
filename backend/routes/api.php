<?php

use App\Http\Controllers\Api\V1\IngredientController;
use App\Http\Controllers\Api\V1\RatingController;
use App\Http\Controllers\Api\V1\RecipeController;
use App\Http\Controllers\Api\V1\RecipeIngredientController;
use App\Http\Controllers\Api\V1\ShoppingListController;
use App\Http\Controllers\Api\V1\ShoppingListItemController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\UserIngredientController;
use App\Models\ShoppingList;
use Illuminate\Support\Facades\Route;


Route::group(
    [
        'prefix' => 'v1',
        'namespace' => 'App\Http\Controllers\Api\V1'
    ],
    function () {

        Route::apiResource(
            'users',
            UserController::class
        );

        Route::post(
            'users/login',
            [UserController::class, 'login']
        );

        Route::apiResource(
            'ingredients',
            IngredientController::class
        );

            Route::get('/recipes', [RatingController::class, 'index']);         //Get all recipes
            Route::post('/recipes/{id}', [RatingController::class, 'show']);    //Get a single recipe by ID
            Route::get('/recipes', [RatingController::class, 'store']);         //Create a new recipe
            Route::put('/recipes/{id}', [RatingController::class, 'update']);   //Update a recipe
            Route::delete('/recipes/{id}', [RatingController::class, 'destroy']); //Delete a recipe

            Route::apiResource(
                'recipe-ingredients',
                RecipeIngredientController::class
            )
                ->exept(['update']);

        // Adding Route for Ratings
        Route::prefix('recipes/{recipe_id}/rating')->group(function () {
            Route::get('/', [RatingController::class, 'index']);
            Route::post('/', [RatingController::class, 'store']);
            Route::get('{id}', [RatingController::class, 'show']);
            Route::put('{id}', [RatingController::class, 'update']);
            Route::delete('{id}', [RatingController::class, 'delete']);
        });
        // Shopping Lists Routes
        Route::prefix('shopping-lists/{user_id}')->group(function () {
            Route::get('/', [ShoppingListController::class, 'index']);  // Show all shopping lists for a user
            Route::get('{id}', [ShoppingListController::class, 'show']);  // Show a specific shopping list with items
        });

        // Shopping List Item Routes
        Route::prefix('shopping-lists/{user_id}/{list_id}/items')->group(function () {
            Route::get('/', [ShoppingListItemController::class, 'index']);
            Route::post('/', [ShoppingListItemController::class, 'store']);  // Add item to shopping list
            Route::put('{id}', [ShoppingListItemController::class, 'update']);  // Update item in shopping list
            Route::delete('{id}', [ShoppingListItemController::class, 'destroy']);  // Delete item from shopping list
        });

        // User Ingredients Routes
        Route::prefix('users/{user_id}/ingredients')->group(function () {
            Route::get('/', [UserIngredientController::class, 'index']);  // Get all ingredients for a user
            Route::post('/', [UserIngredientController::class, 'store']);  // Add ingredient to user's list
            Route::get('{id}', [UserIngredientController::class, 'show']);  // Show specific user ingredient
            Route::put('{id}', [UserIngredientController::class, 'update']);  // Update user ingredient
            Route::delete('{id}', [UserIngredientController::class, 'destroy']);  // Delete user ingredient
        });
    }
);
