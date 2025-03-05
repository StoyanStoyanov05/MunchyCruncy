<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource; // Import the UserResource
use App\Models\AuthToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;

class UserController extends Controller
{
    // GET: api/v1/users
    public function index()
    {
        // Return all users wrapped in the UserResource collection
        $users = User::all();
        return UserResource::collection($users);
    }

    // GET: api/v1/users/{id}
    public function show($id)
    {
        // Find the user by ID
        $user = User::find($id);

        if ($user) {
            // Return the user wrapped in a UserResource
            return new UserResource($user);
        } else {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
    }

    // POST: api/v1/users/login 
    public function login(Request $request)
    {
        // Validate input data
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if the user exists
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);  // Unauthorized
        }

        $user->tokens()->delete();
        
        // Step 1: Create the token using `createToken` method
        $newToken = $user->createToken('YourAppName');

        // Step 2: Get the plain-text token generated by `createToken`
        $plainTextToken = $newToken->plainTextToken;

        // Step 3: Access the `PersonalAccessToken` model
        $tokenModel = $newToken->accessToken; // Access the underlying PersonalAccessToken model

        // Step 4: Manually update fields such as `abilities`, `expires_at`, etc.
        $tokenModel->tokenable_type = 'App\Models\User';  // Set the tokenable type (polymorphic relation)
        $tokenModel->tokenable_id = $user->id;  // Set the user ID for the polymorphic relation
        $tokenModel->name = 'YourAppName';  // Set the name of the token
        $tokenModel->token = $plainTextToken;  // Set the token value (using the generated token)
        $tokenModel->abilities = json_encode(['*']);  // Set the abilities for the token
        $tokenModel->expires_at = now()->addDays(7);  // Set the expiration time (7 days from now)
        $tokenModel->save();  // Save the changes to the database


        // $token = PersonalAccessToken::create([
        //     'tokenable_type' => 'App\Models\User',  // Correctly set the type for polymorphic relation
        //     'tokenable_id' => $user->id,            // Correctly set the user's ID for polymorphic relation

        //     'name' => 'YourAppName',

        //     'token' => "asd",

        //     'abilities' => json_encode(['*']),
        //     'expires_at' => now()->addDays(7),
        // ]);

        // Return the token along with user info
        return response()->json([
            'message' => 'Login successful',
            'token' => $plainTextToken,
            'user' => new UserResource($user)
        ], 200);
    }
    
    // POST: api/v1/users
    public function store(Request $request)
    {
        // Validation rules
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create a new user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Return the created user wrapped in a UserResource
        return new UserResource($user);
    }

    // PUT: api/v1/users/{id}
    public function update(Request $request, $id)
    {
        // Find the user by ID
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Validation rules
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Update the user details
        $user->update([
            'name' => $request->name ?? $user->name,
            'email' => $request->email ?? $user->email,
            'password' => $request->password ? Hash::make($request->password) : $user->password,
        ]);

        // Return the updated user wrapped in a UserResource
        return new UserResource($user);
    }

    // DELETE: api/v1/users/{id}
    public function destroy($id)
    {
        // Find the user by ID
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Delete the user
        $user->delete();

        return response()->json(['message' => 'User deleted successfully'], 200);
    }
}
