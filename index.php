<?php
/**
 * Online Course Management System
 */

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
        BASE_PATH . '/lib/' . $classPath . '.php'
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
    
    // Auth
    $router->get('/auth/login', [AuthController::class, 'showLogin']);
    $router->post('/auth/login', [AuthController::class, 'login']);

    // ----------------- TEAM MEMBER 2: Authentication & Student Dashboard -----------------

    // ----------------- TEAM MEMBER 3: Instructor Module (Full-Stack) -----------------

    // ----------------- TEAM MEMBER 4: Admin Module (Full-Stack) -----------------
    
    // Admin Dashboard
    $router->get('/admin/dashboard', [AdminController::class, 'dashboard']);
    $router->get('/admin/users', [AdminController::class, 'manageUsers']);
    $router->post('/admin/users/{id}/toggle-status', [AdminController::class, 'toggleUserStatus']);

    // Admin Categories
    $router->get('/admin/categories', [AdminController::class, 'listCategories']);
    $router->get('/admin/categories/create', [AdminController::class, 'createCategory']);
    $router->post('/admin/categories/store', [AdminController::class, 'storeCategory']);
    $router->get('/admin/categories/{id}/edit', [AdminController::class, 'editCategory']);
    $router->post('/admin/categories/{id}/update', [AdminController::class, 'updateCategory']);
    $router->post('/admin/categories/{id}/delete', [AdminController::class, 'deleteCategory']);

    // Admin Course Approval
    $router->post('/admin/courses/{id}/approve', [AdminController::class, 'approveCourse']);
    $router->post('/admin/courses/{id}/reject', [AdminController::class, 'rejectCourse']);

    // Admin Reports
    $router->get('/admin/reports/statistics', [AdminController::class, 'statistics']);

    // Dispatch
    $router->dispatch($_SERVER['REQUEST_METHOD'], $requestUri);

} catch (Lib\ValidationException $e) {
    // Handle Validation Errors
    $_SESSION['error'] = implode('<br>', $e->errors);
    $_SESSION['old'] = $e->old;
    
    $referer = $_SERVER['HTTP_REFERER'] ?? '/';
    header("Location: $referer");
    exit;

} catch (Exception $e) {
    http_response_code(500);
    echo 'Server Error: ' . $e->getMessage();
}