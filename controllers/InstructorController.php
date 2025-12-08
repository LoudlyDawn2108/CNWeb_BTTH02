<?php

require_once __DIR__ . '/../models/Course.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Lesson.php';
require_once __DIR__ . '/../models/Enrollment.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../viewmodels/instructor/CourseFormViewModel.php';
require_once __DIR__ . '/../viewmodels/instructor/CourseManageViewModel.php';
require_once __DIR__ . '/../viewmodels/instructor/InstructorDashboardViewModel.php';
require_once __DIR__ . '/../viewmodels/instructor/StudentListViewModel.php';
require_once __DIR__ . '/../viewmodels/instructor/UploadMaterialsViewModel.php';

use Functional\Collection;
use Functional\Option;
use Functional\Result;
use JetBrains\PhpStorm\NoReturn;
use Lib\Controller;
use Models\Course;
use Models\Category;
use Models\Lesson;
use Models\Enrollment;
use Models\User;
use Models\CourseTable;
use Models\EnrollmentTable;
use Models\UserTable;
use ViewModels\Instructor\CourseFormViewModel;
use ViewModels\Instructor\CourseManageViewModel;
use ViewModels\Instructor\InstructorDashboardViewModel;
use ViewModels\Instructor\StudentListViewModel;
use ViewModels\Instructor\UploadMaterialsViewModel;

class InstructorController extends Controller
{

    public function dashboard(): void
    {
        $this->user()->match(
            function ($user) {
                $courseModel = new Course();
                // Lấy dữ liệu thô (Array) từ Model
                $rawCourses = $courseModel->getByInstructor($user['id']);

                // DEBUG
                error_log("Raw courses: " . print_r($rawCourses, true));

                // 2. BIẾN HÌNH: Ép kiểu Array thành Collection
                // (Giả sử class Collection của bạn có hàm static make())
                $coursesCollection = Collection::make($rawCourses);

                // Bây giờ mới ném vào ViewModel được
                $viewModel = new InstructorDashboardViewModel($coursesCollection);

                $this->render('instructor/dashboard', $viewModel);
            },
            function () {
                $this->redirect('/auth/login');
            }
        );
    }

    public function myCourses(): void
    {
        $this->user()->match(
            function ($user) {
                $courseModel = new Course();
                $rawCourses = $courseModel->getByInstructor($user['id']);

                $coursesCollection = Collection::make($rawCourses);
                $viewModel = new InstructorDashboardViewModel($coursesCollection);

                // Render view riêng cho trang "Khóa học của tôi"
                $this->render('instructor/courses/index', $viewModel);
            },
            function () {
                $this->redirect('/auth/login');
            }
        );
    }


    public function createCourse(): void
    {
        $this->user()->match(
            function ($user) {
                $categories = Collection::make(Category::all());

                $viewModel = new CourseFormViewModel(
                    $categories,
                    Option::none()
                );
                $this->render('instructor/courses/create', $viewModel);
            },
            fn() => $this->redirect('/auth/login')
        );
    }

    #[NoReturn]
    public function storeCourse(): void
    {
        $this->user()->match(
            function($user) {
                $categories = Collection::make(Category::all());
                
                // Create ViewModel and bind POST data
                $viewModel = new CourseFormViewModel(
                    $categories,
                    Option::none()
                );
                $viewModel->handleRequest($_POST);

                // If validation fails, re-render form with errors
                if (!$viewModel->modelState->isValid) {
                    $this->render('instructor/courses/create', $viewModel);
                    return;
                }

                // Validation passed - proceed to save
                $courseModel = new Course();
                $imageResult = $this->handleImageUpload($_FILES['image'] ?? null);

                $data = [
                    'title' => $viewModel->title,
                    'description' => $viewModel->description,
                    'image' => $imageResult->getOrElse(''),
                    'instructor_id' => $user['id'],
                    'category_id' => $viewModel->category_id,
                    'level' => $viewModel->level,
                    'price' => $viewModel->price,
                    'duration_weeks' => $viewModel->duration_weeks
                ];

                $courseModel->createCourse($data)->match(
                    function($courseId) {
                        $this->setSuccessMessage('Khóa học đã được tạo thành công');
                        $this->redirect('/instructor/dashboard');
                    },
                    function() use ($viewModel) {
                        $viewModel->modelState->addError('title', 'Không thể tạo khóa học. Vui lòng thử lại.');
                        $this->render('instructor/courses/create', $viewModel);
                    }
                );
            },
            fn() => $this->redirect('/auth/login')
        );
    }



