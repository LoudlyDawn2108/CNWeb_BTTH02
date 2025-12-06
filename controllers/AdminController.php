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
        $this->requireRole(User::ROLE_ADMIN);
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
