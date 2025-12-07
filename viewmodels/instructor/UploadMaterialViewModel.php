<?php

namespace ViewModels\Instructor;

use Lib\ViewModel;

class UploadMaterialsViewModel extends ViewModel {
    public function __construct(
        public string $title,
        public array $course,
        public array $lessons
    ) {}
}