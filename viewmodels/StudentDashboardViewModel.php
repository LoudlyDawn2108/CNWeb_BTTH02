<?php

namespace ViewModels;

use Lib\ViewModel;
use Models\Enrollment;

class EnrollmentView extends Enrollment {
    public ?string $course_title = null;
    public ?string $course_image = null;
    public ?string $level = null;
    public ?int $duration_weeks = null;
    public ?string $category_name = null;
    public ?string $instructor_name = null;
}

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
