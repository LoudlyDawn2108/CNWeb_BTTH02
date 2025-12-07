<!-- Admin Sidebar Navigation -->
<div class="d-flex" style="min-height: calc(100vh - 56px);">
    <div class="bg-dark text-white p-0" style="width: 250px; min-height: 100%;">
        <div class="p-3">
            <h6 class="text-uppercase small fw-bold mb-3 text-white-50">QUẢN TRỊ HỆ THỐNG</h6>
            
            <ul class="nav flex-column">
                <li class="nav-item mb-1">
                    <a class="nav-link text-white <?= str_contains($_SERVER['REQUEST_URI'], '/admin/dashboard') && !str_contains($_SERVER['REQUEST_URI'], '/admin/reports') ? 'bg-primary rounded' : '' ?>" 
                       href="/admin/dashboard">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                </li>
                
                <li class="nav-item mb-1">
                    <a class="nav-link text-white <?= str_contains($_SERVER['REQUEST_URI'], '/admin/users') ? 'bg-primary rounded' : '' ?>" 
                       href="/admin/users">
                        <i class="bi bi-people me-2"></i> Quản lý người dùng
                    </a>
                </li>
                
                <li class="nav-item mb-1">
                    <a class="nav-link text-white <?= str_contains($_SERVER['REQUEST_URI'], '/admin/categories') ? 'bg-primary rounded' : '' ?>" 
                       href="/admin/categories">
                        <i class="bi bi-folder me-2"></i> Quản lý danh mục
                    </a>
                </li>
                
                <li class="nav-item mb-1">
                    <a class="nav-link text-white <?= str_contains($_SERVER['REQUEST_URI'], '/admin/reports') ? 'bg-primary rounded' : '' ?>" 
                       href="/admin/reports/statistics">
                        <i class="bi bi-bar-chart me-2"></i> Thống kê & Báo cáo
                    </a>
                </li>
            </ul>

            <hr class="bg-secondary">

            <h6 class="text-uppercase small fw-bold mb-3 text-white-50">LIÊN KẾT NHANH</h6>
            <ul class="nav flex-column">
                <li class="nav-item mb-1">
                    <a class="nav-link text-white" href="/">
                        <i class="bi bi-house me-2"></i> Trang chủ
                    </a>
                </li>
                <li class="nav-item mb-1">
                    <a class="nav-link text-white" href="/auth/logout">
                        <i class="bi bi-box-arrow-right me-2"></i> Đăng xuất
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="flex-grow-1">
