<?php

namespace ViewModels;

use Lib\ViewModel;

class CourseProgressViewModel extends ViewModel {
    public function __construct(
        public string $title,
        public array $course,
        public array $lessons,
        public array $enrollment
    ) {
        parent::__construct();
    }
}