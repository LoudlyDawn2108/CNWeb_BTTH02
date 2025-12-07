<?php
namespace ViewModels\Instructor;

use Functional\Collection;
use Functional\Option;
use Lib\ViewModel;

class CourseFormViewModel extends ViewModel {
    public string $title;
    public string $actionUrl;
    public Collection $categories;
    public Option $course;
    public Collection $levels;

    public function __construct(Collection $categories, Option $course) {
        parent::__construct();
        $this->categories = $categories->map(fn($c) => (object)$c);
        $this->course = $course->map(fn($c) => (object)$c);

        $this->levels = Collection::make(['Beginner', 'Intermediate', 'Advanced']);

        $this->course->match(
            function($courseData) {
                $this->title = 'Chỉnh sửa khóa học: ' . $courseData->title;
                $this->actionUrl = '/instructor/courses/' . $courseData->id . '/update';
            },
            function() {
                $this->title = 'Tạo khóa học mới';
                $this->actionUrl = '/instructor/courses/store';
            }
        );
    }

    public function getCourseValue(string $field, $default = '') {
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
        return $this->categories->map(function($cat) {
            $selected = $this->course->match(
                fn($c) => $c->category_id == $cat->id,
                fn() => false
            );

            return (object)[
                'id' => $cat->id,
                'name' => $cat->name,
                'selected' => $selected
            ];
        });
    }
}
