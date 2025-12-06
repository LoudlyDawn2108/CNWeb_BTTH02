<div class="sidebar bg-light border-end" style="min-height: calc(100vh - 56px); width: 250px;">
    <div class="p-3">
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 2): ?>
            <!-- Admin Sidebar -->
            <h6 class="text-muted text-uppercase small fw-bold mb-3">Quản trị</h6>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/admin/dashboard') ? 'active' : '' ?>"
                       href="/admin/dashboard">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/admin/users') ? 'active' : '' ?>"
                       href="/admin/users">
                        <i class="bi bi-people me-2"></i> Người dùng
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/admin/categories') ? 'active' : '' ?>"
                       href="/admin/categories">
                        <i class="bi bi-tags me-2"></i> Danh mục
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/admin/reports') ? 'active' : '' ?>"
                       href="/admin/reports/statistics">
                        <i class="bi bi-bar-chart me-2"></i> Thống kê
                    </a>
                </li>
            </ul>
        <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] == 1): ?>
            <!-- Instructor Sidebar -->
            <h6 class="text-muted text-uppercase small fw-bold mb-3">Giảng viên</h6>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/instructor/dashboard') ? 'active' : '' ?>"
                       href="/instructor/dashboard">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/instructor/my-courses') ? 'active' : '' ?>"
                       href="/instructor/my-courses">
                        <i class="bi bi-book me-2"></i> Khóa học của tôi
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/instructor/courses/create') ? 'active' : '' ?>"
                       href="/instructor/courses/create">
                        <i class="bi bi-plus-circle me-2"></i> Tạo khóa học
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/instructor/students') ? 'active' : '' ?>"
                       href="/instructor/students">
                        <i class="bi bi-people me-2"></i> Học viên
                    </a>
                </li>
            </ul>
        <?php else: ?>
            <!-- Student Sidebar -->
            <h6 class="text-muted text-uppercase small fw-bold mb-3">Học viên</h6>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/student/dashboard') ? 'active' : '' ?>"
                       href="/student/dashboard">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], '/student/my-courses') ? 'active' : '' ?>"
                       href="/student/my-courses">
                        <i class="bi bi-book me-2"></i> Khóa học của tôi
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/courses">
                        <i class="bi bi-search me-2"></i> Tìm khóa học
                    </a>
                </li>
            </ul>
        <?php endif; ?>
    </div>
</div>
