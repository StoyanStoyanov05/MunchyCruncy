<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class CheckBearerToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if the Authorization header is present
        if (!$request->hasHeader('Authorization')) {
            return response()->json(['message' => 'Authorization token is missing'], 401);
        }

        $authHeader = $request->header('Authorization');

        // Extract the token from the Authorization header
        if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $rawToken = $matches[1]; // Extract raw token

            // Find the token in the personal_access_tokens table using the raw token
            $accessToken = PersonalAccessToken::where(
                'token',
                 $rawToken
                 )->first();

            // If token is not found or expired
            if (!$accessToken || $accessToken->expires_at <= now()) {
                 return response()->json(['message' => 'Invalid or expired token'], 401);
            }
        
        
            // Get the user associated with the token using the polymorphic relation
            $user = $accessToken->tokenable; // This assumes the token is for a user

            // Optionally, check if the user ID from the token matches the requested user ID
            // If the route expects a user ID, e.g. in the URL, check if it's authorized to perform the action
            if ($request->route('user') && $request->route('user') != $user->id) {
                return response()->json(['message' => 'User ID not authorized to perform this action'], 403);
            }

            // Authenticate the user associated with the token
            Auth::setUser($user);  // Set the authenticated user

            // Proceed with the next middleware
            return $next($request);
    
        }
        // If the Bearer token is not found, return an error
        return response()->json(['message' => 'Invalid Authorization format'], 401);
    }
}