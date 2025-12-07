<?php
namespace ViewModels\Instructor;

use Functional\Collection;
use Functional\Option;
use Lib\ViewModel;
use Lib\Validation\Attributes\Required;
use Lib\Validation\Attributes\MinLength;
use Lib\Validation\Attributes\DisplayName;

class LessonFormViewModel extends ViewModel {
    // ===== PAGE PROPERTIES (không validate) =====
    public string $pageTitle;
    public string $actionUrl;
    public int $courseId;
    public Option $lesson;
    public Collection $materials;

    // ===== FORM INPUT PROPERTIES (có validate) =====
    #[Required("Vui lòng nhập {field}")]
    #[MinLength(3, "{field} phải có ít nhất 3 ký tự")]
    #[DisplayName("Tên bài học")]
    public string $title = '';

    #[DisplayName("Nội dung")]
    public string $content = '';

    #[DisplayName("Link Video")]
    public string $video_url = '';

    #[DisplayName("Thứ tự")]
    public int $order = 0;

    public function __construct(int $courseId, Option $lesson, ?Collection $materials = null) {
        parent::__construct();
        $this->courseId = $courseId;
        $this->lesson = $lesson->map(fn($l) => (object)$l);
        $this->materials = $materials ?? Collection::make([]);

        $this->lesson->match(
            function($lessonData) {
                $this->pageTitle = 'Sửa bài học: ' . $lessonData->title;
                $this->actionUrl = '/instructor/lessons/' . $lessonData->id . '/update';
                // Pre-fill form values
                $this->title = $lessonData->title ?? '';
                $this->content = $lessonData->content ?? '';
                $this->video_url = $lessonData->video_url ?? '';
                $this->order = $lessonData->order ?? 0;
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
        // If form was submitted and has value, use it
        if (property_exists($this, $field) && !empty($this->$field)) {
            return $this->$field;
        }
        // Otherwise get from existing lesson
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
