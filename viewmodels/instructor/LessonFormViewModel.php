<?php
namespace ViewModels\Instructor;

use Functional\Collection;
use Functional\Option;
use Lib\ViewModel;

class LessonFormViewModel extends ViewModel {
    public string $pageTitle;
    public string $actionUrl;
    public int $courseId;
    public Option $lesson;
    public Collection $materials;

    public function __construct(int $courseId, Option $lesson, ?Collection $materials = null) {
        parent::__construct();
        $this->courseId = $courseId;
        $this->lesson = $lesson->map(fn($l) => (object)$l);
        $this->materials = $materials ?? Collection::make([]);

        $this->lesson->match(
            function($lessonData) {
                $this->pageTitle = 'Sửa bài học: ' . $lessonData->title;
                $this->actionUrl = '/instructor/lessons/' . $lessonData->id . '/update';
            },
            function() {
                $this->pageTitle = 'Thêm bài học mới';
                $this->actionUrl = '/instructor/courses/' . $this->courseId . '/lessons/store';
            }
        );
    }

    public function isEditMode(): bool {
        return $this->lesson->match(
            fn($l) => true,
            fn() => false
        );
    }

    public function getLessonValue(string $field, $default = '') {
        return $this->lesson->match(
            fn($l) => $l->$field ?? $default,
            fn() => $default
        );
    }

    public function hasMaterials(): bool {
        return !$this->materials->isEmpty();
    }

    public function getMaterialsCount(): int {
        return $this->materials->count();
    }
}
