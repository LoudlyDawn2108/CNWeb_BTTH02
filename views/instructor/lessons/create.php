<?php
/** @var ViewModels\Instructor\LessonFormViewModel $viewModel */
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-9">

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold mb-1">
                        <i class="bi bi-journal-text text-primary me-2"></i><?= htmlspecialchars($viewModel->pageTitle) ?>
                    </h4>
                    <p class="text-muted mb-0 small">Tạo và quản lý nội dung bài học</p>
                </div>
                <a href="/instructor/courses/<?= $viewModel->courseId ?>/manage" class="btn btn-light rounded-pill px-3 shadow-sm">
                    <i class="bi bi-arrow-left me-1"></i> Quay lại
                </a>
            </div>

            <!-- Form thông tin bài học -->
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header py-3 border-0 rounded-top-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h6 class="mb-0 fw-bold text-white">
                        <i class="bi bi-pencil-square me-2"></i>Thông tin bài học
                    </h6>
                </div>
                <div class="card-body p-4">
                    
                    <?php if (!$viewModel->modelState->isValid): ?>
                    <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <strong>Vui lòng kiểm tra lại thông tin:</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>
                    
                    <form action="<?= $viewModel->actionUrl ?>" method="POST">

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Tên bài học <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="title" 
                                   class="form-control form-control-lg rounded-3 <?= $viewModel->modelState->hasError('title') ? 'is-invalid' : '' ?>"
                                   value="<?= htmlspecialchars($viewModel->getLessonValue('title')) ?>"
                                   placeholder="Ví dụ: Bài 1 - Giới thiệu về PHP">
                            <?php if ($viewModel->modelState->hasError('title')): ?>
                                <div class="invalid-feedback">
                                    <?= htmlspecialchars($viewModel->modelState->getFirstError('title')) ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-play-circle me-1"></i>Link Video (YouTube/Drive)
                            </label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-light rounded-start-3">
                                    <i class="bi bi-youtube text-danger"></i>
                                </span>
                                <input type="url" name="video_url" class="form-control rounded-end-3"
                                       value="<?= htmlspecialchars($viewModel->getLessonValue('video_url')) ?>"
                                       placeholder="https://www.youtube.com/watch?v=...">
                            </div>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>Để trống nếu đây là bài đọc không có video
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-text-paragraph me-1"></i>Nội dung bài học
                            </label>
                            <textarea name="content" class="form-control rounded-3" rows="8"
                                      placeholder="Nội dung chi tiết, ghi chú, hướng dẫn cho học viên..."><?= htmlspecialchars($viewModel->getLessonValue('content')) ?></textarea>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>Nội dung sẽ hiển thị dưới video để hỗ trợ học viên
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-sort-numeric-down me-1"></i>Thứ tự hiển thị
                                </label>
                                <input type="number" name="order" class="form-control form-control-lg rounded-3" min="0"
                                       value="<?= htmlspecialchars($viewModel->getLessonValue('order', 0)) ?>"
                                       placeholder="0">
                                <div class="form-text">Số thứ tự trong khóa học</div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                            <a href="/instructor/courses/<?= $viewModel->courseId ?>/manage" class="btn btn-outline-secondary rounded-pill px-4">
                                <i class="bi bi-x-lg me-1"></i> Hủy bỏ
                            </a>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                                <i class="bi bi-save me-2"></i>Lưu bài học
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Phần tài liệu đính kèm -->
            <?php if ($viewModel->isEditMode()): ?>
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-header py-3 border-0 rounded-top-3" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold text-white">
                                <i class="bi bi-paperclip me-2"></i>Tài liệu đính kèm
                            </h6>
                            <span class="badge bg-white text-success fw-semibold px-3 py-2 rounded-pill">
                                <?= $viewModel->getMaterialsCount() ?> tài liệu
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-4">

                        <!-- Form upload -->
                        <form action="/instructor/lessons/<?= $viewModel->getLessonValue('id') ?>/materials/upload"
                              method="POST" enctype="multipart/form-data" class="mb-4">
                            <label class="form-label fw-semibold mb-3">
                                <i class="bi bi-cloud-upload me-1"></i>Tải lên tài liệu mới
                            </label>
                            <div class="upload-area p-4 border-2 border-dashed rounded-3 bg-light">
                                <div class="input-group input-group-lg">
                                    <input type="file" name="file" class="form-control rounded-start-3" required
                                           accept=".pdf,.doc,.docx,.zip,.rar,.ppt,.pptx,.xls,.xlsx">
                                    <button class="btn btn-success rounded-end-3 px-4" type="submit">
                                        <i class="bi bi-cloud-upload me-2"></i>Tải lên
                                    </button>
                                </div>
                                <div class="form-text mt-2">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Hỗ trợ: PDF, Word, PowerPoint, Excel, ZIP (Tối đa 50MB)
                                </div>
                            </div>
                        </form>

                        <hr class="my-4">

                        <!-- Danh sách tài liệu -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-semibold mb-0">
                                <i class="bi bi-folder2-open me-2 text-primary"></i>Danh sách tài liệu
                            </h6>
                        </div>

                        <?php if (!$viewModel->hasMaterials()): ?>
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                                </div>
                                <p class="text-muted mb-0">Chưa có tài liệu nào được tải lên</p>
                                <small class="text-muted">Tải lên tài liệu để hỗ trợ học viên học tập tốt hơn</small>
                            </div>
                        <?php else: ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($viewModel->materials as $file): ?>
                                    <div class="list-group-item border rounded-3 mb-2 p-3 material-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center flex-grow-1 overflow-hidden me-3">
                                                <!-- Icon theo loại file -->
                                                <div class="file-icon-wrapper me-3">
                                                    <?php
                                                    $iconClass = 'bi-file-earmark';
                                                    $iconColor = 'text-secondary';
                                                    $bgColor = 'bg-light';

                                                    if (strpos($file->file_type, 'pdf')) {
                                                        $iconClass = 'bi-file-earmark-pdf-fill';
                                                        $iconColor = 'text-danger';
                                                        $bgColor = 'bg-danger-subtle';
                                                    } elseif (strpos($file->file_type, 'doc')) {
                                                        $iconClass = 'bi-file-earmark-word-fill';
                                                        $iconColor = 'text-primary';
                                                        $bgColor = 'bg-primary-subtle';
                                                    } elseif (strpos($file->file_type, 'sheet') || strpos($file->file_type, 'excel')) {
                                                        $iconClass = 'bi-file-earmark-excel-fill';
                                                        $iconColor = 'text-success';
                                                        $bgColor = 'bg-success-subtle';
                                                    } elseif (strpos($file->file_type, 'presentation') || strpos($file->file_type, 'powerpoint')) {
                                                        $iconClass = 'bi-file-earmark-ppt-fill';
                                                        $iconColor = 'text-warning';
                                                        $bgColor = 'bg-warning-subtle';
                                                    } elseif (strpos($file->file_type, 'zip') || strpos($file->file_type, 'rar')) {
                                                        $iconClass = 'bi-file-earmark-zip-fill';
                                                        $iconColor = 'text-dark';
                                                        $bgColor = 'bg-secondary-subtle';
                                                    }
                                                    ?>
                                                    <div class="icon-badge <?= $bgColor ?> rounded-3 p-3 d-flex align-items-center justify-content-center">
                                                        <i class="bi <?= $iconClass ?> <?= $iconColor ?>" style="font-size: 1.75rem;"></i>
                                                    </div>
                                                </div>

                                                <!-- Thông tin file -->
                                                <div class="flex-grow-1 overflow-hidden">
                                                    <a href="/assets/uploads/materials/<?= $file->file_path ?>"
                                                       target="_blank"
                                                       class="text-decoration-none fw-semibold text-dark d-block text-truncate file-link">
                                                        <?= htmlspecialchars($file->filename) ?>
                                                    </a>
                                                    <div class="small text-muted mt-1">
                                                        <i class="bi bi-calendar3 me-1"></i>
                                                        <?= date('d/m/Y H:i', strtotime($file->uploaded_at)) ?>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Nút hành động -->
                                            <div class="d-flex gap-2">
                                                <a href="/assets/uploads/materials/<?= $file->file_path ?>"
                                                   target="_blank"
                                                   class="btn btn-sm btn-outline-primary rounded-pill px-3"
                                                   title="Xem file">
                                                    <i class="bi bi-eye me-1"></i>Xem
                                                </a>
                                                <form action="/instructor/materials/<?= $file->id ?>/delete"
                                                      method="POST"
                                                      onsubmit="return confirm('⚠️ Bạn có chắc chắn muốn xóa tài liệu này?\n\nFile: <?= htmlspecialchars($file->filename) ?>');"
                                                      class="d-inline">
                                                    <button type="submit"
                                                            class="btn btn-sm btn-outline-danger rounded-pill px-3"
                                                            title="Xóa file">
                                                        <i class="bi bi-trash me-1"></i>Xóa
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-info border-0 rounded-3 shadow-sm d-flex align-items-start">
                    <div class="flex-shrink-0">
                        <i class="bi bi-info-circle-fill fs-3 me-3"></i>
                    </div>
                    <div>
                        <h6 class="alert-heading fw-bold mb-2">Lưu bài học trước khi tải tài liệu</h6>
                        <p class="mb-0">
                            Bạn cần <strong>lưu bài học</strong> này trước, sau đó mới có thể tải lên tài liệu đính kèm để hỗ trợ học viên.
                        </p>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<style>
    /* Hover effects cho form controls */
    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    /* Button hover effects */
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4) !important;
        transition: all 0.3s ease;
    }

    .btn-light:hover {
        background-color: #f8f9fa;
        transform: translateY(-1px);
        transition: all 0.3s ease;
    }

    /* Material item hover effect */
    .material-item {
        transition: all 0.3s ease;
        border: 1px solid #e9ecef !important;
    }

    .material-item:hover {
        background-color: #f8f9fc;
        border-color: #667eea !important;
        transform: translateX(5px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    /* File link hover */
    .file-link:hover {
        color: #667eea !important;
        transition: color 0.3s ease;
    }

    /* Icon badge animation */
    .icon-badge {
        transition: all 0.3s ease;
    }

    .material-item:hover .icon-badge {
        transform: scale(1.1);
    }

    /* Upload area hover */
    .upload-area {
        transition: all 0.3s ease;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }

    .upload-area:hover {
        border-color: #667eea !important;
        background: linear-gradient(135deg, #f0f1ff 0%, #e8ebff 100%);
    }

    /* Button action hover */
    .btn-outline-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(102, 126, 234, 0.3);
    }

    .btn-outline-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
    }

    /* Smooth transitions */
    * {
        transition: all 0.2s ease;
    }

    /* Alert animation */
    .alert {
        animation: slideIn 0.5s ease-out;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>