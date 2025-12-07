<?php

namespace ViewModels;

use Lib\ViewModel;

class AdminDashboardViewModel extends ViewModel
{
    public function __construct(
        public string $title,
        public int $totalUsers,
        public int $totalStudents,
        public int $totalInstructors,
        public int $totalCourses,
        public int $totalEnrollments,
        public array $pendingCourses,
        public array $recentUsers
    ) {}
}

class AdminUsersViewModel extends ViewModel
{
    public function __construct(
        public string $title,
        public array $users,
        public array $roleStats,
        public int $currentPage,
        public int $totalPages,
        public int $totalUsers,
        public string $search,
        public string $roleFilter,
        public string $statusFilter
    ) {}
}

class AdminCategoriesViewModel extends ViewModel
{
    public function __construct(
        public string $title,
        public array $categories,
        public array $stats,
        public int $currentPage,
        public int $totalPages,
        public string $search
    ) {}
}
