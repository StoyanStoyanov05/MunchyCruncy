<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource; // Import the UserResource
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
