<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        // Get the Authorization header value
        $authHeader = $request->header('Authorization');

        // Ensure the header contains the "Bearer" keyword and the token
        if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $token = $matches[1]; // The token itself

            // Check if the token is valid
            if (Auth::guard('sanctum')->user()) {
                return $next($request);
            } else {
                return response()->json(['message' => 'Invalid or expired token'], 401);
            }
        }

        // If the Bearer token is not found, return an error
        return response()->json(['message' => 'Invalid Authorization format'], 401);
    }
}