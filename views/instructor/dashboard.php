<?php
/** @var ViewModels\Instructor\InstructorDashboardViewModel $viewModel */
?>

<style>
    .stat-card {
        transition: all 0.3s ease;
        border: none;
        border-radius: 12px;
        overflow: hidden;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
    }

    .stat-card .card-body {
        padding: 1.5rem;
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255,255,255,0.2);
    }

    .course-table-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    }

    .course-table-card .card-header {
        border-bottom: 2px solid #f0f0f0;
        border-radius: 12px 12px 0 0 !important;
    }

    .table-hover tbody tr {
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
        transform: scale(1.01);
    }

    .table-hover tbody tr.empty-row {
        cursor: default;
    }

    .table-hover tbody tr.empty-row:hover {
        background-color: transparent;
        transform: none;
    }

    .course-img {
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transition: transform 0.2s ease;
    }

    .course-img:hover {
        transform: scale(1.1);
    }

    .action-btn {
        transition: all 0.2s ease;
    }

    .action-btn:hover {
        transform: translateY(-2px);
    }

    .badge {
        padding: 0.4rem 0.8rem;
        font-weight: 500;
        border-radius: 6px;
    }

    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        border-radius: 12px;
        margin-bottom: 2rem;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    .empty-state {
        padding: 3rem 1rem;
        text-align: center;
    }

    .empty-state i {
        font-size: 4rem;
        color: #dee2e6;
        margin-bottom: 1rem;
    }

    .pagination {
        gap: 0.25rem;
    }

    .page-link {
        border-radius: 6px;
        border: 1px solid #dee2e6;
        color: #667eea;
        transition: all 0.2s ease;
    }

    .page-link:hover {
        background-color: #667eea;
        border-color: #667eea;
        color: white;
        transform: translateY(-2px);
    }

    .page-item.active .page-link {
        background-color: #667eea;
        border-color: #667eea;
    }

    .page-item.disabled .page-link {
        background-color: #f8f9fa;
        border-color: #dee2e6;
        cursor: not-allowed;
    }

    .card-footer {
        border-radius: 0 0 12px 12px;
    }
</style>

