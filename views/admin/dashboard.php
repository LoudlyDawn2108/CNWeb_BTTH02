<?php
/**
 * @var ViewModels\AdminDashboardViewModel $viewModel
 */
?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0">Admin Dashboard</h1>
            <p class="text-muted">Chào mừng quay trở lại, <?= htmlspecialchars($_SESSION['fullname']) ?></p>
        </div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Tổng người dùng</h6>
                            <h3 class="mb-0"><?= number_format($viewModel->totalUsers) ?></h3>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="bi bi-people fs-1 text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Học viên</h6>
                            <h3 class="mb-0"><?= number_format($viewModel->totalStudents) ?></h3>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="bi bi-person-badge fs-1 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Giảng viên</h6>
                            <h3 class="mb-0"><?= number_format($viewModel->totalInstructors) ?></h3>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="bi bi-person-workspace fs-1 text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Tổng khóa học</h6>
                            <h3 class="mb-0"><?= number_format($viewModel->totalCourses) ?></h3>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="bi bi-book fs-1 text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Courses -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Khóa học chờ duyệt</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($viewModel->pendingCourses)): ?>
                        <p class="text-muted mb-0">Không có khóa học nào chờ duyệt.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Tiêu đề</th>
                                        <th>Giảng viên</th>
                                        <th>Danh mục</th>
                                        <th>Ngày tạo</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($viewModel->pendingCourses as $course): ?>
                                        <tr>
                                            <td><?= $course['id'] ?></td>
                                            <td><?= htmlspecialchars($course['title']) ?></td>
                                            <td><?= htmlspecialchars($course['instructor_name']) ?></td>
                                            <td><?= htmlspecialchars($course['category_name']) ?></td>
                                            <td><?= date('d/m/Y', strtotime($course['created_at'])) ?></td>
                                            <td>
                                                <a href="/admin/courses/<?= $course['id'] ?>" class="btn btn-sm btn-primary">
                                                    <i class="bi bi-eye"></i> Xem
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Users -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Người dùng mới</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($viewModel->recentUsers)): ?>
                        <p class="text-muted mb-0">Không có người dùng mới.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Username</th>
                                        <th>Họ tên</th>
                                        <th>Email</th>
                                        <th>Vai trò</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày tạo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($viewModel->recentUsers as $user): ?>
                                        <tr>
                                            <td><?= $user['id'] ?></td>
                                            <td><?= htmlspecialchars($user['username']) ?></td>
                                            <td><?= htmlspecialchars($user['fullname']) ?></td>
                                            <td><?= htmlspecialchars($user['email']) ?></td>
                                            <td>
                                                <?php
                                                $roleBadge = match((int)$user['role']) {
                                                    0 => '<span class="badge bg-primary">Học viên</span>',
                                                    1 => '<span class="badge bg-info">Giảng viên</span>',
                                                    2 => '<span class="badge bg-danger">Admin</span>',
                                                    default => '<span class="badge bg-secondary">Unknown</span>'
                                                };
                                                echo $roleBadge;
                                                ?>
                                            </td>
                                            <td>
                                                <?php if ($user['status'] == 1): ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= date('d/m/Y H:i', strtotime($user['created_at'])) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
