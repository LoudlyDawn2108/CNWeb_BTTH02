<?php

namespace ViewModels;

use Models\Course;
use Models\Lesson;
use Models\Material;

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
