<?php

namespace ViewModels;

use Models\Lesson;
use Models\Material;

class LessonView extends Lesson {
    public ?int $material_count = null;
}

class MaterialView extends Material {
    // Additional properties if needed
}
