<?php

namespace App\Presentation\Middleware;

class ApiKeyMiddleware
{
    private string $apiKey;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function handle(array $request, callable $next)
    {
        // Skip API key validation for health check endpoint
        if ($request['uri'] === '/' && $request['method'] === 'GET') {
            return $next($request);
        }

        $providedKey = $this->getApiKeyFromRequest();

        if ($providedKey === null || $providedKey !== $this->apiKey) {
            return [
                'status' => 401,
                'body' => json_encode([
                    'error' => 'Unauthorized',
                    'message' => 'Invalid or missing API key. Include X-API-KEY header.',
                ]),
            ];
        }

        return $next($request);
    }

    private function getApiKeyFromRequest(): ?string
    {
        // Check X-API-KEY header
        if (isset($_SERVER['HTTP_X_API_KEY'])) {
            return $_SERVER['HTTP_X_API_KEY'];
        }

        // Check Authorization header (Bearer token)
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $auth = $_SERVER['HTTP_AUTHORIZATION'];
            if (preg_match('/Bearer\s+(.*)$/i', $auth, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }
}
