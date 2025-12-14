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
require_once __DIR__ . '/../viewmodels/EnrollmentViewModels.php';
require_once __DIR__ . '/../models/Course.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Lesson.php';
require_once __DIR__ . '/../models/Material.php';

use Lib\Controller;
use ViewModels\StudentDashboardViewModel;
use ViewModels\MyCoursesViewModel;
use ViewModels\CourseProgressViewModel;
use ViewModels\LessonViewModel;
use ViewModels\EnrollViewModel;
use ViewModels\UnenrollViewModel;
use ViewModels\EnrollmentView;
use Models\Course;
use Models\CourseTable;
use Models\Enrollment;
use Models\EnrollmentTable;
use Models\User;
use Models\UserTable;
use Models\Lesson;
use Models\LessonTable;
use Models\Material;
use Models\MaterialTable;
use Models\CategoryTable;

class EnrollmentController extends Controller {

    /**
     * Enroll in a course
     */
    public function enroll(): void {
        $this->requireRole(User::ROLE_STUDENT);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/courses');
        }

        $viewModel = new EnrollViewModel();
        $viewModel->handleRequest($_POST);

        if (!$viewModel->modelState->isValid) {
            $this->setErrorMessage('Khóa học không hợp lệ.');
            $this->redirect('/courses');
        }

        $courseId = $viewModel->course_id;
        $studentId = $_SESSION['user_id'];

        $course = Course::find($courseId);
        if ($course) {
            if ($course->status !== 'approved') {
                $this->setErrorMessage('Khóa học chưa được phê duyệt.');
                $this->redirect('/courses');
            }

            $e = new EnrollmentTable();
            $existing = Enrollment::query()
                ->where($e->STUDENT_ID, $studentId)
                ->where($e->COURSE_ID, $courseId)
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
    }

    /**
     * Unenroll from a course
     */
    public function unenroll(): void {
        $this->requireRole(User::ROLE_STUDENT);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/student/my-courses');
        }

        $viewModel = new UnenrollViewModel();
        $viewModel->handleRequest($_POST);

        if (!$viewModel->modelState->isValid) {
            $this->setErrorMessage('Khóa học không hợp lệ.');
            $this->redirect('/student/my-courses');
        }

        $courseId = $viewModel->course_id;
        $studentId = $_SESSION['user_id'];

