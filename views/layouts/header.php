<?php
/** @var ViewModel $viewModel */

use Lib\ViewModel;

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $viewModel->title ?? 'Online Course' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/">
            <i class="bi bi-mortarboard-fill"></i> Online Course
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/">Trang chủ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/courses">Khóa học</a>
                </li>
            </ul>
            <form class="d-flex me-3" action="/courses/search" method="GET">
                <input class="form-control me-2" type="search" name="q" placeholder="Tìm kiếm khóa học..."
                       value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                <button class="btn btn-outline-light" type="submit">
                    <i class="bi bi-search"></i>
                </button>
            </form>
            <ul class="navbar-nav">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> <?= htmlspecialchars($_SESSION['fullname']) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <?php if ($_SESSION['role'] == 2): ?>
                                <li><a class="dropdown-item" href="/admin/dashboard">
                                        <i class="bi bi-speedometer2"></i> Admin Dashboard
                                    </a></li>
                            <?php elseif ($_SESSION['role'] == 1): ?>
                                <li><a class="dropdown-item" href="/instructor/dashboard">
                                        <i class="bi bi-speedometer2"></i> Dashboard
                                    </a></li>
                                <li><a class="dropdown-item" href="/instructor/my-courses">
                                        <i class="bi bi-book"></i> Khóa học của tôi
                                    </a></li>
                            <?php else: ?>
                                <li><a class="dropdown-item" href="/student/dashboard">
                                        <i class="bi bi-speedometer2"></i> Dashboard
                                    </a></li>
                                <li><a class="dropdown-item" href="/student/my-courses">
                                        <i class="bi bi-book"></i> Khóa học của tôi
                                    </a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="/auth/logout">
                                    <i class="bi bi-box-arrow-right"></i> Đăng xuất
                                </a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/auth/login">Đăng nhập</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-light ms-2" href="/auth/register">Đăng ký</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-3">
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
</div>

<main>

