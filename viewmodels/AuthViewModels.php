<?php

namespace ViewModels;

use Lib\ViewModel;
use Lib\Validation\Attributes\Required;
use Lib\Validation\Attributes\Email;
use Lib\Validation\Attributes\MinLength;
use Lib\Validation\Attributes\DisplayName;
use Models\User;

class AuthLoginViewModel extends ViewModel
{
    #[Required("Please enter {field}")]
    #[DisplayName("Username or Email")]
    public string $username = '';

    #[Required("Please enter your password")]
    #[MinLength(1)]
    #[DisplayName("Password")]
    public string $password = '';

    public function __construct(
        public string $title = "Login"
    )
    {
        parent::__construct();
    }
}

class AuthRegisterViewModel extends ViewModel
{
    #[Required]
    #[MinLength(3)]
    #[DisplayName("Username")]
    public string $username = '';

    #[Required]
    #[Email]
    #[DisplayName("Email Address")]
    public string $email = '';

    #[Required]
    #[MinLength(6)]
    #[DisplayName("Password")]
    public string $password = '';

    #[Required]
    #[DisplayName("Confirm Password")]
    public string $confirm_password = '';

    #[Required]
    #[DisplayName("Full Name")]
    public string $fullname = '';

    public int $role = 0;

    #[Required]
    #[DisplayName("Terms Agreement")]
    public ?string $terms = null;

    public function __construct(
        public string $title = "Register"
    )
    {
        parent::__construct();
    }

    protected function validateCustom(): void
    {
        // Password Match
        if ($this->password !== $this->confirm_password) {
            $this->modelState->addError('confirm_password', 'Mật khẩu xác nhận không khớp.');
        }

        // Role Validation
        if (!in_array($this->role, [User::ROLE_STUDENT, User::ROLE_INSTRUCTOR])) {
            $this->modelState->addError('role', 'Vai trò không hợp lệ.');
        }

        // Duplicate Check (Only run if username/email are basically valid to save DB hits)
        if (!$this->modelState->hasError('username')) {
            $exists = User::query()->where(User::USERNAME, $this->username)->count() > 0;
            if ($exists) {
                $this->modelState->addError('username', 'Tên đăng nhập đã tồn tại.');
            }
        }

        if (!$this->modelState->hasError('email')) {
            $emailExists = User::query()->where(User::EMAIL, $this->email)->count() > 0;
            if ($emailExists) {
                $this->modelState->addError('email', 'Email đã được sử dụng.');
            }
        }
    }
}

