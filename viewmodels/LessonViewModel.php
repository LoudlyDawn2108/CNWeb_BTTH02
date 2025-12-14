<?php

namespace ViewModels;

use Lib\ViewModel;
use Models\Course;
use Models\Lesson;
use Models\Material;
use Models\Enrollment;

class CourseView extends Course {
    public ?string $category_name = null;
    public ?string $instructor_name = null;
}

class LessonView extends Lesson {
    public ?int $material_count = null;
}

class MaterialView extends Material {
    // Additional properties if needed
}

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