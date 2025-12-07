<?php

use Functional\Collection;
use Functional\Option;
use Functional\Result;
use JetBrains\PhpStorm\NoReturn;
use Lib\Controller;
use Models\Course;
use Models\Category;
use Models\Lesson;
use ViewModels\Instructor\CourseFormViewModel;
use ViewModels\Instructor\CourseManageViewModel;
use ViewModels\Instructor\InstructorDashboardViewModel;

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


    public function createForm(): void
    {
        $categoryModel = new Category();
        $categories = $categoryModel->getAll();

        $viewModel = new CourseFormViewModel(
            $categories,
            Option::none()
        );
        $this->render('instructor/courses/create', $viewModel);
    }

    #[NoReturn]
    public function storeCourse() {
        error_log("=== START CREATE COURSE ===");

        $this->user()->match(
            function($user) {
                error_log("User ID: " . $user['id']);

                $courseModel = new Course();
                $imageResult = $this->handleImageUpload($_FILES['image'] ?? null);

                $data = [
                    'title' => $this->getPost('title'),
                    'description' => $this->getPost('description'),
                    'image' => $imageResult->getOrElse(''),
                    'instructor_id' => $user['id'],
                    'category_id' => $this->getPost('category_id'),
                    'level' => $this->getPost('level'),
                    'price' => $this->getPost('price')
                ];

                error_log("Data to save: " . print_r($data, true));

                $courseModel->createCourse($data)->match(
                    function($courseId) {
                        error_log("✅ SUCCESS: Course ID = $courseId");
                        $this->setSuccessMessage('Khóa học đã được tạo thành công');
                        $this->redirect('/instructor/dashboard');
                    },
                    function() {
                        error_log("❌ FAILED: Cannot create course");
                        $this->setErrorMessage('Không thể tạo khóa học');
                        $this->redirect('/instructor/courses/create');
                    }
                );
            },
            function() {
                error_log("❌ User not logged in");
                $this->redirect('/auth/login');
            }
        );
    }



    public function editForm($id)
    {
        $this->user()->match(
            function ($user) use ($id) {
                $courseModel = new Course();
                $categoryModel = new Category();

                $courseModel->getById($id)->match(
                    function ($course) use ($user, $categoryModel) {
                        if ($course->instructor_id != $user['id']) {
                            http_response_code(403);
                            die('Không có quyền truy cập');
                        }

                        $categories = $categoryModel->getAll();
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
}
