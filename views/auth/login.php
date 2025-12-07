<?php
/** @var AuthLoginViewModel $viewModel */

use ViewModels\AuthLoginViewModel;

?>
<div class="d-flex align-items-center justify-content-center flex-grow-1">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow">
                    <div class="card-body p-4">
                        <h3 class="card-title text-center mb-4">
                            <i class="bi bi-box-arrow-in-right"></i> <?= $viewModel->title ?>
                        </h3>

                        <?php if ($viewModel->modelState->hasError('global')): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($viewModel->modelState->getFirstError('global')) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form action="/auth/login" method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Tên đăng nhập hoặc Email</label>
                                <input type="text" 
                                       class="form-control <?= $viewModel->modelState->hasError('username') ? 'is-invalid' : '' ?>" 
                                       id="username" 
                                       name="username" 
                                       value="<?= htmlspecialchars($viewModel->username) ?>"
                                       autofocus>
                                <?php if ($viewModel->modelState->hasError('username')): ?>
                                    <div class="invalid-feedback">
                                        <?= htmlspecialchars($viewModel->modelState->getFirstError('username')) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Mật khẩu</label>
                                <input type="password" 
                                       class="form-control <?= $viewModel->modelState->hasError('password') ? 'is-invalid' : '' ?>" 
                                       id="password" 
                                       name="password">
                                <?php if ($viewModel->modelState->hasError('password')): ?>
                                    <div class="invalid-feedback">
                                        <?= htmlspecialchars($viewModel->modelState->getFirstError('password')) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">Ghi nhớ đăng nhập</label>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-box-arrow-in-right"></i> Đăng nhập
                            </button>
                        </form>

                        <hr class="my-4">

                        <p class="text-center mb-0">
                            Chưa có tài khoản? <a href="/auth/register">Đăng ký ngay</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
