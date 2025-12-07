<?php
/**
 * @var ViewModels\AdminStatisticsViewModel $viewModel
 */
?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0">Thống kê & Báo cáo</h1>
            <p class="text-muted">Tổng quan chi tiết về hệ thống</p>
        </div>
    </div>

    <!-- User Statistics -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <h5 class="mb-3"><i class="bi bi-people"></i> Thống kê người dùng</h5>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Tổng người dùng</h6>
                            <h3 class="mb-0"><?= number_format($viewModel->userStats['total']) ?></h3>
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
                            <h3 class="mb-0"><?= number_format($viewModel->userStats['students']) ?></h3>
                            <small class="text-muted"><?= round($viewModel->userStats['students'] / max(1, $viewModel->userStats['total']) * 100, 1) ?>%</small>
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
                            <h3 class="mb-0"><?= number_format($viewModel->userStats['instructors']) ?></h3>
                            <small class="text-muted"><?= round($viewModel->userStats['instructors'] / max(1, $viewModel->userStats['total']) * 100, 1) ?>%</small>
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
                            <h6 class="text-muted mb-1">Đang hoạt động</h6>
                            <h3 class="mb-0"><?= number_format($viewModel->userStats['active']) ?></h3>
                            <small class="text-muted"><?= round($viewModel->userStats['active'] / max(1, $viewModel->userStats['total']) * 100, 1) ?>%</small>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="bi bi-check-circle fs-1 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Course & Enrollment Statistics -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <h5 class="mb-3"><i class="bi bi-book"></i> Thống kê khóa học</h5>
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="text-center p-3 border rounded">
                                <h2 class="mb-1 text-primary"><?= number_format($viewModel->courseStats['total']) ?></h2>
                                <small class="text-muted">Tổng khóa học</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 border rounded">
                                <h2 class="mb-1 text-success"><?= number_format($viewModel->courseStats['approved']) ?></h2>
                                <small class="text-muted">Đã duyệt</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 border rounded">
                                <h2 class="mb-1 text-warning"><?= number_format($viewModel->courseStats['pending']) ?></h2>
                                <small class="text-muted">Chờ duyệt</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 border rounded">
                                <h2 class="mb-1 text-danger"><?= number_format($viewModel->courseStats['rejected']) ?></h2>
                                <small class="text-muted">Từ chối</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <h5 class="mb-3"><i class="bi bi-clipboard-check"></i> Thống kê đăng ký</h5>
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="text-center p-3 border rounded">
                                <h2 class="mb-1 text-primary"><?= number_format($viewModel->enrollmentStats['total']) ?></h2>
                                <small class="text-muted">Tổng lượt đăng ký</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 border rounded">
                                <h2 class="mb-1 text-info"><?= number_format($viewModel->enrollmentStats['active']) ?></h2>
                                <small class="text-muted">Đang học</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 border rounded">
                                <h2 class="mb-1 text-success"><?= number_format($viewModel->enrollmentStats['completed']) ?></h2>
                                <small class="text-muted">Hoàn thành</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-3 mb-4">
        <!-- Category Distribution -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Khóa học theo danh mục</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($viewModel->categoryStats)): ?>
                        <p class="text-muted text-center py-4">Chưa có dữ liệu</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Danh mục</th>
                                        <th class="text-end">Số khóa học</th>
                                        <th style="width: 40%;">Tỷ lệ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $totalCourses = array_sum(array_column($viewModel->categoryStats, 'course_count'));
                                    foreach ($viewModel->categoryStats as $cat): 
                                        $percentage = $totalCourses > 0 ? round($cat['course_count'] / $totalCourses * 100, 1) : 0;
                                    ?>
                                        <tr>
                                            <td><?= htmlspecialchars($cat['name']) ?></td>
                                            <td class="text-end"><?= number_format($cat['course_count']) ?></td>
                                            <td>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar" role="progressbar" style="width: <?= $percentage ?>%">
                                                        <?= $percentage ?>%
                                                    </div>
                                                </div>
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

        <!-- Monthly User Growth -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-graph-up"></i> Người dùng mới (6 tháng gần đây)</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($viewModel->monthlyUsers)): ?>
                        <p class="text-muted text-center py-4">Chưa có dữ liệu</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Tháng</th>
                                        <th class="text-end">Số người dùng</th>
                                        <th style="width: 40%;">Biểu đồ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $maxCount = max(array_column($viewModel->monthlyUsers, 'count'));
                                    foreach ($viewModel->monthlyUsers as $month): 
                                        $percentage = $maxCount > 0 ? round($month['count'] / $maxCount * 100, 1) : 0;
                                        $date = DateTime::createFromFormat('Y-m', $month['month']);
                                        $monthName = $date ? $date->format('m/Y') : $month['month'];
                                    ?>
                                        <tr>
                                            <td><?= $monthName ?></td>
                                            <td class="text-end"><?= number_format($month['count']) ?></td>
                                            <td>
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar bg-success" role="progressbar" style="width: <?= $percentage ?>%">
                                                        <?= $month['count'] ?>
                                                    </div>
                                                </div>
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

    <!-- Top Lists -->
    <div class="row g-3 mb-4">
        <!-- Top Instructors -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-star"></i> Top 10 giảng viên</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($viewModel->topInstructors)): ?>
                        <p class="text-muted text-center py-4">Chưa có dữ liệu</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Giảng viên</th>
                                        <th>Email</th>
                                        <th class="text-end">Số khóa học</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($viewModel->topInstructors as $index => $instructor): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td><?= htmlspecialchars($instructor['fullname']) ?></td>
                                            <td><?= htmlspecialchars($instructor['email']) ?></td>
                                            <td class="text-end">
                                                <span class="badge bg-primary"><?= number_format($instructor['course_count']) ?></span>
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

        <!-- Popular Courses -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-trophy"></i> Top 10 khóa học phổ biến</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($viewModel->popularCourses)): ?>
                        <p class="text-muted text-center py-4">Chưa có dữ liệu</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Khóa học</th>
                                        <th>Giảng viên</th>
                                        <th class="text-end">Lượt đăng ký</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($viewModel->popularCourses as $index => $course): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td><?= htmlspecialchars(mb_substr($course['title'], 0, 40)) ?><?= mb_strlen($course['title']) > 40 ? '...' : '' ?></td>
                                            <td><?= htmlspecialchars($course['instructor_name']) ?></td>
                                            <td class="text-end">
                                                <span class="badge bg-success"><?= number_format($course['enrollment_count']) ?></span>
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
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
