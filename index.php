<?php
session_start();

// Define base path
define('BASE_PATH', __DIR__);

// Autoload controllers
spl_autoload_register(function ($class) {
    // Handle namespaced classes (e.g., Functional\Option)
    $classPath = str_replace('\\', '/', $class);

    // Fix for Lib namespace mapping to lib directory
    if (str_starts_with($class, 'Lib\\')) {
        $libClassPath = str_replace('Lib\\', '', $class); // Remove 'Lib\' prefix
        $libClassPath = str_replace('\\', '/', $libClassPath);
        $libFile = BASE_PATH . '/lib/' . $libClassPath . '.php';
        if (file_exists($libFile)) {
            require_once $libFile;
            return;
        }
    }

    $paths = [
        BASE_PATH . '/controllers/' . $class . '.php',
        BASE_PATH . '/models/' . $class . '.php',
        BASE_PATH . '/config/' . $class . '.php',
        BASE_PATH . '/lib/' . $classPath . '.php',
        BASE_PATH . '/' . $classPath . '.php'
    ];

    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// Get the request URI
$requestUri = $_SERVER['REQUEST_URI'];
$requestUri = parse_url($requestUri, PHP_URL_PATH);
$requestUri = rtrim($requestUri, '/');

if (empty($requestUri)) {
    $requestUri = '/';
}

try {
    $router = new Router();

    // ----------------- TEAM MEMBER 1: Core Infrastructure & Public Course Catalog  & Auth -----------------
    
    // Home
    $router->get('/', [HomeController::class, 'index']);
    $router->get('/home', [HomeController::class, 'index']);

    // Public Course
    $router->get('/courses', [CourseController::class, 'index']);
    $router->get('/courses/search', [CourseController::class, 'search']);
    $router->get('/course/{id}', [CourseController::class, 'detail']);
    
    // Auth
    $router->get('/auth/login', [AuthController::class, 'showLogin']);
    $router->post('/auth/login', [AuthController::class, 'login']);
    $router->get('/auth/register', [AuthController::class, 'showRegister']);
    $router->post('/auth/register', [AuthController::class, 'register']);
    $router->get('/auth/logout', [AuthController::class, 'logout']);

    // ----------------- TEAM MEMBER 2: Authentication & Student Dashboard -----------------

    // ----------------- TEAM MEMBER 3: Instructor Module (Full-Stack) -----------------

    // ----------------- TEAM MEMBER 4: Admin Module (Full-Stack) -----------------


    // Dispatch
    $router->dispatch($_SERVER['REQUEST_METHOD'], $requestUri);

} catch (Exception $e) {
    // Clear session to "log them out" as requested
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_unset();
        session_destroy();
    }

    http_response_code(500);
    
    // Read the content of the 500 error page
    $errorPageContent = file_get_contents(BASE_PATH . '/views/errors/500.php');
    if ($errorPageContent !== false) {
        echo $errorPageContent;
    } else {
        // Fallback if view file is missing
        echo "<!DOCTYPE html><html lang=\"vi\"><head><title>500 - Server Error</title></head><body>";
        echo "<div style=\"text-align: center; padding: 50px;\">";
        echo "<h1>500 Internal Server Error</h1>";
        echo "<p>Something went wrong. Please try again later.</p>";
        echo "<a href=\"/\">Go to Homepage</a>";
        echo "</div></body></html>";
    }
}