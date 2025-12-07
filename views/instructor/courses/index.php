<?php
/** @var ViewModels\Instructor\InstructorDashboardViewModel $model */
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Khóa học của tôi</h2>
            <p class="text-muted mb-0">Quản lý tất cả các khóa học bạn đang giảng dạy</p>
        </div>
        <a href="/instructor/courses/create" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Tạo khóa học mới
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                    <tr>
                        <th class="ps-4">Khóa học</th>
                        <th>Giá</th>
                        <th>Trạng thái</th>
                        <th>Học viên</th>
                        <th>Doanh thu</th>
                        <th class="text-end pe-4">Hành động</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if ($model->courses->isEmpty()): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="80" class="mb-3 opacity-50" alt="Empty">
                                <p class="text-muted">Bạn chưa tạo khóa học nào.</p>
                                <a href="/instructor/courses/create" class="btn btn-sm btn-outline-primary">Bắt đầu ngay</a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($model->courses as $course): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <img src="<?= htmlspecialchars($course->image) ?>"
                                             class="rounded me-3" width="60" height="40" style="object-fit: cover;">
                                        <div>
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($course->title) ?></div>
                                            <small class="text-muted">ID: #<?= $course->id ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td><?= $course->price ?></td>
                                <td>
                                    <span class="badge bg-<?= $course->statusClass ?> bg-opacity-10 text-<?= $course->statusClass ?> px-3 py-2 rounded-pill">
                                        <i class="bi bi-circle-fill small me-1"></i> <?= $course->statusLabel ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-people text-muted me-2"></i>
                                        <?= $course->enrollmentCount ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-success">
                                        <?= $course->revenueFormatted ?>
                                    </div>
                                </td>

                                <td class="text-end pe-4">
                                    <a href="/instructor/courses/<?= $course->id ?>/manage"
                                       class="btn btn-sm btn-info text-white me-1" title="Quản lý bài học">
                                        <i class="bi bi-gear"></i>
                                    </a>

                                    <a href="/instructor/courses/<?= $course->id ?>/edit"
                                       class="btn btn-sm btn-warning text-white me-1" title="Sửa thông tin">
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