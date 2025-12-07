<?php
/**
 * @var ViewModels\AdminCategoryFormViewModel $viewModel
 */
$category = $viewModel->category;
?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="/admin/categories">Danh mục</a></li>
                    <li class="breadcrumb-item active">Chỉnh sửa</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0">Chỉnh sửa danh mục</h1>
        </div>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="POST" action="/admin/categories/<?= $viewModel->id ?>/update" id="categoryForm">
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                Tên danh mục <span class="text-danger">*</span>
                            </label>
                            <input 
                                type="text" 
                                class="form-control <?= $viewModel->modelState->hasError('name') ? 'is-invalid' : '' ?>" 
                                id="name" 
                                name="name" 
                                value="<?= htmlspecialchars($viewModel->name) ?>"
                                required
                                maxlength="100"
                                placeholder="Ví dụ: Lập trình Web, Khoa học dữ liệu...">
                            <?php if ($viewModel->modelState->hasError('name')): ?>
                                <div class="invalid-feedback">
                                    <?= htmlspecialchars($viewModel->modelState->getFirstError('name')) ?>
                                </div>
                            <?php endif; ?>
                            <div class="form-text">
                                Tên danh mục phải rõ ràng và dễ hiểu (tối đa 100 ký tự)
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Mô tả</label>
                            <textarea 
                                class="form-control <?= $viewModel->modelState->hasError('description') ? 'is-invalid' : '' ?>" 
                                id="description" 
                                name="description" 
                                rows="5"
                                maxlength="500"
                                placeholder="Mô tả chi tiết về danh mục này..."><?= htmlspecialchars($viewModel->description) ?></textarea>
                            <?php if ($viewModel->modelState->hasError('description')): ?>
                                <div class="invalid-feedback">
                                    <?= htmlspecialchars($viewModel->modelState->getFirstError('description')) ?>
                                </div>
                            <?php endif; ?>
                            <div class="form-text">
                                Mô tả ngắn gọn về danh mục (tối đa 500 ký tự)
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Cập nhật
                            </button>
                            <a href="/admin/categories" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Hủy
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Thông tin</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5">ID:</dt>
                        <dd class="col-sm-7"><?= $category['id'] ?? $viewModel->id ?></dd>

                        <dt class="col-sm-5">Số khóa học:</dt>
                        <dd class="col-sm-7">
                            <span class="badge bg-primary"><?= number_format($category['course_count'] ?? 0) ?></span>
                        </dd>

                        <dt class="col-sm-5">Ngày tạo:</dt>
                        <dd class="col-sm-7"><?= isset($category['created_at']) ? date('d/m/Y H:i', strtotime($category['created_at'])) : 'N/A' ?></dd>
                    </dl>

                    <?php if (($category['course_count'] ?? 0) > 0): ?>
                        <hr>
                        <div class="alert alert-warning mb-0 small">
                            <i class="bi bi-exclamation-triangle"></i> Danh mục này đang được sử dụng bởi <strong><?= number_format($category['course_count']) ?></strong> khóa học.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-lightbulb"></i> Hướng dẫn</h5>
                </div>
                <div class="card-body">
                    <h6>Chỉnh sửa danh mục</h6>
                    <ul class="small">
                        <li>Tên danh mục phải là duy nhất</li>
                        <li>Cẩn thận khi đổi tên nếu đã có khóa học</li>
                        <li>Mô tả giúp người dùng hiểu rõ hơn về danh mục</li>
                        <li>Thay đổi sẽ ảnh hưởng đến tất cả khóa học liên quan</li>
                    </ul>

                    <hr>

                    <div class="alert alert-info mb-0 small">
                        <i class="bi bi-info-circle"></i> <strong>Lưu ý:</strong> Thay đổi tên danh mục có thể ảnh hưởng đến SEO và các liên kết hiện có.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<script>
document.getElementById('categoryForm').addEventListener('submit', function(e) {
    const name = document.getElementById('name').value.trim();
    
    if (!name) {
        e.preventDefault();
        alert('Vui lòng nhập tên danh mục');
        document.getElementById('name').focus();
        return false;
    }
    
    if (name.length < 3) {
        e.preventDefault();
        alert('Tên danh mục phải có ít nhất 3 ký tự');
        document.getElementById('name').focus();
        return false;
    }
});

// Character counter for description
const descTextarea = document.getElementById('description');
const maxLength = 500;

descTextarea.addEventListener('input', function() {
    const remaining = maxLength - this.value.length;
    const formText = this.parentElement.querySelector('.form-text');
    
    if (remaining < 50) {
        formText.innerHTML = `Còn lại: ${remaining} ký tự`;
        formText.classList.add('text-warning');
    } else {
        formText.innerHTML = 'Mô tả ngắn gọn về danh mục (tối đa 500 ký tự)';
        formText.classList.remove('text-warning');
    }
});
</script>
