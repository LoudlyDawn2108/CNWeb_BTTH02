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

                    <?php if ($viewModel->modelState->hasError('global')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($viewModel->modelState->getFirstError('global')) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form action="/auth/register" method="POST">
                        <div class="mb-3">
                            <label for="fullname" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control <?= $viewModel->modelState->hasError('fullname') ? 'is-invalid' : '' ?>"
                                   id="fullname"
                                   name="fullname"
                                   value="<?= htmlspecialchars($viewModel->fullname) ?>" required>
                            <?php if ($viewModel->modelState->hasError('fullname')): ?>
                                <div class="invalid-feedback">
                                    <?= htmlspecialchars($viewModel->modelState->getFirstError('fullname')) ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label">Tên đăng nhập <span class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control <?= $viewModel->modelState->hasError('username') ? 'is-invalid' : '' ?>"
                                       id="username"
                                       name="username"
                                       value="<?= htmlspecialchars($viewModel->username) ?>" required>
                                <div class="form-text">Tối thiểu 3 ký tự</div>
                                <?php if ($viewModel->modelState->hasError('username')): ?>
                                    <div class="invalid-feedback">
                                        <?= htmlspecialchars($viewModel->modelState->getFirstError('username')) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email"
                                       class="form-control <?= $viewModel->modelState->hasError('email') ? 'is-invalid' : '' ?>"
                                       id="email"
                                       name="email"
                                       value="<?= htmlspecialchars($viewModel->email) ?>" required>
                                <?php if ($viewModel->modelState->hasError('email')): ?>
                                    <div class="invalid-feedback">
                                        <?= htmlspecialchars($viewModel->modelState->getFirstError('email')) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                                <input type="password"
                                       class="form-control <?= $viewModel->modelState->hasError('password') ? 'is-invalid' : '' ?>"
                                       id="password"
                                       name="password" required>
                                <div class="form-text">Tối thiểu 6 ký tự</div>
                                <?php if ($viewModel->modelState->hasError('password')): ?>
                                    <div class="invalid-feedback">
                                        <?= htmlspecialchars($viewModel->modelState->getFirstError('password')) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="confirm_password" class="form-label">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                                <input type="password"
                                       class="form-control <?= $viewModel->modelState->hasError('confirm_password') ? 'is-invalid' : '' ?>"
                                       id="confirm_password"
                                       name="confirm_password" required>
                                <?php if ($viewModel->modelState->hasError('confirm_password')): ?>
                                    <div class="invalid-feedback">
                                        <?= htmlspecialchars($viewModel->modelState->getFirstError('confirm_password')) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Bạn muốn đăng ký với vai trò? <span class="text-danger">*</span></label>
                            <div class="form-check">
                                <input class="form-check-input <?= $viewModel->modelState->hasError('role') ? 'is-invalid' : '' ?>" type="radio" name="role" id="role_student" value="0"
                                    <?= $viewModel->role == 0 ? 'checked' : '' ?>>
                                <label class="form-check-label" for="role_student">
                                    <i class="bi bi-mortarboard"></i> Học viên - Tôi muốn học các khóa học
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input <?= $viewModel->modelState->hasError('role') ? 'is-invalid' : '' ?>" type="radio" name="role" id="role_instructor" value="1"
                                    <?= $viewModel->role == 1 ? 'checked' : '' ?>>
                                <label class="form-check-label" for="role_instructor">
                                    <i class="bi bi-person-badge"></i> Giảng viên - Tôi muốn tạo và dạy khóa học
                                </label>
                            </div>
                            <?php if ($viewModel->modelState->hasError('role')): ?>
                                <div class="invalid-feedback d-block">
                                    <?= htmlspecialchars($viewModel->modelState->getFirstError('role')) ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox"
                                   class="form-check-input <?= $viewModel->modelState->hasError('terms') ? 'is-invalid' : '' ?>"
                                   id="terms" name="terms" <?= $viewModel->terms ? 'checked' : '' ?> required>
                            <label class="form-check-label" for="terms">
                                Tôi đồng ý với <a href="#">Điều khoản dịch vụ</a> và <a href="#">Chính sách bảo mật</a>
                            </label>
                            <?php if ($viewModel->modelState->hasError('terms')): ?>
                                <div class="invalid-feedback">
                                    <?= htmlspecialchars($viewModel->modelState->getFirstError('terms')) ?>
                                </div>
                            <?php endif; ?>
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
