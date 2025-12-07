<?php

namespace ViewModels;

use Lib\ViewModel;

class LessonViewModel extends ViewModel {
    public function __construct(
        public string $title,
        public array $course,
        public array $lesson,
        public array $lessons,
        public array $materials,
        public array $enrollment,
        public ?array $nextLesson = null,
        public ?array $prevLesson = null
    ) {
        parent::__construct();
    }
}