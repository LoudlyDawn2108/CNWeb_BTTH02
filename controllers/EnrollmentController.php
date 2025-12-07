<?php
/**
 * Enrollment Controller
 * Handles student course enrollments and progress tracking
 */

require_once __DIR__ . '/../models/Enrollment.php';
require_once __DIR__ . '/../viewmodels/StudentDashboardViewModel.php';
require_once __DIR__ . '/../viewmodels/MyCoursesViewModel.php';
require_once __DIR__ . '/../viewmodels/CourseProgressViewModels.php';
require_once __DIR__ . '/../viewmodels/LessonViewModel.php';
require_once __DIR__ . '/../models/Course.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Lesson.php';
require_once __DIR__ . '/../models/Material.php';

use Lib\Controller;
use ViewModels\StudentDashboardViewModel;
use ViewModels\MyCoursesViewModel;
use ViewModels\CourseProgressViewModel;
use ViewModels\LessonViewModel;
use Models\Course;
use Models\Enrollment;
use Models\User;
use Models\Lesson;
use Models\Material;

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

        $viewModel = new MyCoursesViewModel(
            title: 'Khóa học của tôi - FeetCode',
            enrollments: $enrollments
        );

        $this->render('student/my_courses', $viewModel);
    }

    /**
     * Display course progress with lessons
     */
    public function courseProgress($courseId) {
        $this->requireRole(User::ROLE_STUDENT);

        $studentId = $_SESSION['user_id'];
        
        $enrollment = Enrollment::query()
            ->where('student_id', $studentId)
            ->where('course_id', $courseId)
            ->first();
            
        if ($enrollment) {
            $course = Course::find($courseId);
            if ($course) {
                $lessons = Lesson::query()
                    ->where('course_id', $courseId)
                    ->orderBy('`order`', 'ASC')
                    ->get();
                $lessons = array_map(fn($l) => $l->toArray(), $lessons);

                $viewModel = new CourseProgressViewModel(
                    title: 'Tiến độ học tập - ' . $course->title,
                    course: $course->toArray(),
                    lessons: $lessons,
                    enrollment: $enrollment->toArray()
                );

                $this->render('student/course_progress', $viewModel);
            } else {
                $this->setErrorMessage('Khóa học không tồn tại.');
                $this->redirect('/student/my-courses');
            }
        } else {
            $this->setErrorMessage('Bạn chưa đăng ký khóa học này.');
            $this->redirect('/student/my-courses');
        }
    }

    /**
     * View lesson content
     */
    public function viewLesson($lessonId) {
        $this->requireRole(User::ROLE_STUDENT);

        $studentId = $_SESSION['user_id'];
        $lesson = Lesson::find($lessonId);
        
        if ($lesson) {
             $enrollment = Enrollment::query()
                ->where('student_id', $studentId)
                ->where('course_id', $lesson->course_id)
                ->first();
                
            if ($enrollment) {
                $course = Course::find($lesson->course_id);
                if ($course) {
                    $lessons = Lesson::query()
                        ->where('course_id', $lesson->course_id)
                        ->orderBy('`order`', 'ASC')
                        ->get();
                    $lessons = array_map(fn($l) => $l->toArray(), $lessons);
                    
                    $materials = Material::query()
                        ->where('lesson_id', $lessonId)
                        ->orderBy('uploaded_at', 'DESC')
                        ->get();
                    $materials = array_map(fn($m) => $m->toArray(), $materials);

                    $nextLesson = Lesson::query()
                        ->where('course_id', $lesson->course_id)
                        ->where('`order`', '>', $lesson->order)
                        ->orderBy('`order`', 'ASC')
                        ->first();
                        
                    $prevLesson = Lesson::query()
                        ->where('course_id', $lesson->course_id)
                        ->where('`order`', '<', $lesson->order)
                        ->orderBy('`order`', 'DESC')
                        ->first();

                    // Update progress
                    $totalLessons = count($lessons);
                    $currentIndex = -1;
                    foreach($lessons as $idx => $l) {
                        if ($l['id'] == $lesson->id) {
                            $currentIndex = $idx;
                            break;
                        }
                    }
                    
                    $newProgress = round((($currentIndex + 1) / $totalLessons) * 100);

                    if ($newProgress > $enrollment->progress) {
                        $enrollment->progress = $newProgress;
                        if ($newProgress >= 100) {
                            $enrollment->status = Enrollment::STATUS_COMPLETED;
                        }
                        $enrollment->save();
                    }

                    $viewModel = new LessonViewModel(
                        title: $lesson->title . ' - ' . $course->title,
                        course: $course->toArray(),
                        lesson: $lesson->toArray(),
                        lessons: $lessons,
                        materials: $materials,
                        enrollment: $enrollment->toArray(),
                        nextLesson: $nextLesson ? $nextLesson->toArray() : null,
                        prevLesson: $prevLesson ? $prevLesson->toArray() : null
                    );

                    $this->render('student/lesson', $viewModel);
                }
            } else {
                $this->setErrorMessage('Bạn chưa đăng ký khóa học này.');
                $this->redirect('/courses/' . $lesson->course_id);
            }
        } else {
            http_response_code(404);
            echo 'Bài học không tồn tại.';
            exit;
        }
    }

    /**
     * Update progress manually
     */
    public function updateProgress() {
        $this->requireRole(User::ROLE_STUDENT);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/student/my-courses');
        }

        $enrollmentId = intval($this->getPost('enrollment_id', 0));
        $progress = intval($this->getPost('progress', 0));

        $enrollment = Enrollment::find($enrollmentId);

        if ($enrollment) {
            if ($enrollment->student_id != $_SESSION['user_id']) {
                $this->setErrorMessage('Không có quyền truy cập.');
                $this->redirect('/student/my-courses');
            }

            $progress = max(0, min(100, $progress));
            $enrollment->progress = $progress;
            if ($progress >= 100) {
                $enrollment->status = Enrollment::STATUS_COMPLETED;
            } else {
                // Keep current status if not completed, or set to active? 
                // Usually just check completion.
                if ($enrollment->status == Enrollment::STATUS_COMPLETED && $progress < 100) {
                    $enrollment->status = Enrollment::STATUS_ACTIVE;
                }
            }
            $enrollment->save();

            $this->setSuccessMessage('Đã cập nhật tiến độ.');
            $this->redirect('/student/course/' . $enrollment->course_id . '/progress');
        } else {
             $this->setErrorMessage('Dữ liệu không tồn tại.');
             $this->redirect('/student/my-courses');
        }
        exit;
    }
}