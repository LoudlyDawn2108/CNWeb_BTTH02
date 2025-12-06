<?php
/**
 * Enrollment Model
 * Handles all enrollment-related database operations
 */

require_once __DIR__ . '/../config/Database.php';
use Functional\Option;
use Functional\Result;

class Enrollment {
    private $conn;
    private $table = 'enrollments';

    // Status constants
    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_DROPPED = 'dropped';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Get all enrollments
     * @return Result<array>
     */
    public function getAll($limit = 100, $offset = 0): Result {
        return Result::try(function() use ($limit, $offset) {
            $query = "SELECT e.*, c.title as course_title, u.fullname as student_name
                      FROM {$this->table} e
                      LEFT JOIN courses c ON e.course_id = c.id
                      LEFT JOIN users u ON e.student_id = u.id
                      ORDER BY e.enrolled_date DESC
                      LIMIT :limit OFFSET :offset";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        });
    }

    /**
     * Get enrollment by ID
     * @return Option<array>
     */
    public function getById($id): Option {
        return Result::try(function() use ($id) {
            $query = "SELECT e.*, c.title as course_title, u.fullname as student_name
                      FROM {$this->table} e
                      LEFT JOIN courses c ON e.course_id = c.id
                      LEFT JOIN users u ON e.student_id = u.id
                      WHERE e.id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch() ?: null;
        })->match(
            fn($val) => Option::fromNullable($val),
            fn() => Option::none()
        );
    }

    /**
     * Get enrollments by student
     * @return Result<array>
     */
    public function getByStudent($studentId): Result {
        return Result::try(function() use ($studentId) {
            $query = "SELECT e.*, c.title as course_title, c.image as course_image, 
                      c.level, c.duration_weeks, cat.name as category_name,
                      u.fullname as instructor_name
                      FROM {$this->table} e
                      LEFT JOIN courses c ON e.course_id = c.id
                      LEFT JOIN categories cat ON c.category_id = cat.id
                      LEFT JOIN users u ON c.instructor_id = u.id
                      WHERE e.student_id = :student_id
                      ORDER BY e.enrolled_date DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':student_id', $studentId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        });
    }

    /**
     * Get enrollments by course
     * @return Result<array>
     */
    public function getByCourse($courseId): Result {
        return Result::try(function() use ($courseId) {
            $query = "SELECT e.*, u.fullname as student_name, u.email as student_email, u.avatar
                      FROM {$this->table} e
                      LEFT JOIN users u ON e.student_id = u.id
                      WHERE e.course_id = :course_id
                      ORDER BY e.enrolled_date DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':course_id', $courseId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        });
    }

    /**
     * Check if student is enrolled in course
     * @return bool
     */
    public function isEnrolled($studentId, $courseId) {
        return Result::try(function() use ($studentId, $courseId) {
            $query = "SELECT COUNT(*) FROM {$this->table} 
                      WHERE student_id = :student_id AND course_id = :course_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':student_id', $studentId, PDO::PARAM_INT);
            $stmt->bindParam(':course_id', $courseId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        })->match(
            fn($b) => $b,
            fn() => false
        );
    }

    /**
     * Get enrollment by student and course
     * @return Option<array>
     */
    public function getByStudentAndCourse($studentId, $courseId): Option {
        return Result::try(function() use ($studentId, $courseId) {
            $query = "SELECT * FROM {$this->table} 
                      WHERE student_id = :student_id AND course_id = :course_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':student_id', $studentId, PDO::PARAM_INT);
            $stmt->bindParam(':course_id', $courseId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch() ?: null;
        })->match(
            fn($val) => Option::fromNullable($val),
            fn() => Option::none()
        );
    }

    /**
     * Enroll student in course
     * @return Result<int>
     */
    public function enroll($studentId, $courseId): Result {
        if ($this->isEnrolled($studentId, $courseId)) {
            return Result::err('Môn học đã được đăng ký trước đó.');
        }

        return Result::try(function() use ($studentId, $courseId) {
            $query = "INSERT INTO {$this->table} (course_id, student_id, status, progress) 
                      VALUES (:course_id, :student_id, :status, 0)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':course_id', $courseId, PDO::PARAM_INT);
            $stmt->bindParam(':student_id', $studentId, PDO::PARAM_INT);
            $status = self::STATUS_ACTIVE;
            $stmt->bindParam(':status', $status);

            $stmt->execute();
            return $this->conn->lastInsertId();
        });
    }

    /**
     * Update enrollment status
     * @return Result<bool>
     */
    public function updateStatus($id, $status): Result {
        return Result::try(function() use ($id, $status) {
            $query = "UPDATE {$this->table} SET status = :status WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        });
    }

    /**
     * Update progress
     * @return Result<bool>
     */
    public function updateProgress($id, $progress): Result {
        return Result::try(function() use ($id, $progress) {
            $progress = max(0, min(100, $progress));
            $status = $progress >= 100 ? self::STATUS_COMPLETED : self::STATUS_ACTIVE;

            $query = "UPDATE {$this->table} SET progress = :progress, status = :status WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':progress', $progress, PDO::PARAM_INT);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        });
    }

    /**
     * Unenroll student from course
     * @return Result<bool>
     */
    public function unenroll($studentId, $courseId): Result {
        return Result::try(function() use ($studentId, $courseId) {
            $query = "DELETE FROM {$this->table} 
                      WHERE student_id = :student_id AND course_id = :course_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':student_id', $studentId, PDO::PARAM_INT);
            $stmt->bindParam(':course_id', $courseId, PDO::PARAM_INT);
            return $stmt->execute();
        });
    }

    /**
     * Count enrollments
     * @return int
     */
    public function count($filters = []) {
        return Result::try(function() use ($filters) {
            $where = [];
            $params = [];

            if (!empty($filters['course_id'])) {
                $where[] = "course_id = :course_id";
                $params[':course_id'] = $filters['course_id'];
            }

            if (!empty($filters['student_id'])) {
                $where[] = "student_id = :student_id";
                $params[':student_id'] = $filters['student_id'];
            }

            if (!empty($filters['status'])) {
                $where[] = "status = :status";
                $params[':status'] = $filters['status'];
            }

            $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

            $query = "SELECT COUNT(*) FROM {$this->table} {$whereClause}";
            $stmt = $this->conn->prepare($query);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            return $stmt->fetchColumn();
        })->match(
            fn($c) => $c,
            fn() => 0
        );
    }

    /**
     * Get students enrolled by instructor's courses
     * @return Result<array>
     */
    public function getStudentsByInstructor($instructorId): Result {
        return Result::try(function() use ($instructorId) {
            $query = "SELECT e.*, c.title as course_title, u.fullname as student_name, u.email as student_email
                      FROM {$this->table} e
                      INNER JOIN courses c ON e.course_id = c.id
                      LEFT JOIN users u ON e.student_id = u.id
                      WHERE c.instructor_id = :instructor_id
                      ORDER BY e.enrolled_date DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':instructor_id', $instructorId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        });
    }
}