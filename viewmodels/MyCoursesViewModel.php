<?php

namespace ViewModels;

use Lib\ViewModel;

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
