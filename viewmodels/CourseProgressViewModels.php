<?php

namespace ViewModels;

use Lib\ViewModel;
use Models\Course;
use Models\Lesson;
use Models\Enrollment;

class CourseView extends Course {
    public ?string $category_name = null;
    public ?string $instructor_name = null;
}

class LessonView extends Lesson {
    public ?int $material_count = null;
}

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