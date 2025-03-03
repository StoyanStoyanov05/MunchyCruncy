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

       

        Route::post(
            'users/login',
            [UserController::class, 'login']
        );

        Route::apiResource(
            'users',
            UserController::class
          )->only(
            [
                'index',
                'store'
            ]
          ); //Public routes

        Route::middleware([
            'check.bearer.token',
        ])->group(function () {
            Route::apiResource(
                'users',
                UserController::class
            )->except([
                'index',
                'store'
            ]); // Protect everythigng else

        });
        
        Route::apiResource(
            'ingredients',
            IngredientController::class
        );

            Route::get('/recipes', [RecipeController::class, 'index']);         //Get all recipes
            Route::get(
                'recipes/{id}',
                 [RecipeController::class, 'show']
                )->where('id', '[0-9]+');   
                
            Route::post(
                'recipes/{recipe_id}/ratings/update-or-create',
                [
                    RecipeController::class,
                    'updateOrCreateRating'
                ]
            );
                
            Route::get('/recipes/user/{user_id}', [
                RecipeController::class,
                 'recipesByUser'
            ]);

            Route::get(
                'recipes/search',
                [RecipeController::class, 'searchByIngredientNames']
            );
            
            Route::delete(
                '/recipes/{recipe_id}/ingredients/{ingredient_id}',
                [RecipeController::class, 'removeIngredient']
            );
            
            Route::post('/recipes', [RecipeController::class, 'store']);         //Create a new recipe
            Route::put('/recipes/{id}', [RecipeController::class, 'update']);   //Update a recipe
            Route::delete('/recipes/{id}', [RecipeController::class, 'destroy']); //Delete a recipe

            Route::apiResource(
                'recipe-ingredients',
                RecipeIngredientController::class
            );

            // Adding Route for Ratings
        Route::prefix('recipes/{recipe_id}/ratings')->group(function () {
            Route::get('/', [RatingController::class, 'index']);
            Route::post('/', [RatingController::class, 'store']);
            Route::get('{id}', [RatingController::class, 'show']);
            Route::put('{id}', [RatingController::class, 'update']);
            Route::delete('{id}', [RatingController::class, 'destroy']);
        });

            // Shopping Lists Routes
        Route::get('shopping-lists/{user_id}', [ShoppingListController::class, 'index']); // Get all shopping lists for a user
        Route::get('shopping-lists/{user_id}/{id}', [ShoppingListController::class, 'show']); // Get a specific shopping list with items
        Route::post('shopping-lists/{user_id}', [ShoppingListController::class, 'store']); // Create a new shopping list
        Route::put('shopping-lists/{user_id}/{id}', [ShoppingListController::class, 'update']); // Update a shopping list (including items)
        Route::delete('shopping-lists/{user_id}/{id}', [ShoppingListController::class, 'destroy']); // Delete a shopping list
        

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