<div class="row">
    <div class="col-md-12 p-4">
        <!-- Header -->
        <div class="page-header">
            <h2 class="mb-2">
                <i class="bi bi-speedometer2"></i> Dashboard Giảng Viên
            </h2>
            <p class="mb-0 opacity-75">Quản lý khóa học và theo dõi hiệu suất của bạn</p>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card stat-card bg-primary text-white h-100 shadow">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title opacity-75 mb-2">Tổng khóa học</h6>
                                <h2 class="fw-bold mb-0"><?= $viewModel->totalCourses ?></h2>
                                <small class="opacity-75">Khóa học đang hoạt động</small>
                            </div>
                            <div class="stat-icon">
                                <i class="bi bi-book fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card stat-card bg-success text-white h-100 shadow">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title opacity-75 mb-2">Tổng học viên</h6>
                                <h2 class="fw-bold mb-0"><?= $viewModel->totalStudents ?></h2>
                                <small class="opacity-75">Học viên đã đăng ký</small>
                            </div>
                            <div class="stat-icon">
                                <i class="bi bi-people fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card stat-card bg-info text-white h-100 shadow">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title opacity-75 mb-2">Thu nhập</h6>
                                <h2 class="fw-bold mb-0"><?= $viewModel->totalRevenue ?></h2>
                                <small class="opacity-75">Tạm tính</small>
                            </div>
                            <div class="stat-icon">
                                <i class="bi bi-cash-coin fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Courses Table -->
        <div class="card course-table-card">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">
                            <i class="bi bi-mortarboard-fill text-primary"></i> Khóa học của tôi
                        </h5>
                        <small class="text-muted">Quản lý và cập nhật khóa học của bạn</small>
                    </div>
                    <a href="/instructor/courses/create" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Tạo khóa học mới
                    </a>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                        <tr>
                            <th style="width: 70px;" class="ps-4">Ảnh</th>
                            <th>Tên khóa học</th>
                            <th style="width: 120px;">Giá</th>
                            <th style="width: 120px;">Trạng thái</th>
                            <th style="width: 100px;" class="text-center">Học viên</th>
                            <th style="width: 140px;">Doanh thu</th>
                            <th style="width: 180px;" class="text-end pe-4">Hành động</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if ($viewModel->courses->isEmpty()): ?>
                            <tr class="empty-row">
                                <td colspan="7" class="border-0">
                                    <div class="empty-state">
                                        <i class="bi bi-inbox"></i>
                                        <h5 class="text-muted mb-2">Chưa có khóa học nào</h5>
                                        <p class="text-muted mb-3">Bắt đầu tạo khóa học đầu tiên để chia sẻ kiến thức của bạn!</p>
                                        <a href="/instructor/courses/create" class="btn btn-primary">
                                            <i class="bi bi-plus-lg"></i> Tạo khóa học đầu tiên
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($viewModel->courses as $course): ?>
                                <tr onclick="window.location.href='/instructor/courses/<?= $course->id ?>/manage'" style="cursor: pointer;">
                                    <td class="ps-4" onclick="event.stopPropagation();">
                                        <img src="<?= htmlspecialchars($course->image) ?>"
                                             class="course-img"
                                             width="50"
                                             height="50"
                                             style="object-fit: cover;"
                                             alt="<?= htmlspecialchars($course->title) ?>">
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($course->title) ?></div>
                                    </td>
                                    <td>
                                        <span class="fw-semibold text-primary"><?= $course->price ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $course->statusClass ?>">
                                            <?= $course->statusLabel ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border">
                                            <i class="bi bi-people-fill"></i> <?= $course->enrollmentCount ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-success">
                                            <i class="bi bi-currency-dollar"></i> <?= $course->revenueFormatted ?>
                                        </div>
                                    </td>
                                    <td class="text-end pe-4" onclick="event.stopPropagation();">
                                        <div class="btn-group" role="group">
                                            <a href="/instructor/courses/<?= $course->id ?>/manage"
                                               class="btn btn-sm btn-info text-white action-btn"
                                               title="Quản lý bài học"
                                               data-bs-toggle="tooltip">
                                                <i class="bi bi-gear-fill"></i>
                                            </a>
                                            <a href="/instructor/courses/<?= $course->id ?>/edit"
                                               class="btn btn-sm btn-warning text-white action-btn"
                                               title="Sửa thông tin"
                                               data-bs-toggle="tooltip">
                                                <i class="bi bi-pencil-fill"></i>
                                            </a>
                                        </div>
                                        <form action="/instructor/courses/<?= $course->id ?>/delete"
                                              method="POST"
                                              class="d-inline delete-form">

                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="Xóa">
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

            <!-- Pagination -->
            <?php if (!$viewModel->courses->isEmpty() && isset($viewModel->pagination)): ?>
                <div class="card-footer bg-white border-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Hiển thị <strong><?= $viewModel->pagination->from ?></strong>
                            đến <strong><?= $viewModel->pagination->to ?></strong>
                            trong tổng số <strong><?= $viewModel->pagination->total ?></strong> khóa học
                        </div>

                        <?php if ($viewModel->pagination->lastPage > 1): ?>
                            <nav aria-label="Phân trang khóa học">
                                <ul class="pagination mb-0">
                                    <!-- Previous -->
                                    <li class="page-item <?= $viewModel->pagination->currentPage == 1 ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?page=<?= $viewModel->pagination->currentPage - 1 ?>" aria-label="Trang trước">
                                            <i class="bi bi-chevron-left"></i>
                                        </a>
                                    </li>

                                    <!-- First Page -->
                                    <?php if ($viewModel->pagination->currentPage > 3): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=1">1</a>
                                        </li>
                                        <?php if ($viewModel->pagination->currentPage > 4): ?>
                                            <li class="page-item disabled">
                                                <span class="page-link">...</span>
                                            </li>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <!-- Page Numbers -->
                                    <?php
                                    $start = max(1, $viewModel->pagination->currentPage - 2);
                                    $end = min($viewModel->pagination->lastPage, $viewModel->pagination->currentPage + 2);
                                    for ($i = $start; $i <= $end; $i++):
                                        ?>
                                        <li class="page-item <?= $i == $viewModel->pagination->currentPage ? 'active' : '' ?>">
                                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                        </li>
                                    <?php endfor; ?>

                                    <!-- Last Page -->
                                    <?php if ($viewModel->pagination->currentPage < $viewModel->pagination->lastPage - 2): ?>
                                        <?php if ($viewModel->pagination->currentPage < $viewModel->pagination->lastPage - 3): ?>
                                            <li class="page-item disabled">
                                                <span class="page-link">...</span>
                                            </li>
                                        <?php endif; ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?= $viewModel->pagination->lastPage ?>"><?= $viewModel->pagination->lastPage ?></a>
                                        </li>
                                    <?php endif; ?>

                                    <!-- Next -->
                                    <li class="page-item <?= $viewModel->pagination->currentPage == $viewModel->pagination->lastPage ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?page=<?= $viewModel->pagination->currentPage + 1 ?>" aria-label="Trang sau">
                                            <i class="bi bi-chevron-right"></i>
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</div>

<script>
    // Enable Bootstrap tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>

<script>
    // Tìm tất cả các form có class 'delete-form'
    const deleteForms = document.querySelectorAll('.delete-form');

    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            // 1. Ngăn không cho form gửi đi ngay lập tức
            e.preventDefault();

            // 2. Hiện thông báo đẹp bằng SweetAlert2
            Swal.fire({
                title: 'Bạn có chắc chắn?',
                text: "Hành động này sẽ xóa bài học và không thể khôi phục!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',   // Màu đỏ cho nút Xóa
                cancelButtonColor: '#3085d6', // Màu xanh cho nút Hủy
                confirmButtonText: 'Vâng, xóa nó!',
                cancelButtonText: 'Hủy bỏ'
            }).then((result) => {
                // 3. Nếu người dùng bấm nút "Vâng, xóa nó!"
                if (result.isConfirmed) {
                    // Thì mới cho phép form gửi đi
                    this.submit();
                }
            });
        });
    });
</script>