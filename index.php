<?php
/**
 * Online Course Management System
 */

session_start();

// =================================================================
// 🔥 AUTO LOGIN (CHẾ ĐỘ TEST CHO NGƯỜI SỐ 3)
// Xóa đoạn này khi nộp bài hoặc khi ghép code với nhóm
// =================================================================
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 2;        // Phải trùng với ID trong Database ở Bước 1
    $_SESSION['role'] = 1;             // 1 = Giảng viên
    $_SESSION['fullname'] = 'GV Test'; // Tên hiển thị trên menu
    $_SESSION['email'] = 'gv@test.com';
    $_SESSION['username'] = 'test_gv';
}
// =================================================================

// Define base path
define('BASE_PATH', __DIR__);

// Autoload controllers
spl_autoload_register(function ($class) {
    // Handle namespaced classes (e.g., Functional\Option)
    $classPath = str_replace('\\', '/', $class);

    if (str_starts_with($class, 'lib\\')) {
        $libClassPath = str_replace('lib\\', '', $class);
        $libClassPath = str_replace('\\', '/', $libClassPath);
        $libFile = BASE_PATH . '/lib/' . $libClassPath . '.php';
        if (file_exists($libFile)) {
            require_once $libFile;
            return;
        }
    }

    // Xử lý namespace controllers\
    if (str_starts_with($class, 'controllers\\')) {
        $className = str_replace('controllers\\', '', $class);
        $file = BASE_PATH . '/controllers/' . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }

    // Xử lý namespace models\
    if (str_starts_with($class, 'models\\')) {
        $className = str_replace('models\\', '', $class);
        $file = BASE_PATH . '/models/' . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }

    // Xử lý namespace viewmodels\
    if (str_starts_with($class, 'viewmodels\\')) {
        $classPath = str_replace('viewmodels\\', '', $class);
        $classPath = str_replace('\\', '/', $classPath);
        $file = BASE_PATH . '/viewmodels/' . $classPath . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }

    // Xử lý namespace Functional\
    if (str_starts_with($class, 'Functional\\')) {
        $className = str_replace('Functional\\', '', $class);
        $file = BASE_PATH . '/lib/Functional/' . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
    // Fallback cho các class không có namespace
    $classPath = str_replace('\\', '/', $class);

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

// Sau spl_autoload_register(...)

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
// 1. Dashboard
    $router->get('/instructor/dashboard', [InstructorController::class, 'dashboard']);
    $router->get('/instructor/my-courses', [InstructorController::class, 'myCourses']);

    // 2. Quản lý Khóa học (Courses)
    $router->get('/instructor/courses/create', [InstructorController::class, 'createForm']); // Form tạo
    $router->post('/instructor/courses/store', [InstructorController::class, 'storeCourse']);  // Lưu tạo

    $router->get('/instructor/courses/{id}/edit', [InstructorController::class, 'editForm']);   // Form sửa
    $router->post('/instructor/courses/{id}/update', [InstructorController::class, 'updateCourse']); // Lưu sửa
    $router->post('/instructor/courses/{id}/delete', [InstructorController::class, 'deleteCourse']); // Xóa

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