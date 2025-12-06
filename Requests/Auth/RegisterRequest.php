<?php

namespace Requests\Auth;

use Lib\FormRequest;
use Lib\ValidationException;
use Models\User; // Need to check DB for duplicates

class RegisterRequest extends FormRequest {
    public string $username = '';
    public string $email = '';
    public string $password = '';
    public string $confirm_password = '';
    public string $fullname = '';
    public int $role = 0;
    public ?string $terms = null;

    protected function validate(): void {
        $errors = [];

        if (empty($this->username)) {
            $errors[] = 'Tên đăng nhập không được để trống.';
        } elseif (strlen($this->username) < 3) {
            $errors[] = 'Tên đăng nhập phải có ít nhất 3 ký tự.';
        } elseif (User::query()->where(User::USERNAME, '=', $this->username)->first() !== null) {
            $errors[] = 'Tên đăng nhập đã tồn tại.';
        }

        if (empty($this->email)) {
            $errors[] = 'Email không được để trống.';
        } elseif (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không hợp lệ.';
        } elseif (User::query()->where(User::USERNAME, '=', $this->username)->first() !== null) {
            $errors[] = 'Email đã được sử dụng.';
        }

        if (empty($this->password)) {
            $errors[] = 'Mật khẩu không được để trống.';
        } elseif (strlen($this->password) < 6) {
            $errors[] = 'Mật khẩu phải có ít nhất 6 ký tự.';
        }

        if ($this->password !== $this->confirm_password) {
            $errors[] = 'Xác nhận mật khẩu không khớp.';
        }

        if (empty($this->fullname)) {
            $errors[] = 'Họ và tên không được để trống.';
        }

        if (!in_array($this->role, [User::ROLE_STUDENT, User::ROLE_INSTRUCTOR])) {
            $errors[] = 'Vai trò không hợp lệ.';
        }

        if (empty($this->terms)) {
            $errors[] = 'Bạn chưa đồng ý điều khoản.';
        }

        if (!empty($errors)) {
            throw new ValidationException($errors, [
                'username' => $this->username,
                'email' => $this->email,
                'fullname' => $this->fullname,
                'role' => $this->role
            ]);
        }
    }
}
