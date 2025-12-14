<?php
/** @var MyCoursesViewModel $viewModel */
use ViewModels\MyCoursesViewModel;
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-3 col-lg-2">
            <?php include __DIR__ . '/../layouts/sidebar.php'; ?>
        </div>
        <div class="col-md-9 col-lg-10">
            <h2 class="mb-4"><i class="bi bi-book"></i> Khóa học của tôi</h2>

            <?php if (empty($viewModel->enrollments)): ?>
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-book fs-1 text-muted"></i>
                        <p class="text-muted mt-2">Bạn chưa đăng ký khóa học nào.</p>
                        <a href="/courses" class="btn btn-primary">Khám phá khóa học</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($viewModel->enrollments as $enrollment): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100">
                                <?php if ($enrollment->course_image): ?>
                                    <img src="/<?= htmlspecialchars($enrollment->course_image) ?>"
                                         class="card-img-top" style="height: 150px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" style="height: 150px;">
                                        <i class="bi bi-image text-white fs-1"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="badge bg-primary"><?= htmlspecialchars($enrollment->category_name) ?></span>
                                        <span class="badge bg-<?= $enrollment->status === 'completed' ? 'success' : ($enrollment->status === 'active' ? 'warning' : 'secondary') ?>">
                                            <?= $enrollment->status === 'completed' ? 'Hoàn thành' : ($enrollment->status === 'active' ? 'Đang học' : 'Đã hủy') ?>
                                        </span>
                                    </div>
                                    <h5 class="card-title"><?= htmlspecialchars($enrollment->course_title) ?></h5>
                                    <p class="text-muted small mb-2">
                                        <i class="bi bi-person"></i> <?= htmlspecialchars($enrollment->instructor_name) ?>
                                    </p>
                                    <div class="mb-2">
                                        <small class="text-muted">Tiến độ:</small>
                                        <div class="progress" style="height: 10px;">
                                            <div class="progress-bar bg-<?= $enrollment->progress >= 100 ? 'success' : 'primary' ?>"
                                                 style="width: <?= $enrollment->progress ?>%"></div>
                                        </div>
                                        <small class="text-muted"><?= $enrollment->progress ?>% hoàn thành</small>
                                    </div>
                                </div>
                                <div class="card-footer bg-white">
                                    <div class="d-flex gap-2">
                                        <a href="/student/course/<?= $enrollment->course_id ?>/progress"
                                           class="btn btn-sm btn-primary flex-grow-1">
                                            <i class="bi bi-play-circle"></i> Tiếp tục học
                                        </a>
                                        <form action="/enrollment/unenroll" method="POST" class="d-inline"
                                              onsubmit="return confirm('Bạn có chắc muốn hủy đăng ký khóa học này?');">
                                            <input type="hidden" name="course_id" value="<?= $enrollment->course_id ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
