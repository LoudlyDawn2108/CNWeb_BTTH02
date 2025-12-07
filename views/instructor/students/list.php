<?php
/** @var StudentListViewModel $viewModel */
use ViewModels\Instructor\StudentListViewModel;
?>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-3 col-lg-2">
            <?php include __DIR__ . '/../../layouts/sidebar.php'; ?>
        </div>
        <div class="col-md-9 col-lg-10">
            <h2 class="mb-4">
                <i class="bi bi-people"></i>
                <?= $viewModel->course ? 'Học viên - ' . htmlspecialchars($viewModel->course['title']) : 'Tất cả học viên' ?>
            </h2>

            <?php if (empty($viewModel->students)): ?>
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-people fs-1 text-muted"></i>
                        <p class="text-muted mt-2">Chưa có học viên nào đăng ký.</p>
                    </div>
                </div>
            <?php else: ?>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Học viên</th>
                                        <th>Email</th>
                                        <?php if (!$viewModel->course): ?><th>Khóa học</th><?php endif; ?>
                                        <th>Ngày đăng ký</th>
                                        <th>Tiến độ</th>
                                        <th>Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($viewModel->students as $student): ?>
                                        <tr>
                                            <td>
                                                <strong><?= htmlspecialchars($student['student_name']) ?></strong>
                                            </td>
                                            <td><small><?= htmlspecialchars($student['student_email']) ?></small></td>
                                            <?php if (!$viewModel->course): ?>
                                                <td><small><?= htmlspecialchars($student['course_title']) ?></small></td>
                                            <?php endif; ?>
                                            <td><small><?= date('d/m/Y', strtotime($student['enrolled_date'])) ?></small></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="progress flex-grow-1 me-2" style="height: 8px; width: 80px;">
                                                        <div class="progress-bar bg-<?= $student['progress'] >= 100 ? 'success' : 'primary' ?>"
                                                             style="width: <?= $student['progress'] ?>%"></div>
                                                    </div>
                                                    <small><?= $student['progress'] ?>%</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?= $student['status'] === 'completed' ? 'success' : ($student['status'] === 'active' ? 'warning' : 'secondary') ?>">
                                                    <?= $student['status'] === 'completed' ? 'Hoàn thành' : ($student['status'] === 'active' ? 'Đang học' : 'Đã hủy') ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>