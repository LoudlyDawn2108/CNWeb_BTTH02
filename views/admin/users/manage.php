<?php
/**
 * @var ViewModels\AdminUsersViewModel $viewModel
 */
?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Quản lý người dùng</h1>
                    <p class="text-muted mb-0">Tổng cộng <?= number_format($viewModel->totalUsers) ?> người dùng</p>
                </div>
                <div>
                    <a href="/admin/users/create" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Thêm người dùng
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Tổng người dùng</h6>
                            <h4 class="mb-0"><?= number_format($viewModel->roleStats['total']) ?></h4>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="bi bi-people fs-2 text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Học viên</h6>
                            <h4 class="mb-0"><?= number_format($viewModel->roleStats['students']) ?></h4>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="bi bi-person-badge fs-2 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Giảng viên</h6>
                            <h4 class="mb-0"><?= number_format($viewModel->roleStats['instructors']) ?></h4>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="bi bi-person-workspace fs-2 text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Quản trị viên</h6>
                            <h4 class="mb-0"><?= number_format($viewModel->roleStats['admins']) ?></h4>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="bi bi-shield-check fs-2 text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="/admin/users" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Tìm kiếm</label>
                    <input type="text" name="search" class="form-control" placeholder="Tên, email, username..." value="<?= htmlspecialchars($viewModel->search) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Vai trò</label>
                    <select name="role" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="0" <?= $viewModel->roleFilter === '0' ? 'selected' : '' ?>>Học viên</option>
                        <option value="1" <?= $viewModel->roleFilter === '1' ? 'selected' : '' ?>>Giảng viên</option>
                        <option value="2" <?= $viewModel->roleFilter === '2' ? 'selected' : '' ?>>Admin</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="">Tất cả</option>
                        <option value="1" <?= $viewModel->statusFilter === '1' ? 'selected' : '' ?>>Active</option>
                        <option value="0" <?= $viewModel->statusFilter === '0' ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Lọc
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <?php if (empty($viewModel->users)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-inbox fs-1 text-muted"></i>
                    <p class="text-muted mt-2">Không tìm thấy người dùng nào.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Avatar</th>
                                <th>Username</th>
                                <th>Họ tên</th>
                                <th>Email</th>
                                <th>Vai trò</th>
                                <th>Trạng thái</th>
                                <th>Ngày tạo</th>
                                <th class="text-end">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($viewModel->users as $user): ?>
                                <tr>
                                    <td><?= $user['id'] ?></td>
                                    <td>
                                        <?php if (!empty($user['avatar'])): ?>
                                            <img src="/assets/uploads/avatars/<?= htmlspecialchars($user['avatar']) ?>" 
                                                 class="rounded-circle" 
                                                 width="40" 
                                                 height="40"
                                                 alt="Avatar">
                                        <?php else: ?>
                                            <div class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center text-white" 
                                                 style="width: 40px; height: 40px;">
                                                <?= strtoupper(substr($user['fullname'], 0, 1)) ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($user['username']) ?></td>
                                    <td><?= htmlspecialchars($user['fullname']) ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td>
                                        <?php
                                        $roleBadge = match((int)$user['role']) {
                                            0 => '<span class="badge bg-primary">Học viên</span>',
                                            1 => '<span class="badge bg-info">Giảng viên</span>',
                                            2 => '<span class="badge bg-danger">Admin</span>',
                                            default => '<span class="badge bg-secondary">Unknown</span>'
                                        };
                                        echo $roleBadge;
                                        ?>
                                    </td>
                                    <td>
                                        <?php if ($user['status'] == 1): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($user['created_at'])) ?></td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="/admin/users/<?= $user['id'] ?>/edit" 
                                               class="btn btn-outline-primary" 
                                               title="Chỉnh sửa">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <?php if ($user['status'] == 1): ?>
                                                <button type="button" 
                                                        class="btn btn-outline-warning" 
                                                        onclick="toggleUserStatus(<?= $user['id'] ?>, 0)"
                                                        title="Vô hiệu hóa">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                            <?php else: ?>
                                                <button type="button" 
                                                        class="btn btn-outline-success" 
                                                        onclick="toggleUserStatus(<?= $user['id'] ?>, 1)"
                                                        title="Kích hoạt">
                                                    <i class="bi bi-check-circle"></i>
                                                </button>
                                            <?php endif; ?>
                                            <button type="button" 
                                                    class="btn btn-outline-danger" 
                                                    onclick="deleteUser(<?= $user['id'] ?>)"
                                                    title="Xóa">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($viewModel->totalPages > 1): ?>
                    <nav class="mt-4">
                        <ul class="pagination justify-content-center">
                            <!-- Previous Page -->
                            <li class="page-item <?= $viewModel->currentPage <= 1 ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $viewModel->currentPage - 1 ?>&search=<?= urlencode($viewModel->search) ?>&role=<?= urlencode($viewModel->roleFilter) ?>&status=<?= urlencode($viewModel->statusFilter) ?>">
                                    <i class="bi bi-chevron-left"></i>
                                </a>
                            </li>

                            <?php
                            $startPage = max(1, $viewModel->currentPage - 2);
                            $endPage = min($viewModel->totalPages, $viewModel->currentPage + 2);
                            
                            if ($startPage > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=1&search=<?= urlencode($viewModel->search) ?>&role=<?= urlencode($viewModel->roleFilter) ?>&status=<?= urlencode($viewModel->statusFilter) ?>">1</a>
                                </li>
                                <?php if ($startPage > 2): ?>
                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                <li class="page-item <?= $i == $viewModel->currentPage ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($viewModel->search) ?>&role=<?= urlencode($viewModel->roleFilter) ?>&status=<?= urlencode($viewModel->statusFilter) ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($endPage < $viewModel->totalPages): ?>
                                <?php if ($endPage < $viewModel->totalPages - 1): ?>
                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                <?php endif; ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $viewModel->totalPages ?>&search=<?= urlencode($viewModel->search) ?>&role=<?= urlencode($viewModel->roleFilter) ?>&status=<?= urlencode($viewModel->statusFilter) ?>"><?= $viewModel->totalPages ?></a>
                                </li>
                            <?php endif; ?>

                            <!-- Next Page -->
                            <li class="page-item <?= $viewModel->currentPage >= $viewModel->totalPages ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $viewModel->currentPage + 1 ?>&search=<?= urlencode($viewModel->search) ?>&role=<?= urlencode($viewModel->roleFilter) ?>&status=<?= urlencode($viewModel->statusFilter) ?>">
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<script>
function toggleUserStatus(userId, status) {
    if (confirm(`Bạn có chắc muốn ${status == 1 ? 'kích hoạt' : 'vô hiệu hóa'} người dùng này?`)) {
        fetch(`/admin/users/${userId}/toggle-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Có lỗi xảy ra: ' + data.message);
            }
        })
        .catch(error => {
            alert('Có lỗi xảy ra khi thực hiện thao tác.');
            console.error('Error:', error);
        });
    }
}

function deleteUser(userId) {
    if (confirm('Bạn có chắc muốn xóa người dùng này? Hành động này không thể hoàn tác!')) {
        fetch(`/admin/users/${userId}/delete`, {
            method: 'POST',
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Có lỗi xảy ra: ' + data.message);
            }
        })
        .catch(error => {
            alert('Có lỗi xảy ra khi thực hiện thao tác.');
            console.error('Error:', error);
        });
    }
}
</script>
