<?php
/**
 * Online Course Management System
 */

use Controllers\InstructorController;
use Controllers\LessonController;

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
    
    // Auth
    $router->get('/auth/login', [AuthController::class, 'showLogin']);
    $router->post('/auth/login', [AuthController::class, 'login']);
    $router->get('/auth/register', [AuthController::class, 'showRegister']);
    $router->post('/auth/register', [AuthController::class, 'register']);
    $router->get('/auth/logout', [AuthController::class, 'logout']);

    // ----------------- TEAM MEMBER 2: Authentication & Student Dashboard -----------------

    // ----------------- TEAM MEMBER 3: Instructor Module (Full-Stack) -----------------
// 1. Dashboard
    $router->get('/instructor/dashboard', [InstructorController::class, 'dashboard']);
    $router->get('/instructor/my-courses', [InstructorController::class, 'myCourses']);

    // 2. Quản lý Khóa học (Courses)
    $router->get('/instructor/courses/create', [InstructorController::class, 'create']); // Form tạo
    $router->post('/instructor/courses/store', [InstructorController::class, 'store']);  // Lưu tạo

    $router->get('/instructor/courses/{id}/edit', [InstructorController::class, 'edit']);   // Form sửa
    $router->post('/instructor/courses/{id}/update', [InstructorController::class, 'update']); // Lưu sửa
    $router->post('/instructor/courses/{id}/delete', [InstructorController::class, 'delete']); // Xóa

    $router->get('/instructor/courses/{id}/manage', [InstructorController::class, 'manageCourse']); // Trang chi tiết khóa học

    // 3. Quản lý Bài học (Lessons - Nested trong Course)
    // URL: /instructor/courses/{id khóa học}/lessons/...
    $router->get('/instructor/courses/{id}/lessons', [LessonController::class, 'manage']);
    $router->get('/instructor/courses/{id}/lessons/create', [LessonController::class, 'create']);
    $router->post('/instructor/courses/{id}/lessons/store', [LessonController::class, 'store']);

    // 4. Thao tác trên Bài học cụ thể
    // URL: /instructor/lessons/{id bài học}/...
    $router->get('/instructor/lessons/{id}/edit', [LessonController::class, 'edit']);
    $router->post('/instructor/lessons/{id}/update', [LessonController::class, 'update']);
    $router->post('/instructor/lessons/{id}/delete', [LessonController::class, 'delete']);

    // 5. Quản lý Tài liệu (Materials)
    $router->post('/instructor/lessons/{id}/materials/upload', [LessonController::class, 'uploadMaterial']);
    $router->post('/instructor/materials/{id}/delete', [LessonController::class, 'deleteMaterial']);

    // ----------------- TEAM MEMBER 4: Admin Module (Full-Stack) -----------------


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