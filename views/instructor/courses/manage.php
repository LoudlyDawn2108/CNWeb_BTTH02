<?php
/** @var ViewModels\Instructor\CourseManageViewModel $viewModel */
?>

<div class="container py-4">
    <!-- Course Overview Card -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-2">
                    <div class="position-relative">
                        <img src="<?= !empty($viewModel->course->image) ? '/assets/uploads/courses/' . $viewModel->course->image : '/assets/img/default-course.png' ?>"
                             class="img-fluid rounded shadow-sm" alt="Thumbnail" style="object-fit: cover; aspect-ratio: 16/9;">
                        <span class="position-absolute top-0 start-0 m-2 badge bg-<?= $viewModel->course->status == 'approved' ? 'success' : 'warning' ?>">
                            <?= $viewModel->course->status == 'approved' ? 'Đã duyệt' : 'Chờ duyệt' ?>
                        </span>
                    </div>
                </div>
                <div class="col-md-7 ps-4">
                    <div class="mb-3">
                        <h3 class="fw-bold mb-2 text-dark"><?= htmlspecialchars($viewModel->course->title) ?></h3>
                        <p class="text-muted mb-0 small" style="line-height: 1.6;">
                            <?= !empty($viewModel->course->description) ? htmlspecialchars(substr($viewModel->course->description, 0, 120)) . '...' : 'Chưa có mô tả' ?>
                        </p>
                    </div>

                    <div class="d-flex flex-wrap gap-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 rounded p-2 me-2">
                                <i class="bi bi-currency-dollar text-primary"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block" style="font-size: 0.75rem;">Giá khóa học</small>
                                <strong class="text-dark"><?= $viewModel->course->price > 0 ? number_format($viewModel->course->price) . 'đ' : 'Miễn phí' ?></strong>
                            </div>
                        </div>

                        <div class="d-flex align-items-center">
                            <div class="bg-info bg-opacity-10 rounded p-2 me-2">
                                <i class="bi bi-clock-history text-info"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block" style="font-size: 0.75rem;">Thời lượng</small>
                                <strong class="text-dark"><?= $viewModel->course->duration_weeks ?> tuần</strong>
                            </div>
                        </div>

                        <div class="d-flex align-items-center">
                            <div class="bg-success bg-opacity-10 rounded p-2 me-2">
                                <i class="bi bi-collection-play text-success"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block" style="font-size: 0.75rem;">Bài học</small>
                                <strong class="text-dark"><?= $viewModel->lessons->count() ?> bài</strong>
                            </div>
                        </div>

                        <?php if (isset($viewModel->course->student_count)): ?>
                            <div class="d-flex align-items-center">
                                <div class="bg-warning bg-opacity-10 rounded p-2 me-2">
                                    <i class="bi bi-people text-warning"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">Học viên</small>
                                    <strong class="text-dark"><?= $viewModel->course->student_count ?? 0 ?></strong>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="d-grid gap-2">
                        <a href="/instructor/courses/<?= $viewModel->course->id ?>/edit" class="btn btn-outline-primary">
                            <i class="bi bi-pencil-square me-2"></i>Chỉnh sửa khóa học
                        </a>
                        <a href="/instructor/courses/<?= $viewModel->course->id ?>/students" class="btn btn-outline-warning">
                            <i class="bi bi-people-fill me-2"></i>Danh sách học viên
                        </a>
                        <a href="/instructor/courses/<?= $viewModel->course->id ?>/analytics" class="btn btn-outline-info">
                            <i class="bi bi-graph-up me-2"></i>Thống kê
                        </a>
                        <button class="btn btn-outline-secondary" onclick="alert('Xem trước khóa học')">
                            <i class="bi bi-eye me-2"></i>Xem trước
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Course Content Section -->
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="fw-bold mb-1 text-dark">
                                <i class="bi bi-journals text-primary me-2"></i>Nội dung khóa học
                            </h5>
                            <small class="text-muted">Quản lý và sắp xếp các bài học trong khóa học của bạn</small>
                        </div>
                        <a href="/instructor/courses/<?= $viewModel->course->id ?>/lessons/create" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Thêm bài học mới
                        </a>
                    </div>
                </div>

                <div class="card-body p-0">
                    <?php if ($viewModel->lessons->isEmpty()): ?>
                        <div class="text-center py-5 px-4">
                            <div class="mb-4">
                                <i class="bi bi-journal-x text-muted" style="font-size: 4rem;"></i>
                            </div>
                            <h5 class="text-muted mb-2">Chưa có bài học nào</h5>
                            <p class="text-muted mb-4">Hãy bắt đầu tạo nội dung cho khóa học của bạn</p>
                            <a href="/instructor/courses/<?= $viewModel->course->id ?>/lessons/create" class="btn btn-primary">
                                <i class="bi bi-plus-lg me-2"></i>Tạo bài học đầu tiên
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                <tr>
                                    <th class="ps-4" style="width: 80px;">
                                        <small class="text-muted fw-semibold">STT</small>
                                    </th>
                                    <th>
                                        <small class="text-muted fw-semibold">TÊN BÀI HỌC</small>
                                    </th>
                                    <th style="width: 150px;">
                                        <small class="text-muted fw-semibold">LOẠI</small>
                                    </th>
                                    <th style="width: 150px;">
                                        <small class="text-muted fw-semibold">TÀI LIỆU</small>
                                    </th>
                                    <th class="text-center" style="width: 220px;">
                                        <small class="text-muted fw-semibold">THAO TÁC</small>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                // Pagination settings
                                $lessonsPerPage = 10;
                                $currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
                                $totalLessons = $viewModel->lessons->count();
                                $totalPages = ceil($totalLessons / $lessonsPerPage);
                                $offset = ($currentPage - 1) * $lessonsPerPage;

                                // Get lessons for current page
                                $lessonsArray = $viewModel->lessons->toArray();
                                $paginatedLessons = array_slice($lessonsArray, $offset, $lessonsPerPage);

                                foreach ($paginatedLessons as $lesson):
                                    ?>
                                    <tr class="lesson-row" style="cursor: pointer;"
                                        onclick="window.location.href='/instructor/lessons/<?= $lesson->id ?>/edit'">
                                        <td class="ps-4" onclick="event.stopPropagation();">
                                            <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto"
                                                 style="width: 40px; height: 40px;">
                                                <span class="fw-bold text-primary"><?= $lesson->order ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div>
                                                    <h6 class="mb-1 fw-semibold text-dark">
                                                        <?= htmlspecialchars($lesson->title) ?>
                                                    </h6>
                                                    <?php if (!empty($lesson->description)): ?>
                                                        <small class="text-muted d-block" style="max-width: 500px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                            <?= htmlspecialchars($lesson->description) ?>
                                                        </small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if (!empty($lesson->video_url)): ?>
                                                <span class="badge bg-primary bg-opacity-10 text-primary">
                                                        <i class="bi bi-play-circle me-1"></i>Video
                                                    </span>
                                            <?php else: ?>
                                                <span class="badge bg-info bg-opacity-10 text-info">
                                                        <i class="bi bi-file-text me-1"></i>Bài đọc
                                                    </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($lesson->material_count > 0): ?>
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                                        <i class="bi bi-paperclip me-1"></i><?= $lesson->material_count ?> tài liệu
                                                    </span>
                                            <?php else: ?>
                                                <small class="text-muted">Không có</small>
                                            <?php endif; ?>
                                        </td>
                                        <td onclick="event.stopPropagation();">
                                            <div class="d-flex justify-content-center gap-1">
                                                <button class="btn btn-sm btn-outline-success"
                                                        title="Xem nội dung"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#lessonModal<?= $lesson->id ?>">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <a href="/instructor/lessons/<?= $lesson->id ?>/edit"
                                                   class="btn btn-sm btn-outline-primary"
                                                   title="Chỉnh sửa">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="/instructor/lessons/<?= $lesson->id ?>/delete"
                                                      method="POST"
                                                      class="d-inline delete-form">

                                                    <button type="submit"
                                                            class="btn btn-sm btn-outline-danger"
                                                            title="Xóa">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if ($totalPages > 1): ?>
                            <div class="d-flex justify-content-between align-items-center p-3 border-top">
                                <div class="text-muted small">
                                    Hiển thị <?= $offset + 1 ?> - <?= min($offset + $lessonsPerPage, $totalLessons) ?>
                                    trong tổng số <?= $totalLessons ?> bài học
                                </div>
                                <nav aria-label="Page navigation">
                                    <ul class="pagination pagination-sm mb-0">
                                        <!-- Previous Button -->
                                        <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                                            <a class="page-link" href="?page=<?= $currentPage - 1 ?>" aria-label="Previous">
                                                <span aria-hidden="true">&laquo;</span>
                                            </a>
                                        </li>

                                        <!-- Page Numbers -->
                                        <?php
                                        $startPage = max(1, $currentPage - 2);
                                        $endPage = min($totalPages, $currentPage + 2);

                                        if ($startPage > 1): ?>
                                            <li class="page-item"><a class="page-link" href="?page=1">1</a></li>
                                            <?php if ($startPage > 2): ?>
                                                <li class="page-item disabled"><span class="page-link">...</span></li>
                                            <?php endif; ?>
                                        <?php endif; ?>

                                        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                            <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                            </li>
                                        <?php endfor; ?>

                                        <?php if ($endPage < $totalPages): ?>
                                            <?php if ($endPage < $totalPages - 1): ?>
                                                <li class="page-item disabled"><span class="page-link">...</span></li>
                                            <?php endif; ?>
                                            <li class="page-item"><a class="page-link" href="?page=<?= $totalPages ?>"><?= $totalPages ?></a></li>
                                        <?php endif; ?>

                                        <!-- Next Button -->
                                        <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                                            <a class="page-link" href="?page=<?= $currentPage + 1 ?>" aria-label="Next">
                                                <span aria-hidden="true">&raquo;</span>
                                            </a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        <?php endif; ?>

                        <!-- Lesson Summary Footer -->
                        <div class="card-footer bg-light border-top">
                            <div class="row text-center">
                                <div class="col-md-4">
                                    <small class="text-muted d-block">Tổng số bài học</small>
                                    <strong class="text-dark"><?= $viewModel->lessons->count() ?></strong>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted d-block">Bài học có video</small>
                                    <strong class="text-primary">
                                        <?php
                                        $videoCount = 0;
                                        foreach ($viewModel->lessons as $l) {
                                            if (!empty($l->video_url)) $videoCount++;
                                        }
                                        echo $videoCount;
                                        ?>
                                    </strong>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted d-block">Tổng tài liệu</small>
                                    <strong class="text-secondary">
                                        <?php
                                        $totalMaterials = 0;
                                        foreach ($viewModel->lessons as $l) {
                                            $totalMaterials += $l->material_count;
                                        }
                                        echo $totalMaterials;
                                        ?>
                                    </strong>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals for Lesson Details -->
