<?php
namespace ViewModels\Instructor;
use Functional\Collection;

class InstructorDashboardViewModel {
    public Collection $courses; // Đổi type thành Collection
    public int $totalCourses;
    public int $totalStudents;

    public function __construct(Collection $rawCourses) {
        $this->totalCourses = $rawCourses->count();
        $this->totalStudents = 0;

        // Dùng map thay vì foreach để biến đổi dữ liệu
        $this->courses = $rawCourses->map(function($c) {
            $c = (object)$c; // Ép kiểu

            // Tính tổng sinh viên luôn ở đây (nếu có logic)
            // $this->totalStudents += ... (Lưu ý: scope biến trong closure)

            return (object)[
                'id' => $c->id,
                'title' => $c->title,
                'image' => !empty($c->image) ? '/assets/uploads/courses/' . $c->image : '/assets/img/default-course.png',
                'price' => $c->price == 0 ? 'Miễn phí' : number_format($c->price) . ' đ',
                'statusLabel' => $c->status == 'approved' ? 'Đã duyệt' : 'Chờ duyệt',
                'statusClass' => $c->status == 'approved' ? 'success' : 'warning',
                'enrollmentCount' => $c->enrollment_count ?? 0
            ];
        });

        // Tính tổng sinh viên sau khi map
        $this->totalStudents = $this->courses->reduce(fn($sum, $c) => $sum + $c->enrollmentCount, 0);
    }
}