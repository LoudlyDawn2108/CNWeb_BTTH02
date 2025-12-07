<?php
/**
 * Course Model
 * Handles all course-related database operations
 */
namespace Models;
use Lib\Model;
use PDO;
use Functional\Option;

require_once __DIR__ . '/../config/Database.php';

class CourseTable {
    public function __toString(): string {
        return 'courses';
    }
    public string $ID = 'courses.id';
    public string $TITLE = 'courses.title';
    public string $DESCRIPTION = 'courses.description';
    public string $INSTRUCTOR_ID = 'courses.instructor_id';
    public string $CATEGORY_ID = 'courses.category_id';
    public string $PRICE = 'courses.price';
    public string $DURATION_WEEKS = 'courses.duration_weeks';
    public string $LEVEL = 'courses.level';
    public string $IMAGE = 'courses.image';
    public string $STATUS = 'courses.status';
    public string $CREATED_AT = 'courses.created_at';
    public string $UPDATED_AT = 'courses.updated_at';
}

class Course extends Model {
    protected ?string $table = 'courses';

    public int $id;
    public string $title;
    public ?string $description = null;
    public int $instructor_id;
    public int $category_id;
    public float $price = 0.00;
    public int $duration_weeks = 1;
    public string $level = 'Beginner';
    public ?string $image = null;
    public string $status = 'pending';
    public ?string $created_at = null;
    public ?string $updated_at = null;

    private $conn;

    // 2. Không cần khai báo fillable nếu lib\Model chưa hỗ trợ lọc
    // Nhưng cứ để đây để sau này nâng cấp base model
    protected array $fillable = [
        'title', 'description', 'instructor_id',
        'category_id', 'level', 'price'
    ];

    public static function getWithDetails($id) {
        $sql = "SELECT c.*, 
                       u.fullname as instructor_name, 
                       cat.name as category_name
                FROM courses c
                LEFT JOIN users u ON c.instructor_id = u.id
                LEFT JOIN categories cat ON c.category_id = cat.id
                WHERE c.id = :id";

        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute(['id' => $id]);

        // Trả về Object Course hoặc null
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $instance = new static();
            $instance->fill($result); // Đổ dữ liệu vào model
            return $instance;
        }
        return null;
    }

    /**
     * Lấy danh sách khóa học của Giảng viên (Kèm thống kê)
     */
    public static function getByInstructor($instructorId) {
        $sql = "SELECT c.*, 
                       COUNT(DISTINCT e.id) as enrollment_count
                FROM courses c
                LEFT JOIN enrollments e ON c.id = e.course_id
                WHERE c.instructor_id = :id
                GROUP BY c.id
                ORDER BY c.created_at DESC";

        $stmt = self::getConnection()->prepare($sql);
        $stmt->execute(['id' => $instructorId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Trả về mảng array thường
    }
    public function getById(int $id): Option {
        // 1. Gọi hàm find có sẵn của lib\Model (Trả về Object hoặc Null)
        $course = self::find($id);

        // 2. Bọc kết quả vào Option
        // Nếu có course -> Some(course), nếu null -> None
        return Option::fromNullable($course);
    }

    /**
     * Toggle Publish (Hàm nghiệp vụ riêng)
     */
//    public function togglePublish(int $id): Option {
//        try {
//            // 1. Tìm khóa học
//            $course = self::find($id);
//
//            if (!$course) {
//                return Option::none();
//            }
//
//            // 2. Đổi trạng thái (0 -> 1 hoặc 1 -> 0)
//            // Lưu ý: Cần ép kiểu về int để đảm bảo logic
//            $course->is_published = $course->is_published == 1 ? 0 : 1;
//
//            // 3. Lưu lại
//            if ($course->save()) {
//                return Option::some(true); // Trả về Option để Controller match() được
//            }
//
//            return Option::none();
//        } catch (\Throwable $e) {
//            return Option::none();
//        }
//    }
    public function createCourse(array $data): Option {
        try {
            // 1. Gọi hàm create của lib\Model (trả về Object)
            $course = self::create($data);

            // 2. Bọc kết quả vào Option để Controller dùng được hàm ->match()
            // Trả về ID của khóa học mới tạo
            return Option::some($course->id);
        } catch (\Throwable $e) {
            return Option::none();
        }
    }

    /**
     * Wrapper cho hàm Update
     */
    public function updateCourse(int $id, array $data): Option {
        try {
            // 1. Tìm bản ghi (dùng hàm find của lib\Model)
            $course = self::find($id);

            if (!$course) return Option::none();

            // 2. Gán dữ liệu mới
            foreach ($data as $key => $value) {
                $course->$key = $value;
            }

            // 3. Lưu (dùng hàm save của lib\Model)
            $course->save();

            return Option::some(true);
        } catch (\Throwable $e) {
            return Option::none();
        }
    }
    public function deleteCourse(int $id): Option {
        try {
            // 1. Tìm bản ghi (dùng hàm find của cha)
            $course = self::find($id);

            if (!$course) return Option::none();

            // 2. Xóa (dùng hàm delete của cha)
            // Hàm cha trả về bool, nhưng ta cần trả về Option
            if ($course->delete()) {
                return Option::some(true);
            }
            return Option::none();
        } catch (\Throwable $e) {
            return Option::none();
        }
    }
}