<?php
/** @var CourseListViewModel $viewModel */

use ViewModels\CourseListViewModel;

?>
<div class="container py-4">
    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-lg-3 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <i class="bi bi-funnel"></i> Bộ lọc
                </div>
                <div class="card-body">
                    <form action="/courses" method="GET">
                        <!-- Search -->
                        <div class="mb-3">
                            <label for="search" class="form-label fw-bold">Tìm kiếm</label>
                            <input type="text" class="form-control" id="search" name="search"
                                   value="<?= htmlspecialchars($viewModel->filters['search'] ?? '') ?>"
                                   placeholder="Nhập từ khóa...">
                        </div>

                        <!-- Category -->
                        <div class="mb-3">
                            <label for="category" class="form-label fw-bold">Danh mục</label>
                            <select class="form-select" id="category" name="category">
                                <option value="">Tất cả danh mục</option>
                                <?php foreach ($viewModel->categories as $cat): ?>
                                    <option value="<?= $cat->id ?>"
                                            <?= ($viewModel->filters['category_id'] ?? '') == $cat->id ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat->name) ?>categories
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Level -->
                        <div class="mb-3">
                            <label for="level" class="form-label fw-bold">Cấp độ</label>
                            <select class="form-select" id="level" name="level">
                                <?php foreach ($viewModel->levels as $level): ?>
                                    <option value="<?= $level ?>"
                                            <?= ($viewModel->filters['level'] ?? '') == $level ? 'selected' : '' ?>>
                                        <?= $level ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i> Tìm kiếm
                        </button>
                        <a href="/courses" class="btn btn-outline-secondary w-100 mt-2">
                            <i class="bi bi-x-circle"></i> Xóa bộ lọc
                        </a>
                    </form>
                </div>
            </div>
        </div>

        <!-- Course List -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>
                    Tất cả khóa học
                </h2>
                <span class="text-muted"><?= count($viewModel->courses) ?> khóa học</span>
            </div>

            <?php if (empty($viewModel->courses)): ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Không tìm thấy khóa học nào phù hợp.
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($viewModel->courses as $course): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 hover-shadow">
                                <?php if ($course->image): ?>
                                    <img src="/<?= htmlspecialchars($course->image) ?>" class="card-img-top"
                                         alt="<?= htmlspecialchars($course->title) ?>"
                                         style="height: 160px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center"
                                         style="height: 160px;">
                                        <i class="bi bi-image text-white fs-1"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <span class="badge bg-primary"><?= htmlspecialchars(
                                                    $course->category_name) ?></span>
                                        <span class="badge bg-<?= $course->level === 'Beginner' ? 'success' : ($course->level === 'Intermediate' ? 'warning' : 'danger') ?>">
                                            <?= htmlspecialchars($course->level) ?>
                                        </span>
                                    </div>
                                    <h6 class="card-title"><?= htmlspecialchars($course->title) ?></h6>
                                    <p class="card-text text-muted small mb-2">
                                        <?= htmlspecialchars(substr($course->description ?? '', 0, 80)) ?>...
                                    </p>
                                    <div class="d-flex align-items-center small text-muted">
                                        <i class="bi bi-person me-1"></i> <?= htmlspecialchars(
                                                $course->instructor_name) ?>
                                        <span class="mx-2">|</span>
                                        <i class="bi bi-people me-1"></i> <?= $course->enrollment_count ?? 0 ?>
                                    </div>
                                </div>
                                <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                                    <span class="fw-bold text-primary">
                                        <?= $course->price > 0 ? number_format(
                                                        $course->price, 0, ',', '.') . '₫' : 'Miễn phí' ?>
                                    </span>
                                    <a href="/course/<?= $course->id ?>" class="btn btn-sm btn-outline-primary">
                                        Chi tiết
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($viewModel->totalPages > 1): ?>
                    <nav class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php for ($i = 1; $i <= $viewModel->totalPages; $i++): ?>
                                <li class="page-item <?= $i == $viewModel->currentPage ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>&<?= http_build_query(
                                            array_filter($viewModel->filters)) ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
