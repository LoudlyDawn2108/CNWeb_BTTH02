<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Course.php';
require_once __DIR__ . '/../models/Enrollment.php';
require_once __DIR__ . '/../viewmodels/AdminViewModel.php';

use Lib\Controller;
use ViewModels\AdminDashboardViewModel;

class AdminController extends Controller
{
    public function __construct()
    {
        // Ensure admin is logged in
        // $this->requireRole(User::ROLE_ADMIN);
    }

    /**
     * Manage Users - Display, filter, and manage all users
     */
    public function manageUsers(): void
    {
        // Get query parameters for filtering and pagination
        $search = $_GET['search'] ?? '';
        $roleFilter = $_GET['role'] ?? '';
        $statusFilter = $_GET['status'] ?? '';
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 15;
        $offset = ($page - 1) * $perPage;

        // Build query
        $query = User::query();

        // Apply search filter
        if (!empty($search)) {
            $query->whereRaw('(username LIKE :search OR fullname LIKE :search2 OR email LIKE :search3)', [
                ':search' => "%{$search}%",
                ':search2' => "%{$search}%",
                ':search3' => "%{$search}%"
            ]);
        }

        // Apply role filter
        if ($roleFilter !== '') {
            $query->where('role', $roleFilter);
        }

        // Apply status filter
        if ($statusFilter !== '') {
            $query->where('status', $statusFilter);
        }

        // Get total count for pagination
        $totalUsers = $query->count();
        $totalPages = ceil($totalUsers / $perPage);

        // Get users with pagination
        $users = $query
            ->orderBy('created_at', 'DESC')
            ->limit($perPage)
            ->offset($offset)
            ->get();

        $users = array_map(fn($u) => $u->toArray(), $users);

        // Get statistics for each role
        $roleStats = [
            'total' => User::query()->count(),
            'students' => User::query()->where('role', User::ROLE_STUDENT)->count(),
            'instructors' => User::query()->where('role', User::ROLE_INSTRUCTOR)->count(),
            'admins' => User::query()->where('role', User::ROLE_ADMIN)->count(),
        ];

        $viewModel = new \ViewModels\AdminUsersViewModel(
            title: "Quản lý người dùng - Feetcode",
            users: $users,
            roleStats: $roleStats,
            currentPage: $page,
            totalPages: $totalPages,
            totalUsers: $totalUsers,
            search: $search,
            roleFilter: $roleFilter,
            statusFilter: $statusFilter
        );

        $this->render('admin/users/manage', $viewModel);
    }

    /**
     * Toggle User Status (Active/Inactive)
     */
    public function toggleUserStatus(int $id): void
    {
        header('Content-Type: application/json');
        
        try {
            // Get the status from request body
            $input = json_decode(file_get_contents('php://input'), true);
            $newStatus = isset($input['status']) ? (int)$input['status'] : null;
            
            if ($newStatus === null || !in_array($newStatus, [0, 1])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Trạng thái không hợp lệ'
                ]);
                return;
            }
            
            // Find user
            $user = User::find($id);
            
            if (!$user) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Không tìm thấy người dùng'
                ]);
                return;
            }
            
            // Prevent admin from deactivating themselves
            if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $id && $newStatus == 0) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Bạn không thể vô hiệu hóa tài khoản của chính mình'
                ]);
                return;
            }
            
            // Update status
            $user->status = $newStatus;
            $user->save();
            
            $_SESSION['success'] = 'Cập nhật trạng thái người dùng thành công';
            
            echo json_encode([
                'success' => true,
                'message' => 'Cập nhật thành công'
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * List Categories - Display all categories with course count
     */
    public function listCategories(): void
    {
        // Get search parameter
        $search = $_GET['search'] ?? '';
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        // Build query with course count
        $query = Category::query()
            ->select([
                'c.*',
                'COUNT(co.id) as course_count'
            ])
            ->table('categories c')
            ->leftJoin('courses co', 'c.id', '=', 'co.category_id')
            ->groupBy('c.id');

        // Apply search filter
        if (!empty($search)) {
            $query->whereRaw('(c.name LIKE :search OR c.description LIKE :search2)', [
                ':search' => "%{$search}%",
                ':search2' => "%{$search}%"
            ]);
        }

        // Get total count for pagination
        $totalCategories = Category::query()->count();
        
        // Get categories with pagination
        $categories = $query
            ->orderBy('c.name', 'ASC')
            ->limit($perPage)
            ->offset($offset)
            ->get();

        $categories = array_map(fn($c) => $c->toArray(), $categories);
        $totalPages = ceil($totalCategories / $perPage);

        // Get statistics
        $stats = [
            'total_categories' => $totalCategories,
            'total_courses' => Course::query()->count(),
        ];

        $viewModel = new \ViewModels\AdminCategoriesViewModel(
            title: "Quản lý danh mục - Feetcode",
            categories: $categories,
            stats: $stats,
            currentPage: $page,
            totalPages: $totalPages,
            search: $search
        );

        $this->render('admin/categories/list', $viewModel);
    }

    /**
     * Create Category - Show create form
     */
    public function createCategory(): void
    {
        $viewModel = new \ViewModels\AdminCategoryFormViewModel(
            title: "Th\u00eam danh m\u1ee5c m\u1edbi - Feetcode",
            category: null,
            isEdit: false
        );

        $this->render('admin/categories/create', $viewModel);
    }

    /**
     * Admin Dashboard - Display statistics and overview
     */
    public function dashboard(): void
    {
        // Get statistics
        $totalUsers = User::query()->count();
        $totalStudents = User::query()->where('role', User::ROLE_STUDENT)->count();
        $totalInstructors = User::query()->where('role', User::ROLE_INSTRUCTOR)->count();
        $totalCourses = Course::query()->count();
        $totalEnrollments = Enrollment::query()->count();
        
        // Get pending courses for approval
        $pendingCourses = Course::query()
            ->select(['c.*', 'u.fullname as instructor_name', 'cat.name as category_name'])
            ->table('courses c')
            ->leftJoin('users u', 'c.instructor_id', '=', 'u.id')
            ->leftJoin('categories cat', 'c.category_id', '=', 'cat.id')
            ->where('c.status', 'pending')
            ->orderBy('c.created_at', 'DESC')
            ->limit(10)
            ->get();

        $pendingCourses = array_map(fn($c) => $c->toArray(), $pendingCourses);

        // Get recent users
        $recentUsers = User::query()
            ->orderBy('created_at', 'DESC')
            ->limit(10)
            ->get();

        $recentUsers = array_map(fn($u) => $u->toArray(), $recentUsers);

        $viewModel = new AdminDashboardViewModel(
            title: "Admin Dashboard - Feetcode",
            totalUsers: $totalUsers,
            totalStudents: $totalStudents,
            totalInstructors: $totalInstructors,
            totalCourses: $totalCourses,
            totalEnrollments: $totalEnrollments,
            pendingCourses: $pendingCourses,
            recentUsers: $recentUsers
        );

        $this->render('admin/dashboard', $viewModel);
    }
}
