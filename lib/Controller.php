<?php

namespace Lib;

use Functional\Option;
use JetBrains\PhpStorm\NoReturn;

abstract class Controller {

    /**
     * Render a view with a ViewModel
     */
    protected function render(string $viewPath, ViewModel $viewModel): void
    {
        // Start buffering for the main content
        ob_start();
        $viewFile = BASE_PATH . "/views/$viewPath.php";
        if (file_exists($viewFile)) {
            // Pass $viewModel directly to the view script
            require_once $viewFile;
        } else {
            // Handle view not found, perhaps render a generic error view
            echo "View not found: " . htmlspecialchars($viewPath);
        }
        $content = ob_get_clean();

        // Pass $viewModel to the header and footer layout files as well.
        // These layout files will now expect a $viewModel variable.
        require_once BASE_PATH . '/views/layouts/header.php';
        echo $content;
        require_once BASE_PATH . '/views/layouts/footer.php';
    }

    /**
     * Redirect helper
     */
    #[NoReturn]
    protected function redirect(string $url): void
    {
        header("Location: $url");
        exit;
    }

    /**
     * Authentication Middleware
     * @param array|int $roles Single role (int) or array of roles
     */
    protected function requireRole(int|array $roles): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Vui lòng đăng nhập để tiếp tục.';
            $this->redirect('/auth/login');
        }

        if (!is_array($roles)) {
            $roles = [$roles];
        }

        if (!in_array($_SESSION['role'], $roles)) {
            http_response_code(403);
            echo 'Access Denied';
            exit;
        }
    }

    /**
     * Get current user securely
     * @return Option
     */
    protected function user(): Option {
        if (isset($_SESSION['user_id'])) {
            return Option::some([
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'fullname' => $_SESSION['fullname'],
                'role' => $_SESSION['role'],
                'email' => $_SESSION['email']
            ]);
        }
        return Option::none();
    }

    /**
     * Get request data type-safely
     */
    protected function getPost(string $key, $default = null) {
        return $_POST[$key] ?? $default;
    }

    protected function getQuery(string $key, $default = null) {
        return $_GET[$key] ?? $default;
    }

    /**
     * Set an error message for the next render
     */
    protected function setErrorMessage(string $message): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['error'] = $message;
    }

    /**
     * Set a success message for the next render
     */
    protected function setSuccessMessage(string $message): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['success'] = $message;
    }
}