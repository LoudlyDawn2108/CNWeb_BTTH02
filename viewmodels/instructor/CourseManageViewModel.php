<?php
namespace ViewModels\Instructor;

use Functional\Collection;
use Lib\ViewModel;

class CourseManageViewModel extends ViewModel {
    public object $course;
    public Collection $lessons;
    public object $stats;

    public function __construct(object $courseData, Collection $lessonsCollection) {
        parent::__construct();
        $this->course = $courseData;

        $this->lessons = $lessonsCollection->map(function($l) {
            return (object)[
                'id' => $l['id'],
                'title' => $l['title'],
                'content' => $l['content'] ?? '',
                'video_url' => $l['video_url'] ?? '',
                'order' => $l['order'] ?? 0,
                'material_count' => $l['material_count'] ?? 0
            ];
        });

        $this->stats = (object)[
            'totalLessons' => $this->lessons->count(),
            'totalMaterials' => $this->lessons->reduce(
                fn($sum, $lesson) => $sum + $lesson->material_count,
                0
            ),
            'hasVideo' => $this->lessons->contains(
                fn($lesson) => !empty($lesson->video_url)
            )
        ];
    }

    public function getLessonsWithMaterials(): Collection {
        return $this->lessons->filter(
            fn($lesson) => $lesson->material_count > 0
        );
    }

    public function getLessonsWithVideo(): Collection {
        return $this->lessons->filter(
            fn($lesson) => !empty($lesson->video_url)
        );
    }

    public function getFirstLesson(): ?object {
        return $this->lessons->first();
    }
}
