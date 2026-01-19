<?php

namespace App\Presentation;

class Router
{
    private array $routes = [];

    public function get(string $pattern, callable $handler): void
    {
        $this->addRoute('GET', $pattern, $handler);
    }

    public function post(string $pattern, callable $handler): void
    {
        $this->addRoute('POST', $pattern, $handler);
    }

    public function put(string $pattern, callable $handler): void
    {
        $this->addRoute('PUT', $pattern, $handler);
    }

    public function delete(string $pattern, callable $handler): void
    {
        $this->addRoute('DELETE', $pattern, $handler);
    }

    private function addRoute(string $method, string $pattern, callable $handler): void
    {
        $this->routes[] = [
            'method' => $method,
            'pattern' => $pattern,
            'handler' => $handler,
        ];
    }

    public function dispatch(string $method, string $uri): mixed
    {
        // Remove query string
        $uri = parse_url($uri, PHP_URL_PATH);

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $matches = $this->matchRoute($route['pattern'], $uri);

            if ($matches !== null) {
                return call_user_func_array($route['handler'], $matches);
            }
        }

        // No route matched
        http_response_code(404);
        return [
            'error' => 'Not Found',
            'message' => 'The requested endpoint does not exist',
        ];
    }

    private function matchRoute(string $pattern, string $uri): ?array
    {
        // Convert route pattern to regex
        // Example: /products/:code becomes /products/([^/]+)
        $regex = preg_replace('/:\w+/', '([^/]+)', $pattern);
        $regex = '#^' . $regex . '$#';

        if (preg_match($regex, $uri, $matches)) {
            // Remove the full match, keep only captured groups
            array_shift($matches);
            return $matches;
        }

        return null;
    }

    public function run(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];

        try {
            $result = $this->dispatch($method, $uri);

            if (is_array($result)) {
                echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
            } else {
                echo $result;
            }
        } catch (\Exception $e) {
            http_response_code(500);

            $response = [
                'error' => 'Internal Server Error',
                'message' => 'An unexpected error occurred',
            ];

            // Show detailed error in debug mode
            if (($_ENV['APP_DEBUG'] ?? false) === 'true' || ($_ENV['APP_DEBUG'] ?? false) === true) {
                $response['debug'] = [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                ];
            }

            echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        }
    }
}
