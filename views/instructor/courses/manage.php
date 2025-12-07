<?php
/** @var ViewModels\Instructor\CourseManageViewModel $viewModel */
?>

<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-2">
                    <img src="<?= !empty($viewModel->course->image) ? '/assets/uploads/courses/' . $viewModel->course->image : '/assets/img/default-course.png' ?>"
                         class="img-fluid rounded" alt="Thumbnail">
                </div>
                <div class="col-md-7">
                    <h4 class="fw-bold mb-1"><?= htmlspecialchars($viewModel->course->title) ?></h4>
                    <span class="badge bg-<?= $viewModel->course->status == 'approved' ? 'success' : 'warning' ?> mb-2">
                        <?= $viewModel->course->status == 'approved' ? 'Đã duyệt' : 'Chờ duyệt' ?>
                    </span>
                    <p class="text-muted small mb-0">
                        <i class="bi bi-tag"></i> <?= $viewModel->course->price > 0 ? number_format($viewModel->course->price) . 'đ' : 'Miễn phí' ?> |
                        <i class="bi bi-clock"></i> <?= $viewModel->course->duration_weeks ?> tuần
                    </p>
                </div>
                <div class="col-md-3 text-end">
                    <a href="/instructor/courses/<?= $viewModel->course->id ?>/edit" class="btn btn-outline-primary btn-sm mb-2 w-100">
                        <i class="bi bi-pencil"></i> Sửa thông tin
                    </a>

<!--                    <form action="/instructor/courses/--><?php //= $model->course->id ?><!--/update" method="POST">-->
<!--                        <input type="hidden" name="toggle_publish" value="1">-->
<!--                    </form>-->
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold m-0"><i class="bi bi-list-ol"></i> Nội dung khóa học</h5>
                <a href="/instructor/courses/<?= $viewModel->course->id ?>/lessons/create" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg"></i> Thêm bài học
                </a>
            </div>

            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <?php if ($viewModel->lessons->isEmpty()): ?>
                        <div class="text-center py-5">
                            <img src="https://cdni.iconscout.com/illustration/premium/thumb/empty-state-2130362-1800926.png" width="150" alt="Empty">
                            <p class="text-muted mt-2">Chưa có bài học nào.</p>
                            <a href="/instructor/courses/<?= $viewModel->course->id ?>/lessons/create" class="btn btn-outline-primary btn-sm">Tạo bài đầu tiên</a>
                        </div>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($viewModel->lessons as $lesson): ?>
                                <div class="list-group-item p-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                <span class="fw-bold text-secondary"><?= $lesson->order ?></span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-bold"><?= htmlspecialchars($lesson->title) ?></h6>
                                                <small class="text-muted">
                                                    <?php if (!empty($lesson->video_url)): ?>
                                                        <i class="bi bi-play-circle text-primary"></i> Video
                                                    <?php else: ?>
                                                        <i class="bi bi-file-text"></i> Bài đọc
                                                    <?php endif; ?>

                                                    <?php if ($lesson->material_count > 0): ?>
                                                        <span class="ms-2 badge bg-secondary rounded-pill"><?= $lesson->material_count ?> tài liệu</span>
                                                    <?php endif; ?>
                                                </small>
                                            </div>
                                        </div>
                                        <div>
                                            <a href="/instructor/lessons/<?= $lesson->id ?>/edit" class="btn btn-sm btn-light text-primary me-1">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="/instructor/lessons/<?= $lesson->id ?>/delete" method="POST" class="d-inline"
                                                  onsubmit="return confirm('Xóa bài học này?');">
                                                <button type="submit" class="btn btn-sm btn-light text-danger">
                                                    <i class="bi bi-trash"></i>
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
        </div>
    </div>
</div>