    public function editCourse($id): void
    {
        $this->user()->match(
            function ($user) use ($id) {
                $courseModel = new Course();

                $courseModel->getById($id)->match(
                    function ($course) use ($user) {
                        if ($course->instructor_id != $user['id']) {
                            http_response_code(403);
                            die('Không có quyền truy cập');
                        }

                        $categories = Collection::make(Category::all());
                        $viewModel = new CourseFormViewModel(
                            $categories,
                            Option::some($course)
                        );
                        $this->render('instructor/courses/create', $viewModel);
                    },
                    function () {
                        $this->setErrorMessage('Không tìm thấy khóa học');
                        $this->redirect('/instructor/dashboard');
                    }
                );
            },
            fn() => $this->redirect('/auth/login')
        );
    }

    public function updateCourse($id): void
    {
        $this->user()->match(
            function ($user) use ($id) {
                $courseModel = new Course();

                $courseModel->getById($id)->match(
                    function ($course) use ($user, $courseModel, $id) {
                        if ($course->instructor_id != $user['id']) {
                            http_response_code(403);
                            die('Không có quyền truy cập');
                        }

                        $imageResult = $this->handleImageUpload($_FILES['thumbnail'] ?? null);

                        $data = [
                            'title' => $this->getPost('title'),
                            'description' => $this->getPost('description'),
                            'category_id' => $this->getPost('category_id'),
                            'level' => $this->getPost('level'),
                            'price' => $this->getPost('price')
                        ];

                        // Xử lý upload ảnh mới
                        // Kiểm tra xem có file upload không
                        if (!empty($_FILES['image']['name'])) {
                            $imageResult = $this->handleImageUpload($_FILES['image']);

                            $imageResult->match(
                                function($newImage) use (&$data, $course) {
                                    // Xóa ảnh cũ nếu tồn tại
                                    if (!empty($course->image)) {
                                        $oldImagePath = BASE_PATH . '/assets/uploads/courses/' . $course->image;
                                        if (file_exists($oldImagePath)) {
                                            unlink($oldImagePath);
                                        }
                                    }
                                    // Gán ảnh mới
                                    $data['image'] = $newImage;
                                },
                                function() {
                                    // Upload thất bại, giữ nguyên ảnh cũ
                                    error_log('Failed to upload new image');
                                }
                            );
                        }

                        $courseModel->updateCourse($id, $data)->match( // ← Đổi thành updateCourse
                            function () use ($id) {
                                $this->setSuccessMessage('Khóa học đã được cập nhật');
                                $this->redirect("/instructor/dashboard");
                            },
                            function () use ($id) {
                                $this->setErrorMessage('Không thể cập nhật khóa học');
                                $this->redirect("/instructor/courses/$id/edit");
                            }
                        );
                    },
                    function () {
                        $this->setErrorMessage('Không tìm thấy khóa học');
                        $this->redirect('/instructor/dashboard');
                    }
                );
            },
            fn() => $this->redirect('/auth/login')
        );
    }

    public function manageCourse($id)
    {
        $this->user()->match(
            function ($user) use ($id) {
                $courseModel = new Course();
                $lessonModel = new Lesson();

                $courseModel->getById($id)->match(
                    function ($course) use ($user, $lessonModel) {
                        if ($course->instructor_id != $user['id']) {
                            http_response_code(403);
                            die('Không có quyền truy cập');
                        }

                        $lessons = $lessonModel->getByCourse($course->id);

                        $viewModel = new CourseManageViewModel($course, $lessons);
                        $this->render('instructor/courses/manage', $viewModel);
                    },
                    function () {
                        $this->setErrorMessage('Không tìm thấy khóa học');
                        $this->redirect('/instructor/dashboard');
                    }
                );
            },
            fn() => $this->redirect('/auth/login')
        );
    }

    public function deleteCourse($id): void
    {
        $this->user()->match(
            function ($user) use ($id) {
                $courseModel = new Course();

                $courseModel->getById($id)->match(
                    function ($course) use ($user, $courseModel, $id) {
                        if ($course->instructor_id != $user['id']) {
                            http_response_code(403);
                            die('Không có quyền truy cập');
                        }

                        if ($courseModel->deleteCourse($id)) { // ← Đổi thành deleteCourse
                            $this->setSuccessMessage('Đã xóa khóa học');
                        } else {
                            $this->setErrorMessage('Không thể xóa khóa học');
                        }
                        $this->redirect('/instructor/dashboard');
                    },
                    function () {
                        $this->setErrorMessage('Không tìm thấy khóa học');
                        $this->redirect('/instructor/dashboard');
                    }
                );
            },
            fn() => $this->redirect('/auth/login')
        );
    }

//    public function togglePublish($id) {
//        $this->user()->match(
//            function($user) use ($id) {
//                $courseModel = new Course();
//
//                $courseModel->getById($id)->match(
//                    function($course) use ($user, $courseModel, $id) {
//                        if ($course['instructor_id'] != $user['id']) {
//                            http_response_code(403);
//                            die('Không có quyền truy cập');
//                        }
//
//                        $courseModel->togglePublish($id)->match(
//                            function() use ($id) {
//                                $this->setSuccessMessage('Đã cập nhật trạng thái khóa học');
//                                $this->redirect("/instructor/course/$id/manage");
//                            },
//                            function() use ($id) {
//                                $this->setErrorMessage('Không thể cập nhật');
//                                $this->redirect("/instructor/course/$id/manage");
//                            }
//                        );
//                    },
//                    function() {
//                        $this->setErrorMessage('Không tìm thấy khóa học');
//                        $this->redirect('/instructor/dashboard');
//                    }
//                );
//            },
//            fn() => $this->redirect('/auth/login')
//        );
//    }

