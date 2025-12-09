<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceJsonForApi
{
    public function handle(Request $request, Closure $next): Response
    {
        // Ensure API routes always expect JSON
        if (str_starts_with($request->getPathInfo(), '/api')) {
            $request->headers->set('Accept', 'application/json');
        }

        $response = $next($request);

        // Convert auth redirects to JSON 401 for API consumers
        if ($response->isRedirection() && str_starts_with($request->getPathInfo(), '/api')) {
            $location = $response->headers->get('Location');
            if ($location && (str_contains($location, '/login') || $location === url('/'))) {
                return response()->json([
                    'message' => 'Unauthenticated.'
                ], 401);
            }
        }

        return $response;
    }
}
