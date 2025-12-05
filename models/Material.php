<?php
/**
 * Material Model
 * Handles database operations for course materials (files)
 */
namespace Models;

use PDO;

require_once __DIR__ . '/../config/Database.php';

class Material {
    private $conn;
    private $table = 'materials';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Lấy tất cả tài liệu của một bài học
     */
    public function getByLesson($lessonId) {
        $query = "SELECT * FROM {$this->table} WHERE lesson_id = :lesson_id ORDER BY uploaded_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':lesson_id', $lessonId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Lấy thông tin 1 tài liệu (Để lấy đường dẫn file trước khi xóa)
     */
    public function getById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Lưu thông tin file mới vào Database
     */
    public function create($data) {
        $query = "INSERT INTO {$this->table} (lesson_id, filename, file_path, file_type) 
                  VALUES (:lesson_id, :filename, :file_path, :file_type)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':lesson_id', $data['lesson_id'], PDO::PARAM_INT);
        $stmt->bindParam(':filename', $data['filename']);
        $stmt->bindParam(':file_path', $data['file_path']);
        $stmt->bindParam(':file_type', $data['file_type']);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * Xóa tài liệu khỏi Database
     */
    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}