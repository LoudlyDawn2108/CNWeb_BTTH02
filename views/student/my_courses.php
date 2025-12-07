<?php
/** @var MyCoursesViewModel $viewModel */
use ViewModels\MyCoursesViewModel;
?>

<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="mb-0">Khóa học của tôi</h1>
            <p class="text-muted">Quản lý tiến độ học tập của bạn</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="/courses" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Đăng ký khóa học mới
            </a>
        </div>
    </div>

    <?php if (empty($viewModel->enrollments)): ?>
        <div class="alert alert-info text-center py-5">
            <i class="bi bi-journal-bookmark fs-1 d-block mb-3"></i>
            <h4>Bạn chưa đăng ký khóa học nào</h4>
            <p class="mb-4">Hãy khám phá các khóa học thú vị của chúng tôi và bắt đầu hành trình học tập ngay hôm nay!</p>
            <a href="/courses" class="btn btn-primary">Khám phá khóa học</a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($viewModel->enrollments as $enrollment): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm">
                        <?php if (!empty($enrollment['course_image'])): ?>
                            <img src="<?= htmlspecialchars($enrollment['course_image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($enrollment['course_title']) ?>" style="height: 200px; object-fit: cover;">
                        <?php else: ?>
                            <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="bi bi-book fs-1 text-muted"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="card-body d-flex flex-column">
                            <div class="mb-2">
                                <span class="badge bg-<?= $enrollment['status'] === 'completed' ? 'success' : ($enrollment['status'] === 'active' ? 'primary' : 'secondary') ?>">
                                    <?= $enrollment['status'] === 'completed' ? 'Đã hoàn thành' : ($enrollment['status'] === 'active' ? 'Đang học' : 'Đã hủy') ?>
                                </span>
                                <span class="badge bg-info text-dark float-end"><?= htmlspecialchars($enrollment['category_name'] ?? 'General') ?></span>
                            </div>
                            
                            <h5 class="card-title"><?= htmlspecialchars($enrollment['course_title']) ?></h5>
                            <p class="card-text text-muted small mb-3">
                                <i class="bi bi-person-circle"></i> <?= htmlspecialchars($enrollment['instructor_name'] ?? 'Unknown') ?>
                            </p>
                            
                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <small class="text-muted">Tiến độ</small>
                                    <small class="fw-bold"><?= $enrollment['progress'] ?>%</small>
                                </div>
                                <div class="progress mb-3" style="height: 6px;">
                                    <div class="progress-bar" role="progressbar" style="width: <?= $enrollment['progress'] ?>%" aria-valuenow="<?= $enrollment['progress'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                
                                <div class="d-grid">
                                    <a href="/learning/<?= $enrollment['course_id'] ?>" class="btn btn-outline-primary">
                                        <?= $enrollment['progress'] > 0 ? 'Tiếp tục học' : 'Bắt đầu học' ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-white text-muted small">
                            <i class="bi bi-calendar3"></i> Đăng ký: <?= date('d/m/Y', strtotime($enrollment['enrolled_date'])) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
