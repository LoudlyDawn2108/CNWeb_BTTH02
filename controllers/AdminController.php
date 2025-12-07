<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Course.php';
require_once __DIR__ . '/../models/Enrollment.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../viewmodels/AdminViewModel.php';

use Lib\Controller;
use Models\Category;
use Models\CategoryTable;
use Models\Course;
use Models\CourseTable;
use Models\Enrollment;
use Models\EnrollmentTable;
use Models\User;
use Models\UserTable;
use ViewModels\AdminDashboardViewModel;

class AdminController extends Controller {

    /**
     * Manage Users - Display, filter, and manage all users
     */
    public function manageUsers(): void {
        // Get query parameters for filtering and pagination
        $search = $_GET['search'] ?? '';
        $roleFilter = $_GET['role'] ?? '';
        $statusFilter = $_GET['status'] ?? '';
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 15;
        $offset = ($page - 1) * $perPage;

        $u = new UserTable();

        // Build query
        $query = User::query();

        // Apply search filter
        if (!empty($search)) {
            $query->whereRaw(
                "($u->USERNAME LIKE :search OR $u->FULLNAME LIKE :search2 OR $u->EMAIL LIKE :search3)", [
                ':search' => "%{$search}%",
                ':search2' => "%{$search}%",
                ':search3' => "%{$search}%"
            ]);
        }

        // Apply role filter
        if ($roleFilter !== '') {
            $query->where($u->ROLE, $roleFilter);
        }

        // Apply status filter
        if ($statusFilter !== '') {
            $query->where($u->STATUS, $statusFilter);
        }

        // Get total count for pagination
        $totalUsers = $query->count();
        $totalPages = ceil($totalUsers / $perPage);

        // Get users with pagination
        $users = $query
            ->orderBy($u->CREATED_AT, 'DESC')
            ->limit($perPage)
            ->offset($offset)
            ->get();

        $users = array_map(fn($u) => $u->toArray(), $users);

        // Get statistics for each role
        $roleStats = [
            'total' => User::query()->count(),
            'students' => User::query()->where($u->ROLE, User::ROLE_STUDENT)->count(),
            'instructors' => User::query()->where($u->ROLE, User::ROLE_INSTRUCTOR)->count(),
            'admins' => User::query()->where($u->ROLE, User::ROLE_ADMIN)->count(),
        ];

        $viewModel = new \ViewModels\AdminUsersViewModel(
            title:        "Quản lý người dùng - Feetcode",
            users:        $users,
            roleStats:    $roleStats,
            currentPage:  $page,
            totalPages:   $totalPages,
            totalUsers:   $totalUsers,
            search:       $search,
            roleFilter:   $roleFilter,
            statusFilter: $statusFilter
        );

        $this->render('admin/users/manage', $viewModel, true);
    }

