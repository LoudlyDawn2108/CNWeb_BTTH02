<?php
/** @var CourseDetailViewModel $viewModel */

use ViewModels\CourseDetailViewModel;
use Models\User;

?>
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="/courses">Khóa học</a></li>
            <li class="breadcrumb-item active"><?= htmlspecialchars($viewModel->course->title) ?></li>
        </ol>
    </nav>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Course Header -->
            <div class="card mb-4">
                <?php if ($viewModel->course->image): ?>
                    <img src="/<?= htmlspecialchars($viewModel->course->image) ?>" class="card-img-top"
                         alt="<?= htmlspecialchars($viewModel->course->title) ?>" style="max-height: 400px; object-fit: cover;">
                <?php endif; ?>
                <div class="card-body">
                    <div class="d-flex gap-2 mb-3">
                        <span class="badge bg-primary"><?= htmlspecialchars($viewModel->course->category_name) ?></span>
                        <span class="badge bg-<?= $viewModel->course->level === 'Beginner' ? 'success' : ($viewModel->course->level === 'Intermediate' ? 'warning' : 'danger') ?>">
                            <?= htmlspecialchars($viewModel->course->level) ?>
                        </span>
                    </div>
                    <h1 class="card-title h2"><?= htmlspecialchars($viewModel->course->title) ?></h1>
                    <p class="lead text-muted"><?= htmlspecialchars($viewModel->course->description) ?></p>

                    <div class="d-flex align-items-center gap-4 text-muted mb-3">
                        <div>
                            <i class="bi bi-person-circle"></i>
                            <strong><?= htmlspecialchars($viewModel->course->instructor_name) ?></strong>
                        </div>
                        <div><i class="bi bi-people"></i> <?= $viewModel->course->enrollment_count ?> học viên</div>
                        <div><i class="bi bi-clock"></i> <?= $viewModel->course->duration_weeks ?> tuần</div>
                        <div><i class="bi bi-journal-text"></i> <?= $viewModel->course->lesson_count ?> bài học</div>
                    </div>
                </div>
            </div>

            <!-- Course Content -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-list-ul"></i> Nội dung khóa học</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($viewModel->lessons)): ?>
                        <p class="text-muted">Khóa học chưa có bài học nào.</p>
                    <?php else: ?>
                        <div class="accordion" id="lessonAccordion">
                            <?php foreach ($viewModel->lessons as $index => $lesson): ?>
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button <?= $index > 0 ? 'collapsed' : '' ?>" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#lesson<?= $lesson->id ?>">
                                            <span class="badge bg-secondary me-2"><?= $index + 1 ?></span>
                                            <?= htmlspecialchars($lesson->title) ?>
                                        </button>
                                    </h2>
                                    <div id="lesson<?= $lesson->id ?>" class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>">
                                        <div class="accordion-body d-flex justify-content-between align-content-center">
                                            <?php if ($viewModel->isEnrolled): ?>
                                                <p><?= nl2br(htmlspecialchars(substr($lesson->content ?? '', 0, 200))) ?>...</p>
                                                <a href="/student/lesson/<?= $lesson->id ?>" class="btn btn-sm btn-primary">
                                                    <i class="bi bi-play-circle"></i> Học ngay
                                                </a>
                                            <?php elseif ($viewModel->currentUser): ?>
                                                <p><?= nl2br(htmlspecialchars(substr($lesson->content ?? '', 0, 200))) ?>...</p>
                                                
                                                <?php if ($viewModel->currentUser['role'] == User::ROLE_INSTRUCTOR): ?>
                                                    <!-- Instructor View -->
                                                    <button class="btn btn-sm btn-secondary" disabled>
                                                        <i class="bi bi-person-badge"></i> Giảng viên
                                                    </button>
                                                <?php else: ?>
                                                    <!-- Student View (Original) -->
                                                    <a href="/enrollment/enroll<?= $lesson->id ?>" class="btn btn-sm btn-primary">
                                                        <i class="bi bi-play-circle"></i> Đăng ký ngay
                                                    </a>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <p><?= nl2br(htmlspecialchars(substr($lesson->content ?? '', 0, 200))) ?>...</p>
                                                <a href="/auth/login" class="btn btn-sm btn-primary">
                                                    <i class="bi bi-play-circle"></i> Đăng ký ngay
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="card sticky-top" style="top: 80px;">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h2 class="text-primary mb-0">
                            <?= $viewModel->course->price > 0 ? number_format($viewModel->course->price, 0, ',', '.') . '₫' : 'Miễn phí' ?>
                        </h2>
                    </div>

                    <?php if ($viewModel->isEnrolled): ?>
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle"></i> Bạn đã đăng ký khóa học này
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tiến độ học tập</label>
                            <div class="progress" style="height: 25px;">
                                <div class="progress-bar" role="progressbar"
                                     style="width: <?= $viewModel->enrollment['progress'] ?>%">
                                    <?= $viewModel->enrollment['progress'] ?>%
                                </div>
                            </div>
                        </div>
                        <a href="/student/course/<?= $viewModel->course->id ?>/progress" class="btn btn-primary w-100 mb-2">
                            <i class="bi bi-play-circle"></i> Tiếp tục học
                        </a>
                    <?php elseif ($viewModel->currentUser): ?>
                        <?php if ($viewModel->currentUser['role'] == User::ROLE_INSTRUCTOR): ?>
                            <?php if ($viewModel->course->instructor_id == $viewModel->currentUser['id']): ?>
                                <a href="/instructor/courses/<?= $viewModel->course->id ?>/manage" class="btn btn-warning btn-lg w-100 mb-2">
                                    <i class="bi bi-gear-fill me-2"></i>Quản lý khóa học
                                </a>
                                <div class="text-center text-muted small">
                                    <i class="bi bi-eye"></i> Bạn đang xem với tư cách chủ sở hữu
                                </div>
                            <?php else: ?>
                                <button class="btn btn-secondary btn-lg w-100" disabled>
                                    <i class="bi bi-person-badge me-2"></i>Tài khoản Giảng viên
                                </button>
                                <div class="alert alert-info mt-3 mb-0 small">
                                    <i class="bi bi-info-circle"></i> Giảng viên chỉ có quyền xem thông tin khóa học.
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <!-- Student View (Original) -->
                            <form action="/enrollment/enroll" method="POST">
                                <input type="hidden" name="course_id" value="<?= $viewModel->course->id ?>">
                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    <i class="bi bi-cart-plus"></i> Đăng ký ngay
                                </button>
                            </form>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="/auth/login" class="btn btn-primary btn-lg w-100">
                            <i class="bi bi-box-arrow-in-right"></i> Đăng nhập để đăng ký
                        </a>
                    <?php endif; ?>

                    <hr>

                    <h6>Khóa học bao gồm:</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="bi bi-journal-text text-primary me-2"></i> <?= $viewModel->course->lesson_count ?> bài học</li>
                        <li class="mb-2"><i class="bi bi-clock text-primary me-2"></i> <?= $viewModel->course->duration_weeks ?> tuần học</li>
                        <li class="mb-2"><i class="bi bi-trophy text-primary me-2"></i> Chứng chỉ hoàn thành</li>
                        <li class="mb-2"><i class="bi bi-infinity text-primary me-2"></i> Truy cập trọn đời</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Courses -->
    <?php if (!empty($viewModel->relatedCourses)): ?>
        <section class="mt-5">
            <h4 class="mb-4">Khóa học liên quan</h4>
            <div class="row g-4">
                <?php foreach ($viewModel->relatedCourses as $related): ?>
                    <div class="col-md-6 col-lg-3">
                        <div class="card h-100 hover-shadow">
                            <?php if ($related->image): ?>
                                <img src="/<?= htmlspecialchars($related->image) ?>" class="card-img-top"
                                     style="height: 120px; object-fit: cover;" alt="related course image">
                            <?php endif; ?>
                            <div class="card-body">
                                <h6 class="card-title"><?= htmlspecialchars($related->title) ?></h6>
                                <p class="text-muted small mb-0">
                                    <?= $related->price > 0 ? number_format($related->price, 0, ',', '.') . '₫' : 'Miễn phí' ?>
                                </p>
                            </div>
                            <div class="card-footer bg-white">
                                <a href="/course/<?= $related->id ?>" class="btn btn-sm btn-outline-primary w-100">Xem chi tiết</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</div>
