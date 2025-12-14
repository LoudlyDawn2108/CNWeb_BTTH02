<?php
/** @var LessonViewModel $viewModel */
use ViewModels\LessonViewModel;
?>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-3 col-lg-2">
            <?php include __DIR__ . '/../layouts/sidebar.php'; ?>
        </div>
        <div class="col-md-9 col-lg-10">
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/student/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="/student/course/<?= $viewModel->course->id ?>/progress"><?= htmlspecialchars($viewModel->course->title) ?></a></li>
                    <li class="breadcrumb-item active"><?= htmlspecialchars($viewModel->lesson->title) ?></li>
                </ol>
            </nav>

            <div class="row">
                <!-- Main Content -->
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h4 class="mb-0"><?= htmlspecialchars($viewModel->lesson->title) ?></h4>
                        </div>
                        <div class="card-body">
                            <?php if ($viewModel->lesson->video_url): ?>
                                <div class="ratio ratio-16x9 mb-4">
                                    <?php
                                    $videoUrl = $viewModel->lesson->video_url;
                                    // Convert YouTube URL to embed
                                    if (strpos($videoUrl, 'youtube.com') !== false || strpos($videoUrl, 'youtu.be') !== false) {
                                        preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $videoUrl, $matches);
                                        if (isset($matches[1])) {
                                            $videoUrl = 'https://www.youtube.com/embed/' . $matches[1];
                                        }
                                    }
                                    ?>
                                    <iframe src="<?= htmlspecialchars($videoUrl) ?>"
                                            allowfullscreen class="rounded"></iframe>
                                </div>
                            <?php endif; ?>

                            <div class="lesson-content">
                                <?= nl2br(htmlspecialchars($viewModel->lesson->content)) ?>
                            </div>
                        </div>
                    </div>

                    <!-- Materials -->
                    <?php if (!empty($viewModel->materials)): ?>
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="bi bi-file-earmark-text"></i> Tài liệu bài học</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <?php foreach ($viewModel->materials as $material): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                            <div>
                                                <i class="bi bi-file-earmark-<?= $material->file_type === 'pdf' ? 'pdf text-danger' : 'text text-primary' ?> me-2"></i>
                                                <?= htmlspecialchars($material->filename) ?>
                                            </div>
                                            <a href="/<?= htmlspecialchars($material->file_path) ?>"
                                               class="btn btn-sm btn-outline-primary" download>
                                                <i class="bi bi-download"></i> Tải xuống
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Navigation -->
                    <div class="d-flex justify-content-between mb-4">
                        <?php if ($viewModel->prevLesson): ?>
                            <a href="/student/lesson/<?= $viewModel->prevLesson->id ?>" class="btn btn-outline-primary">
                                <i class="bi bi-chevron-left"></i> Bài trước: <?= htmlspecialchars(substr($viewModel->prevLesson->title, 0, 20)) ?>...
                            </a>
                        <?php else: ?>
                            <span></span>
                        <?php endif; ?>

                        <?php if ($viewModel->nextLesson): ?>
                            <a href="/student/lesson/<?= $viewModel->nextLesson->id ?>" class="btn btn-primary">
                                Bài tiếp: <?= htmlspecialchars(substr($viewModel->nextLesson->title, 0, 20)) ?>... <i class="bi bi-chevron-right"></i>
                            </a>
                        <?php else: ?>
                            <a href="/student/course/<?= $viewModel->course->id ?>/progress" class="btn btn-success">
                                <i class="bi bi-check-circle"></i> Hoàn thành khóa học
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Sidebar - Lesson List -->
                <div class="col-lg-4">
                    <div class="card sticky-top" style="top: 80px;">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-list-ol"></i> Danh sách bài học</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush" style="max-height: 400px; overflow-y: auto;">
                                <?php foreach ($viewModel->lessons as $index => $l): ?>
                                    <a href="/student/lesson/<?= $l->id ?>"
                                       class="list-group-item list-group-item-action <?= $l->id == $viewModel->lesson->id ? 'active' : '' ?>">
                                        <span class="badge bg-<?= $l->id == $viewModel->lesson->id ? 'light text-dark' : 'secondary' ?> me-2">
                                            <?= $index + 1 ?>
                                        </span>
                                        <?= htmlspecialchars($l->title) ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <small>Tiến độ: <?= $viewModel->enrollment->progress ?>%</small>
                                <div class="progress flex-grow-1 mx-2" style="height: 8px;">
                                    <div class="progress-bar" style="width: <?= $viewModel->enrollment->progress ?>%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>