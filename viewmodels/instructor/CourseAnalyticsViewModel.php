<?php

namespace ViewModels\Instructor;

use Functional\Collection;
use Lib\ViewModel;

/**
 * ViewModel cho trang thống kê khóa học
 */
class CourseAnalyticsViewModel extends ViewModel
{
    public object $course;
    public Collection $lessons;
    public object $stats;
    public Collection $enrollments;

    public function __construct(
        object $courseData,
        Collection $lessonsCollection,
        Collection $enrollmentsCollection
    ) {
        parent::__construct();

        $this->course = $courseData;

        // Map lessons
        $this->lessons = $lessonsCollection->map(function ($l) {
            return (object)[
                'id' => $l['id'],
                'title' => $l['title'],
                'video_url' => $l['video_url'] ?? '',
                'order' => $l['order'] ?? 0,
                'material_count' => $l['material_count'] ?? 0
            ];
        });

        // Map enrollments
        $this->enrollments = $enrollmentsCollection->map(function ($e) {
            return (object)[
                'id' => $e['id'],
                'student_id' => $e['student_id'],
                'student_name' => $e['student_name'] ?? 'Unknown',
                'enrolled_date' => $e['enrolled_date'],
                'status' => $e['status'] ?? 'active',
                'progress' => $e['progress'] ?? 0
            ];
        });

        // Tính toán thống kê
        $this->stats = $this->calculateStats();
    }

    private function calculateStats(): object
    {
        // Thống kê học viên
        $totalEnrollments = $this->enrollments->count();
        $activeStudents = $this->enrollments->filter(fn($e) => $e->status === 'active')->count();
        $completedStudents = $this->enrollments->filter(fn($e) => $e->status === 'completed')->count();
        $droppedStudents = $this->enrollments->filter(fn($e) => $e->status === 'dropped')->count();

        // Tiến độ trung bình
        $avgProgress = $totalEnrollments > 0
            ? $this->enrollments->reduce(fn($sum, $e) => $sum + $e->progress, 0) / $totalEnrollments
            : 0;

        // Phân bố tiến độ
        $progressDistribution = [
            'not_started' => $this->enrollments->filter(fn($e) => $e->progress == 0)->count(),
            'in_progress' => $this->enrollments->filter(fn($e) => $e->progress > 0 && $e->progress < 50)->count(),
            'halfway' => $this->enrollments->filter(fn($e) => $e->progress >= 50 && $e->progress < 100)->count(),
            'completed' => $this->enrollments->filter(fn($e) => $e->progress == 100)->count()
        ];

        // Thống kê nội dung
        $totalLessons = $this->lessons->count();
        $lessonsWithVideo = $this->lessons->filter(fn($l) => !empty($l->video_url))->count();
        $totalMaterials = $this->lessons->reduce(fn($sum, $l) => $sum + $l->material_count, 0);

        // Doanh thu
        $price = $this->course->price ?? 0;
        $totalRevenue = $price * $totalEnrollments;
        $activeRevenue = $price * $activeStudents;

        return (object)[
            // Học viên
            'totalEnrollments' => $totalEnrollments,
            'activeStudents' => $activeStudents,
            'completedStudents' => $completedStudents,
            'droppedStudents' => $droppedStudents,

            // Tiến độ
            'averageProgress' => round($avgProgress, 1),
            'progressDistribution' => $progressDistribution,

            // Nội dung
            'totalLessons' => $totalLessons,
            'lessonsWithVideo' => $lessonsWithVideo,
            'totalMaterials' => $totalMaterials,

            // Doanh thu
            'totalRevenue' => $totalRevenue,
            'totalRevenueFormatted' => number_format($totalRevenue) . ' đ',
            'priceFormatted' => $price == 0 ? 'Miễn phí' : number_format($price) . ' đ'
        ];
    }

    /**
     * Lấy enrollment gần đây (5 mới nhất)
     */
    public function getRecentEnrollments(): Collection
    {
        return new Collection(array_slice($this->enrollments->toArray(), 0, 5));
    }

    /**
     * Lấy top students (tiến độ cao nhất)
     */
    public function getTopStudents(): Collection
    {
        $sorted = $this->enrollments->toArray();
        usort($sorted, fn($a, $b) => $b->progress - $a->progress);
        return new Collection(array_slice($sorted, 0, 5));
    }
}
