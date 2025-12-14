<?php

namespace ViewModels;

use Lib\ViewModel;

class StudentDashboardViewModel extends ViewModel {
    /**
     * @param string $title
     * @param EnrollmentView[] $enrollments Array of all student enrollments with course details
     * @param EnrollmentView[] $recentCourses Array of recent enrolled courses (max 4)
     * @param array $stats Statistics array (total_courses, completed, in_progress, avg_progress)
     */
    public function __construct(
        public string $title,
        public array  $enrollments,
        public array  $recentCourses,
        public array  $stats
    ) {
        parent::__construct();
    }
}
