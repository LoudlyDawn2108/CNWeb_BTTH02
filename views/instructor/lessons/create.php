<?php
/** @var ViewModels\Instructor\LessonFormViewModel $model */
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold mb-0"><?= htmlspecialchars($model->pageTitle) ?></h4>
                <a href="/instructor/courses/<?= $model->courseId ?>/manage" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Quay lại khóa học
                </a>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-info-circle"></i> Thông tin bài học</h6>
                </div>
                <div class="card-body">
                    <form action="<?= $model->actionUrl ?>" method="POST">

                        <div class="mb-3">
                            <label class="form-label fw-bold">Tên bài học <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" required
                                   value="<?= htmlspecialchars($model->getLessonValue('title')) ?>"
                                   placeholder="Ví dụ: Bài 1 - Giới thiệu">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Link Video (YouTube/Drive)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-youtube"></i></span>
                                <input type="url" name="video_url" class="form-control"
                                       value="<?= htmlspecialchars($model->getLessonValue('video_url')) ?>"
                                       placeholder="https://www.youtube.com/watch?v=...">
                            </div>
                            <div class="form-text">Để trống nếu đây là bài đọc.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nội dung bài học</label>
                            <textarea name="content" class="form-control" rows="6"
                                      placeholder="Nội dung chi tiết, ghi chú cho học viên..."><?= htmlspecialchars($model->getLessonValue('content')) ?></textarea>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Thứ tự hiển thị</label>
                                <input type="number" name="order" class="form-control"
                                       value="<?= htmlspecialchars($model->getLessonValue('order', 0)) ?>">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-save"></i> Lưu bài học
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <?php if ($model->isEditMode()): ?>
                <div class="card shadow-sm border-top-primary">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold text-success"><i class="bi bi-paperclip"></i> Tài liệu đính kèm</h6>
                    </div>
                    <div class="card-body">

                        <form action="/instructor/lessons/<?= $model->getLessonValue('id') ?>/materials/upload"
                              method="POST" enctype="multipart/form-data" class="mb-4">
                            <label class="form-label small text-muted">Tải lên file mới (PDF, Docx, Zip...)</label>
                            <div class="input-group">
                                <input type="file" name="file" class="form-control" required>
                                <button class="btn btn-success" type="submit">
                                    <i class="bi bi-cloud-upload"></i> Tải lên
                                </button>
                            </div>
                        </form>

                        <hr>

                        <h6 class="small fw-bold text-muted mb-3">Danh sách tài liệu (<?= $model->getMaterialsCount() ?>)</h6>

                        <?php if (!$model->hasMaterials()): ?>
                            <div class="text-center text-muted py-3 bg-light rounded small">
                                Chưa có tài liệu nào đính kèm.
                            </div>
                        <?php else: ?>
                            <ul class="list-group">
                                <?php foreach ($model->materials as $file): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center overflow-hidden">
                                            <?php
                                            $icon = 'bi-file-earmark';
                                            if (strpos($file['file_type'], 'pdf')) $icon = 'bi-file-pdf text-danger';
                                            elseif (strpos($file['file_type'], 'doc')) $icon = 'bi-file-word text-primary';
                                            elseif (strpos($file['file_type'], 'zip')) $icon = 'bi-file-zip text-warning';
                                            ?>
                                            <i class="bi <?= $icon ?> fs-4 me-3"></i>

                                            <div class="text-truncate">
                                                <a href="/assets/uploads/materials/<?= $file['file_path'] ?>" target="_blank" class="text-decoration-none fw-bold text-dark">
                                                    <?= htmlspecialchars($file['filename']) ?>
                                                </a>
                                                <div class="small text-muted">Ngày đăng: <?= date('d/m/Y', strtotime($file['uploaded_at'])) ?></div>
                                            </div>
                                        </div>

                                        <form action="/instructor/materials/<?= $file['id'] ?>/delete" method="POST"
                                              onsubmit="return confirm('Xóa file này?');">
                                            <button type="submit" class="btn btn-sm btn-outline-danger border-0">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </form>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-info d-flex align-items-center mt-4">
                    <i class="bi bi-info-circle-fill me-2 fs-4"></i>
                    <div>
                        Bạn cần <strong>Lưu bài học</strong> trước, sau đó mới có thể tải lên tài liệu đính kèm.
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>