<?php
/** @var CourseSearchViewModel $viewModel */

use ViewModels\CourseSearchViewModel;
?>
<div class="container py-4">
    <h2 class="mb-4">
        <i class="bi bi-search"></i> Kết quả tìm kiếm: "<?= htmlspecialchars($viewModel->keyword) ?>"
    </h2>

    <?php if (empty($viewModel->courses)): ?>
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> Không tìm thấy khóa học nào với từ khóa "<?= htmlspecialchars(
                $viewModel->keyword) ?>".
        </div>
        <a href="/courses" class="btn btn-primary">Xem tất cả khóa học</a>
    <?php else: ?>
        <p class="text-muted mb-4">Tìm thấy <?= count($viewModel->courses) ?> khóa học</p>

        <div class="row g-4">
            <?php foreach ($viewModel->courses as $course): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 hover-shadow">
                        <?php if ($course->image): ?>
                            <img src="/<?= htmlspecialchars($course->image) ?>" class="card-img-top"
                                 style="height: 160px; object-fit: cover;" alt="course image">
                        <?php else: ?>
                            <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center"
                                 style="height: 160px;">
                                <i class="bi bi-image text-white fs-1"></i>
                            </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-primary"><?= htmlspecialchars($course->category_name) ?></span>
                                <span class="badge bg-<?= $course->level === 'Beginner' ? 'success' : ($course->level === 'Intermediate' ? 'warning' : 'danger') ?>">
                                    <?= htmlspecialchars($course->level) ?>
                                </span>
                            </div>
                            <h5 class="card-title"><?= htmlspecialchars($course->title) ?></h5>
                            <p class="card-text text-muted small">
                                <?= htmlspecialchars(substr($course->description ?? '', 0, 100)) ?>...
                            </p>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-person-circle me-1"></i>
                                <small class="text-muted"><?= htmlspecialchars($course->instructor_name) ?></small>
                            </div>
                        </div>
                        <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-primary">
                                <?= $course->price > 0 ? number_format(
                                        $course->price, 0, ',', '.') . '₫' : 'Miễn phí' ?>
                            </span>
                            <a href="/course/<?= $course->id ?>" class="btn btn-sm btn-primary">Xem chi tiết</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
