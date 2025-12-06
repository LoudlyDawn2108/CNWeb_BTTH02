<?php
/**
 * Course Model
 * Handles all course-related database operations
 */
require_once __DIR__ . '/../config/Database.php';
use Functional\Option;
use Functional\Result;

class Course {
    private $conn;
    private $table = 'courses';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    /**
     * Get course by ID
     * @return Option<array>
     */
    public function getById($id): Option {
        return Result::try(function() use ($id) {
            $query = "SELECT c.*, cat.name as category_name, u.fullname as instructor_name, u.email as instructor_email,
                      (SELECT COUNT(*) FROM enrollments e WHERE e.course_id = c.id) as enrollment_count,
                      (SELECT COUNT(*) FROM lessons l WHERE l.course_id = c.id) as lesson_count
                      FROM {$this->table} c
                      LEFT JOIN categories cat ON c.category_id = cat.id
                      LEFT JOIN users u ON c.instructor_id = u.id
                      WHERE c.id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch() ?: null;
        })->match(
            fn($val) => Option::fromNullable($val),
            fn() => Option::none()
        );
    }
}