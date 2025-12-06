<?php
/** @var HomeIndexViewModel $viewModel */

use ViewModels\HomeIndexViewModel;

?>
<!-- Hero Section -->
<section class="bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-3">Học mọi lúc, mọi nơi</h1>
                <p class="lead mb-4">Khám phá hàng nghìn khóa học chất lượng từ các giảng viên hàng đầu. Nâng cao kỹ năng và phát triển sự nghiệp của bạn ngay hôm nay!</p>
                <a href="/courses" class="btn btn-light btn-lg me-2">
                    <i class="bi bi-search"></i> Khám phá khóa học
                </a>
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="/auth/register" class="btn btn-outline-light btn-lg">Đăng ký miễn phí</a>
                <?php endif; ?>
            </div>
            <div class="col-lg-6 d-none d-lg-block text-center">
                <i class="bi bi-mortarboard-fill" style="font-size: 15rem; opacity: 0.3;"></i>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-4">Danh mục khóa học</h2>
        <div class="row g-4">
            <?php foreach ($viewModel->categories as $category): ?>
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="/courses?category=<?= $category->id ?>" class="text-decoration-none">
                        <div class="card h-100 text-center hover-shadow">
                            <div class="card-body">
                                <i class="bi bi-folder2-open fs-1 text-primary"></i>
                                <h6 class="card-title mt-2 mb-0"><?= htmlspecialchars($category->name) ?></h6>
                                <small class="text-muted"><?= $category->course_count ?> khóa học</small>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured Courses Section -->
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Khóa học nổi bật</h2>
            <a href="/courses" class="btn btn-outline-primary">Xem tất cả <i class="bi bi-arrow-right"></i></a>
        </div>

        <?php if (empty($viewModel->featuredCourses)): ?>
            <div class="alert alert-info">Chưa có khóa học nào.</div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($viewModel->featuredCourses as $course): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 hover-shadow">
                            <?php if ($course->image): ?>
                                <img src="/<?= htmlspecialchars($course->image) ?>" class="card-img-top" alt="<?= htmlspecialchars($course->title) ?>" style="height: 180px; object-fit: cover;">
                            <?php else: ?>
                                <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" style="height: 180px;">
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
                                    <?= htmlspecialchars(substr($course->description, 0, 100)) ?>...
                                </p>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-person-circle me-1"></i>
                                    <small class="text-muted"><?= htmlspecialchars($course->instructor_name) ?></small>
                                </div>
                            </div>
                            <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-bold text-primary">
                                        <?= $course->price > 0 ? number_format($course->price, 0, ',', '.') . '₫' : 'Miễn phí' ?>
                                    </span>
                                </div>
                                <a href="/course/<?= $course->id ?>" class="btn btn-sm btn-primary">Xem chi tiết</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Stats Section -->
<section class="py-5 bg-primary text-white">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3 mb-3">
                <i class="bi bi-book fs-1"></i>
                <h2 class="fw-bold">100+</h2>
                <p>Khóa học</p>
            </div>
            <div class="col-md-3 mb-3">
                <i class="bi bi-people fs-1"></i>
                <h2 class="fw-bold">1000+</h2>
                <p>Học viên</p>
            </div>
            <div class="col-md-3 mb-3">
                <i class="bi bi-person-badge fs-1"></i>
                <h2 class="fw-bold">50+</h2>
                <p>Giảng viên</p>
            </div>
            <div class="col-md-3 mb-3">
                <i class="bi bi-award fs-1"></i>
                <h2 class="fw-bold">500+</h2>
                <p>Chứng chỉ</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5">
    <div class="container text-center">
        <h2 class="mb-3">Bạn là giảng viên?</h2>
        <p class="lead text-muted mb-4">Chia sẻ kiến thức và kiếm thu nhập cùng chúng tôi. Tạo khóa học của riêng bạn ngay hôm nay!</p>
        <a href="/auth/register" class="btn btn-primary btn-lg">Trở thành giảng viên</a>
    </div>
</section>
