<?php
/** @var UploadMaterialsViewModel $viewModel */
use ViewModels\Instructor\UploadMaterialsViewModel;
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-3 col-lg-2">
            <?php include __DIR__ . '/../../layouts/sidebar.php'; ?>
        </div>
        <div class="col-md-9 col-lg-10">
            <h2 class="mb-4"><i class="bi bi-file-earmark-arrow-up"></i> Tải tài liệu - <?= htmlspecialchars($viewModel->course['title']) ?></h2>

            <?php if (empty($viewModel->lessons)): ?>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i> Khóa học chưa có bài học.
                    <a href="/instructor/course/<?= $viewModel->course['id'] ?>/lessons/create">Tạo bài học trước</a>.
                </div>
            <?php else: ?>
                <div class="card">
                    <div class="card-body">
                        <p class="text-muted">Chọn bài học và tải lên tài liệu học tập (PDF, DOC, PPTX, XLS, ZIP...)</p>

                        <?php foreach ($viewModel->lessons as $index => $lesson): ?>
                            <div class="card mb-3">
                                <div class="card-header">
                                    <strong><span class="badge bg-secondary me-2"><?= $index + 1 ?></span><?= htmlspecialchars($lesson['title']) ?></strong>
                                </div>
                                <div class="card-body">
                                    <form action="/instructor/lesson/<?= $lesson['id'] ?>/materials/upload" method="POST" enctype="multipart/form-data">
                                        <div class="row align-items-end">
                                            <div class="col-md-8 mb-2">
                                                <input type="file" class="form-control" name="material" required>
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <button type="submit" class="btn btn-primary w-100">
                                                    <i class="bi bi-upload"></i> Tải lên
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>