<?php
/** @var StudentDashboardViewModel $viewModel */
use ViewModels\StudentDashboardViewModel;
?>
<div class="container-fluid py-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2">
            <?php include __DIR__ . '/../layouts/sidebar.php'; ?>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10">
            <h2 class="mb-4">
                <i class="bi bi-speedometer2"></i> Dashboard
            </h2>

            <!-- Stats Cards -->
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title mb-0">Tổng khóa học</h6>
                                    <h2 class="mb-0"><?= $viewModel->stats['total_courses'] ?></h2>
                                </div>
                                <i class="bi bi-book fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title mb-0">Hoàn thành</h6>
                                    <h2 class="mb-0"><?= $viewModel->stats['completed'] ?></h2>
                                </div>
                                <i class="bi bi-check-circle fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title mb-0">Đang học</h6>
                                    <h2 class="mb-0"><?= $viewModel->stats['in_progress'] ?></h2>
                                </div>
                                <i class="bi bi-hourglass-split fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title mb-0">Tiến độ TB</h6>
                                    <h2 class="mb-0"><?= $viewModel->stats['avg_progress'] ?>%</h2>
                                </div>
                                <i class="bi bi-graph-up fs-1 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Courses -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Khóa học gần đây</h5>
                    <a href="/student/my-courses" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
                </div>
                <div class="card-body">
                    <?php if (empty($viewModel->recentCourses)): ?>
                        <div class="text-center py-4">
                            <i class="bi bi-book fs-1 text-muted"></i>
                            <p class="text-muted mt-2">Bạn chưa đăng ký khóa học nào.</p>
                            <a href="/courses" class="btn btn-primary">Khám phá khóa học</a>
                        </div>
                    <?php else: ?>
                        <div class="row g-4">
                            <?php foreach ($viewModel->recentCourses as $enrollment): ?>
                                <div class="col-md-6 col-lg-3">
                                    <div class="card h-100">
                                        <?php if ($enrollment['course_image']): ?>
                                            <img src="/<?= htmlspecialchars($enrollment['course_image']) ?>"
                                                 class="card-img-top" style="height: 120px; object-fit: cover;">
                                        <?php endif; ?>
                                        <div class="card-body">
                                            <h6 class="card-title"><?= htmlspecialchars($enrollment['course_title']) ?></h6>
                                            <div class="progress mb-2" style="height: 8px;">
                                                <div class="progress-bar" style="width: <?= $enrollment['progress'] ?>%"></div>
                                            </div>
                                            <small class="text-muted"><?= $enrollment['progress'] ?>% hoàn thành</small>
                                        </div>
                                        <div class="card-footer bg-white">
                                            <a href="/student/course/<?= $enrollment['course_id'] ?>/progress"
                                               class="btn btn-sm btn-primary w-100">Tiếp tục</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>