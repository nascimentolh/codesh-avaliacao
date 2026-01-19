<?php

declare(strict_types=1);

use App\Application\UseCase\GetProduct;
use App\Application\UseCase\ListProducts;
use App\Application\UseCase\UpdateProduct;
use App\Application\UseCase\DeleteProduct;
use App\Infrastructure\Database\Connection;
use App\Infrastructure\Repository\SQLiteProductRepository;
use App\Infrastructure\Repository\SQLiteImportHistoryRepository;
use App\Presentation\Controller\HealthController;
use App\Presentation\Controller\ProductController;
use App\Presentation\Middleware\ApiKeyMiddleware;
use App\Presentation\Middleware\JsonResponseMiddleware;
use App\Presentation\Router;

// Record start time for uptime calculation
$startTime = microtime(true);

// Load autoloader
require_once __DIR__ . "/../vendor/autoload.php";

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/..");
$dotenv->load();

// Load configuration
$dbConfig = require __DIR__ . "/../config/database.php";
$appConfig = require __DIR__ . "/../config/app.php";

// Initialize database connection
Connection::setConfig($dbConfig);
$pdo = Connection::getInstance();

// Initialize repositories
$productRepository = new SQLiteProductRepository($pdo);
$historyRepository = new SQLiteImportHistoryRepository($pdo);

// Initialize use cases
$getProduct = new GetProduct($productRepository);
$listProducts = new ListProducts($productRepository);
$updateProduct = new UpdateProduct($productRepository);
$deleteProduct = new DeleteProduct($productRepository);

// Initialize controllers
$healthController = new HealthController($historyRepository, $startTime);
$productController = new ProductController(
    $getProduct,
    $listProducts,
    $updateProduct,
    $deleteProduct,
);

// Initialize router
$router = new Router();

// Define routes
$router->get("/", fn() => $healthController->index());

$router->get("/products", function () use ($productController) {
    return $productController->index($_GET);
});

$router->get("/products/:code", function ($code) use ($productController) {
    return $productController->show((int) $code);
});

$router->put("/products/:code", function ($code) use ($productController) {
    $data = json_decode(file_get_contents("php://input"), true) ?? [];
    return $productController->update((int) $code, $data);
});

$router->delete("/products/:code", function ($code) use ($productController) {
    return $productController->delete((int) $code);
});

// Create middleware chain
$jsonMiddleware = new JsonResponseMiddleware();
$apiKeyMiddleware = new ApiKeyMiddleware($appConfig["api_key"]);

// Build request array
$request = [
    "method" => $_SERVER["REQUEST_METHOD"],
    "uri" => $_SERVER["REQUEST_URI"],
];

// Apply JSON middleware first
$jsonMiddleware->handle($request, function () {});

// Check API key
$handler = function ($request) use ($router) {
    $router->run();
};

$response = $apiKeyMiddleware->handle($request, $handler);

// Handle middleware response (e.g., 401 Unauthorized)
if (isset($response["status"]) && is_int($response["status"])) {
    http_response_code($response["status"]);
    echo $response["body"] ?? "";
}
