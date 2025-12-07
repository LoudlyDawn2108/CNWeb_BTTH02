<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../viewmodels/AuthViewModels.php';

use JetBrains\PhpStorm\NoReturn;
use Lib\Controller;
use Models\User;
use Requests\Auth\LoginRequest;
use Requests\Auth\RegisterRequest;
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
    #[NoReturn]
    public function login(LoginRequest $request): void {
        $user = User::query()->where(User::USERNAME, $request->username)->first();

        if (!$user) {
            $user = User::query()->where(User::EMAIL, $request->username)->first();
        }

        if ($user && password_verify($request->password, $user->password) && $user->status == 1) {
            $_SESSION['user_id'] = $user->id;
            $_SESSION['username'] = $user->username;
            $_SESSION['fullname'] = $user->fullname;
            $_SESSION['role'] = $user->role;
            $_SESSION['email'] = $user->email;

            $this->redirectByRole();
        } else {
            $this->setErrorMessage('Tên đăng nhập hoặc mật khẩu không đúng, hoặc tài khoản đã bị vô hiệu hóa.');
            $this->redirect('/auth/login');
        }
    }

    /**
     * Show registration form
     */
    public function showRegister(): void {
        if ($this->isLoggedIn()) {
            $this->redirectByRole();
        }

        $viewModel = new AuthRegisterViewModel(
            title: 'Đăng ký - Online Course',
            old:   $_SESSION['old'] ?? []
        );
        unset($_SESSION['old']);

        $this->render('auth/register', $viewModel);
    }

    /**
     * Process registration
     */
    public function register(RegisterRequest $request): void {
        $data = [
            'username' => $request->username,
            'email' => $request->email,
            'password' => $request->password,
            'fullname' => $request->fullname,
            'role' => $request->role
        ];

        // Hash password
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        try {
            // Check for duplicates first to avoid raw SQL errors if possible,
            // but strict AR might rely on DB constraints.
            // Old code didn't explicitly check here, relied on Model catch?
            // Let's check explicitly for better UX.
            $exists = User::query()->where(User::USERNAME, $data['username'])->count() > 0
                || User::query()->where(User::EMAIL, $data['email'])->count() > 0;

            if ($exists) {
                $this->setErrorMessage("Username or Email already exists");
                $this->redirect('/auth/register');
            }

            User::create($data);
            $this->setSuccessMessage('Đăng ký thành công! Vui lòng đăng nhập.');
            $this->redirect('/auth/login');

        } catch (Exception $e) {
            $this->setErrorMessage('Có lỗi xảy ra. Vui lòng thử lại. ' . $e->getMessage());
            $_SESSION['old'] = [
                'username' => $request->username,
                'email' => $request->email,
                'fullname' => $request->fullname,
                'role' => $request->role
            ];
            $this->redirect('/auth/register');
        }
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