<?php foreach ($viewModel->lessons as $lesson): ?>
    <div class="modal fade" id="lessonModal<?= $lesson->id ?>" tabindex="-1" aria-labelledby="lessonModalLabel<?= $lesson->id ?>" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <div>
                        <h5 class="modal-title fw-bold" id="lessonModalLabel<?= $lesson->id ?>">
                            <span class="badge bg-primary me-2"><?= $lesson->order ?></span>
                            <?= htmlspecialchars($lesson->title) ?>
                        </h5>
                        <small class="text-muted">Xem nội dung bài học</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Video Section -->
                    <?php if (!empty($lesson->video_url)): ?>
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">
                                <i class="bi bi-play-circle text-primary me-2"></i>Video bài học
                            </h6>
                            <div class="ratio ratio-16x9 rounded overflow-hidden shadow-sm">
                                <?php
                                // Convert YouTube URL to embed format
                                $videoUrl = $lesson->video_url;
                                if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $videoUrl, $matches)) {
                                    $videoId = $matches[1];
                                    $embedUrl = "https://www.youtube.com/embed/" . $videoId;
                                } else {
                                    $embedUrl = $videoUrl;
                                }
                                ?>
                                <iframe src="<?= htmlspecialchars($embedUrl) ?>"
                                        title="YouTube video player"
                                        frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen>
                                </iframe>
                            </div>
                            <div class="mt-2">
                                <a href="<?= htmlspecialchars($lesson->video_url) ?>"
                                   target="_blank"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-box-arrow-up-right me-1"></i>Mở video trên YouTube
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Description Section -->
                    <?php if (!empty($lesson->description)): ?>
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">
                                <i class="bi bi-file-text text-info me-2"></i>Mô tả bài học
                            </h6>
                            <div class="p-3 bg-light rounded">
                                <p class="mb-0" style="white-space: pre-wrap;"><?= htmlspecialchars($lesson->description) ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Content Section -->
                    <?php if (!empty($lesson->content)): ?>
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">
                                <i class="bi bi-journal-text text-success me-2"></i>Nội dung chi tiết
                            </h6>
                            <div class="p-3 border rounded">
                                <?= $lesson->content ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Materials Section -->
                    <?php if ($lesson->material_count > 0): ?>
                        <div class="mb-3">
                            <h6 class="fw-bold mb-3">
                                <i class="bi bi-paperclip text-secondary me-2"></i>Tài liệu đính kèm
                                <span class="badge bg-secondary ms-2"><?= $lesson->material_count ?></span>
                            </h6>
                            <div class="list-group">
                                <?php
                                // Giả sử bạn có materials trong lesson object hoặc cần load từ database
                                // Đây là placeholder, bạn cần thay thế bằng dữ liệu thực
                                if (isset($lesson->materials) && !empty($lesson->materials)):
                                    foreach ($lesson->materials as $material):
                                        ?>
                                        <a href="/assets/uploads/materials/<?= htmlspecialchars($material->file_path) ?>"
                                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                                           target="_blank">
                                            <div>
                                                <i class="bi bi-file-earmark-pdf text-danger me-2"></i>
                                                <span><?= htmlspecialchars($material->title) ?></span>
                                            </div>
                                            <i class="bi bi-download text-primary"></i>
                                        </a>
                                    <?php
                                    endforeach;
                                else:
                                    ?>
                                    <div class="alert alert-info mb-0">
                                        <i class="bi bi-info-circle me-2"></i>
                                        Có <?= $lesson->material_count ?> tài liệu. Vui lòng vào trang chỉnh sửa để xem chi tiết.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-2"></i>Đóng
                    </button>
                    <a href="/instructor/lessons/<?= $lesson->id ?>/edit" class="btn btn-primary">
                        <i class="bi bi-pencil-square me-2"></i>Chỉnh sửa bài học
                    </a>
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </div>
<?php endforeach; ?>

