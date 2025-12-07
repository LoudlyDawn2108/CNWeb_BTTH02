<?php

namespace ViewModels;

use Lib\ViewModel;

class StudentDashboardViewModel extends ViewModel {
    public function __construct(
        public string $title,
        public array $enrollments,
        public array $recentCourses,
        public array $stats
    ){
        parent::__construct();
    }
}
