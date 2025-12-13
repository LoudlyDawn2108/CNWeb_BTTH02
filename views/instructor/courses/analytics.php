<?php
/** @var ViewModels\Instructor\CourseAnalyticsViewModel $viewModel */
?>

<div class="container py-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="/instructor/dashboard" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="/instructor/courses/<?= $viewModel->course->id ?>/manage" class="text-decoration-none">Quản lý khóa học</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Thống kê</li>
                </ol>
            </nav>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-graph-up text-primary me-2"></i>Thống kê khóa học
            </h2>
            <p class="text-muted mb-0"><?= htmlspecialchars($viewModel->course->title) ?></p>
        </div>
        <a href="/instructor/courses/<?= $viewModel->course->id ?>/manage" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Quay lại
        </a>
    </div>

    <!-- Overview Stats Cards -->
    <div class="row g-4 mb-4">
        <!-- Tổng học viên -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                                <i class="bi bi-people-fill"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0 fw-bold"><?= $viewModel->stats->totalEnrollments ?></h3>
                            <small class="text-muted">Tổng học viên</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Doanh thu -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stat-icon bg-success bg-opacity-10 text-success">
                                <i class="bi bi-currency-dollar"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0 fw-bold"><?= $viewModel->stats->totalRevenueFormatted ?></h3>
                            <small class="text-muted">Doanh thu</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tiến độ trung bình -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stat-icon bg-info bg-opacity-10 text-info">
                                <i class="bi bi-speedometer2"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0 fw-bold"><?= $viewModel->stats->averageProgress ?>%</h3>
                            <small class="text-muted">Tiến độ TB</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Số bài học -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                                <i class="bi bi-collection-play"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0 fw-bold"><?= $viewModel->stats->totalLessons ?></h3>
                            <small class="text-muted">Bài học</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Phân bố trạng thái học viên -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-pie-chart text-primary me-2"></i>Trạng thái học viên
                    </h5>
                </div>
                <div class="card-body">
                    <?php if ($viewModel->stats->totalEnrollments > 0): ?>
                        <div class="row text-center mb-4">
                            <div class="col-4">
                                <div class="p-3 rounded-3 bg-success bg-opacity-10">
                                    <h2 class="fw-bold text-success mb-0"><?= $viewModel->stats->activeStudents ?></h2>
                                    <small class="text-muted">Đang học</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-3 rounded-3 bg-primary bg-opacity-10">
                                    <h2 class="fw-bold text-primary mb-0"><?= $viewModel->stats->completedStudents ?></h2>
                                    <small class="text-muted">Hoàn thành</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="p-3 rounded-3 bg-danger bg-opacity-10">
                                    <h2 class="fw-bold text-danger mb-0"><?= $viewModel->stats->droppedStudents ?></h2>
                                    <small class="text-muted">Bỏ học</small>
                                </div>
                            </div>
                        </div>

                        <!-- Stacked Progress Bar -->
                        <div class="progress" style="height: 30px;">
                            <?php
                            $total = $viewModel->stats->totalEnrollments;
                            $activePercent = ($viewModel->stats->activeStudents / $total) * 100;
                            $completedPercent = ($viewModel->stats->completedStudents / $total) * 100;
                            $droppedPercent = ($viewModel->stats->droppedStudents / $total) * 100;
                            ?>
                            <div class="progress-bar bg-success" style="width: <?= $activePercent ?>%"
                                 data-bs-toggle="tooltip" title="Đang học: <?= $viewModel->stats->activeStudents ?>">
                                <?= round($activePercent) ?>%
                            </div>
                            <div class="progress-bar bg-primary" style="width: <?= $completedPercent ?>%"
                                 data-bs-toggle="tooltip" title="Hoàn thành: <?= $viewModel->stats->completedStudents ?>">
                                <?= round($completedPercent) ?>%
                            </div>
                            <div class="progress-bar bg-danger" style="width: <?= $droppedPercent ?>%"
                                 data-bs-toggle="tooltip" title="Bỏ học: <?= $viewModel->stats->droppedStudents ?>">
                                <?= round($droppedPercent) ?>%
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mt-2">
                            <small><span class="badge bg-success me-1"></span> Đang học</small>
                            <small><span class="badge bg-primary me-1"></span> Hoàn thành</small>
                            <small><span class="badge bg-danger me-1"></span> Bỏ học</small>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2 mb-0">Chưa có học viên nào đăng ký</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Phân bố tiến độ học -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-bar-chart text-info me-2"></i>Phân bố tiến độ
                    </h5>
                </div>
                <div class="card-body">
                    <?php if ($viewModel->stats->totalEnrollments > 0):
                        $dist = $viewModel->stats->progressDistribution;
                        $total = $viewModel->stats->totalEnrollments;
                    ?>
                        <div class="row g-3">
                            <div class="col-6 col-md-3">
                                <div class="progress-stat text-center p-3 border rounded-3">
                                    <div class="progress-circle bg-secondary bg-opacity-10 mx-auto mb-2">
                                        <span class="text-secondary"><?= $dist['not_started'] ?></span>
                                    </div>
                                    <small class="text-muted d-block">Chưa bắt đầu</small>
                                    <small class="text-secondary">(0%)</small>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="progress-stat text-center p-3 border rounded-3">
                                    <div class="progress-circle bg-warning bg-opacity-10 mx-auto mb-2">
                                        <span class="text-warning"><?= $dist['in_progress'] ?></span>
                                    </div>
                                    <small class="text-muted d-block">Đang học</small>
                                    <small class="text-warning">(1-49%)</small>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="progress-stat text-center p-3 border rounded-3">
                                    <div class="progress-circle bg-info bg-opacity-10 mx-auto mb-2">
                                        <span class="text-info"><?= $dist['halfway'] ?></span>
                                    </div>
                                    <small class="text-muted d-block">Gần xong</small>
                                    <small class="text-info">(50-99%)</small>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="progress-stat text-center p-3 border rounded-3">
                                    <div class="progress-circle bg-success bg-opacity-10 mx-auto mb-2">
                                        <span class="text-success"><?= $dist['completed'] ?></span>
                                    </div>
                                    <small class="text-muted d-block">Hoàn thành</small>
                                    <small class="text-success">(100%)</small>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="bi bi-bar-chart text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2 mb-0">Chưa có dữ liệu tiến độ</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Top học viên -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-trophy text-warning me-2"></i>Top học viên
                    </h5>
                    <a href="/instructor/courses/<?= $viewModel->course->id ?>/students" class="btn btn-sm btn-outline-primary">
                        Xem tất cả
                    </a>
                </div>
                <div class="card-body p-0">
                    <?php
                    $topStudents = $viewModel->getTopStudents();
                    if (!$topStudents->isEmpty()):
                    ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">#</th>
                                        <th>Học viên</th>
                                        <th>Trạng thái</th>
                                        <th>Tiến độ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $rank = 1;
                                    foreach ($topStudents->toArray() as $student):
                                        $statusClass = match($student->status) {
                                            'completed' => 'success',
                                            'active' => 'primary',
                                            'dropped' => 'danger',
                                            default => 'secondary'
                                        };
                                        $statusLabel = match($student->status) {
                                            'completed' => 'Hoàn thành',
                                            'active' => 'Đang học',
                                            'dropped' => 'Bỏ học',
                                            default => 'Không rõ'
                                        };
                                    ?>
                                        <tr>
                                            <td class="ps-4">
                                                <?php if ($rank <= 3): ?>
                                                    <span class="badge bg-warning text-dark"><?= $rank ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted"><?= $rank ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px;">
                                                        <span class="text-primary fw-bold"><?= strtoupper(substr($student->student_name ?? 'U', 0, 1)) ?></span>
                                                    </div>
                                                    <span><?= htmlspecialchars($student->student_name ?? 'Unknown') ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?= $statusClass ?> bg-opacity-10 text-<?= $statusClass ?>">
                                                    <?= $statusLabel ?>
                                                </span>
                                            </td>
                                            <td style="width: 200px;">
                                                <div class="d-flex align-items-center">
                                                    <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                                        <div class="progress-bar bg-<?= $student->progress >= 100 ? 'success' : 'primary' ?>"
                                                             style="width: <?= $student->progress ?>%"></div>
                                                    </div>
                                                    <small class="text-muted"><?= $student->progress ?>%</small>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php
                                        $rank++;
                                    endforeach;
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="bi bi-trophy text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2 mb-0">Chưa có học viên</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Thông tin khóa học -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-info-circle text-primary me-2"></i>Thông tin khóa học
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Giá khóa học</span>
                            <strong><?= $viewModel->stats->priceFormatted ?></strong>
                        </li>
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Thời lượng</span>
                            <strong><?= $viewModel->course->duration_weeks ?? 0 ?> tuần</strong>
                        </li>
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Cấp độ</span>
                            <strong><?= $viewModel->course->level ?? 'N/A' ?></strong>
                        </li>
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Trạng thái</span>
                            <span class="badge bg-<?= $viewModel->course->status == 'approved' ? 'success' : 'warning' ?>">
                                <?= $viewModel->course->status == 'approved' ? 'Đã duyệt' : 'Chờ duyệt' ?>
                            </span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Thống kê nội dung -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-folder text-success me-2"></i>Nội dung khóa học
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3 p-3 bg-light rounded">
                        <div class="stat-icon-sm bg-primary bg-opacity-10 text-primary me-3">
                            <i class="bi bi-collection-play"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold"><?= $viewModel->stats->totalLessons ?></h5>
                            <small class="text-muted">Tổng bài học</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-3 p-3 bg-light rounded">
                        <div class="stat-icon-sm bg-danger bg-opacity-10 text-danger me-3">
                            <i class="bi bi-play-btn"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold"><?= $viewModel->stats->lessonsWithVideo ?></h5>
                            <small class="text-muted">Bài có video</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center p-3 bg-light rounded">
                        <div class="stat-icon-sm bg-warning bg-opacity-10 text-warning me-3">
                            <i class="bi bi-paperclip"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold"><?= $viewModel->stats->totalMaterials ?></h5>
                            <small class="text-muted">Tài liệu đính kèm</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Học viên gần đây -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-clock-history text-info me-2"></i>Đăng ký gần đây
                    </h5>
                </div>
                <div class="card-body p-0">
                    <?php
                    $recentEnrollments = $viewModel->getRecentEnrollments();
                    if (!$recentEnrollments->isEmpty()):
                    ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($recentEnrollments->toArray() as $enrollment): ?>
                                <li class="list-group-item d-flex align-items-center">
                                    <div class="avatar-sm bg-info bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                        <span class="text-info fw-bold"><?= strtoupper(substr($enrollment->student_name ?? 'U', 0, 1)) ?></span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold"><?= htmlspecialchars($enrollment->student_name ?? 'Unknown') ?></div>
                                        <small class="text-muted">
                                            <?php
                                            if ($enrollment->enrolled_date) {
                                                $date = new DateTime($enrollment->enrolled_date);
                                                echo $date->format('d/m/Y');
                                            } else {
                                                echo 'N/A';
                                            }
                                            ?>
                                        </small>
                                    </div>
                                    <span class="badge bg-<?= $enrollment->status == 'active' ? 'success' : ($enrollment->status == 'completed' ? 'primary' : 'danger') ?> bg-opacity-10 text-<?= $enrollment->status == 'active' ? 'success' : ($enrollment->status == 'completed' ? 'primary' : 'danger') ?>">
                                        <?= $enrollment->progress ?>%
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="bi bi-clock-history text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mt-2 mb-0">Chưa có đăng ký</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .stat-card {
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .stat-icon-sm {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }

    .progress-circle {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        font-weight: bold;
    }

    .avatar-sm {
        width: 35px;
        height: 35px;
        font-size: 0.875rem;
    }

    .card {
        transition: all 0.3s ease;
    }

    .progress {
        border-radius: 10px;
        overflow: hidden;
    }

    .progress-bar {
        transition: width 0.6s ease;
    }

    .list-group-item {
        transition: background-color 0.2s ease;
    }

    .list-group-item:hover {
        background-color: rgba(0, 123, 255, 0.03);
    }

    .table tbody tr {
        transition: background-color 0.2s ease;
    }

    .table tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.05);
    }
</style>

<script>
    // Initialize Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
</script>
