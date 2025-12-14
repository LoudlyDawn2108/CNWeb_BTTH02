<?php

namespace ViewModels;

use Lib\ViewModel;
use Models\Enrollment;
use Models\Lesson;

class CourseProgressViewModel extends ViewModel {
    /**
     * @param string $title
     * @param CourseView $course
     * @param Lesson[] $lessons
     * @param Enrollment $enrollment
     */
    public function __construct(
        public string     $title,
        public CourseView $course,
        public array      $lessons,
        public Enrollment $enrollment
    ) {
        parent::__construct();
    }
}