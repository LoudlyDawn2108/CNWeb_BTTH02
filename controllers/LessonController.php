<?php

use JetBrains\PhpStorm\NoReturn;
use Lib\Controller;
use Models\Lesson;
use Models\Course;
use Models\Material;
use ViewModels\Instructor\LessonFormViewModel;
use Functional\Collection; // Dùng Collection thì ok
use Functional\Option;     // Dùng Option cho ViewModel thì ok

class LessonController extends Controller {

    // 1. Form tạo bài học
    public function create($courseId): void 
{
    $this->user()->match(
        function ($user) use ($courseId) {
            // Check user có phải instructor của course này không
            $course = Course::find($courseId);
            if (!$course || $course->instructor_id != $user['id']) {
                http_response_code(403);
                die('Không có quyền');
            }
            
            $viewModel = new LessonFormViewModel(
                (int)$courseId,
                Option::none()
            );
            $this->render('instructor/lessons/create', $viewModel);
        },
        fn() => $this->redirect('/auth/login')
    );
}

    // 2. Lưu bài học (với validation)
    public function store($courseId): void
    {
        $this->user()->match(
            function ($user) use ($courseId) {
                // Check quyền sở hữu
                $course = Course::find($courseId);
                if (!$course || $course->instructor_id != $user['id']) {
                    http_response_code(403);
                    die('Không có quyền');
                }

                // Create ViewModel and bind POST data
                $viewModel = new LessonFormViewModel(
                    (int)$courseId,
                    Option::none()
                );
                $viewModel->handleRequest($_POST);

                // If validation fails, re-render form with errors
                if (!$viewModel->modelState->isValid) {
                    $this->render('instructor/lessons/create', $viewModel);
                    return;
                }

                // Validation passed - proceed to save
                try {
                    Lesson::create([
                        'course_id' => $courseId,
                        'title' => $viewModel->title,
                        'content' => $viewModel->content,
                        'video_url' => $viewModel->video_url,
                        'order' => $viewModel->order
                    ]);

                    $this->setSuccessMessage('Bài học đã được tạo');
                    $this->redirect("/instructor/courses/$courseId/manage");
                } catch (\Exception $e) {
                    $viewModel->modelState->addError('title', 'Không thể tạo bài học: ' . $e->getMessage());
                    $this->render('instructor/lessons/create', $viewModel);
                }
            },
            fn() => $this->redirect('/auth/login')
        );
    }

    // 3. Form sửa bài học
    public function edit($id): void
    {
        // lib\Model::find trả về Object hoặc Null
        $lesson = Lesson::find($id);

        if (!$lesson) {
            $this->setErrorMessage('Không tìm thấy bài học');
            $this->redirect('/instructor/dashboard');
        }

        // Lấy tài liệu (Giả sử Material model cũng kế thừa lib\Model)
        // Lưu ý: lib\Model::all() trả về array, bạn cần ép sang Collection nếu ViewModel cần

        $materialModel = new Material();

        // Nếu Material Model chưa viết hàm getByLesson theo chuẩn mới thì dùng query builder
        $materialsRaw = Material::query()->where('lesson_id', $id)->get();
        $materials = Collection::make($materialsRaw);

        // Chuyển Object Lesson thành Array hoặc Option tùy ViewModel yêu cầu
        // Giả sử ViewModel nhận Option<Object>
        $viewModel = new LessonFormViewModel(
            (int)$lesson->course_id,
            Option::some($lesson), // Bọc object vào Option
            $materials
        );

        try {
            $this->render('instructor/lessons/create', $viewModel);
        } catch (\Exception $e) {
            $this->setErrorMessage('Lỗi hiển thị trang: ' . $e->getMessage());
            $this->redirect('/instructor/dashboard');
        }

    }

