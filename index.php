<?php
session_start();
define('BASE_PATH', __DIR__);

// Autoload Controllers & Models
spl_autoload_register(function ($class) {
    $paths = [
        BASE_PATH . '/controllers/' . $class . '.php',
        BASE_PATH . '/models/' . $class . '.php',
        BASE_PATH . '/config/' . $class . '.php'
    ];
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestUri = rtrim($requestUri, '/');
if (empty($requestUri)) $requestUri = '/';

// Router - Định tuyến
try {
    // --- KHU VỰC PUBLIC ---
    if ($requestUri === '/' || $requestUri === '/home') {
        $controller = new HomeController();
        $controller->index();
    }
    // --- KHU VỰC AUTH (Login/Register) ---
    elseif ($requestUri === '/auth/login') {
        $controller = new AuthController();
        $_SERVER['REQUEST_METHOD'] === 'POST' ? $controller->login() : $controller->showLogin();
    }
    // --- KHU VỰC SINH VIÊN ---
    elseif ($requestUri === '/student/dashboard') {
        echo 'Trang Dashboard Sinh Vien';
    }
    // --- KHU VỰC GIẢNG VIÊN ---
    elseif ($requestUri === '/instructor/dashboard') {
        echo 'Trang Dashboard Giang Vien';
    }
    // --- KHU VỰC ADMIN ---
    elseif ($requestUri === '/admin/dashboard') {
        echo 'Trang Dashboard Admin';
    }
    else {
        http_response_code(404);
        echo '<h1>404 Page Not Found</h1>';
    }
} catch (Exception $e) {
    echo 'Lỗi: ' . $e->getMessage();
}
?>
