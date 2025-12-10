<?php
/** @var ViewModels\Instructor\CourseFormViewModel $viewModel */
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-9 col-lg-8">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-gradient py-3 border-0 rounded-top-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-white">
                            <i class="bi bi-mortarboard-fill me-2"></i><?= htmlspecialchars($viewModel->pageTitle) ?>
                        </h5>
                        <a href="/instructor/dashboard" class="btn btn-light btn-sm rounded-pill px-3 shadow-sm">
                            <i class="bi bi-arrow-left me-1"></i> Quay lại
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
                    
                    <?php if (!$viewModel->modelState->isValid): ?>
                    <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <strong>Vui lòng kiểm tra lại thông tin:</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>
                    
                    <form action="<?= $viewModel->actionUrl ?>" method="POST" enctype="multipart/form-data">

                        <!-- Thông tin cơ bản -->
                        <div class="mb-4">
                            <h6 class="text-muted text-uppercase fw-bold mb-3" style="font-size: 0.875rem; letter-spacing: 0.5px;">
                                <i class="bi bi-info-circle me-2"></i>Thông tin cơ bản
                            </h6>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Tên khóa học <span class="text-danger">*</span></label>
                                <input type="text" 
                                       name="title" 
                                       class="form-control form-control-lg rounded-3 <?= $viewModel->modelState->hasError('title') ? 'is-invalid' : '' ?>"
                                       value="<?= htmlspecialchars($viewModel->getCourseValue('title')) ?>"
                                       placeholder="Ví dụ: Lập trình PHP căn bản">
                                <?php if ($viewModel->modelState->hasError('title')): ?>
                                    <div class="invalid-feedback">
                                        <?= htmlspecialchars($viewModel->modelState->getFirstError('title')) ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="row g-3">   
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Danh mục <span class="text-danger">*</span></label>
                                    <select name="category_id" 
                                            class="form-select rounded-3 <?= $viewModel->modelState->hasError('category_id') ? 'is-invalid' : '' ?>">
                                        <option value="">-- Chọn danh mục --</option>
                                        <?php foreach ($viewModel->getCategoryOptions() as $cat): ?>
                                            <option value="<?= $cat->id ?>" <?= $cat->selected ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($cat->name) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if ($viewModel->modelState->hasError('category_id')): ?>
                                        <div class="invalid-feedback">
                                            <?= htmlspecialchars($viewModel->modelState->getFirstError('category_id')) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Cấp độ</label>
                                    <select name="level" class="form-select rounded-3">
                                        <?php foreach ($viewModel->levels as $level): ?>
                                            <option value="<?= $level ?>"
                                                    <?= $viewModel->getCourseValue('level') == $level ? 'selected' : '' ?>>
                                                <?= $level ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Giá và thời lượng -->
                        <div class="mb-4">
                            <h6 class="text-muted text-uppercase fw-bold mb-3" style="font-size: 0.875rem; letter-spacing: 0.5px;">
                                <i class="bi bi-cash-stack me-2"></i>Giá và thời lượng
                            </h6>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Giá bán (VNĐ)</label>
                                    <div class="input-group input-group-lg">
                                        <input type="number" name="price" class="form-control rounded-start-3" min="0" step="1000"
                                               value="<?= htmlspecialchars($viewModel->getCourseValue('price', 0)) ?>"
                                               placeholder="0">
                                        <span class="input-group-text bg-light fw-semibold rounded-end-3">₫</span>
                                    </div>
                                    <div class="form-text">
                                        <i class="bi bi-info-circle me-1"></i>Nhập 0 để miễn phí
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Thời lượng (Tuần)</label>
                                    <div class="input-group input-group-lg">
                                        <input type="number" name="duration_weeks" class="form-control rounded-start-3" min="1"
                                               value="<?= htmlspecialchars($viewModel->getCourseValue('duration_weeks', 1)) ?>"
                                               placeholder="1">
                                        <span class="input-group-text bg-light fw-semibold rounded-end-3">
                                            <i class="bi bi-calendar-week"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Hình ảnh -->
                        <div class="mb-4">
                            <h6 class="text-muted text-uppercase fw-bold mb-3" style="font-size: 0.875rem; letter-spacing: 0.5px;">
                                <i class="bi bi-image me-2"></i>Hình ảnh khóa học
                            </h6>

                            <label class="form-label fw-semibold">Ảnh bìa khóa học</label>
                            <input type="file" name="image" class="form-control form-control-lg rounded-3" accept="image/*">

                            <?php if ($viewModel->isEditMode() && $viewModel->getCourseValue('image')): ?>
                                <div class="mt-3 p-3 bg-light rounded-3 border">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="/assets/uploads/courses/<?= $viewModel->getCourseValue('image') ?>"
                                             class="img-thumbnail rounded-3 shadow-sm" width="120" alt="Ảnh hiện tại">
                                        <div>
                                            <small class="text-muted d-block fw-semibold">
                                                <i class="bi bi-check-circle-fill text-success me-1"></i>Ảnh hiện tại
                                            </small>
                                            <small class="text-muted">Tải lên ảnh mới để thay thế</small>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <hr class="my-4">

                        <!-- Mô tả -->
                        <div class="mb-4">
                            <h6 class="text-muted text-uppercase fw-bold mb-3" style="font-size: 0.875rem; letter-spacing: 0.5px;">
                                <i class="bi bi-text-paragraph me-2"></i>Nội dung khóa học
                            </h6>


                            <label class="form-label fw-semibold">Mô tả chi tiết <span class="text-danger">*</span></label>
                            <textarea name="description" 
                                      class="form-control rounded-3 <?= $viewModel->modelState->hasError('description') ? 'is-invalid' : '' ?>" 
                                      rows="6"
                                      placeholder="Giới thiệu về nội dung khóa học, những gì học viên sẽ học được..."><?= htmlspecialchars($viewModel->getCourseValue('description')) ?></textarea>
                            <?php if ($viewModel->modelState->hasError('description')): ?>
                                <div class="invalid-feedback">
                                    <?= htmlspecialchars($viewModel->modelState->getFirstError('description')) ?>
                                </div>
                            <?php else: ?>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>Mô tả chi tiết sẽ giúp học viên hiểu rõ hơn về khóa học
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Nút submit -->
                        <div class="d-flex justify-content-end gap-2 pt-3">
                            <a href="/instructor/dashboard" class="btn btn-outline-secondary rounded-pill px-4">
                                <i class="bi bi-x-lg me-1"></i> Hủy bỏ
                            </a>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                                <i class="bi bi-save me-2"></i><?= $viewModel->isEditMode() ? 'Cập nhật khóa học' : 'Tạo khóa học' ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Hover effects */
    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4) !important;
        transition: all 0.3s ease;
    }

    .btn-light:hover {
        background-color: #f8f9fa;
        transform: translateY(-1px);
        transition: all 0.3s ease;
    }

    .card {
        transition: all 0.3s ease;
    }

    .img-thumbnail {
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
    }

    .img-thumbnail:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
</style>