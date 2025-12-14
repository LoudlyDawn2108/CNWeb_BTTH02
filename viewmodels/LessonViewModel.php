<?php

namespace ViewModels;

require_once __DIR__ . '/StudentViewModels.php';

use Lib\ViewModel;
use Models\Enrollment;

class LessonViewModel extends ViewModel {
    /**
     * @param string $title
     * @param CourseView $course
     * @param LessonView $lesson
     * @param LessonView[] $lessons
     * @param MaterialView[] $materials
     * @param Enrollment $enrollment
     * @param LessonView|null $nextLesson
     * @param LessonView|null $prevLesson
     */
    public function __construct(
        public string      $title,
        public CourseView  $course,
        public LessonView  $lesson,
        public array       $lessons,
        public array       $materials,
        public Enrollment  $enrollment,
        public ?LessonView $nextLesson = null,
        public ?LessonView $prevLesson = null
    ) {
        parent::__construct();
    }
}