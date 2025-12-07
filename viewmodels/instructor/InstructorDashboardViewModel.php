<?php
namespace ViewModels\Instructor;
use Functional\Collection;
use Lib\ViewModel;

class InstructorDashboardViewModel extends ViewModel {
    public Collection $courses;
    public int $totalCourses;
    public int $totalStudents;
    public string $totalRevenue; // Thêm biến tổng doanh thu toàn hệ thống

    public function __construct(Collection $rawCourses) {

        parent::__construct();

        $this->totalCourses = $rawCourses->count();
        $revenueSum = 0; // Biến tạm để tính tổng

        // Map dữ liệu
        $this->courses = $rawCourses->map(function($c) {
            $c = (object)$c;

            // ... (Logic status giữ nguyên) ...
            $statusClass = 'secondary';
            $statusLabel = 'Nháp';
            if (isset($c->status)) {
                if ($c->status === 'approved') {
                    $statusClass = 'success';
                    $statusLabel = 'Đã duyệt';
                } elseif ($c->status === 'pending') {
                    $statusClass = 'warning';
                    $statusLabel = 'Chờ duyệt';
                } elseif ($c->status === 'rejected') {
                    $statusClass = 'danger';
                    $statusLabel = 'Bị từ chối';
                }
            }

            $enrollmentCount = $c->enrollment_count ?? 0;
            $price = $c->price ?? 0; // Lấy giá tiền gốc (chưa format)

            // --- LOGIC TÍNH DOANH THU TỪNG KHÓA ---
            $revenue = $price * $enrollmentCount;

            return (object)[
                'id' => $c->id,
                'title' => $c->title,
                'image' => !empty($c->image) ? '/assets/uploads/courses/' . $c->image : '/assets/img/default-course.png',
                'price' => $price == 0 ? 'Miễn phí' : number_format($price) . ' đ',

                'statusLabel' => $statusLabel,
                'statusClass' => $statusClass,
                'enrollmentCount' => $enrollmentCount,
                'hasStudents' => $enrollmentCount > 0,

                // Thêm thuộc tính doanh thu đã format
                'revenueFormatted' => number_format($revenue) . ' đ',
                'revenueRaw' => $revenue // Lưu số thô để tính tổng bên ngoài nếu cần
            ];
        });

        // Tính tổng sinh viên
        $this->totalStudents = $this->courses->reduce(
            fn($sum, $c) => $sum + $c->enrollmentCount,
            0
        );

        // Tính tổng doanh thu tất cả khóa học
        $totalRev = $this->courses->reduce(
            fn($sum, $c) => $sum + $c->revenueRaw,
            0
        );
        $this->totalRevenue = number_format($totalRev) . ' đ';

    }
}