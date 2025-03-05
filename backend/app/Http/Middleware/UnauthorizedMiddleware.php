<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UnauthorizedMiddleware
{
    /**
     * Handle an incoming request and always return an unauthorized response.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        return response()->json([
            'message' => 'Unauthorized request'
        ], 401);
    }
}