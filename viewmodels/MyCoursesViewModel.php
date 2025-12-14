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

class MyCoursesViewModel extends ViewModel {
    /**
     * @param string $title
     * @param EnrollmentView[] $enrollments Array of enrollment data with course details
     */
    public function __construct(
        public string $title,
        public array  $enrollments
    ) {
        parent::__construct();
    }
}
