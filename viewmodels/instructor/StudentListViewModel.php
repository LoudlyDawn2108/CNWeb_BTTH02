<?php

namespace ViewModels\Instructor;

use Lib\ViewModel;

class StudentListViewModel extends ViewModel {
    public function __construct(
        public string $title,
        public array $students,
        public ?array $course = null
    ) {}
}