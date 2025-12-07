<?php
/** @var ViewModels\Instructor\InstructorDashboardViewModel $viewModel */
?>

<div class="row">
    <div class="col-md-12 p-4">
        <h2 class="mb-4"><i class="bi bi-speedometer2"></i> Dashboard Giảng Viên</h2>

        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Tổng khóa học</h6>
                                <h2 class="fw-bold mb-0"><?= $viewModel->totalCourses ?></h2>
                            </div>
                            <i class="bi bi-book fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Tổng học viên</h6>
                                <h2 class="fw-bold mb-0"><?= $viewModel->totalStudents ?></h2>
                            </div>
                            <i class="bi bi-people fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">Thu nhập (Tạm tính)</h6>
                                <h2 class="fw-bold mb-0"> <?= $viewModel->totalRevenue ?> </h2> </div>
                            <i class="bi bi-cash-coin fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Khóa học của tôi</h5>
                <a href="/instructor/courses/create" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg"></i> Tạo mới
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">Ảnh</th>
                            <th>Tên khóa học</th>
                            <th>Giá</th>
                            <th>Trạng thái</th>
                            <th>Học viên</th>
                            <th>Doanh thu</th>
                            <th class="text-end">Hành động</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if ($viewModel->courses->isEmpty()): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    Bạn chưa có khóa học nào. Hãy tạo khóa học đầu tiên!
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($viewModel->courses as $course): ?>
                                <tr>
                                    <td>
                                        <img src="<?= htmlspecialchars($course->image) ?>"
                                             class="rounded" width="40" height="40" style="object-fit: cover;">
                                    </td>
                                    <td>
                                        <div class="fw-bold"><?= htmlspecialchars($course->title) ?></div>
                                    </td>
                                    <td><?= $course->price ?></td>
                                    <td>
                                        <span class="badge bg-<?= $course->statusClass ?>">
                                            <?= $course->statusLabel ?>
                                        </span>
                                    </td>
                                    <td><?= $course->enrollmentCount ?></td>
                                    <td>
                                        <div class="fw-bold text-success">
                                            <?= $course->revenueFormatted ?>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <a href="/instructor/courses/<?= $course->id ?>/manage"
                                           class="btn btn-sm btn-info text-white" title="Quản lý bài học">
                                            <i class="bi bi-gear"></i>
                                        </a>
                                        <a href="/instructor/courses/<?= $course->id ?>/edit"
                                           class="btn btn-sm btn-warning text-white" title="Sửa thông tin">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="/instructor/courses/<?= $course->id ?>/delete"
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Bạn có chắc muốn xóa khóa học này? Hành động này không thể hoàn tác!');">
                                            <button type="submit" class="btn btn-sm btn-danger" title="Xóa">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>