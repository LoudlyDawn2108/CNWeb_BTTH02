<?php

namespace ViewModels;

require_once __DIR__ . '/StudentViewModels.php';

use Lib\ViewModel;
use Models\Enrollment;

class CourseProgressViewModel extends ViewModel {
    /**
     * @param string $title
     * @param CourseView $course
     * @param LessonView[] $lessons
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