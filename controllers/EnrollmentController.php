<?php
/**
 * Enrollment Controller
 * Handles student course enrollments and progress tracking
 */

require_once __DIR__ . '/../models/Enrollment.php';
require_once __DIR__ . '/../viewmodels/StudentViewModels.php';
require_once __DIR__ . '/../models/Course.php';

use Lib\Controller;

use Functional\Option;
use Functional\Result;

class EnrollmentController extends Controller {
    private $enrollmentModel;
    private $courseModel;

    public function __construct() {
        $this->enrollmentModel = new Enrollment();
        $this->courseModel = new Course();
    }

    /**
     * Enroll in a course
     */
    public function enroll() {
        $this->requireRole(0); // 0 = Student role

        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/courses'); // Only allow POST requests
        }

        $courseId = intval($this->getPost('course_id', 0)); // Get course ID from POST data
        $studentId = $_SESSION['user_id'];

        if ($courseId <= 0) { // Invalid course ID
            $this->setErrorMessage('Khóa học không hợp lệ.');
            $this->redirect('/courses');
        }
        $this->courseModel->getById($courseId)->match( // Fetch course details
            function($course) use ($studentId, $courseId){
                if ($course['status'] !== 'approved') { // Course not approved
                    $this->setErrorMessage('Khóa học chưa được phê duyệt.');
                    $this->redirect('/courses');
                }
                if ($this->enrollmentModel->isEnrolled($studentId, $courseId)) { // Already enrolled
                    $this->setErrorMessage('Bạn đã đăng ký khóa học này rồi.');
                    $this->redirect('/course/' . $courseId); // Redirect back to course page
                }
                $this->enrollmentModel->enroll($studentId, $courseId)->match(
                    function($enrollmentId) { // Success
                        $this->setSuccessMessage('Đăng ký khóa học thành công!');
                        $this->redirect('/student/my-courses'); // Redirect to student's courses
                    },
                    function() use ($courseId) { // Failure
                        $this->setErrorMessage('Có lỗi xảy ra. Vui lòng thử lại.');
                        $this->redirect('/course/' . $courseId);
                    }
                );
            },
            function() { // Course not found
                $this->setErrorMessage('Khóa học không tồn tại.'); 
                $this->redirect('/courses'); 
            }
        );
    }

    /**
     * Unenroll from a course
     */
    public function unenroll() {
        $this->requireRole(0); 

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { // Only allow POST requests
            $this->redirect('/student/my-courses');
        }

        $courseId = intval($this->getPost('course_id', 0)); // Get course ID from POST data
        $studentId = $_SESSION['user_id'];

        $this->enrollmentModel->unenroll($studentId, $courseId)->match(
            function() {
                $this->setSuccessMessage('Đã hủy đăng ký khóa học.'); // Success
            },
            function() {
                $this->setErrorMessage('Có lỗi xảy ra. Vui lòng thử lại.'); // Failure
            }
        );
        $this->redirect('/student/my-courses'); // Redirect to student's courses
    }

    /**
     * Display student dashboard
     */
    public function studentDashboard() {
        
    }

    /**
     * Display student's enrolled courses
     */
    public function myCourses() {

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