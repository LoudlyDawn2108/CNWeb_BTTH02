<?php
namespace ViewModels\Instructor;

use Functional\Collection;
use Functional\Option;
use Lib\ViewModel;
use Lib\Validation\Attributes\Required;
use Lib\Validation\Attributes\MinLength;
use Lib\Validation\Attributes\DisplayName;

class CourseFormViewModel extends ViewModel {
    // ===== PAGE PROPERTIES (không validate) =====
    public string $pageTitle;
    public string $actionUrl;
    public Collection $categories;
    public Option $course;
    public Collection $levels;

    // ===== FORM INPUT PROPERTIES (có validate) =====
    #[Required("Vui lòng nhập {field}")]
    #[MinLength(5, "{field} phải có ít nhất 5 ký tự")]
    #[DisplayName("Tên khóa học")]
    public string $title = '';

    #[Required("Vui lòng nhập {field}")]
    #[MinLength(20, "{field} phải có ít nhất 20 ký tự")]
    #[DisplayName("Mô tả")]
    public string $description = '';

    #[Required("Vui lòng chọn {field}")]
    #[DisplayName("Danh mục")]
    public ?string $category_id = null;

    #[DisplayName("Cấp độ")]
    public string $level = 'Beginner';

    #[DisplayName("Giá")]
    public float $price = 0;

    #[DisplayName("Thời lượng")]
    public int $duration_weeks = 1;

    public function __construct(Collection $categories, Option $course) {
        parent::__construct();
        $this->categories = $categories->map(fn($c) => (object)$c);
        $this->course = $course->map(fn($c) => (object)$c);

        $this->levels = Collection::make(['Beginner', 'Intermediate', 'Advanced']);

        $this->course->match(
            function($courseData) {
                $this->pageTitle = 'Chỉnh sửa khóa học: ' . $courseData->title;
                $this->actionUrl = '/instructor/courses/' . $courseData->id . '/update';
                // Pre-fill form values from existing course
                $this->title = $courseData->title ?? '';
                $this->description = $courseData->description ?? '';
                $this->category_id = $courseData->category_id ?? null;
                $this->level = $courseData->level ?? 'Beginner';
                $this->price = $courseData->price ?? 0;
                $this->duration_weeks = $courseData->duration_weeks ?? 1;
            },
            function() {
                $this->pageTitle = 'Tạo khóa học mới';
                $this->actionUrl = '/instructor/courses/store';
            }
        );
    }

    /**
     * Get value for form field - prioritizes current input over course data
     */
    public function getCourseValue(string $field, $default = '') {
        // If form was submitted and has value, use it
        if (property_exists($this, $field) && !empty($this->$field)) {
            return $this->$field;
        }
        // Otherwise get from existing course
        return $this->course->match(
            fn($c) => $c->$field ?? $default,
            fn() => $default
        );
    }

    public function isEditMode(): bool {
        return $this->course->match(
            fn($c) => true,
            fn() => false
        );
    }

    public function getCategoryOptions(): Collection {
        $currentCategoryId = $this->category_id ?? $this->course->match(
            fn($c) => $c->category_id ?? null,
            fn() => null
        );

        return $this->categories->map(function($cat) use ($currentCategoryId) {
            return (object)[
                'id' => $cat->id,
                'name' => $cat->name,
                'selected' => $currentCategoryId == $cat->id
            ];
        });
    }
}
