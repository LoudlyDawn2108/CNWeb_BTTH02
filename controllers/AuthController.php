<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../viewmodels/AuthViewModels.php';

use JetBrains\PhpStorm\NoReturn;
use Lib\Controller;
use Models\User;
use Models\UserTable;
use ViewModels\AuthLoginViewModel;
use ViewModels\AuthRegisterViewModel;

class AuthController extends Controller {

    /**
     * Show login form
     */
    public function showLogin(): void {
        if ($this->isLoggedIn()) {
            $this->redirectByRole();
        }

        $viewModel = new AuthLoginViewModel(
            title: 'Đăng nhập - Online Course'
        );

        $this->render('auth/login', $viewModel);
    }

    /**
     * Check if user is logged in
     */
    public function isLoggedIn(): bool {
        return isset($_SESSION['user_id']);
    }

    /**
     * Redirect user based on role
     */
    #[NoReturn]
    private function redirectByRole(): void {
        switch ($_SESSION['role']) {
            case User::ROLE_ADMIN:
                $this->redirect('/admin/dashboard');
            case User::ROLE_INSTRUCTOR:
                $this->redirect('/instructor/dashboard');
            default:
                $this->redirect('/student/dashboard');
        }
    }

    /**
     * Process login
     */
    public function login(): void {
        $viewModel = new AuthLoginViewModel(title: 'Đăng nhập - Online Course');
        $viewModel->handleRequest($_POST);

        if ($viewModel->modelState->isValid) {
            $u = new UserTable();
            $user = User::query()->where($u->USERNAME, $viewModel->username)->first();

            if (!$user) {
                $user = User::query()->where($u->EMAIL, $viewModel->username)->first();
            }

            if ($user && password_verify($viewModel->password, $user->password) && $user->status == 1) {
                $_SESSION['user_id'] = $user->id;
                $_SESSION['username'] = $user->username;
                $_SESSION['fullname'] = $user->fullname;
                $_SESSION['role'] = $user->role;
                $_SESSION['email'] = $user->email;

                $this->redirectByRole();
            }

            // Add model error for invalid credentials
            $viewModel->modelState->addError('username', 'Tên đăng nhập hoặc mật khẩu không đúng, hoặc tài khoản đã bị vô hiệu hóa.');
        }

        // If we got here, something failed, redisplay form
        $this->render('auth/login', $viewModel);
    }

    /**
     * Show registration form
     */
    public function showRegister(): void {
        if ($this->isLoggedIn()) {
            $this->redirectByRole();
        }

        $viewModel = new AuthRegisterViewModel(
            title: 'Đăng ký - Online Course'
        );

        $this->render('auth/register', $viewModel);
    }

    /**
     * Process registration
     */
    public function register(): void {
        $viewModel = new AuthRegisterViewModel(title: 'Đăng ký - Online Course');
        $viewModel->handleRequest($_POST);

        if ($viewModel->modelState->isValid) {
            try {
                $data = [
                    'username' => $viewModel->username,
                    'email' => $viewModel->email,
                    'password' => password_hash($viewModel->password, PASSWORD_DEFAULT),
                    'fullname' => $viewModel->fullname,
                    'role' => $viewModel->role,
                    'status' => 1
                ];

                User::create($data);
                $this->setSuccessMessage('Đăng ký thành công! Vui lòng đăng nhập.');
                $this->redirect('/auth/login');

            } catch (Exception $e) {
                $this->setErrorMessage('Có lỗi xảy ra: ' . $e->getMessage());
            }
        }

        // Render view with errors
        $this->render('auth/register', $viewModel);
    }

    /**
     * Logout user
     */
    #[NoReturn]
    public function logout(): void {
        session_destroy();
        $this->redirect('/');
    }
}

