<?php

namespace ViewModels;

use Lib\ViewModel;
use Models\Category;
use Models\Course;
use Models\Lesson;

class CourseView extends Course {
    public ?string $category_name;
    public ?string $instructor_name;
    public ?string $enrollment_count;
    public ?string $lesson_count;
}

class CourseListViewModel extends ViewModel {
    /**
     * @param CourseView[] $courses
     * @param Category[] $categories
     */
    public function __construct(
        public string $title,
        public array  $courses,
        public array  $categories,
        public array  $filters,
        public int    $currentPage,
        public int    $totalPages,
        public array  $levels,
    ) {
        parent::__construct();
    }
}

class CourseDetailViewModel extends ViewModel {
    /**
     * @param string $title
     * @param CourseView $course
     * @param Lesson[] $lessons
     * @param bool $isEnrolled
     * @param ?array $enrollment
     * @param CourseView[] $relatedCourses
     * @param ?array $currentUser
     */
    public function __construct(
        public string     $title,
        public CourseView $course,
        public array      $lessons,
        public bool       $isEnrolled,
        public ?array      $enrollment = [],
        public array      $relatedCourses,
        public ?array      $currentUser = [],
    ) {
        parent::__construct();
    }
}

class CourseSearchViewModel extends ViewModel {
    /**
     * @param string $title
     * @param CourseView[] $courses
     * @param string $keyword
     * @param Category[] $categories
     */
    public function __construct(
        public string $title,
        public array  $courses,
        public string $keyword,
        public array  $categories,
    ) {
        parent::__construct();
    }
}
