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

        $this->render('admin/users', $viewModel);
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
