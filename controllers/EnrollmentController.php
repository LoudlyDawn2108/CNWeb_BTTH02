<?php
/**
 * Enrollment Controller
 * Handles student course enrollments and progress tracking
 */

require_once __DIR__ . '/../models/Enrollment.php';
require_once __DIR__ . '/../viewmodels/StudentDashboardViewModel.php';
require_once __DIR__ . '/../models/Course.php';
require_once __DIR__ . '/../models/User.php';

use Lib\Controller;
use Functional\Option;
use Functional\Result;
use ViewModels\StudentDashboardViewModel;

class EnrollmentController extends Controller {

    public function __construct() {
    }

    /**
     * Enroll in a course
     */
    public function enroll() {
        $this->requireRole(User::ROLE_STUDENT); // Ensure user is a student

        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/courses'); // Only allow POST requests
        }

        $courseId = intval($this->getPost('course_id', 0)); // Get course ID from POST data
        $studentId = $_SESSION['user_id'];

        if ($courseId <= 0) { // Invalid course ID
            $this->setErrorMessage('Khóa học không hợp lệ.');
            $this->redirect('/courses');
        }
        $course = Course::find($courseId);
        if ($course) {
            if ($course->status !== 'approved') {
                $this->setErrorMessage('Khóa học chưa được phê duyệt.');
                $this->redirect('/courses');
            }
            $existing = Enrollment::query()
                ->where('student_id', $studentId)
                ->where('course_id', $courseId)
                ->first();
            if ($existing) {
                $this->setErrorMessage('Bạn đã đăng ký khóa học này rồi.');
                $this->redirect('/course/' . $courseId);
            }

            // Create enrollment
            try {
                Enrollment::create([
                    'course_id' => $courseId,
                    'student_id' => $studentId,
                    'status' => Enrollment::STATUS_ACTIVE,
                    'progress' => 0
                ]);
                $this->setSuccessMessage('Đăng ký khóa học thành công!');
                $this->redirect('/student/my-courses');
            } catch (Exception $e) {
                $this->setErrorMessage('Có lỗi xảy ra. Vui lòng thử lại.');
                $this->redirect('/course/' . $courseId);
            }
            } else {
            $this->setErrorMessage('Khóa học không tồn tại.');
            $this->redirect('/courses');
        }
        exit;
    }

    /**
     * Unenroll from a course
     */
    public function unenroll() {
        $this->requireRole(User::ROLE_STUDENT); // Ensure user is a student

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { // Only allow POST requests
            $this->redirect('/student/my-courses');
        }

        $courseId = intval($this->getPost('course_id', 0)); // Get course ID from POST data
        $studentId = $_SESSION['user_id'];
        try {
            Enrollment::query()
                ->where('student_id', $studentId)
                ->where('course_id', $courseId)
                ->delete();
            $this->setSuccessMessage('Đã hủy đăng ký khóa học.');
        } catch (Exception $e) {
            $this->setErrorMessage('Có lỗi xảy ra. Vui lòng thử lại.');
        }

        $this->redirect('/student/my-courses');
    }

    /**
     * Display student dashboard
     */
    public function studentDashboard() {
        $this->requireRole(User::ROLE_STUDENT);

        $studentId = $_SESSION['user_id'];

        $enrollments = Enrollment::query()
            ->select(['e.*', 'c.title as course_title', 'c.image as course_image', 
                      'c.level', 'c.duration_weeks', 'cat.name as category_name',
                      'u.fullname as instructor_name'])
            ->table('enrollments e')
            ->leftJoin('courses c', 'e.course_id', '=', 'c.id')
            ->leftJoin('categories cat', 'c.category_id', '=', 'cat.id')
            ->leftJoin('users u', 'c.instructor_id', '=', 'u.id')
            ->where('e.student_id', $studentId)
            ->orderBy('e.enrolled_date', 'DESC')
            ->get();
            
        $enrollments = array_map(fn($e) => $e->toArray(), $enrollments);
        
        $stats = [
            'total_courses' => count($enrollments),
            'completed' => count(array_filter($enrollments, fn($e) => $e['status'] === 'completed')),
            'in_progress' => count(array_filter($enrollments, fn($e) => $e['status'] === 'active')),
            'avg_progress' => count($enrollments) > 0
                ? round(array_sum(array_column($enrollments, 'progress')) / count($enrollments))
                : 0
        ];

        $recentCourses = array_slice($enrollments, 0, 4);

        $viewModel = new StudentDashboardViewModel(
            title: 'Student Dashboard - FeetCode',
            enrollments: $enrollments,
            recentCourses: $recentCourses,
            stats: $stats
        );

        $this->render('student/dashboard', $viewModel);
    }

    /**
     * Display student's enrolled courses
     */
    public function myCourses() {
        $this->requireRole(User::ROLE_STUDENT);
    }

    /**
     * Display course progress with lessons
     */
    public function courseProgress($courseId) {

    }

    /**
     * View lesson content
     */
    public function viewLesson($lessonId) {

    }

    /**
     * Update progress manually
     */
    public function updateProgress() {

    }
}