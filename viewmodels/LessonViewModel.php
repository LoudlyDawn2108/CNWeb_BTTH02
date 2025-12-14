<?php

namespace ViewModels;

use Lib\ViewModel;
use Models\Enrollment;
use Models\Material;
use Models\Lesson;

class LessonViewModel extends ViewModel {
    /**
     * @param string $title
     * @param CourseView $course
     * @param Lesson $lesson
     * @param Lesson[] $lessons
     * @param Material[] $materials
     * @param Enrollment $enrollment
     * @param Lesson|null $nextLesson
     * @param Lesson|null $prevLesson
     */
    public function __construct(
        public string      $title,
        public CourseView  $course,
        public Lesson  $lesson,
        public array       $lessons,
        public array       $materials,
        public Enrollment  $enrollment,
        public ?Lesson $nextLesson = null,
        public ?Lesson $prevLesson = null
    ) {
        parent::__construct();
    }
}