    // 4. Update bài học (Sửa lại theo lib\Model)
    #[NoReturn]
    public function update($id): void
    {
        $lesson = Lesson::find($id);

        if (!$lesson) {
            $this->setErrorMessage('Không tìm thấy bài học');
            $this->redirect('/instructor/dashboard');
        }

        // ✅ Dùng fill() thay vì gán từng property
        $lesson->fill([
            'title' => $_POST['title'],
            'content' => $_POST['content'],
            'video_url' => $_POST['video_url'] ?? '',
            'order' => $_POST['order'] ?? 0
        ]);

        if ($lesson->save()) {
            $this->setSuccessMessage('Bài học đã được cập nhật');
            $this->redirect("/instructor/courses/{$lesson->course_id}/manage");
        } else {
            $this->setErrorMessage('Lỗi khi lưu');
            $this->redirect("/instructor/lessons/$id/edit");
        }
    }
    #[NoReturn]
    public function delete($id): void
    {
        // 1. Tìm bài học
        $lesson = Lesson::find($id);

        if (!$lesson) {
            $this->setErrorMessage('Không tìm thấy bài học');
            $this->redirect('/instructor/dashboard');
        }

        // Lưu lại course_id để redirect về đúng chỗ sau khi xóa
        $courseId = $lesson->course_id;

        try {
            // 2. Xóa bài học (Hàm delete của Model sẽ tự lo xóa record)
            // Lưu ý: Nếu database có ràng buộc khóa ngoại (ON DELETE CASCADE),
            // các tài liệu (materials) của bài này sẽ tự động bị xóa theo.
            if ($lesson->delete()) {
                $this->setSuccessMessage('Đã xóa bài học thành công');
            } else {
                $this->setErrorMessage('Không thể xóa bài học này');
            }
        } catch (\Exception $e) {
            $this->setErrorMessage('Lỗi: ' . $e->getMessage());
        }

        // 3. Quay về trang quản lý khóa học
        $this->redirect("/instructor/courses/$courseId/manage");
    }

    // 5. Upload tài liệu
    #[NoReturn]
    public function uploadMaterial($lessonId): void
    {
        $lesson = Lesson::find($lessonId);

        if (!$lesson) {
            $this->setErrorMessage('Không tìm thấy bài học');
            $this->redirect('/instructor/dashboard');
        }

        if (empty($_FILES['file']['name'])) {
            $this->setErrorMessage('Vui lòng chọn file');
            $this->redirect("/instructor/lessons/$lessonId/edit");
        }

        $file = $_FILES['file'];
        $allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip'];

        if (!in_array($file['type'], $allowedTypes)) {
            $this->setErrorMessage('Chỉ chấp nhận file PDF, Word, Zip');
            $this->redirect("/instructor/lessons/$lessonId/edit");
        }

        // Tạo tên file unique
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = time() . '_' . uniqid() . '.' . $extension;
        $uploadPath = BASE_PATH . '/assets/uploads/materials/';

        // Tạo thư mục nếu chưa có
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        if (move_uploaded_file($file['tmp_name'], $uploadPath . $filename)) {
            Material::create([
                'lesson_id' => $lessonId,
                'filename' => $file['name'],
                'file_path' => $filename,
                'file_type' => $file['type']
    //                'file_size' => $file['size']
            ]);

            $this->setSuccessMessage('Tài liệu đã được tải lên');
        } else {
            $this->setErrorMessage('Lỗi khi tải file');
        }

        $this->redirect("/instructor/lessons/$lessonId/edit");
    }

// 6. Xóa tài liệu
    #[NoReturn]
    public function deleteMaterial($materialId): void
    {
        $material = Material::find($materialId);

        if (!$material) {
            $this->setErrorMessage('Không tìm thấy tài liệu');
            $this->redirect('/instructor/dashboard');
        }

        // Lấy lesson_id trước khi xóa
        $lessonId = $material->lesson_id;

        // Xóa file vật lý
        $filePath = BASE_PATH . '/assets/uploads/materials/' . $material->file_path;
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Xóa record trong DB
        if ($material->delete()) {
            $this->setSuccessMessage('Tài liệu đã được xóa');
        } else {
            $this->setErrorMessage('Lỗi khi xóa tài liệu');
        }

        $this->redirect("/instructor/lessons/$lessonId/edit");
    }

}