<?php

namespace Requests\Auth;

use Lib\FormRequest;
use Lib\ValidationException;

class LoginRequest extends FormRequest {
    public string $username = '';
    public string $password = '';

    protected function validate(): void {
        $errors = [];
        if (empty($this->username)) {
            $errors[] = "Tên đăng nhập không được để trống.";
        }
        if (empty($this->password)) {
            $errors[] = "Mật khẩu không được để trống.";
        }

        if (!empty($errors)) {
            throw new ValidationException($errors, ['username' => $this->username]);
        }
    }
}