<style>
    .card {
        transition: all 0.3s ease;
    }

    .card:hover {
        transform: translateY(-2px);
    }

    .btn {
        transition: all 0.2s ease;
    }

    .table tbody tr {
        transition: background-color 0.2s ease;
    }

    .table tbody tr.lesson-row:hover {
        background-color: rgba(0, 123, 255, 0.08);
        cursor: pointer;
    }

    .table tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.03);
    }

    .badge {
        font-weight: 500;
        padding: 0.4em 0.8em;
    }

    .modal-content {
        border: none;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .list-group-item {
        transition: all 0.2s ease;
    }

    .list-group-item:hover {
        background-color: rgba(0, 123, 255, 0.05);
        transform: translateX(5px);
    }
</style>

<script>
    // Tìm tất cả các form có class 'delete-form'
    const deleteForms = document.querySelectorAll('.delete-form');

    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            // 1. Ngăn không cho form gửi đi ngay lập tức
            e.preventDefault();

            // 2. Hiện thông báo đẹp bằng SweetAlert2
            Swal.fire({
                title: 'Bạn có chắc chắn?',
                text: "Hành động này sẽ xóa bài học và không thể khôi phục!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',   // Màu đỏ cho nút Xóa
                cancelButtonColor: '#3085d6', // Màu xanh cho nút Hủy
                confirmButtonText: 'Vâng, xóa nó!',
                cancelButtonText: 'Hủy bỏ'
            }).then((result) => {
                // 3. Nếu người dùng bấm nút "Vâng, xóa nó!"
                if (result.isConfirmed) {
                    // Thì mới cho phép form gửi đi
                    this.submit();
                }
            });
        });
    });
</script>