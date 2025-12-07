<?php

namespace ViewModels;

use Lib\ViewModel;

class MyCoursesViewModel extends ViewModel {
    public function __construct(
        public string $title,
        public array $enrollments
    ) {
        parent::__construct();
    }
}
