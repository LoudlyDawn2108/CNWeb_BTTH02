<?php
namespace Controllers;

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
        // ViewModel của bạn cần Option, nên ở đây dùng Option::none() là đúng
        $viewModel = new LessonFormViewModel(
            (int)$courseId,
            Option::none()
        );
        $this->render('instructor/lessons/create', $viewModel);
    }

    // 2. Lưu bài học (Sửa lại theo Lib\Model)
    public function store($courseId) {
        $data = [
            'course_id' => $courseId,
            'title' => $_POST['title'],
            'content' => $_POST['content'],
            'video_url' => $_POST['video_url'] ?? '',
            'order' => $_POST['order'] ?? 0
        ];

        try {
            // Lib\Model::create trả về Object, không phải Result
            Lesson::create($data);

            $this->setSuccessMessage('Bài học đã được tạo');
            $this->redirect("/instructor/course/$courseId/manage");
        } catch (\Exception $e) {
            $this->setErrorMessage('Lỗi: ' . $e->getMessage());
            $this->redirect("/instructor/courses/$courseId/lessons/create");
        }
    }

    // 3. Form sửa bài học
    public function edit($id): void
    {
        // Lib\Model::find trả về Object hoặc Null
        $lesson = Lesson::find($id);

        if (!$lesson) {
            $this->setErrorMessage('Không tìm thấy bài học');
            $this->redirect('/instructor/dashboard');
        }

        // Lấy tài liệu (Giả sử Material model cũng kế thừa Lib\Model)
        // Lưu ý: Lib\Model::all() trả về array, bạn cần ép sang Collection nếu ViewModel cần

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

    // 4. Update bài học (Sửa lại theo Lib\Model)
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
            $this->redirect("/instructor/course/{$lesson->course_id}/manage");
        } else {
            $this->setErrorMessage('Lỗi khi lưu');
            $this->redirect("/instructor/lesson/$id/edit");
        }
    }

    // ... (Các hàm upload/delete material sửa tương tự: dùng find() -> delete())
}