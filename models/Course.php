<?php
/**
 * Course Model
 * Handles all course-related database operations
 */
namespace Models;

use PDO;

require_once __DIR__ . '/../config/Database.php';

class Course {
    private $conn;
    private $table = 'courses';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Get all courses with optional filters
     */
    public function getAll($filters = [], $limit = 100, $offset = 0) {
        $where = ["c.status = 'approved'"];
        $params = [];

        if (!empty($filters['category_id'])) {
            $where[] = "c.category_id = :category_id";
            $params[':category_id'] = $filters['category_id'];
        }

        if (!empty($filters['level'])) {
            $where[] = "c.level = :level";
            $params[':level'] = $filters['level'];
        }

        if (!empty($filters['search'])) {
            $where[] = "(c.title LIKE :search OR c.description LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['instructor_id'])) {
            $where[] = "c.instructor_id = :instructor_id";
            $params[':instructor_id'] = $filters['instructor_id'];
        }

        if (isset($filters['status'])) {
            $where = array_filter($where, fn($w) => strpos($w, 'status') === false);
            $where[] = "c.status = :status";
            $params[':status'] = $filters['status'];
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $query = "SELECT c.*, cat.name as category_name, u.fullname as instructor_name,
                  (SELECT COUNT(*) FROM enrollments e WHERE e.course_id = c.id) as enrollment_count
                  FROM {$this->table} c
                  LEFT JOIN categories cat ON c.category_id = cat.id
                  LEFT JOIN users u ON c.instructor_id = u.id
                  {$whereClause}
                  ORDER BY c.created_at DESC
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Get course by ID
     */
    public function getById($id) {
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
        return $stmt->fetch();
    }

    /**
     * Get courses by instructor
     */
    public function getByInstructor($instructorId) {
        $query = "SELECT c.*, cat.name as category_name,
                  (SELECT COUNT(*) FROM enrollments e WHERE e.course_id = c.id) as enrollment_count
                  FROM {$this->table} c
                  LEFT JOIN categories cat ON c.category_id = cat.id
                  WHERE c.instructor_id = :instructor_id
                  ORDER BY c.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':instructor_id', $instructorId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get courses by category
     */
    public function getByCategory($categoryId) {
        $query = "SELECT c.*, u.fullname as instructor_name
                  FROM {$this->table} c
                  LEFT JOIN users u ON c.instructor_id = u.id
                  WHERE c.category_id = :category_id AND c.status = 'approved'
                  ORDER BY c.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Create new course
     */
    public function create($data) {
        $query = "INSERT INTO {$this->table} 
                  (title, description, instructor_id, category_id, price, duration_weeks, level, image, status) 
                  VALUES (:title, :description, :instructor_id, :category_id, :price, :duration_weeks, :level, :image, :status)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':instructor_id', $data['instructor_id'], PDO::PARAM_INT);
        $stmt->bindParam(':category_id', $data['category_id'], PDO::PARAM_INT);
        $stmt->bindParam(':price', $data['price']);
        $stmt->bindParam(':duration_weeks', $data['duration_weeks'], PDO::PARAM_INT);
        $stmt->bindParam(':level', $data['level']);
        $stmt->bindParam(':image', $data['image']);
        $status = $data['status'] ?? 'pending';
        $stmt->bindParam(':status', $status);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    /**
     * Update course
     */
    public function update($id, $data) {
        $fields = [];
        $params = [':id' => $id];

        $allowedFields = ['title', 'description', 'category_id', 'price', 'duration_weeks', 'level', 'image', 'status'];

        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $fields[] = "{$field} = :{$field}";
                $params[":{$field}"] = $data[$field];
            }
        }

        if (empty($fields)) {
            return false;
        }

        $query = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute($params);
    }

    /**
     * Delete course
     */
    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Count courses
     */
    public function count($filters = []) {
        $where = [];
        $params = [];

        if (isset($filters['status'])) {
            $where[] = "status = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['instructor_id'])) {
            $where[] = "instructor_id = :instructor_id";
            $params[':instructor_id'] = $filters['instructor_id'];
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $query = "SELECT COUNT(*) FROM {$this->table} {$whereClause}";
        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    /**
     * Get pending courses for approval
     */
    public function getPending() {
        $query = "SELECT c.*, cat.name as category_name, u.fullname as instructor_name
                  FROM {$this->table} c
                  LEFT JOIN categories cat ON c.category_id = cat.id
                  LEFT JOIN users u ON c.instructor_id = u.id
                  WHERE c.status = 'pending'
                  ORDER BY c.created_at ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Approve or reject course
     */
    public function setStatus($id, $status) {
        $query = "UPDATE {$this->table} SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Search courses
     */
    public function search($keyword, $limit = 20) {
        $query = "SELECT c.*, cat.name as category_name, u.fullname as instructor_name
                  FROM {$this->table} c
                  LEFT JOIN categories cat ON c.category_id = cat.id
                  LEFT JOIN users u ON c.instructor_id = u.id
                  WHERE c.status = 'approved' 
                  AND (c.title LIKE :keyword OR c.description LIKE :keyword)
                  ORDER BY c.created_at DESC
                  LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $keyword = '%' . $keyword . '%';
        $stmt->bindParam(':keyword', $keyword);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}

