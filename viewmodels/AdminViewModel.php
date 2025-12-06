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
