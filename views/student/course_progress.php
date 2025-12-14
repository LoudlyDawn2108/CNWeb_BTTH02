<?php
/** @var CourseProgressViewModel $viewModel */
use ViewModels\CourseProgressViewModel;
?>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-3 col-lg-2">
            <?php include __DIR__ . '/../layouts/sidebar.php'; ?>
        </div>
        <div class="col-md-9 col-lg-10">
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/student/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="/student/my-courses">Khóa học của tôi</a></li>
                    <li class="breadcrumb-item active"><?= htmlspecialchars($viewModel->course->title) ?></li>
                </ol>
            </nav>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h4 class="mb-0"><?= htmlspecialchars($viewModel->course->title) ?></h4>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span>Tiến độ học tập</span>
                                <span class="fw-bold"><?= $viewModel->enrollment->progress ?>%</span>
                            </div>
                            <div class="progress mb-4" style="height: 20px;">
                                <div class="progress-bar bg-<?= $viewModel->enrollment->progress >= 100 ? 'success' : 'primary' ?>"
                                     style="width: <?= $viewModel->enrollment->progress ?>%">
                                    <?= $viewModel->enrollment->progress ?>%
                                </div>
                            </div>

                            <h5 class="mb-3"><i class="bi bi-list-ol"></i> Danh sách bài học</h5>

                            <?php if (empty($viewModel->lessons)): ?>
                                <div class="alert alert-info">Khóa học chưa có bài học nào.</div>
                            <?php else: ?>
                                <div class="list-group">
                                    <?php foreach ($viewModel->lessons as $index => $lesson): ?>
                                        <a href="/student/lesson/<?= $lesson->id ?>"
                                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="badge bg-secondary me-2"><?= $index + 1 ?></span>
                                                <?= htmlspecialchars($lesson->title) ?>
                                            </div>
                                            <div>
                                                <i class="bi bi-chevron-right"></i>
                                            </div>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-info-circle"></i> Thông tin khóa học</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <i class="bi bi-bar-chart text-primary me-2"></i>
                                    <strong>Cấp độ:</strong> <?= htmlspecialchars($viewModel->course->level ?? 'N/A') ?>
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-clock text-primary me-2"></i>
                                    <strong>Thời lượng:</strong> <?= $viewModel->course->duration_weeks ?? 'N/A' ?> tuần
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-journal-text text-primary me-2"></i>
                                    <strong>Số bài học:</strong> <?= count($viewModel->lessons) ?>
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-calendar text-primary me-2"></i>
                                    <strong>Ngày đăng ký:</strong> <?= date('d/m/Y', strtotime($viewModel->enrollment->enrolled_date)) ?>
                                </li>
                                <li>
                                    <i class="bi bi-flag text-primary me-2"></i>
                                    <strong>Trạng thái:</strong>
                                    <span class="badge bg-<?= $viewModel->enrollment->status === 'completed' ? 'success' : 'warning' ?>">
                                        <?= $viewModel->enrollment->status === 'completed' ? 'Hoàn thành' : 'Đang học' ?>
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>