        $e = new EnrollmentTable();
        try {
            Enrollment::query()
                ->where($e->STUDENT_ID, $studentId)
                ->where($e->COURSE_ID, $courseId)
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
    public function studentDashboard(): void {
        $this->requireRole(User::ROLE_STUDENT);

        $studentId = $_SESSION['user_id'];

        $e = new EnrollmentTable();
        $c = new CourseTable();
        $cat = new CategoryTable();
        $u = new UserTable();

        $enrollments = Enrollment::query()
            ->select(["$e.*", "$c->TITLE as course_title", "$c->IMAGE as course_image", 
                      "$c->LEVEL as level", "$c->DURATION_WEEKS as duration_weeks", "$cat->NAME as category_name",
                      "$u->FULLNAME as instructor_name"])
            ->table($e)
            ->leftJoin($c, $e->COURSE_ID, '=', $c->ID)
            ->leftJoin($cat, $c->CATEGORY_ID, '=', $cat->ID)
            ->leftJoin($u, $c->INSTRUCTOR_ID, '=', $u->ID)
            ->where($e->STUDENT_ID, $studentId)
            ->orderBy($e->ENROLLED_DATE, 'DESC')
            ->get(EnrollmentView::class);
        
        $stats = [
            'total_courses' => count($enrollments),
            'completed' => count(array_filter($enrollments, fn($e) => $e->status === 'completed')),
            'in_progress' => count(array_filter($enrollments, fn($e) => $e->status === 'active')),
            'avg_progress' => count($enrollments) > 0
                ? round(array_sum(array_map(fn($e) => $e->progress, $enrollments)) / count($enrollments))
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
    public function myCourses(): void {
        $this->requireRole(User::ROLE_STUDENT);

        $studentId = $_SESSION['user_id'];
        
        $e = new EnrollmentTable();
        $c = new CourseTable();
        $cat = new CategoryTable();
        $u = new UserTable();

        $enrollments = Enrollment::query()
            ->select(["$e.*", "$c->TITLE as course_title", "$c->IMAGE as course_image", 
                      "$c->LEVEL as level", "$c->DURATION_WEEKS as duration_weeks", "$cat->NAME as category_name",
                      "$u->FULLNAME as instructor_name"])
            ->table($e)
            ->leftJoin($c, $e->COURSE_ID, '=', $c->ID)
            ->leftJoin($cat, $c->CATEGORY_ID, '=', $cat->ID)
            ->leftJoin($u, $c->INSTRUCTOR_ID, '=', $u->ID)
            ->where($e->STUDENT_ID, $studentId)
            ->orderBy($e->ENROLLED_DATE, 'DESC')
            ->get(EnrollmentView::class);

        $viewModel = new MyCoursesViewModel(
            title: 'Khóa học của tôi - FeetCode',
            enrollments: $enrollments
        );

        $this->render('student/my_courses', $viewModel);
    }

    /**
     * Display course progress with lessons
     */
    public function courseProgress($courseId): void {
        $this->requireRole(User::ROLE_STUDENT);

        $studentId = $_SESSION['user_id'];
        
        $e = new EnrollmentTable();
        $enrollment = Enrollment::query()
            ->where($e->STUDENT_ID, $studentId)
            ->where($e->COURSE_ID, $courseId)
            ->first();
            
        if ($enrollment) {
            $course = Course::find($courseId);
            if ($course) {
                $l = new LessonTable();
                $lessons = Lesson::query()
                    ->where($l->COURSE_ID, $courseId)
                    ->orderBy($l->ORDER, 'ASC')
                    ->get();

                $viewModel = new CourseProgressViewModel(
                    title: 'Tiến độ học tập - ' . $course->title,
                    course: $course,
                    lessons: $lessons,
                    enrollment: $enrollment
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
    public function viewLesson($lessonId): void {
        $this->requireRole(User::ROLE_STUDENT);

        $studentId = $_SESSION['user_id'];
        $lesson = Lesson::find($lessonId);
        
        if ($lesson) {
            $e = new EnrollmentTable();
            $enrollment = Enrollment::query()
                ->where($e->STUDENT_ID, $studentId)
                ->where($e->COURSE_ID, $lesson->course_id)
                ->first();
                
            if ($enrollment) {
                $course = Course::find($lesson->course_id);
                if ($course) {
                    $l = new LessonTable();
                    $lessons = Lesson::query()
                        ->where($l->COURSE_ID, $lesson->course_id)
                        ->orderBy($l->ORDER, 'ASC')
                        ->get();
                    
                    $m = new MaterialTable();
                    $materials = Material::query()
                        ->where($m->LESSON_ID, $lessonId)
                        ->orderBy($m->UPLOADED_AT, 'DESC')
                        ->get();

                    $nextLesson = Lesson::query()
                        ->where($l->COURSE_ID, $lesson->course_id)
                        ->where($l->ORDER, '>', $lesson->order)
                        ->orderBy($l->ORDER, 'ASC')
                        ->first();
                        
                    $prevLesson = Lesson::query()
                        ->where($l->COURSE_ID, $lesson->course_id)
                        ->where($l->ORDER, '<', $lesson->order)
                        ->orderBy($l->ORDER, 'DESC')
                        ->first();

                    // Update progress
                    $totalLessons = count($lessons);
                    $currentIndex = -1;
                    foreach($lessons as $idx => $lessonItem) {
                        if ($lessonItem->id == $lesson->id) {
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
                        course: $course,
                        lesson: $lesson,
                        lessons: $lessons,
                        materials: $materials,
                        enrollment: $enrollment,
                        nextLesson: $nextLesson,
                        prevLesson: $prevLesson
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
}