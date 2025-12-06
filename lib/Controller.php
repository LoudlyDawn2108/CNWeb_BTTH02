<?php

namespace Lib;

use Functional\Option;
use JetBrains\PhpStorm\NoReturn;

abstract class Controller {

    /**
     * Render view với Layout tự động (Header/Footer)
     */
    protected function render(string $viewPath, $viewModel = null): void
    {
        // Giải nén biến để View dùng được tên ngắn gọn $model
        if ($viewModel) {
            // Hỗ trợ cả $model và $viewModel
            $model = $viewModel;
        }

        // Bắt đầu vùng đệm để lấy nội dung view con
        ob_start();

        $viewFile = BASE_PATH . "/views/$viewPath.php";
        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            echo "View not found: " . htmlspecialchars($viewPath);
        }

        $content = ob_get_clean(); // Nội dung view con đã được lưu vào biến $content

        // Nhúng Layout chung (Header -> Content -> Footer)
        // Lưu ý: Đảm bảo bạn có 2 file này trong views/layouts/
        $headerPath = BASE_PATH . '/views/layouts/header.php';
        $footerPath = BASE_PATH . '/views/layouts/footer.php';

        if (file_exists($headerPath)) require_once $headerPath;
        echo $content; // In nội dung view con ra giữa
        if (file_exists($footerPath)) require_once $footerPath;
    }

    /**
     * Chuyển hướng trang
     */
    #[NoReturn]
    protected function redirect(string $url): void
    {
        header("Location: $url");
        exit;
    }

    /**
     * Lấy thông tin User hiện tại (An toàn với Option)
     */
    protected function user(): Option {
        // Ưu tiên dùng cách lưu session phẳng (user_id) vì phổ biến hơn
        if (isset($_SESSION['user_id'])) {
            return Option::some([
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'] ?? '',
                'fullname' => $_SESSION['fullname'] ?? '',
                'role' => $_SESSION['role'] ?? 0,
                'email' => $_SESSION['email'] ?? ''
            ]);
        }
        return Option::none();
    }

    /**
     * Middleware: Kiểm tra đăng nhập và Quyền hạn
     */
    protected function requireRole(int|array $roles): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        // 1. Kiểm tra đã đăng nhập chưa
        if (!isset($_SESSION['user_id'])) {
            $this->setErrorMessage('Vui lòng đăng nhập để tiếp tục.');
            $this->redirect('/auth/login');
        }

        // 2. Kiểm tra quyền
        if (!is_array($roles)) {
            $roles = [$roles];
        }

        if (!in_array($_SESSION['role'], $roles)) {
            http_response_code(403);
            die('Access Denied: Bạn không có quyền truy cập trang này.');
        }
    }

    /**
     * Lấy dữ liệu POST an toàn
     */
    protected function getPost(string $key, $default = null) {
        return $_POST[$key] ?? $default;
    }

    /**
     * Quản lý thông báo (Flash Message)
     */
    protected function setSuccessMessage(string $message): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['success_message'] = $message;
    }

    protected function setErrorMessage(string $message): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['error_message'] = $message;
    }
}