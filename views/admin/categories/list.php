<?php
/**
 * @var ViewModels\AdminCategoriesViewModel $viewModel
 */
?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Quản lý danh mục</h1>
                    <p class="text-muted mb-0">Tổng cộng <?= number_format($viewModel->stats['total_categories']) ?> danh mục</p>
                </div>
                <div>
                    <a href="/admin/categories/create" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Thêm danh mục
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
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Tổng danh mục</h6>
                            <h3 class="mb-0"><?= number_format($viewModel->stats['total_categories']) ?></h3>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="bi bi-folder fs-1 text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="text-muted mb-1">Tổng khóa học</h6>
                            <h3 class="mb-0"><?= number_format($viewModel->stats['total_courses']) ?></h3>
                        </div>
                        <div class="flex-shrink-0">
                            <i class="bi bi-book fs-1 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="/admin/categories" class="row g-3">
                <div class="col-md-10">
                    <label class="form-label">Tìm kiếm</label>
                    <input type="text" name="search" class="form-control" placeholder="Tên danh mục, mô tả..." value="<?= htmlspecialchars($viewModel->search) ?>">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Tìm kiếm
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Categories Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <?php if (empty($viewModel->categories)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-inbox fs-1 text-muted"></i>
                    <p class="text-muted mt-2">Không tìm thấy danh mục nào.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 80px;">ID</th>
                                <th>Tên danh mục</th>
                                <th>Mô tả</th>
                                <th style="width: 150px;" class="text-center">Số khóa học</th>
                                <th style="width: 150px;">Ngày tạo</th>
                                <th style="width: 180px;" class="text-end">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($viewModel->categories as $category): ?>
                                <tr>
                                    <td><?= $category['id'] ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($category['name']) ?></strong>
                                    </td>
                                    <td>
                                        <?php if (!empty($category['description'])): ?>
                                            <span class="text-muted"><?= htmlspecialchars(mb_substr($category['description'], 0, 100)) ?><?= mb_strlen($category['description']) > 100 ? '...' : '' ?></span>
                                        <?php else: ?>
                                            <span class="text-muted fst-italic">Chưa có mô tả</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary rounded-pill"><?= number_format($category['course_count'] ?? 0) ?></span>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($category['created_at'])) ?></td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="/admin/categories/<?= $category['id'] ?>" 
                                               class="btn btn-outline-info" 
                                               title="Xem chi tiết">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="/admin/categories/<?= $category['id'] ?>/edit" 
                                               class="btn btn-outline-primary" 
                                               title="Chỉnh sửa">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-outline-danger" 
                                                    onclick="deleteCategory(<?= $category['id'] ?>, '<?= htmlspecialchars(addslashes($category['name'])) ?>', <?= $category['course_count'] ?? 0 ?>)"
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
                                <a class="page-link" href="?page=<?= $viewModel->currentPage - 1 ?>&search=<?= urlencode($viewModel->search) ?>">
                                    <i class="bi bi-chevron-left"></i>
                                </a>
                            </li>

                            <?php
                            $startPage = max(1, $viewModel->currentPage - 2);
                            $endPage = min($viewModel->totalPages, $viewModel->currentPage + 2);
                            
                            if ($startPage > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=1&search=<?= urlencode($viewModel->search) ?>">1</a>
                                </li>
                                <?php if ($startPage > 2): ?>
                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                <li class="page-item <?= $i == $viewModel->currentPage ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($viewModel->search) ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($endPage < $viewModel->totalPages): ?>
                                <?php if ($endPage < $viewModel->totalPages - 1): ?>
                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                <?php endif; ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $viewModel->totalPages ?>&search=<?= urlencode($viewModel->search) ?>"><?= $viewModel->totalPages ?></a>
                                </li>
                            <?php endif; ?>

                            <!-- Next Page -->
                            <li class="page-item <?= $viewModel->currentPage >= $viewModel->totalPages ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $viewModel->currentPage + 1 ?>&search=<?= urlencode($viewModel->search) ?>">
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
function deleteCategory(categoryId, categoryName, courseCount) {
    if (courseCount > 0) {
        alert(`Không thể xóa danh mục "${categoryName}" vì có ${courseCount} khóa học đang sử dụng.\n\nVui lòng di chuyển hoặc xóa các khóa học trước.`);
        return;
    }
    
    if (confirm(`Bạn có chắc muốn xóa danh mục "${categoryName}"?\n\nHành động này không thể hoàn tác!`)) {
        fetch(`/admin/categories/${categoryId}/delete`, {
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
