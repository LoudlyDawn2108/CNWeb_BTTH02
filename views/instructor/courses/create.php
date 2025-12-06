<?php
/** @var ViewModels\Instructor\CourseFormViewModel $model */
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold"><?= htmlspecialchars($model->title) ?></h5>
                        <a href="/instructor/dashboard" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="<?= $model->actionUrl ?>" method="POST" enctype="multipart/form-data">

                        <div class="mb-3">
                            <label class="form-label fw-bold">Tên khóa học <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" required
                                   value="<?= htmlspecialchars($model->getCourseValue('title')) ?>"
                                   placeholder="Ví dụ: Lập trình PHP căn bản">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Danh mục <span class="text-danger">*</span></label>
                                <select name="category_id" class="form-select" required>
                                    <option value="">-- Chọn danh mục --</option>
                                    <?php foreach ($model->getCategoryOptions() as $cat): ?>
                                        <option value="<?= $cat->id ?>" <?= $cat->selected ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cat->name) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Cấp độ</label>
                                <select name="level" class="form-select">
                                    <?php foreach ($model->levels as $level): ?>
                                        <option value="<?= $level ?>"
                                            <?= $model->getCourseValue('level') == $level ? 'selected' : '' ?>>
                                            <?= $level ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Giá bán (VNĐ)</label>
                                <div class="input-group">
                                    <input type="number" name="price" class="form-control" min="0" step="1000"
                                           value="<?= htmlspecialchars($model->getCourseValue('price', 0)) ?>">
                                    <span class="input-group-text">₫</span>
                                </div>
                                <div class="form-text">Nhập 0 để miễn phí.</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Thời lượng (Tuần)</label>
                                <input type="number" name="duration_weeks" class="form-control" min="1"
                                       value="<?= htmlspecialchars($model->getCourseValue('duration_weeks', 1)) ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Ảnh bìa khóa học</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <?php if ($model->isEditMode() && $model->getCourseValue('image')): ?>
                                <div class="mt-2">
                                    <img src="/assets/uploads/courses/<?= $model->getCourseValue('image') ?>"
                                         class="img-thumbnail" width="150" alt="Ảnh hiện tại">
                                    <small class="text-muted d-block">Ảnh hiện tại</small>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Mô tả chi tiết</label>
                            <textarea name="description" class="form-control" rows="5" required
                                      placeholder="Giới thiệu về nội dung khóa học..."><?= htmlspecialchars($model->getCourseValue('description')) ?></textarea>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> <?= $model->isEditMode() ? 'Cập nhật' : 'Tạo khóa học' ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>