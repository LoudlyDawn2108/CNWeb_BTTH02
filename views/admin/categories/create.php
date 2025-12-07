<?php
/**
 * @var ViewModels\AdminCategoryFormViewModel $viewModel
 */
?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="/admin/categories">Danh mục</a></li>
                    <li class="breadcrumb-item active">Thêm mới</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0">Thêm danh mục mới</h1>
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
                    <form method="POST" action="/admin/categories/store" id="categoryForm">
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                Tên danh mục <span class="text-danger">*</span>
                            </label>
                            <input 
                                type="text" 
                                class="form-control <?= isset($_SESSION['errors']['name']) ? 'is-invalid' : '' ?>" 
                                id="name" 
                                name="name" 
                                value="<?= isset($_SESSION['old']['name']) ? htmlspecialchars($_SESSION['old']['name']) : '' ?>"
                                required
                                maxlength="100"
                                placeholder="Ví dụ: Lập trình Web, Khoa học dữ liệu...">
                            <?php if (isset($_SESSION['errors']['name'])): ?>
                                <div class="invalid-feedback">
                                    <?= $_SESSION['errors']['name']; ?>
                                </div>
                            <?php endif; ?>
                            <div class="form-text">
                                Tên danh mục phải rõ ràng và dễ hiểu (tối đa 100 ký tự)
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Mô tả</label>
                            <textarea 
                                class="form-control <?= isset($_SESSION['errors']['description']) ? 'is-invalid' : '' ?>" 
                                id="description" 
                                name="description" 
                                rows="5"
                                maxlength="500"
                                placeholder="Mô tả chi tiết về danh mục này..."><?= isset($_SESSION['old']['description']) ? htmlspecialchars($_SESSION['old']['description']) : '' ?></textarea>
                            <?php if (isset($_SESSION['errors']['description'])): ?>
                                <div class="invalid-feedback">
                                    <?= $_SESSION['errors']['description']; ?>
                                </div>
                            <?php endif; ?>
                            <div class="form-text">
                                Mô tả ngắn gọn về danh mục (tối đa 500 ký tự)
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Tạo danh mục
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
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Hướng dẫn</h5>
                </div>
                <div class="card-body">
                    <h6>Tạo danh mục mới</h6>
                    <ul class="small">
                        <li>Tên danh mục phải là duy nhất</li>
                        <li>Tên nên ngắn gọn và rõ ràng</li>
                        <li>Mô tả giúp người dùng hiểu rõ hơn về danh mục</li>
                        <li>Sau khi tạo, bạn có thể thêm khóa học vào danh mục</li>
                    </ul>

                    <hr>

                    <h6>Ví dụ danh mục:</h6>
                    <div class="small">
                        <strong>Tên:</strong> Lập trình Web<br>
                        <strong>Mô tả:</strong> Các khóa học về phát triển web, bao gồm HTML, CSS, JavaScript, và các framework hiện đại.
                    </div>

                    <hr>

                    <div class="alert alert-info mb-0 small">
                        <i class="bi bi-lightbulb"></i> <strong>Mẹo:</strong> Chọn tên danh mục phổ biến và dễ tìm kiếm để thu hút nhiều học viên hơn.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Clear old input and errors after displaying
unset($_SESSION['old']);
unset($_SESSION['errors']);
?>

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