    /**
     * Toggle User Status (Active/Inactive)
     */
    public function toggleUserStatus(int $id): void {
        header('Content-Type: application/json');

        try {
            // Get the status from request body
            $input = json_decode(file_get_contents('php://input'), true);
            $newStatus = isset($input['status']) ? (int)$input['status'] : null;

            if ($newStatus === null || !in_array($newStatus, [0, 1])) {
                echo json_encode(
                    [
                        'success' => false,
                        'message' => 'Trạng thái không hợp lệ'
                    ]);
                return;
            }

            // Find user
            $user = User::find($id);

            if (!$user) {
                echo json_encode(
                    [
                        'success' => false,
                        'message' => 'Không tìm thấy người dùng'
                    ]);
                return;
            }

            // Prevent admin from deactivating themselves
            if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $id && $newStatus == 0) {
                echo json_encode(
                    [
                        'success' => false,
                        'message' => 'Bạn không thể vô hiệu hóa tài khoản của chính mình'
                    ]);
                return;
            }

            // Update status
            $user->status = $newStatus;
            $user->save();

            $this->setSuccessMessage('Cập nhật trạng thái người dùng thành công');

            echo json_encode(
                [
                    'success' => true,
                    'message' => 'Cập nhật thành công'
                ]);

        } catch (Exception $e) {
            echo json_encode(
                [
                    'success' => false,
                    'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
                ]);
        }
    }

    /**
     * List Categories - Display all categories with course count
     */
    public function listCategories(): void {
        // Get search parameter
        $search = $_GET['search'] ?? '';
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $cat = new CategoryTable();
        $c = new CourseTable();

        // Build query with course count
        $query = Category::query()
                         ->select(
                             [
                                 "$cat.*",
                                 "COUNT($c->ID) as course_count"
                             ])
                         ->table($cat)
                         ->leftJoin($c, $cat->ID, '=', $c->CATEGORY_ID)
                         ->groupBy($cat->ID);

        // Apply search filter
        if (!empty($search)) {
            $query->whereRaw(
                "($cat->NAME LIKE :search OR $cat->DESCRIPTION LIKE :search2)", [
                ':search' => "%{$search}%",
                ':search2' => "%{$search}%"
            ]);
        }

        // Get total count for pagination
        $totalCategories = Category::query()->count();

        // Get categories with pagination
        $categories = $query
            ->orderBy($cat->NAME, 'ASC')
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
            title:       "Quản lý danh mục - Feetcode",
            categories:  $categories,
            stats:       $stats,
            currentPage: $page,
            totalPages:  $totalPages,
            search:      $search
        );

        $this->render('admin/categories/list', $viewModel, true);
    }

    /**
     * Create Category - Show create form
     */
    public function createCategory(): void {
        $viewModel = new \ViewModels\AdminCategoryFormViewModel(
            title: "Thêm danh mục mới - Feetcode"
        );
        $viewModel->isEdit = false;

        $this->render('admin/categories/create', $viewModel, true);
    }

    /**
     * Store Category - Handle form submission
     */
    public function storeCategory(): void {
        $viewModel = new \ViewModels\AdminCategoryFormViewModel(title: "Thêm danh mục mới - Feetcode");
        $viewModel->isEdit = false;
        $viewModel->handleRequest($_POST);

        if ($viewModel->modelState->isValid) {
            try {
                $category = new Category();
                $category->name = $viewModel->name;
                $category->description = !empty($viewModel->description) ? $viewModel->description : null;
                $category->save();

                $this->setSuccessMessage('Thêm danh mục thành công');
                $this->redirect('/admin/categories');
                return;

            } catch (Exception $e) {
                $this->setErrorMessage('Có lỗi xảy ra: ' . $e->getMessage());
            }
        }

        // If validation failed or error occurred
        $this->render('admin/categories/create', $viewModel, true);
    }

    /**
     * Edit Category - Show edit form with existing data
     */
    public function editCategory(int $id): void {
        // Find category
        $category = Category::find($id);

        if (!$category) {
            $this->setErrorMessage('Không tìm thấy danh mục');
            $this->redirect('/admin/categories');
        }

        $viewModel = new \ViewModels\AdminCategoryFormViewModel(
            title: "Chỉnh sửa danh mục - Feetcode"
        );

        // Hydrate ViewModel from DB
        $viewModel->id = $id;
        $viewModel->name = $category->name;
        $viewModel->description = $category->description ?? '';
        $viewModel->isEdit = true;
        $viewModel->category = $category->toArray(); // Keep for view compatibility if needed

        $this->render('admin/categories/edit', $viewModel, true);
    }

    /**
     * Update Category - Handle edit form submission
     */
    public function updateCategory(int $id): void {
        $viewModel = new \ViewModels\AdminCategoryFormViewModel(title: "Chỉnh sửa danh mục - Feetcode");
        $viewModel->isEdit = true;
        $viewModel->id = $id;
        $viewModel->handleRequest($_POST);

        // Find category first to ensure it exists
        $category = Category::find($id);
        if (!$category) {
            $this->setErrorMessage('Không tìm thấy danh mục');
            $this->redirect('/admin/categories');
        }

        if ($viewModel->modelState->isValid) {
            try {
                $category->name = $viewModel->name;
                $category->description = !empty($viewModel->description) ? $viewModel->description : null;
                $category->save();

                $this->setSuccessMessage('Cập nhật danh mục thành công');
                $this->redirect('/admin/categories');
                return;

            } catch (Exception $e) {
                $this->setErrorMessage('Có lỗi xảy ra: ' . $e->getMessage());
            }
        }

        // Restore category data for view context if needed
        $viewModel->category = $category->toArray();

        // Render view with errors
        $this->render('admin/categories/edit', $viewModel, true);
    }

    /**
     * Delete Category - Remove category if not in use
     */
    public function deleteCategory(int $id): void {
        header('Content-Type: application/json');

        try {
            // Find category
            $category = Category::find($id);

            if (!$category) {
                echo json_encode(
                    [
                        'success' => false,
                        'message' => 'Không tìm thấy danh mục'
                    ]);
                return;
            }

            // Get course count for this category
            $c = new CourseTable();
            $courseCount = Course::query()
                                 ->where($c->CATEGORY_ID, $id)
                                 ->count();

            $categoryData = $category->toArray();

            if ($courseCount > 0) {
                echo json_encode(
                    [
                        'success' => false,
                        'message' => "Không thể xóa danh mục này vì đang có {$courseCount} khóa học sử dụng"
                    ]);
                return;
            }

            // Delete category
            $categoryName = $category->name;
            $category->delete();

            $this->setSuccessMessage("Đã xóa danh mục '{$categoryName}' thành công");

            echo json_encode(
                [
                    'success' => true,
                    'message' => 'Xóa danh mục thành công'
                ]);

        } catch (Exception $e) {
            echo json_encode(
                [
                    'success' => false,
                    'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
                ]);
        }
    }

    /**
     * Approve Course - Change course status (approve/reject)
     */
    public function approveCourse(int $id): void {
        header("Content - Type: application / json");

        try {
            // Get action from request body
            $input = json_decode(file_get_contents('php://input'), true);
            $action = $input['action'] ?? 'approve'; // approve or reject
            $rejectReason = $input['reason'] ?? null;

            // Validate action
            if (!in_array($action, ['approve', 'reject'])) {
                echo json_encode(
                    [
                        'success' => false,
                        'message' => 'Hành động không hợp lệ'
                    ]);
                return;
            }

            // Find course
            $course = Course::find($id);

            if (!$course) {
                echo json_encode(
                    [
                        'success' => false,
                        'message' => 'Không tìm thấy khóa học'
                    ]);
                return;
            }

            // Check if course is pending
            if ($course->status !== 'pending') {
                echo json_encode(
                    [
                        'success' => false,
                        'message' => 'Khóa học này đã được xử lý rồi'
                    ]);
                return;
            }

            // Update course status
            if ($action === 'approve') {
                $course->status = 'approved';
                $message = 'Phê duyệt khóa học thành công';
                $sessionMessage = "Đã phê duyệt khóa học '{$course->title}'";
            } else {
                $course->status = 'rejected';
                $message = 'Từ chối khóa học thành công';
                $sessionMessage = "Đã từ chối khóa học '{$course->title}'";

                if ($rejectReason) {
                    $sessionMessage .= ". Lý do: {$rejectReason}";
                }
            }

            $course->save();

            $this->setSuccessMessage($sessionMessage);

            echo json_encode(
                [
                    'success' => true,
                    'message' => $message,
                    'new_status' => $course->status
                ]);

        } catch (Exception $e) {
            echo json_encode(
                [
                    'success' => false,
                    'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
                ]);
        }
    }

    /**
     * Reject Course - Explicitly reject a course with reason
     */
    public function rejectCourse(int $id): void {
        header('Content-Type: application/json');

        try {
            // Get reason from request body
            $input = json_decode(file_get_contents('php://input'), true);
            $reason = trim($input['reason'] ?? '');

            // Validate reason is provided
            if (empty($reason)) {
                echo json_encode(
                    [
                        'success' => false,
                        'message' => 'Vui lòng nhập lý do từ chối'
                    ]);
                return;
            }

            if (strlen($reason) < 10) {
                echo json_encode(
                    [
                        'success' => false,
                        'message' => 'Lý do từ chối phải có ít nhất 10 ký tự'
                    ]);
                return;
            }

            // Find course
            $course = Course::find($id);

            if (!$course) {
                echo json_encode(
                    [
                        'success' => false,
                        'message' => 'Không tìm thấy khóa học'
                    ]);
                return;
            }

            // Check if course is pending
            if ($course->status !== 'pending') {
                echo json_encode(
                    [
                        'success' => false,
                        'message' => 'Khóa học này đã được xử lý rồi'
                    ]);
                return;
            }

            // Update course status to rejected
            $course->status = 'rejected';

            $course->save();

            $this->setSuccessMessage("Đã từ chối khóa học '{$course->title}'. Lý do: {$reason}");

            echo json_encode(
                [
                    'success' => true,
                    'message' => 'Từ chối khóa học thành công',
                    'new_status' => 'rejected'
                ]);

        } catch (Exception $e) {
            echo json_encode(
                [
                    'success' => false,
                    'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
                ]);
        }
    }

    /**
     * Statistics Report - Comprehensive analytics and reports
     */
    public function statistics(): void {
        $u = new UserTable();
        $c = new CourseTable();
        $e = new EnrollmentTable();
        $cat = new CategoryTable();

        // User Statistics
        $userStats = [
            'total' => User::query()->count(),
            'students' => User::query()->where($u->ROLE, User::ROLE_STUDENT)->count(),
            'instructors' => User::query()->where($u->ROLE, User::ROLE_INSTRUCTOR)->count(),
            'admins' => User::query()->where($u->ROLE, User::ROLE_ADMIN)->count(),
            'active' => User::query()->where($u->STATUS, 1)->count(),
            'inactive' => User::query()->where($u->STATUS, 0)->count(),
        ];

        // Course Statistics
        $courseStats = [
            'total' => Course::query()->count(),
            'approved' => Course::query()->where($c->STATUS, 'approved')->count(),
            'pending' => Course::query()->where($c->STATUS, 'pending')->count(),
            'rejected' => Course::query()->where($c->STATUS, 'rejected')->count(),
        ];

        // Enrollment Statistics
        $enrollmentStats = [
            'total' => Enrollment::query()->count(),
            'active' => Enrollment::query()->where($e->STATUS, 'active')->count(),
            'completed' => Enrollment::query()->where($e->STATUS, 'completed')->count(),
        ];

        // Category Statistics
        $categoryStats = Category::query()
                                 ->select(["$cat->ID", "$cat->NAME", "COUNT($c->ID) as course_count"])
                                 ->table($cat)
                                 ->leftJoin($c, $cat->ID, '=', $c->CATEGORY_ID)
                                 ->groupBy($cat->ID, $cat->NAME)
                                 ->orderBy('course_count', 'DESC')
                                 ->limit(10)
                                 ->get();

        $categoryStats = array_map(fn($c) => $c->toArray(), $categoryStats);

        // Top Instructors by course count
        $topInstructors = User::query()
                              ->select(["$u.*", "COUNT($c->ID) as course_count"])
                              ->table($u)
                              ->leftJoin($c, $u->ID, '=', $c->INSTRUCTOR_ID)
                              ->where($u->ROLE, User::ROLE_INSTRUCTOR)
                              ->groupBy($u->ID)
                              ->orderBy('course_count', 'DESC')
                              ->limit(10)
                              ->get();

        $topInstructors = array_map(fn($i) => $i->toArray(), $topInstructors);

        // Popular Courses by enrollment count
        $popularCourses = Course::query()
                                ->select(
                                    ["$c.*", "COUNT($e->ID) as enrollment_count", "$u->FULLNAME as instructor_name"])
                                ->table($c)
                                ->leftJoin($e, $c->ID, '=', $e->COURSE_ID)
                                ->leftJoin($u, $c->INSTRUCTOR_ID, '=', $u->ID)
                                ->where($c->STATUS, 'approved')
                                ->groupBy($c->ID)
                                ->orderBy('enrollment_count', 'DESC')
                                ->limit(10)
                                ->get();

        $popularCourses = array_map(fn($c) => $c->toArray(), $popularCourses);

        // Monthly user growth (last 6 months)
        $monthlyUsers = User::query()
                            ->select(["DATE_FORMAT($u->CREATED_AT, '%Y-%m') as month", 'COUNT(*) as count'])
                            ->whereRaw("$u->CREATED_AT >= DATE_SUB(NOW(), INTERVAL 6 MONTH)")
                            ->groupBy('month')
                            ->orderBy('month', 'ASC')
                            ->get();

        $monthlyUsers = array_map(fn($m) => $m->toArray(), $monthlyUsers);

        $viewModel = new \ViewModels\AdminStatisticsViewModel(
            title:           "Thống kê & Báo cáo - Feetcode",
            userStats:       $userStats,
            courseStats:     $courseStats,
            enrollmentStats: $enrollmentStats,
            categoryStats:   $categoryStats,
            topInstructors:  $topInstructors,
            popularCourses:  $popularCourses,
            monthlyUsers:    $monthlyUsers
        );

        $this->render('admin/reports/statistics', $viewModel, true);
    }

    /**
     * Admin Dashboard - Display statistics and overview
     */
    public function dashboard(): void {
        $u = new UserTable();
        $c = new CourseTable();
        $e = new EnrollmentTable();
        $cat = new CategoryTable();

        // Get statistics
        $totalUsers = User::query()->count();
        $totalStudents = User::query()->where($u->ROLE, User::ROLE_STUDENT)->count();
        $totalInstructors = User::query()->where($u->ROLE, User::ROLE_INSTRUCTOR)->count();
        $totalCourses = Course::query()->count();
        $totalEnrollments = Enrollment::query()->count();

        // Get pending courses for approval
        $pendingCourses = Course::query()
                                ->select(["$c.*", "$u->FULLNAME as instructor_name", "$cat->NAME as category_name"])
                                ->table($c)
                                ->leftJoin($u, $c->INSTRUCTOR_ID, '=', $u->ID)
                                ->leftJoin($cat, $c->CATEGORY_ID, '=', $cat->ID)
                                ->where($c->STATUS, 'pending')
                                ->orderBy($c->CREATED_AT, 'DESC')
                                ->limit(10)
                                ->get();

        $pendingCourses = array_map(fn($c) => $c->toArray(), $pendingCourses);

        // Get recent users
        $recentUsers = User::query()
                           ->orderBy($u->CREATED_AT, 'DESC')
                           ->limit(10)
                           ->get();

        $recentUsers = array_map(fn($u) => $u->toArray(), $recentUsers);

        $viewModel = new AdminDashboardViewModel(
            title:            "Admin Dashboard - Feetcode",
            totalUsers:       $totalUsers,
            totalStudents:    $totalStudents,
            totalInstructors: $totalInstructors,
            totalCourses:     $totalCourses,
            totalEnrollments: $totalEnrollments,
            pendingCourses:   $pendingCourses,
            recentUsers:      $recentUsers
        );

        $this->render('admin/dashboard', $viewModel, true);
    }
}
