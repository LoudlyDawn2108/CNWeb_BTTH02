<?php
namespace Controllers;

use Functional\Option;

abstract class BaseController {

    /**
     * Lấy thông tin user đang đăng nhập, trả về Option
     */
    protected function user(): Option {
        if (isset($_SESSION['user'])) {
            return Option::some($_SESSION['user']);
        }
        return Option::none();
    }

    /**
     * Kiểm tra user đã đăng nhập chưa
     */
    protected function isAuthenticated(): bool {
        return isset($_SESSION['user']);
    }

    /**
     * Kiểm tra user có role cụ thể
     */
    protected function hasRole(string $role): bool {
        return $this->user()->match(
            fn($user) => $user['role'] === $role,
            fn() => false
        );
    }

    /**
     * Render view
     */
    protected function render(string $view, $viewModel = null): void {
        $viewPath = BASE_PATH . "/views/$view.php";

        if (!file_exists($viewPath)) {
            throw new \Exception("View not found: $view");
        }

        if ($viewModel) {
            extract(['model' => $viewModel]);
        }

        require $viewPath;
    }

    /**
     * Redirect
     */
    protected function redirect(string $url): void {
        header("Location: $url");
        exit;
    }

    /**
     * Get POST data
     */
    protected function getPost(string $key, $default = null) {
        return $_POST[$key] ?? $default;
    }

    /**
     * Set flash message
     */
    protected function setSuccessMessage(string $message): void {
        $_SESSION['success_message'] = $message;
    }

    protected function setErrorMessage(string $message): void {
        $_SESSION['error_message'] = $message;
    }

    /**
     * Get and clear flash message
     */
    protected function getFlashMessage(string $type): ?string {
        $key = "{$type}_message";
        $message = $_SESSION[$key] ?? null;
        unset($_SESSION[$key]);
        return $message;
    }
}
