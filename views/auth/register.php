<?php
/** @var AuthRegisterViewModel $viewModel */

use ViewModels\AuthRegisterViewModel;

?>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body p-4">
                    <h3 class="card-title text-center mb-4">
                        <i class="bi bi-person-plus"></i> <?= $viewModel->title ?>
                    </h3>

                    <form action="/auth/register" method="POST">
                        <div class="mb-3">
                            <label for="fullname" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="fullname" name="fullname"
                                   value="<?= htmlspecialchars($viewModel->old['fullname'] ?? '') ?>" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label">Tên đăng nhập <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="username" name="username"
                                       value="<?= htmlspecialchars($viewModel->old['username'] ?? '') ?>" required>
                                <div class="form-text">Tối thiểu 3 ký tự</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email"
                                       value="<?= htmlspecialchars($viewModel->old['email'] ?? '') ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <div class="form-text">Tối thiểu 6 ký tự</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="confirm_password" class="form-label">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Bạn muốn đăng ký với vai trò? <span class="text-danger">*</span></label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="role" id="role_student" value="0"
                                    <?= (!isset($viewModel->old['role']) || $viewModel->old['role'] == 0) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="role_student">
                                    <i class="bi bi-mortarboard"></i> Học viên - Tôi muốn học các khóa học
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="role" id="role_instructor" value="1"
                                    <?= (isset($viewModel->old['role']) && $viewModel->old['role'] == 1) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="role_instructor">
                                    <i class="bi bi-person-badge"></i> Giảng viên - Tôi muốn tạo và dạy khóa học
                                </label>
                            </div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                            <label class="form-check-label" for="terms">
                                Tôi đồng ý với <a href="#">Điều khoản dịch vụ</a> và <a href="#">Chính sách bảo mật</a>
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-person-plus"></i> Đăng ký
                        </button>
                    </form>

                    <hr class="my-4">

                    <p class="text-center mb-0">
                        Đã có tài khoản? <a href="/auth/login">Đăng nhập</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
