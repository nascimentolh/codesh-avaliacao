<?php

namespace App\Presentation\Middleware;

class JsonResponseMiddleware
{
    public function handle(array $request, callable $next)
    {
        // Set JSON response headers
        header('Content-Type: application/json; charset=utf-8');
        header('X-Content-Type-Options: nosniff');

        // CORS headers (adjust as needed for production)
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, X-API-KEY, Authorization');

        // Handle preflight requests
        if ($request['method'] === 'OPTIONS') {
            return [
                'status' => 200,
                'body' => '',
            ];
        }

        return $next($request);
    }
}