    private function handleImageUpload(?array $file): Result
    {
        return Result::try(function () use ($file) {
            if (empty($file['name'])) {
                throw new \Exception('No file uploaded');
            }

            $targetDir = BASE_PATH . '/assets/uploads/courses/';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            $fileName = time() . '_' . basename($file['name']);

            if (!move_uploaded_file($file['tmp_name'], $targetDir . $fileName)) {
                throw new \Exception('Failed to upload image');
            }

            return $fileName;
        });
    }

    /**
     * List enrolled students
     */
    public function listStudents($courseId = null): void
    {
        $this->requireRole(User::ROLE_INSTRUCTOR);

        $instructorId = $_SESSION['user_id'];
        
        $e = new EnrollmentTable();
        $u = new UserTable();
        $c = new CourseTable();

        if ($courseId) {
            $course = Course::find($courseId);
            if ($course) {
                if ($course->instructor_id != $instructorId) {
                     $_SESSION['error'] = 'Bạn không có quyền xem.';
                     $this->redirect('/instructor/my-courses');
                }
                
                $students = Enrollment::query()
                    ->select([
                        $e . '.*', 
                        $u->FULLNAME . ' as student_name', 
                        $u->EMAIL . ' as student_email', 
                        $u->AVATAR
                    ])
                    ->leftJoin($u, $e->STUDENT_ID, '=', $u->ID)
                    ->where($e->COURSE_ID, $courseId)
                    ->orderBy($e->ENROLLED_DATE, 'DESC')
                    ->get();
                $students = array_map(fn($s) => $s->toArray(), $students);
                
                $this->renderStudents($students, $course->toArray(), 'Danh sách học viên - ' . $course->title);
            } else {
                $_SESSION['error'] = 'Khóa học không tồn tại.';
                $this->redirect('/instructor/my-courses');
            }
        } else {
            $students = Enrollment::query()
                ->select([
                    $e . '.*', 
                    $c->TITLE . ' as course_title', 
                    $u->FULLNAME . ' as student_name', 
                    $u->EMAIL . ' as student_email'
                ])
                ->join($c, $e->COURSE_ID, '=', $c->ID)
                ->leftJoin($u, $e->STUDENT_ID, '=', $u->ID)
                ->where($c->INSTRUCTOR_ID, $instructorId)
                ->orderBy($e->ENROLLED_DATE, 'DESC')
                ->get();
            $students = array_map(fn($s) => $s->toArray(), $students);
            
            $this->renderStudents($students, null, 'Tất cả học viên');
        }
    }

    private function renderStudents($students, $course, $pageTitle): void
    {
        $viewModel = new StudentListViewModel(
            title: $pageTitle . ' - FeetCode',
            students: $students,
            course: $course
        );
        $this->render('instructor/students/list', $viewModel);
    }

    /**
     * View material upload page
     */
    public function uploadMaterials($courseId) {
        $this->requireRole(User::ROLE_INSTRUCTOR);

        $course = Course::find($courseId);
        
        if ($course) {
             if ($course->instructor_id != $_SESSION['user_id']) {
                $_SESSION['error'] = 'Bạn không có quyền quản lý.';
                $this->redirect('/instructor/my-courses');
            }

            $lessons = Lesson::query()
                ->where('course_id', $courseId)
                ->orderBy('`order`', 'ASC')
                ->get();
            $lessons = array_map(fn($l) => $l->toArray(), $lessons);

            $viewModel = new UploadMaterialsViewModel(
                title: 'Tải tài liệu - ' . $course->title,
                course: $course->toArray(),
                lessons: $lessons
            );
            unset($_SESSION['success'], $_SESSION['error']);

            $this->render('instructor/materials/upload', $viewModel);
        } else {
             $_SESSION['error'] = 'Khóa học không tồn tại.';
             $this->redirect('/instructor/my-courses');
        }
    }
}
