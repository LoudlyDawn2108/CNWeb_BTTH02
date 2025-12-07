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

class AdminCategoryFormViewModel extends ViewModel
{
    public function __construct(
        public string $title,
        public ?array $category,
        public bool $isEdit
    ) {}
}

class AdminStatisticsViewModel extends ViewModel
{
    public function __construct(
        public string $title,
        public array $userStats,
        public array $courseStats,
        public array $enrollmentStats,
        public array $categoryStats,
        public array $topInstructors,
        public array $popularCourses,
        public array $monthlyUsers
    ) {}
}
